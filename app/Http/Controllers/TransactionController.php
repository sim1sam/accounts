<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Bank;
use App\Models\Payment;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transactions = Transaction::with(['bank', 'payment.customer'])
            ->latest()
            ->paginate(10);
            
        return view('admin.transactions.index', compact('transactions'));
    }

    /**
     * Display the specified transaction.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        return view('admin.transactions.show', compact('transaction'));
    }
}
