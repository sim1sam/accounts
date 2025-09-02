<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\Currency;
use App\Models\Payment;
use App\Models\Refund;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    /**
     * Display the revenue menu.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Get BDT and INR currencies
        $bdtCurrency = Currency::where('code', 'BDT')->first();
        $inrCurrency = Currency::where('code', 'INR')->first();
        $inrRate = $inrCurrency->conversion_rate;
        
        // Get date range for filtering
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // Calculate All Payment = Sum all payment - refund in BDT for the date range
        $totalPayments = Payment::whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])->sum('amount');
        $totalRefunds = Refund::whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])->sum('refund_amount');
        $netPayments = $totalPayments - $totalRefunds;
        
        // Convert to INR
        $totalPaymentsInr = $totalPayments / $inrRate;
        $totalRefundsInr = $totalRefunds / $inrRate;
        $netPaymentsInr = $netPayments / $inrRate;
        
        // Get accounts by category with transactions filtered by date range
        $purchaseAccounts = Account::where('category', 'Purchase')
            ->with(['transactions' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            }])
            ->get();
            
        $overheadAccounts = Account::where('category', 'Overhead')
            ->with(['transactions' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            }])
            ->get();
            
        $tangibleAssetAccounts = Account::where('category', 'Tangible Asset')
            ->with(['transactions' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            }])
            ->get();
            
        $intangibleAssetAccounts = Account::where('category', 'Intangible Asset')
            ->with(['transactions' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            }])
            ->get();
            
        $personalExpenseAccounts = Account::where('category', 'Personal Expense')
            ->with(['transactions' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            }])
            ->get();
            
        $taxAccounts = Account::where('category', 'Tax')
            ->with(['transactions' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            }])
            ->get();
        
        // Calculate totals by category in BDT
        $cogsTotal = 0;
        $overheadTotal = 0;
        $tangibleAssetTotal = 0;
        $intangibleAssetTotal = 0;
        $personalExpenseTotal = 0;
        $taxTotal = 0;
        
        // Calculate totals with currency conversion to BDT for the date range
        foreach ($purchaseAccounts as $account) {
            // Sum only the filtered transactions
            $accountTotal = $account->transactions->sum('amount');
            // Convert to BDT if needed
            if ($account->currency_id != $bdtCurrency->id) {
                $accountTotal *= $account->currency->conversion_rate;
            }
            $cogsTotal += $accountTotal;
        }
        
        foreach ($overheadAccounts as $account) {
            $accountTotal = $account->transactions->sum('amount');
            if ($account->currency_id != $bdtCurrency->id) {
                $accountTotal *= $account->currency->conversion_rate;
            }
            $overheadTotal += $accountTotal;
        }
        
        foreach ($tangibleAssetAccounts as $account) {
            $accountTotal = $account->transactions->sum('amount');
            if ($account->currency_id != $bdtCurrency->id) {
                $accountTotal *= $account->currency->conversion_rate;
            }
            $tangibleAssetTotal += $accountTotal;
        }
        
        foreach ($intangibleAssetAccounts as $account) {
            $accountTotal = $account->transactions->sum('amount');
            if ($account->currency_id != $bdtCurrency->id) {
                $accountTotal *= $account->currency->conversion_rate;
            }
            $intangibleAssetTotal += $accountTotal;
        }
        
        foreach ($personalExpenseAccounts as $account) {
            $accountTotal = $account->transactions->sum('amount');
            if ($account->currency_id != $bdtCurrency->id) {
                $accountTotal *= $account->currency->conversion_rate;
            }
            $personalExpenseTotal += $accountTotal;
        }
        
        foreach ($taxAccounts as $account) {
            $accountTotal = $account->transactions->sum('amount');
            if ($account->currency_id != $bdtCurrency->id) {
                $accountTotal *= $account->currency->conversion_rate;
            }
            $taxTotal += $accountTotal;
        }
        
        $assetTotal = $tangibleAssetTotal + $intangibleAssetTotal;
        
        // Convert totals to INR
        $cogsTotalInr = $cogsTotal / $inrRate;
        $overheadTotalInr = $overheadTotal / $inrRate;
        $tangibleAssetTotalInr = $tangibleAssetTotal / $inrRate;
        $intangibleAssetTotalInr = $intangibleAssetTotal / $inrRate;
        $assetTotalInr = $assetTotal / $inrRate;
        $personalExpenseTotalInr = $personalExpenseTotal / $inrRate;
        $taxTotalInr = $taxTotal / $inrRate;
        
        // Get currency symbols
        $bdtSymbol = $bdtCurrency->symbol;
        $inrSymbol = $inrCurrency->symbol;
        
        // Get transaction data grouped by reference_type and filtered by date range
        $transactionsByType = AccountTransaction::select('reference_type', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as transaction_count'))
            ->whereNotNull('reference_type')
            ->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
            ->groupBy('reference_type')
            ->orderBy('total_amount', 'desc')
            ->get();
        
        // Get date-wise data
        
        $dateWiseTransactions = AccountTransaction::select(DB::raw('DATE(created_at) as transaction_date'), DB::raw('SUM(amount) as daily_total'), DB::raw('COUNT(*) as transaction_count'))
            ->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
            ->groupBy('transaction_date')
            ->orderBy('transaction_date', 'desc')
            ->get();
            
        // Get transaction type from request or default to 'all'
        $transactionType = $request->input('transaction_type', 'all');
        
        // Start building the query for all transactions
        $transactionQuery = AccountTransaction::with('account')
            ->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            
        // Get payment transactions from the transactions table if needed
        $paymentTransactions = collect([]);
        if ($transactionType === 'all' || $transactionType === 'payment') {
            $paymentTransactions = DB::table('transactions')
                ->join('banks', 'transactions.bank_id', '=', 'banks.id')
                ->leftJoin('payments', 'transactions.payment_id', '=', 'payments.id')
                ->leftJoin('customers', 'payments.customer_id', '=', 'customers.id')
                ->whereNotNull('payment_id')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->select(
                    'transactions.id',
                    'transactions.amount',
                    'transactions.description',
                    'transactions.transaction_date as created_at',
                    'transactions.type',
                    'banks.name as bank_name',
                    'customers.name as customer_name'
                )
                ->get();
        }
            
        // Apply transaction type filter if not 'all'
        if ($transactionType !== 'all') {
            if ($transactionType === 'payment') {
                // We'll handle payment transactions separately
                $transactionQuery->where('id', 0); // No results from account_transactions
            } elseif ($transactionType === 'refund') {
                // Include refund transactions (App\Models\Refund)
                $transactionQuery->where('reference_type', 'App\\Models\\Refund');
            } elseif ($transactionType === 'expense') {
                // Include expense transactions (App\Models\Expense)
                $transactionQuery->where('reference_type', 'App\\Models\\Expense');
            } elseif ($transactionType === 'cogs') {
                $transactionQuery->whereHas('account', function($query) {
                    $query->where('category', 'Purchase');
                });
            } elseif ($transactionType === 'overhead') {
                $transactionQuery->whereHas('account', function($query) {
                    $query->where('category', 'Overhead');
                });
            } elseif ($transactionType === 'asset') {
                $transactionQuery->whereHas('account', function($query) {
                    $query->whereIn('category', ['Tangible Asset', 'Intangible Asset']);
                });
            } elseif ($transactionType === 'personal') {
                $transactionQuery->whereHas('account', function($query) {
                    $query->where('category', 'Personal Expense');
                });
            } elseif ($transactionType === 'tax') {
                $transactionQuery->whereHas('account', function($query) {
                    $query->where('category', 'Tax');
                });
            }
        }
        
        // Get all transactions from account_transactions
        $accountTransactions = $transactionQuery->orderBy('created_at', 'desc')->get();
        
        // Combine with payment transactions if needed
        $transactions = $accountTransactions;
        
        // If showing all transactions or specifically payments, include payment transactions
        if ($transactionType === 'all' || $transactionType === 'payment') {
            // Convert payment transactions to a format compatible with the view
            $formattedPaymentTransactions = $paymentTransactions->map(function($payment) {
                return (object) [
                    'id' => 'payment-' . $payment->id,
                    'amount' => $payment->amount,
                    'description' => $payment->description,
                    'created_at' => $payment->created_at,
                    'type' => $payment->type === 'credit' ? 'income' : 'expense',
                    'account' => (object) [
                        'name' => $payment->bank_name . ' - ' . $payment->customer_name,
                        'category' => 'Payment'
                    ]
                ];
            });
            
            // If showing only payments, use only payment transactions
            if ($transactionType === 'payment') {
                $transactions = $formattedPaymentTransactions;
            } else {
                // Otherwise, merge with account transactions
                $transactions = $accountTransactions->concat($formattedPaymentTransactions);
            }
        }
        
        return view('admin.revenue.index', compact(
            'startDate',
            'endDate',
            'transactionType',
            'transactions',
            'transactionsByType',
            'dateWiseTransactions',
            'netPayments',
            'totalPayments',
            'totalRefunds',
            'cogsTotal',
            'overheadTotal',
            'assetTotal',
            'tangibleAssetTotal',
            'intangibleAssetTotal',
            'personalExpenseTotal',
            'taxTotal',
            'purchaseAccounts',
            'overheadAccounts',
            'tangibleAssetAccounts',
            'intangibleAssetAccounts',
            'personalExpenseAccounts',
            'taxAccounts',
            'netPaymentsInr',
            'totalPaymentsInr',
            'totalRefundsInr',
            'cogsTotalInr',
            'overheadTotalInr',
            'assetTotalInr',
            'tangibleAssetTotalInr',
            'intangibleAssetTotalInr',
            'personalExpenseTotalInr',
            'taxTotalInr',
            'bdtSymbol',
            'inrSymbol',
            'inrRate'
        ));
    }
}
