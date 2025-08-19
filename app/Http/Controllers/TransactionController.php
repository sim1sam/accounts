<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Bank;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    /**
     * Void a transaction and revert bank balance.
     */
    public function void(Request $request, Transaction $transaction)
    {
        if ($transaction->voided_at) {
            return back()->with('success', 'Transaction already voided.');
        }

        $bank = Bank::with('currency')->find($transaction->bank_id);
        if (!$bank) {
            return back()->withErrors(['error' => 'Bank not found for this transaction.']);
        }

        DB::beginTransaction();
        try {
            $amountBDT = (float) $transaction->amount; // stored in BDT
            $isBDT = true;

            $txType = strtolower((string) $transaction->type);
            if ($txType === 'credit') {
                // original increased balance -> reverse by decreasing
                if (!$bank->decreaseBalance($amountBDT, $isBDT)) {
                    throw new \Exception('Unable to decrease bank balance to void this transaction.');
                }
            } else {
                // original decreased balance -> reverse by increasing
                if (!$bank->increaseBalance($amountBDT, $isBDT)) {
                    throw new \Exception('Unable to increase bank balance to void this transaction.');
                }
            }

            $transaction->voided_at = now();
            $transaction->void_reason = $request->input('void_reason');
            $transaction->save();

            DB::commit();
            return back()->with('success', 'Transaction voided and bank balance reverted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
