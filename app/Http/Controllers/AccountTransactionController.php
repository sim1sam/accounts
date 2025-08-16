<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransaction;
use Illuminate\Http\Request;

class AccountTransactionController extends Controller
{
    /**
     * Display account transaction history.
     */
    public function index(Request $request)
    {
        $accounts = Account::all();
        $selectedAccountId = $request->get('account_id');
        
        $query = AccountTransaction::with(['account']);
        
        if ($selectedAccountId) {
            $query->where('account_id', $selectedAccountId);
        }
        
        $transactions = $query->latest()->paginate(20);
        
        return view('admin.account-transactions.index', compact('transactions', 'accounts', 'selectedAccountId'));
    }

    /**
     * Display account transaction history for a specific account.
     */
    public function show(Account $account)
    {
        $transactions = AccountTransaction::where('account_id', $account->id)
            ->latest()
            ->paginate(20);
            
        return view('admin.account-transactions.show', compact('account', 'transactions'));
    }
}
