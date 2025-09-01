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
    public function index()
    {
        // Get currencies for conversion
        $bdtCurrency = Currency::where('code', 'BDT')->first();
        $inrCurrency = Currency::where('code', 'INR')->first();
        $inrRate = $inrCurrency->conversion_rate;
        
        // Get symbols for display
        $bdtSymbol = $bdtCurrency->symbol;
        $inrSymbol = $inrCurrency->symbol;
        
        // Revenue (Payments)
        // Calculate All Payment = Sum all payment - refund in BDT
        $totalPayments = Payment::sum('amount');
        $totalRefunds = Refund::sum('refund_amount');
        $totalRevenue = $totalPayments - $totalRefunds;
        $totalRevenueInr = $totalRevenue / $inrRate;
        
        // Also get payment accounts for detailed display
        $paymentAccounts = Account::where('category', 'Payment')->get();
        
        // Expenses (COGS + Overhead)
        // COGS (Purchase accounts)
        $purchaseAccounts = Account::where('category', 'Purchase')->get();
        
        $cogsTotal = $purchaseAccounts->sum(function($account) {
            return $account->getAmountInBDT();
        });
        $cogsTotalInr = $cogsTotal / $inrRate;
        
        // Overhead accounts
        $overheadAccounts = Account::where('category', 'Overhead')->get();
        
        $overheadTotal = $overheadAccounts->sum(function($account) {
            return $account->getAmountInBDT();
        });
        $overheadTotalInr = $overheadTotal / $inrRate;
        
        // Total expenses
        $totalExpenses = $cogsTotal + $overheadTotal;
        $totalExpensesInr = $totalExpenses / $inrRate;
        
        // Gross Profit
        $grossProfit = $totalRevenue - $totalExpenses;
        $grossProfitInr = $grossProfit / $inrRate;
        
        // Gross Profit Margin (%)
        $grossProfitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;
        
        // Tax calculation (from Tax category accounts)
        $taxAccounts = Account::where('category', 'Tax')->get();
        
        $taxAmount = $taxAccounts->sum(function($account) {
            return $account->getAmountInBDT();
        });
        $taxAmountInr = $taxAmount / $inrRate;
        
        // Net Profit (Gross Profit - Tax)
        $netProfit = $grossProfit - $taxAmount;
        $netProfitInr = $netProfit / $inrRate;
        
        // Net Profit Margin (%)
        $netProfitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;
        
        // Assets (for reference)
        $tangibleAssetAccounts = Account::where('category', 'Tangible Asset')->get();
        
        $tangibleAssetTotal = $tangibleAssetAccounts->sum(function($account) {
            return $account->getAmountInBDT();
        });
        $tangibleAssetTotalInr = $tangibleAssetTotal / $inrRate;
        
        $intangibleAssetAccounts = Account::where('category', 'Intangible Asset')->get();
        
        $intangibleAssetTotal = $intangibleAssetAccounts->sum(function($account) {
            return $account->getAmountInBDT();
        });
        $intangibleAssetTotalInr = $intangibleAssetTotal / $inrRate;
        
        $assetTotal = $tangibleAssetTotal + $intangibleAssetTotal;
        $assetTotalInr = $assetTotal / $inrRate;
        
        return view('admin.profit_loss.index', compact(
            'paymentAccounts', 'purchaseAccounts', 'overheadAccounts', 'taxAccounts',
            'tangibleAssetAccounts', 'intangibleAssetAccounts',
            'totalRevenue', 'totalRevenueInr',
            'cogsTotal', 'cogsTotalInr',
            'overheadTotal', 'overheadTotalInr',
            'totalExpenses', 'totalExpensesInr',
            'grossProfit', 'grossProfitInr', 'grossProfitMargin',
            'taxAmount', 'taxAmountInr',
            'netProfit', 'netProfitInr', 'netProfitMargin',
            'tangibleAssetTotal', 'tangibleAssetTotalInr',
            'intangibleAssetTotal', 'intangibleAssetTotalInr',
            'assetTotal', 'assetTotalInr',
            'bdtSymbol', 'inrSymbol', 'inrRate'
        ));
    }
}
