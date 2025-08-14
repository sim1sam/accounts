<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\Customer;
use App\Models\Bank;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RefundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $refunds = Refund::with(['customer', 'bank'])->latest()->get();
        return view('admin.refunds.index', compact('refunds'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::all();
        $banks = Bank::where('is_active', true)->get();
        return view('admin.refunds.create', compact('customers', 'banks'));
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
                $validator = validator($refundData, [
                    'customer_id' => 'required|exists:customers,id',
                    'bank_id' => 'required|exists:banks,id',
                    'refund_amount' => 'required|numeric|min:0',
                    'refund_date' => 'required|date',
                    'remarks' => 'nullable|string'
                ]);
                
                if ($validator->fails()) {
                    \Log::error('Validation failed:', ['errors' => $validator->errors()->toArray()]);
                    return back()
                        ->withErrors($validator)
                        ->withInput();
                }
                
                // Begin transaction
                DB::beginTransaction();
                try {
                    // Create the refund
                    $refund = Refund::create($refundData);
                    
                    // Get bank and decrease balance
                    $bank = Bank::findOrFail($refundData['bank_id']);
                    if (!$bank->decreaseBalance($refundData['refund_amount'])) {
                        throw new \Exception('Insufficient balance in bank account');
                    }
                    
                    // Get customer details for transaction description
                    $customer = Customer::findOrFail($refundData['customer_id']);
                    
                    // Create transaction record
                    Transaction::create([
                        'payment_id' => null,
                        'refund_id' => $refund->id,
                        'bank_id' => $bank->id,
                        'amount' => $refundData['refund_amount'],
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
                'remarks' => 'nullable|string'
            ]);

            DB::beginTransaction();
            try {
                // Create refund
                $refund = Refund::create($request->all());
                
                // Get bank and decrease balance
                $bank = Bank::findOrFail($request->bank_id);
                if (!$bank->decreaseBalance($request->refund_amount)) {
                    throw new \Exception('Insufficient balance in bank account');
                }
                
                // Get customer details for transaction description
                $customer = Customer::findOrFail($request->customer_id);
                
                // Create transaction record
                Transaction::create([
                    'payment_id' => null,
                    'refund_id' => $refund->id,
                    'bank_id' => $bank->id,
                    'amount' => $request->refund_amount,
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
        $refund = Refund::with(['customer', 'bank'])->findOrFail($id);
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
