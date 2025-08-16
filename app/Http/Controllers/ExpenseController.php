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

class ExpenseController extends Controller
{
    /**
     * Display a listing of the expenses.
     */
    public function index()
    {
        $expenses = Expense::with(['account'])->latest()->paginate(15);
        return view('admin.expenses.index', compact('expenses'));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        $accounts = Account::where('is_active', true)->get();
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
        ]);

        try {
            DB::beginTransaction();

            $account = Account::findOrFail($request->account_id);
            
            // Calculate amount in BDT using account's currency
            $amountInBDT = $request->amount * $account->currency->conversion_rate;
            
            // Create expense
            $expense = Expense::create([
                'amount' => $request->amount,
                'account_id' => $request->account_id,
                'remarks' => $request->remarks,
                'amount_in_bdt' => $amountInBDT,
                'status' => 'pending'
            ]);

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
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while creating the expense.'])
                ->withInput();
        }
    }

    /**
     * Display the specified expense.
     */
    public function show(Expense $expense)
    {
        $expense->load(['account', 'transaction', 'accountTransactions']);
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

        $accounts = Account::where('is_active', true)->get();
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

            $newAccount = Account::findOrFail($request->account_id);
            $newAmountInBDT = $request->amount * $newAccount->currency->conversion_rate;
            
            // Reverse the old expense from account (DECREASE since we added it before)
            $oldAccount = $expense->account;
            $oldAccount->current_amount -= $expense->amount_in_bdt;
            $oldAccount->save();

            // Apply new expense to new account (INCREASE for pending expense)
            $newAccount = Account::findOrFail($request->account_id);

            $balanceBefore = $newAccount->current_amount;
            $newAccount->current_amount += $newAmountInBDT;
            $newAccount->save();

            // Update expense
            $expense->update([
                'amount' => $request->amount,
                'account_id' => $request->account_id,
                'remarks' => $request->remarks,
                'amount_in_bdt' => $newAmountInBDT,
            ]);

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

        $banks = Bank::where('is_active', true)->get();
        return view('admin.expenses.payment', compact('expense', 'banks'));
    }

    /**
     * Process payment for an expense.
     */
    public function processPayment(Request $request, Expense $expense)
    {
        if ($expense->isPaid()) {
            return redirect()->route('admin.expenses.show', $expense)
                ->with('error', 'Expense is already paid.');
        }

        $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'payment_amount' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            $bank = Bank::findOrFail($request->bank_id);
            $paymentAmount = $request->payment_amount;

            // Check if bank has sufficient balance
            if ($bank->current_balance < $paymentAmount) {
                return redirect()->back()
                    ->withErrors(['bank_id' => 'Insufficient bank balance.'])
                    ->withInput();
            }

            // Create transaction record in main transaction history
            $transaction = Transaction::create([
                'type' => 'expense',
                'amount' => $paymentAmount,
                'description' => 'Payment for expense on account: ' . $expense->account->name,
                'bank_id' => $bank->id,
                'reference_type' => 'App\Models\Expense',
                'reference_id' => $expense->id
            ]);

            // Update bank balance
            $bank->current_balance -= $paymentAmount;
            $bank->save();

            // Update account balance (DECREASE when paid)
            $account = $expense->account;
            $accountBalanceBefore = $account->current_amount;
            $account->current_amount -= $expense->amount_in_bdt;
            $account->save();

            // Create account transaction record for payment
            AccountTransaction::create([
                'account_id' => $account->id,
                'type' => 'expense',
                'description' => 'Expense paid for account: ' . $expense->account->name,
                'amount' => $expense->amount_in_bdt,
                'balance_before' => $accountBalanceBefore,
                'balance_after' => $account->current_amount,
                'reference_type' => 'App\Models\Expense',
                'reference_id' => $expense->id
            ]);

            // Update expense status
            $expense->update([
                'status' => 'paid',
                'paid_at' => now(),
                'transaction_id' => $transaction->id
            ]);

            DB::commit();

            return redirect()->route('admin.expenses.show', $expense)
                ->with('success', 'Expense payment processed successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while processing payment.'])
                ->withInput();
        }
    }
}
