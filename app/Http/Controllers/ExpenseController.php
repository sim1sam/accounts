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
    public function index()
    {
        $expenses = Expense::with(['account','currency'])->latest()->paginate(15);
        return view('admin.expenses.index', compact('expenses'));
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
        Log::info('processPayment: start', ['expense_id' => $expense->id]);
        if ($expense->isPaid()) {
            Log::warning('processPayment: already paid', ['expense_id' => $expense->id]);
            return redirect()->route('admin.expenses.show', $expense)
                ->with('error', 'Expense is already paid.');
        }

        $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'payment_amount' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            $bank = Bank::with('currency')->findOrFail($request->bank_id);
            $paymentNative = (float) $request->payment_amount; // entered in bank's native currency
            $rate = $bank->currency ? (float) ($bank->currency->conversion_rate ?? 1) : 1.0;
            if ($rate <= 0) { $rate = 1.0; }
            $convertedBDT = $bank->currency && strtoupper($bank->currency->code ?? 'BDT') !== 'BDT'
                ? $paymentNative * $rate
                : $paymentNative;
            Log::info('processPayment: computed amounts', [
                'bank_id' => $bank->id,
                'bank_code' => optional($bank->currency)->code,
                'rate' => $rate,
                'payment_native' => $paymentNative,
                'converted_bdt' => $convertedBDT,
                'expense_bdt' => (float) $expense->amount_in_bdt,
            ]);

            // Determine remaining amount in BDT for this expense (sum of prior payments)
            $expectedBDT = (float) $expense->amount_in_bdt;
            $paidBDT = (float) $expense->accountTransactions()
                ->where('type', 'expense')
                ->sum('amount');
            $remainingBDT = max($expectedBDT - $paidBDT, 0.0);

            // Compute the expected native amount for remaining due in this bank currency
            $expectedNative = strtoupper(optional($bank->currency)->code ?? 'BDT') === 'BDT'
                ? $remainingBDT
                : ($rate > 0 ? ($remainingBDT / $rate) : $remainingBDT);

            // Compare after rounding to 2 decimals to avoid float mismatch; allow partial up to remaining
            $convertedRounded = round($convertedBDT, 2);
            $remainingRounded = round($remainingBDT, 2);
            if ($convertedRounded > $remainingRounded + 0.02) {
                Log::warning('processPayment: attempted overpay', [
                    'convertedRounded' => $convertedRounded,
                    'remainingRounded' => $remainingRounded,
                ]);
                return redirect()->back()
                    ->withErrors(['payment_amount' => 'You cannot pay more than the remaining due. Max allowed about ' . number_format($expectedNative, 2) . ' ' . (optional($bank->currency)->code ?? 'BDT') . ' (≈ BDT ' . number_format($remainingRounded, 2) . ').'])
                    ->withInput();
            }

            // Check bank has sufficient native balance
            if ((float) ($bank->current_balance ?? 0) + 1e-6 < $paymentNative) {
                Log::warning('processPayment: insufficient bank balance', [
                    'bank_balance' => (float) ($bank->current_balance ?? 0),
                    'needed' => $paymentNative,
                ]);
                return redirect()->back()
                    ->withErrors(['bank_id' => 'Insufficient bank balance.'])
                    ->withInput();
            }

            // Create transaction record in main transaction history (store native amount in Transaction.amount)
            $transaction = Transaction::create([
                'type' => 'debit', // per schema enum ['credit','debit']
                'amount' => $paymentNative,
                'description' => 'Payment for expense on account: ' . $expense->account->name,
                'bank_id' => $bank->id,
                'transaction_date' => now()->toDateString(),
                'reference_type' => 'App\\Models\\Expense',
                'reference_id' => $expense->id
            ]);
            Log::info('processPayment: transaction created', ['transaction_id' => $transaction->id ?? null]);

            // Update bank balance in native currency using helper
            if (!$bank->decreaseBalance($paymentNative, false)) {
                Log::error('processPayment: bank decreaseBalance failed');
                return redirect()->back()
                    ->withErrors(['bank_id' => 'Bank balance update failed.'])
                    ->withInput();
            }

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
