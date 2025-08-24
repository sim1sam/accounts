<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\Customer;
use App\Models\Bank;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RefundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Refund::query()->with(['customer', 'bank.currency'])->latest();

        // Free text search: customer name/mobile or remarks
        $q = trim((string) $request->get('q'));
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->whereHas('customer', function ($c) use ($q) {
                        $c->where('name', 'like', "%{$q}%")
                          ->orWhere('mobile', 'like', "%{$q}%");
                    })
                  ->orWhere('remarks', 'like', "%{$q}%");
            });
        }

        // Bank filter
        if ($request->filled('bank_id')) {
            $query->where('bank_id', $request->integer('bank_id'));
        }

        // Refund date range
        if ($request->filled('refund_date_from')) {
            $query->whereDate('refund_date', '>=', $request->date('refund_date_from'));
        }
        if ($request->filled('refund_date_to')) {
            $query->whereDate('refund_date', '<=', $request->date('refund_date_to'));
        }

        // Amount range (values stored in BDT)
        if ($request->filled('min_amount')) {
            $query->where('refund_amount', '>=', (float) $request->get('min_amount'));
        }
        if ($request->filled('max_amount')) {
            $query->where('refund_amount', '<=', (float) $request->get('max_amount'));
        }

        $refunds = $query->paginate(10)->withQueryString();

        // Banks for filter dropdown
        $banks = Bank::with('currency')->where('is_active', true)->orderBy('name')->get();

        return view('admin.refunds.index', compact('refunds', 'banks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::all();
        $banks = Bank::with('currency')->where('is_active', true)->get();

        // Optional prefill from a cancellation
        $prefill = null;
        $fromCancellation = request()->get('from_cancellation');
        if ($fromCancellation) {
            $cancellation = \App\Models\Cancellation::with('customer')->find($fromCancellation);
            if ($cancellation) {
                $prefill = [
                    'customer_id' => $cancellation->customer_id,
                    'refund_amount' => (float) $cancellation->cancellation_value,
                    'refund_date' => now()->toDateString(),
                    'remarks' => 'Refund for Cancellation #' . $cancellation->id,
                    'cancellation_id' => $cancellation->id,
                    'customer_name' => optional($cancellation->customer)->name,
                    'customer_mobile' => optional($cancellation->customer)->mobile,
                ];
            }
        }

        return view('admin.refunds.create', compact('customers', 'banks', 'prefill'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug the request data
        \Log::info('Refund store method called');
        \Log::info('Request data:', $request->all());
        
        // Check if we're dealing with multiple refunds
        if ($request->has('refunds')) {
            $refundsData = $request->refunds;
            $count = 0;
            
            \Log::info('Processing multiple refunds:', ['count' => count($refundsData)]);
            
            foreach ($refundsData as $index => $refundData) {
                \Log::info('Processing refund entry:', ['index' => $index, 'data' => $refundData]);
                
                // Validate each refund entry
                $validator = Validator::make($refundData, [
                    'customer_id' => 'required|exists:customers,id',
                    'bank_id' => 'required|exists:banks,id',
                    'refund_amount' => 'required|numeric|min:0',
                    'refund_date' => 'required|date',
                    'remarks' => 'nullable|string',
                    'cancellation_id' => 'nullable|exists:cancellations,id'
                ]);
                
                if ($validator->fails()) {
                    \Log::error('Validation failed:', ['errors' => $validator->errors()->toArray()]);
                    return back()
                        ->withErrors($validator->errors())
                        ->withInput();
                }
                
                // Begin transaction
                DB::beginTransaction();
                try {
                    // Get bank (with currency) and prepare amounts
                    $bank = Bank::with('currency')->findOrFail($refundData['bank_id']);
                    $isBDT = !$bank->currency || strtoupper($bank->currency->code ?? 'BDT') === 'BDT';
                    $rate = $bank->currency ? (float) ($bank->currency->conversion_rate ?? 1) : 1.0;
                    if ($rate <= 0) { $rate = 1.0; }
                    $inputAmount = (float) $refundData['refund_amount'];
                    $amountBDT = $isBDT ? $inputAmount : $inputAmount * $rate;

                    // Create the refund using BDT amount for consistency
                    $dataToCreate = $refundData;
                    $dataToCreate['refund_amount'] = $amountBDT;
                    $refund = Refund::create($dataToCreate);

                    // Decrease bank balance using native or BDT based on bank currency
                    if (!$bank->decreaseBalance($inputAmount, $isBDT)) {
                        throw new \Exception('Insufficient balance in bank account for entry #' . ($index + 1));
                    }
                    
                    // Get customer details for transaction description
                    $customer = Customer::findOrFail($refundData['customer_id']);
                    
                    // Create transaction record
                    Transaction::create([
                        'payment_id' => null,
                        'refund_id' => $refund->id,
                        'bank_id' => $bank->id,
                        // Store transaction amount in BDT
                        'amount' => $amountBDT,
                        'type' => 'debit',
                        'description' => 'Refund issued to ' . $customer->name . ' (' . $customer->mobile . ')',
                        'transaction_date' => $refundData['refund_date'],
                    ]);
                    
                    DB::commit();
                    $count++;
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()
                        ->withErrors(['error' => $e->getMessage()])
                        ->withInput();
                }
            }
            
            return redirect()->route('admin.refunds.index')
                ->with('success', $count . ' refunds created successfully.');
        } else {
            // Handle single refund (backward compatibility)
            $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'bank_id' => 'required|exists:banks,id',
                'refund_amount' => 'required|numeric|min:0',
                'refund_date' => 'required|date',
                'remarks' => 'nullable|string',
                'cancellation_id' => 'nullable|exists:cancellations,id'
            ]);

            DB::beginTransaction();
            try {
                // Prepare bank and currency conversion
                $bank = Bank::with('currency')->findOrFail($request->bank_id);
                $isBDT = !$bank->currency || strtoupper($bank->currency->code ?? 'BDT') === 'BDT';
                $rate = $bank->currency ? (float) ($bank->currency->conversion_rate ?? 1) : 1.0;
                if ($rate <= 0) { $rate = 1.0; }
                $inputAmount = (float) $request->refund_amount; // native input
                $amountBDT = $isBDT ? $inputAmount : $inputAmount * $rate; // store in BDT

                // Create refund storing BDT amount
                $dataToCreate = $request->only(['customer_id','bank_id','refund_date','remarks','cancellation_id']);
                $dataToCreate['refund_amount'] = $amountBDT;
                $refund = Refund::create($dataToCreate);

                // Decrease bank balance using native amount with currency awareness
                if (!$bank->decreaseBalance($inputAmount, $isBDT)) {
                    throw new \Exception('Insufficient balance in bank account');
                }

                // Get customer details for transaction description
                $customer = Customer::findOrFail($request->customer_id);

                // Create transaction record with BDT amount
                Transaction::create([
                    'payment_id' => null,
                    'refund_id' => $refund->id,
                    'bank_id' => $bank->id,
                    'amount' => $amountBDT,
                    'type' => 'debit',
                    'description' => 'Refund issued to ' . $customer->name . ' (' . $customer->mobile . ')',
                    'transaction_date' => $request->refund_date,
                ]);

                DB::commit();
                return redirect()->route('admin.refunds.index')
                    ->with('success', 'Refund created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()
                    ->withErrors(['error' => $e->getMessage()])
                    ->withInput();
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $refund = Refund::with(['customer', 'bank.currency', 'cancellation'])->findOrFail($id);
        return view('admin.refunds.show', compact('refund'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $refund = Refund::findOrFail($id);
        $customers = Customer::all();
        $banks = Bank::where('is_active', true)->get();
        return view('admin.refunds.edit', compact('refund', 'customers', 'banks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'bank_id' => 'required|exists:banks,id',
            'refund_amount' => 'required|numeric|min:0',
            'refund_date' => 'required|date',
            'remarks' => 'nullable|string'
        ]);

        $refund = Refund::findOrFail($id);
        
        // Begin transaction
        DB::beginTransaction();
        try {
            // Find related transaction if it exists
            $transaction = Transaction::where('refund_id', $refund->id)->first();
            
            // If amount or bank changed, update bank balances
            if ($refund->refund_amount != $request->refund_amount || $refund->bank_id != $request->bank_id) {
                // Restore old bank balance if bank exists
                if ($refund->bank_id) {
                    $oldBank = Bank::findOrFail($refund->bank_id);
                    $oldBank->increaseBalance($refund->refund_amount);
                }
                
                // Decrease new bank balance
                $newBank = Bank::findOrFail($request->bank_id);
                if (!$newBank->decreaseBalance($request->refund_amount)) {
                    throw new \Exception('Insufficient balance in bank account');
                }
                
                // Update transaction if it exists
                if ($transaction) {
                    $customer = Customer::findOrFail($request->customer_id);
                    $transaction->update([
                        'bank_id' => $request->bank_id,
                        'amount' => $request->refund_amount,
                        'description' => 'Refund issued to ' . $customer->name . ' (' . $customer->mobile . ')',
                        'transaction_date' => $request->refund_date,
                    ]);
                }
            }
            
            // Update refund
            $refund->update($request->all());
            
            DB::commit();
            return redirect()->route('admin.refunds.index')
                ->with('success', 'Refund updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $refund = Refund::findOrFail($id);
        $refund->delete();

        return redirect()->route('admin.refunds.index')
            ->with('success', 'Refund deleted successfully.');
    }
}
