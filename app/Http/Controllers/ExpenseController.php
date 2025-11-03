<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Account;
use App\Models\Currency;
use App\Models\Bank;
use App\Models\Transaction;
use App\Models\AccountTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the expenses.
     */
    public function index(Request $request)
    {
        $query = Expense::query()->with(['account','currency'])->latest();

        // Free text search: account name or remarks
        $q = trim((string) $request->get('q'));
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->whereHas('account', function ($a) use ($q) {
                        $a->where('name', 'like', "%{$q}%");
                    })
                  ->orWhere('remarks', 'like', "%{$q}%");
            });
        }

        // Account filter
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->integer('account_id'));
        }

        // Expense date range
        if ($request->filled('expense_date_from')) {
            $query->whereDate('expense_date', '>=', $request->date('expense_date_from'));
        }
        if ($request->filled('expense_date_to')) {
            $query->whereDate('expense_date', '<=', $request->date('expense_date_to'));
        }

        // Amount range (BDT)
        if ($request->filled('min_amount')) {
            $query->where('amount_in_bdt', '>=', (float) $request->get('min_amount'));
        }
        if ($request->filled('max_amount')) {
            $query->where('amount_in_bdt', '<=', (float) $request->get('max_amount'));
        }

        $expenses = $query->paginate(15)->withQueryString();

        $accounts = Account::orderBy('name')->get();

        return view('admin.expenses.index', compact('expenses', 'accounts'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        $accounts = Account::with('currency')->where('is_active', true)->get();
        return view('admin.expenses.create', compact('accounts'));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:accounts,id',
            'remarks' => 'nullable|string|max:1000',
            'expense_date' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $account = Account::with('currency')->findOrFail($request->account_id);
            
            // Calculate amount in BDT using account's currency (fallback rate=1)
            $rate = optional($account->currency)->conversion_rate ?? 1;
            if ($rate <= 0) { $rate = 1; }
            $amountInBDT = $request->amount * $rate;
            
            // Legacy support: purpose column might still exist and be NOT NULL
            $purpose = null;
            if (Schema::hasColumn('expenses', 'purpose')) {
                $purpose = $request->input('purpose');
                if ($purpose === null || $purpose === '') { $purpose = 'Expense'; }
            }

            // Create expense
            $expenseData = [
                'amount' => $request->amount,
                'account_id' => $request->account_id,
                'currency_id' => $account->currency_id,
                'remarks' => $request->remarks,
                'amount_in_bdt' => $amountInBDT,
                'status' => 'pending',
                'expense_date' => $request->expense_date,
            ];
            if ($purpose !== null) { $expenseData['purpose'] = $purpose; }
            $expense = Expense::create($expenseData);

            // Update account balance (INCREASE for pending expense)
            $balanceBefore = $account->current_amount;
            $account->current_amount += $amountInBDT;
            $account->save();

            // Create account transaction record
            AccountTransaction::create([
                'account_id' => $account->id,
                'type' => 'expense',
                'description' => 'Expense pending for account: ' . $account->name,
                'amount' => $amountInBDT,
                'balance_before' => $balanceBefore,
                'balance_after' => $account->current_amount,
                'reference_type' => 'App\Models\Expense',
                'reference_id' => $expense->id
            ]);

            DB::commit();

            return redirect()->route('admin.expenses.index')
                ->with('success', 'Expense created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Expense create failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while creating the expense: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified expense.
     */
    public function show(Expense $expense)
    {
        $expense->load(['account.currency', 'currency', 'transaction.bank.currency', 'accountTransactions']);
        return view('admin.expenses.show', compact('expense'));
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(Expense $expense)
    {
        if ($expense->isPaid()) {
            return redirect()->route('admin.expenses.show', $expense)
                ->with('error', 'Cannot edit a paid expense.');
        }

        $accounts = Account::with('currency')->where('is_active', true)->get();
        return view('admin.expenses.edit', compact('expense', 'accounts'));
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        if ($expense->isPaid()) {
            return redirect()->route('admin.expenses.show', $expense)
                ->with('error', 'Cannot update a paid expense.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'account_id' => 'required|exists:accounts,id',
            'remarks' => 'nullable|string|max:1000',
            'expense_date' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $newAccount = Account::with('currency')->findOrFail($request->account_id);
            $newRate = optional($newAccount->currency)->conversion_rate ?? 1;
            if ($newRate <= 0) { $newRate = 1; }
            $newAmountInBDT = $request->amount * $newRate;
            
            // Reverse the old expense from account (DECREASE since we added it before)
            $oldAccount = $expense->account;
            $oldAccount->current_amount -= $expense->amount_in_bdt;
            $oldAccount->save();

            // Apply new expense to new account (INCREASE for pending expense)
            $newAccount = Account::findOrFail($request->account_id);

            $balanceBefore = $newAccount->current_amount;
            $newAccount->current_amount += $newAmountInBDT;
            $newAccount->save();

            // Legacy support: purpose column might still exist
            $purpose = null;
            if (Schema::hasColumn('expenses', 'purpose')) {
                $purpose = $request->input('purpose');
                if ($purpose === null || $purpose === '') { $purpose = $expense->purpose ?? 'Expense'; }
            }

            // Update expense
            $updateData = [
                'amount' => $request->amount,
                'account_id' => $request->account_id,
                'currency_id' => $newAccount->currency_id,
                'remarks' => $request->remarks,
                'amount_in_bdt' => $newAmountInBDT,
                'expense_date' => $request->expense_date,
            ];
            if ($purpose !== null) { $updateData['purpose'] = $purpose; }
            $expense->update($updateData);

            // Update account transaction record
            $expense->accountTransactions()->delete(); // Remove old transaction
            AccountTransaction::create([
                'account_id' => $newAccount->id,
                'type' => 'expense',
                'description' => 'Expense for account: ' . $newAccount->name,
                'amount' => $newAmountInBDT,
                'balance_before' => $balanceBefore,
                'balance_after' => $newAccount->current_amount,
                'reference_type' => 'App\Models\Expense',
                'reference_id' => $expense->id
            ]);

            DB::commit();

            return redirect()->route('admin.expenses.index')
                ->with('success', 'Expense updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while updating the expense.'])
                ->withInput();
        }
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Expense $expense)
    {
        if ($expense->isPaid()) {
            return redirect()->route('admin.expenses.index')
                ->with('error', 'Cannot delete a paid expense.');
        }

        try {
            DB::beginTransaction();

            // Reverse the expense from account (DECREASE since we added it when creating)
            $account = $expense->account;
            $account->current_amount -= $expense->amount_in_bdt;
            $account->save();

            // Delete account transactions
            $expense->accountTransactions()->delete();

            // Delete expense
            $expense->delete();

            DB::commit();

            return redirect()->route('admin.expenses.index')
                ->with('success', 'Expense deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('admin.expenses.index')
                ->with('error', 'An error occurred while deleting the expense.');
        }
    }

    /**
     * Show the payment form for an expense.
     */
    public function showPaymentForm(Expense $expense)
    {
        if ($expense->isPaid()) {
            return redirect()->route('admin.expenses.show', $expense)
                ->with('error', 'Expense is already paid.');
        }

        $banks = Bank::with('currency')->where('is_active', true)->get();
        $expense->loadMissing(['currency','account.currency']);
        return view('admin.expenses.payment', compact('expense', 'banks'));
    }

    /**
     * Process payment for an expense.
     */
    public function processPayment(Request $request, Expense $expense)
    {
        if ($expense->isPaid()) {
            Log::warning('processPayment: already paid', ['expense_id' => $expense->id]);
            return redirect()->route('admin.expenses.show', $expense)
                ->with('error', 'Expense is already paid.');
        }

        $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_amount_bdt' => 'nullable|numeric',
        ]);

        try {
            DB::beginTransaction();
            
            // Get bank and payment amount
            $bank = Bank::with('currency')->findOrFail($request->bank_id);
            $paymentNative = (float) $request->payment_amount;
            $currencyCode = strtoupper($bank->currency->code ?? 'BDT');
            
            // Calculate BDT amount based on currency
            if ($request->has('payment_amount_bdt') && $request->payment_amount_bdt > 0) {
                // Use provided BDT amount if available
                $convertedBDT = (float) $request->payment_amount_bdt;
                $rate = $currencyCode === 'BDT' ? 1.0 : ($convertedBDT / $paymentNative);
            } else {
                // Calculate based on currency
                if ($currencyCode === 'BDT') {
                    $convertedBDT = $paymentNative;
                    $rate = 1.0;
                } else if ($currencyCode === 'INR') {
                    // Special case for INR: 1 INR = 1.45 BDT
                    $convertedBDT = $paymentNative * 1.45;
                    $rate = 1.45;
                } else {
                    // For other currencies, use the bank's conversion rate
                    $rate = (float) ($bank->currency->conversion_rate ?? 1);
                    if ($rate <= 0) { $rate = 1.0; }
                    $convertedBDT = $paymentNative * $rate;
                }
            }
            
            // Log the conversion details
            Log::info('processPayment: computed amounts', [
                'bank_id' => $bank->id,
                'bank_code' => $currencyCode,
                'rate' => $rate,
                'payment_native' => $paymentNative,
                'converted_bdt' => $convertedBDT,
                'expense_bdt' => (float) $expense->amount_in_bdt,
            ]);

            // Determine remaining amount in BDT for this expense
            $expectedBDT = (float) $expense->amount_in_bdt;
            
            // Get all transactions except the initial pending one (which is created when expense is created)
            $initialTransaction = $expense->accountTransactions()
                ->where('type', 'expense')
                ->orderBy('created_at')
                ->first();
                
            $initialTransactionId = $initialTransaction ? $initialTransaction->id : 0;
            
            $paidBDT = (float) $expense->accountTransactions()
                ->where('type', 'expense')
                ->where('id', '!=', $initialTransactionId)
                ->sum('amount');
                
            $remainingBDT = max($expectedBDT - $paidBDT, 0.0);

            // Compute the expected native amount for remaining due in this bank currency
            $expectedNative = $currencyCode === 'BDT'
                ? $remainingBDT
                : ($rate > 0 ? ($remainingBDT / $rate) : $remainingBDT);

            // Compare after rounding to 2 decimals to avoid float mismatch; allow partial up to remaining
            // Add a small tolerance for rounding errors (0.05 instead of 0.02)
            $convertedRounded = round($convertedBDT, 2);
            $remainingRounded = round($remainingBDT, 2);
            if ($convertedRounded > $remainingRounded + 0.05) {
                Log::warning('processPayment: attempted overpay', [
                    'convertedRounded' => $convertedRounded,
                    'remainingRounded' => $remainingRounded,
                ]);
                return redirect()->back()
                    ->withErrors(['payment_amount' => 'You cannot pay more than the remaining due. Max allowed about ' . number_format($expectedNative, 2) . ' ' . $currencyCode . ' (≈ BDT ' . number_format($remainingRounded, 2) . ').'])
                    ->withInput();
            }

            // Allow payments even when bank has negative balance (credit accounts)
            // Payment will be processed and bank balance will go more negative if needed
            Log::info('processPayment: processing payment', [
                'bank_balance' => (float) ($bank->current_balance ?? 0),
                'payment_amount' => $paymentNative,
                'new_balance_will_be' => (float) ($bank->current_balance ?? 0) - $paymentNative,
            ]);

            // Add currency conversion info to the description
            $currencyDescription = '';
            if ($currencyCode !== 'BDT') {
                $currencyDescription = ' (' . $currencyCode . ' ' . number_format($paymentNative, 2) . 
                    ' = BDT ' . number_format($convertedRounded, 2) . ')';
            }
            
            // Create transaction record in main transaction history
            $transaction = Transaction::create([
                'type' => 'debit', // per schema enum ['credit','debit']
                'amount' => $paymentNative,
                'description' => 'Payment for expense on account: ' . $expense->account->name . $currencyDescription,
                'bank_id' => $bank->id,
                'transaction_date' => now()->toDateString(),
                'reference_type' => 'App\\Models\\Expense',
                'reference_id' => $expense->id,
                'meta' => json_encode([
                    'native_currency' => $currencyCode,
                    'native_amount' => $paymentNative,
                    'bdt_amount' => $convertedRounded,
                    'conversion_rate' => $rate
                ])
            ]);
            Log::info('processPayment: transaction created', ['transaction_id' => $transaction->id ?? null]);

            // Update bank balance in native currency using helper
            // decreaseBalance now allows negative balances, so it should always succeed
            $bank->decreaseBalance($paymentNative, false);
            Log::info('processPayment: bank balance updated', [
                'bank_id' => $bank->id,
                'new_balance' => $bank->current_balance,
                'new_balance_bdt' => $bank->amount_in_bdt,
            ]);

            // Update account balance (DECREASE when paid). For partial, decrease by the partial BDT amount
            $account = $expense->account;
            $accountBalanceBefore = $account->current_amount;
            $account->current_amount -= $convertedRounded;
            $account->save();
            Log::info('processPayment: account updated', ['account_id' => $account->id]);

            // Create account transaction record for payment
            AccountTransaction::create([
                'account_id' => $account->id,
                'type' => 'expense',
                'description' => 'Expense paid for account: ' . $expense->account->name,
                'amount' => $convertedRounded,
                'balance_before' => $accountBalanceBefore,
                'balance_after' => $account->current_amount,
                'reference_type' => 'App\Models\Expense',
                'reference_id' => $expense->id
            ]);

            // Update expense status depending on remaining due after this payment
            $newPaidBDT = $paidBDT + $convertedRounded;
            $newRemainingBDT = max($expectedBDT - $newPaidBDT, 0.0);
            
            // Log payment details for debugging
            Log::info('processPayment: payment details', [
                'expense_id' => $expense->id,
                'expected_bdt' => $expectedBDT,
                'paid_bdt_before' => $paidBDT,
                'current_payment_bdt' => $convertedRounded,
                'new_paid_total' => $newPaidBDT,
                'new_remaining' => $newRemainingBDT
            ]);
            
            // Only mark as paid if the remaining amount is very close to zero
            if ($newRemainingBDT <= 0.02) {
                $expense->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'transaction_id' => $transaction->id
                ]);
                Log::info('processPayment: expense marked paid', ['expense_id' => $expense->id]);
            } else {
                $expense->update([
                    'status' => 'partial',
                    'transaction_id' => $transaction->id
                ]);
                Log::info('processPayment: expense marked partial', ['expense_id' => $expense->id, 'remaining_bdt' => $newRemainingBDT]);
            }

            DB::commit();

            return redirect()->route('admin.expenses.show', $expense)
                ->with('success', 'Expense payment processed successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('processPayment: exception', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()
                ->withErrors(['error' => 'Payment failed: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
