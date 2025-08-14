<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Staff;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('customer')->latest()->paginate(10);
        return view('admin.payments.index', compact('payments'));
    }

    public function create()
    {
        $customers = Customer::all();
        $banks = Bank::where('is_active', 1)->get();
        return view('admin.payments.create', compact('customers', 'banks'));
    }

    public function store(Request $request)
    {
        // Check if we have multiple payments
        if (isset($request->payments) && is_array($request->payments)) {
            $successCount = 0;
            
            foreach ($request->payments as $paymentData) {
                // Add debugging
                \Log::info('Processing payment data:', $paymentData);
                
                $validator = Validator::make($paymentData, [
                    'customer_id' => 'required|exists:customers,id',
                    'amount' => 'required|numeric|min:0',
                    'payment_date' => 'required|date',
                    'bank_id' => 'required|exists:banks,id',
                ]);
                
                if ($validator->fails()) {
                    \Log::error('Payment validation failed:', $validator->errors()->toArray());
                    continue;
                }
                
                // Validation passed, proceed with payment creation
                DB::beginTransaction();
                try {
                    // Create payment
                    $payment = Payment::create($paymentData);
                    
                    // Get bank and increase balance
                    $bank = Bank::findOrFail($paymentData['bank_id']);
                    $bank->increaseBalance($paymentData['amount']);
                    
                    // Get customer details for transaction description
                    $customer = Customer::findOrFail($paymentData['customer_id']);
                    
                    // Create transaction record
                    Transaction::create([
                        'payment_id' => $payment->id,
                        'bank_id' => $bank->id,
                        'amount' => $paymentData['amount'],
                        'type' => 'credit',
                        'description' => 'Payment received from ' . $customer->name . ' (' . $customer->mobile . ')',
                        'transaction_date' => $paymentData['payment_date'],
                    ]);
                    
                    DB::commit();
                    $successCount++;
                } catch (\Exception $e) {
                    DB::rollBack();
                    // Log the error
                    \Log::error('Payment creation failed: ' . $e->getMessage());
                }
            }
            
            return redirect()->route('admin.payments.index')
                ->with('success', $successCount . ' payment(s) created successfully.');
        } else {
            // Single payment (legacy support)
            // Add debugging for single payment
            \Log::info('Processing single payment:', $request->all());
            
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|exists:customers,id',
                'amount' => 'required|numeric|min:0',
                'payment_date' => 'required|date',
                'bank_id' => 'required|exists:banks,id',
            ]);

            if ($validator->fails()) {
                \Log::error('Single payment validation failed:', $validator->errors()->toArray());
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            DB::beginTransaction();
            try {
                // Create payment
                $payment = Payment::create($request->all());
                
                // Get bank and increase balance
                $bank = Bank::findOrFail($request->bank_id);
                $bank->increaseBalance($request->amount);
                
                // Get customer details for transaction description
                $customer = Customer::findOrFail($request->customer_id);
                
                // Create transaction record
                Transaction::create([
                    'payment_id' => $payment->id,
                    'bank_id' => $bank->id,
                    'amount' => $request->amount,
                    'type' => 'credit',
                    'description' => 'Payment received from ' . $customer->name . ' (' . $customer->mobile . ')',
                    'transaction_date' => $request->payment_date,
                ]);
                
                DB::commit();
                return redirect()->route('admin.payments.index')
                    ->with('success', 'Payment created successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                // Log the error
                \Log::error('Payment creation failed: ' . $e->getMessage());
                
                return redirect()->back()
                    ->with('error', 'Payment creation failed: ' . $e->getMessage())
                    ->withInput();
            }
        }
    }

    public function show(Payment $payment)
    {
        return view('admin.payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $customers = Customer::all();
        $banks = Bank::where('is_active', 1)->get();
        return view('admin.payments.edit', compact('payment', 'customers', 'banks'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'bank_id' => 'required|exists:banks,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Get old amount and bank
            $oldAmount = $payment->amount;
            $oldBankId = $payment->bank_id;
            
            // If bank changed or amount changed, update bank balances
            if ($oldBankId != $request->bank_id || $oldAmount != $request->amount) {
                // If bank changed, decrease old bank balance and increase new bank balance
                if ($oldBankId != $request->bank_id) {
                    // Decrease old bank balance
                    $oldBank = Bank::findOrFail($oldBankId);
                    $oldBank->decreaseBalance($oldAmount);
                    
                    // Increase new bank balance
                    $newBank = Bank::findOrFail($request->bank_id);
                    $newBank->increaseBalance($request->amount);
                    
                    // Create transaction records
                    Transaction::create([
                        'payment_id' => $payment->id,
                        'bank_id' => $oldBank->id,
                        'amount' => $oldAmount,
                        'type' => 'debit',
                        'description' => 'Payment updated - funds removed from bank ID: ' . $oldBank->id,
                        'transaction_date' => now(),
                    ]);
                    
                    Transaction::create([
                        'payment_id' => $payment->id,
                        'bank_id' => $newBank->id,
                        'amount' => $request->amount,
                        'type' => 'credit',
                        'description' => 'Payment updated - funds added to bank ID: ' . $newBank->id,
                        'transaction_date' => now(),
                    ]);
                } else {
                    // Same bank but amount changed
                    $bank = Bank::findOrFail($oldBankId);
                    
                    // If new amount is greater, increase balance by difference
                    if ($request->amount > $oldAmount) {
                        $difference = $request->amount - $oldAmount;
                        $bank->increaseBalance($difference);
                        
                        // Create transaction record
                        Transaction::create([
                            'payment_id' => $payment->id,
                            'bank_id' => $bank->id,
                            'amount' => $difference,
                            'type' => 'credit',
                            'description' => 'Payment amount increased by ' . $difference,
                            'transaction_date' => now(),
                        ]);
                    } 
                    // If new amount is less, decrease balance by difference
                    else if ($request->amount < $oldAmount) {
                        $difference = $oldAmount - $request->amount;
                        $bank->decreaseBalance($difference);
                        
                        // Create transaction record
                        Transaction::create([
                            'payment_id' => $payment->id,
                            'bank_id' => $bank->id,
                            'amount' => $difference,
                            'type' => 'debit',
                            'description' => 'Payment amount decreased by ' . $difference,
                            'transaction_date' => now(),
                        ]);
                    }
                }
            }
            
            // Update payment
            $payment->update($request->all());
            
            DB::commit();
            return redirect()->route('admin.payments.index')
                ->with('success', 'Payment updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error
            \Log::error('Payment update failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Payment update failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Payment $payment)
    {
        DB::beginTransaction();
        try {
            // Get bank and decrease balance
            $bank = Bank::findOrFail($payment->bank_id);
            $bank->decreaseBalance($payment->amount);
            
            // Create transaction record
            Transaction::create([
                'payment_id' => null, // Payment will be deleted
                'bank_id' => $bank->id,
                'amount' => $payment->amount,
                'type' => 'debit',
                'description' => 'Payment deleted - funds removed from bank. Original payment ID: ' . $payment->id,
                'transaction_date' => now(),
            ]);
            
            // Delete payment
            $payment->delete();
            
            DB::commit();
            return redirect()->route('admin.payments.index')
                ->with('success', 'Payment deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error
            \Log::error('Payment deletion failed: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Payment deletion failed: ' . $e->getMessage());
        }
    }
    
    public function findCustomer(Request $request)
    {
        $customer = Customer::where('mobile', $request->mobile)->first();
        
        if ($customer) {
            $kam = null;
            if ($customer->kam_id) {
                $staff = Staff::find($customer->kam_id);
                $kam = $staff ? $staff->name : null;
            }
            
            return response()->json([
                'success' => true,
                'customer' => $customer,
                'kam' => $kam
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Customer not found'
        ]);
    }
}
