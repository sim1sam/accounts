<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Currency;
use App\Models\Payment;
use App\Models\Refund;
use Illuminate\Http\Request;

class ProfitLossController extends Controller
{
    public function index(Request $request)
    {
        // Get date range from request or use default (last 30 days)
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        
        // Get currencies for conversion
        $bdtCurrency = Currency::where('code', 'BDT')->first();
        $inrCurrency = Currency::where('code', 'INR')->first();
        $inrRate = $inrCurrency->conversion_rate;
        
        // Get symbols for display
        $bdtSymbol = $bdtCurrency->symbol;
        $inrSymbol = $inrCurrency->symbol;
        
        // Revenue (Payments)
        // Calculate All Payment = Sum all payment - refund in BDT for the date range
        $totalPayments = Payment::whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])->sum('amount');
        $totalRefunds = Refund::whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])->sum('refund_amount');
        $totalRevenue = $totalPayments - $totalRefunds;
        $totalRevenueInr = $totalRevenue / $inrRate;
        
        // Also get payment accounts for detailed display
        $paymentAccounts = Account::where('category', 'Payment')->get();
        
        // Expenses (COGS + Overhead)
        // COGS (Purchase accounts)
        // Get accounts by category with transactions filtered by date range
        $purchaseAccounts = Account::where('category', 'Purchase')
            ->with(['transactions' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            }])
            ->get();
        
        // Calculate COGS total from filtered transactions
        $cogsTotal = 0;
        foreach ($purchaseAccounts as $account) {
            $cogsTotal += $account->getAmountInBDT();
        }
        $cogsTotalInr = $cogsTotal / $inrRate;
        
        // Overhead accounts with filtered transactions
        $overheadAccounts = Account::where('category', 'Overhead')
            ->with(['transactions' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            }])
            ->get();
        
        // Calculate overhead total from filtered transactions
        $overheadTotal = 0;
        foreach ($overheadAccounts as $account) {
            $overheadTotal += $account->getAmountInBDT();
        }
        $overheadTotalInr = $overheadTotal / $inrRate;
        
        // Personal expense accounts with filtered transactions
        $personalExpenseAccounts = Account::where('category', 'Personal Expense')
            ->with(['transactions' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            }])
            ->get();
            
        // Get all paid expenses within the date range
        $paidExpenses = \App\Models\Expense::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
            ->whereIn('account_id', $personalExpenseAccounts->pluck('id'))
            ->with('account')
            ->get();
        
        // Calculate personal expense total from filtered transactions - only paid amounts
        $personalExpenseTotal = 0;
        $personalExpensePaidAmounts = [];
        
        // Group paid expenses by account_id
        $paidExpensesByAccount = [];
        foreach ($paidExpenses as $expense) {
            $accountId = $expense->account_id;
            if (!isset($paidExpensesByAccount[$accountId])) {
                $paidExpensesByAccount[$accountId] = [];
            }
            $paidExpensesByAccount[$accountId][] = $expense;
        }
        
        foreach ($personalExpenseAccounts as $account) {
            // Get all paid expenses for this account
            $accountPaidExpenses = $paidExpensesByAccount[$account->id] ?? [];
            
            // Calculate total paid amount for this account
            $paidAmount = 0;
            foreach ($accountPaidExpenses as $expense) {
                $paidAmount += $expense->amount_in_bdt;
            }
            
            // Store the paid amount for this account
            $personalExpensePaidAmounts[$account->id] = [
                'bdt' => $paidAmount,
                'inr' => $paidAmount / $inrRate
            ];
            
            $personalExpenseTotal += $paidAmount;
        }
        $personalExpenseTotalInr = $personalExpenseTotal / $inrRate;
        
        // Tax accounts with filtered transactions
        $taxAccounts = Account::where('category', 'Tax')
            ->with(['transactions' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            }])
            ->get();
        
        // Calculate tax total from filtered transactions
        $taxTotal = 0;
        foreach ($taxAccounts as $account) {
            $taxTotal += $account->getAmountInBDT();
        }
        $taxTotalInr = $taxTotal / $inrRate;
        
        // Calculate total expenses
        $totalExpenses = $cogsTotal + $overheadTotal + $personalExpenseTotal + $taxTotal;
        $totalExpensesInr = $totalExpenses / $inrRate;
        
        // Calculate profit and loss
        $grossProfit = $totalRevenue - $cogsTotal;
        $operatingProfit = $grossProfit - $overheadTotal;
        // Note: personalExpenseTotal now contains only paid amounts
        $netProfit = $operatingProfit - $taxTotal - $personalExpenseTotal;
        
        // Calculate profit margins
        $grossProfitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue * 100) : 0;
        $operatingProfitMargin = $totalRevenue > 0 ? ($operatingProfit / $totalRevenue * 100) : 0;
        $netProfitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue * 100) : 0;
        
        // Convert profit values to INR
        $grossProfitInr = $grossProfit / $inrRate;
        $operatingProfitInr = $operatingProfit / $inrRate;
        $netProfitInr = $netProfit / $inrRate;
        
        // Assets (for reference) with filtered transactions
        $tangibleAssetAccounts = Account::where('category', 'Tangible Asset')
            ->with(['transactions' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            }])
            ->get();
        
        // Calculate tangible asset total from filtered transactions
        $tangibleAssetTotal = 0;
        foreach ($tangibleAssetAccounts as $account) {
            $tangibleAssetTotal += $account->getAmountInBDT();
        }
        $tangibleAssetTotalInr = $tangibleAssetTotal / $inrRate;
        
        // Intangible assets with filtered transactions
        $intangibleAssetAccounts = Account::where('category', 'Intangible Asset')
            ->with(['transactions' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59']);
            }])
            ->get();
        
        // Calculate intangible asset total from filtered transactions
        $intangibleAssetTotal = 0;
        foreach ($intangibleAssetAccounts as $account) {
            $intangibleAssetTotal += $account->getAmountInBDT();
        }
        $intangibleAssetTotalInr = $intangibleAssetTotal / $inrRate;
        
        $assetTotal = $tangibleAssetTotal + $intangibleAssetTotal;
        $assetTotalInr = $assetTotal / $inrRate;
        
        return view('admin.profit_loss.index', compact(
            'startDate', 'endDate',
            'paymentAccounts', 'purchaseAccounts', 'overheadAccounts', 'taxAccounts', 'personalExpenseAccounts',
            'tangibleAssetAccounts', 'intangibleAssetAccounts',
            'totalRevenue', 'totalRevenueInr',
            'cogsTotal', 'cogsTotalInr',
            'overheadTotal', 'overheadTotalInr',
            'personalExpenseTotal', 'personalExpenseTotalInr',
            'personalExpensePaidAmounts', // Added paid amounts for personal expenses
            'taxTotal', 'taxTotalInr',
            'totalExpenses', 'totalExpensesInr',
            'grossProfit', 'grossProfitInr', 'grossProfitMargin',
            'operatingProfit', 'operatingProfitInr', 'operatingProfitMargin',
            'netProfit', 'netProfitInr', 'netProfitMargin',
            'tangibleAssetTotal', 'tangibleAssetTotalInr',
            'intangibleAssetTotal', 'intangibleAssetTotalInr',
            'assetTotal', 'assetTotalInr',
            'bdtSymbol', 'inrSymbol', 'inrRate'
        ));
    }
}
