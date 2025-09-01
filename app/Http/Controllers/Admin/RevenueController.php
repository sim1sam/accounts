<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Currency;
use App\Models\Payment;
use App\Models\Refund;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    /**
     * Display the revenue menu.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get BDT and INR currencies
        $bdtCurrency = Currency::where('code', 'BDT')->first();
        $inrCurrency = Currency::where('code', 'INR')->first();
        $inrRate = $inrCurrency->conversion_rate;
        
        // Calculate All Payment = Sum all payment - refund in BDT
        $totalPayments = Payment::sum('amount');
        $totalRefunds = Refund::sum('refund_amount');
        $netPayments = $totalPayments - $totalRefunds;
        
        // Convert to INR
        $totalPaymentsInr = $totalPayments / $inrRate;
        $totalRefundsInr = $totalRefunds / $inrRate;
        $netPaymentsInr = $netPayments / $inrRate;
        
        // Get accounts by category
        $purchaseAccounts = Account::where('category', 'Purchase')->get();
        $overheadAccounts = Account::where('category', 'Overhead')->get();
        $tangibleAssetAccounts = Account::where('category', 'Tangible Asset')->get();
        $intangibleAssetAccounts = Account::where('category', 'Intangible Asset')->get();
        
        // Calculate totals by category in BDT
        $cogsTotal = 0;
        $overheadTotal = 0;
        $tangibleAssetTotal = 0;
        $intangibleAssetTotal = 0;
        
        // Calculate totals with currency conversion to BDT
        foreach ($purchaseAccounts as $account) {
            $cogsTotal += $account->getAmountInBDT();
        }
        
        foreach ($overheadAccounts as $account) {
            $overheadTotal += $account->getAmountInBDT();
        }
        
        foreach ($tangibleAssetAccounts as $account) {
            $tangibleAssetTotal += $account->getAmountInBDT();
        }
        
        foreach ($intangibleAssetAccounts as $account) {
            $intangibleAssetTotal += $account->getAmountInBDT();
        }
        
        $assetTotal = $tangibleAssetTotal + $intangibleAssetTotal;
        
        // Convert totals to INR
        $cogsTotalInr = $cogsTotal / $inrRate;
        $overheadTotalInr = $overheadTotal / $inrRate;
        $tangibleAssetTotalInr = $tangibleAssetTotal / $inrRate;
        $intangibleAssetTotalInr = $intangibleAssetTotal / $inrRate;
        $assetTotalInr = $assetTotal / $inrRate;
        
        // Get currency symbols
        $bdtSymbol = $bdtCurrency->symbol;
        $inrSymbol = $inrCurrency->symbol;
        
        return view('admin.revenue.index', compact(
            'netPayments',
            'totalPayments',
            'totalRefunds',
            'cogsTotal',
            'overheadTotal',
            'assetTotal',
            'tangibleAssetTotal',
            'intangibleAssetTotal',
            'purchaseAccounts',
            'overheadAccounts',
            'tangibleAssetAccounts',
            'intangibleAssetAccounts',
            'netPaymentsInr',
            'totalPaymentsInr',
            'totalRefundsInr',
            'cogsTotalInr',
            'overheadTotalInr',
            'assetTotalInr',
            'tangibleAssetTotalInr',
            'intangibleAssetTotalInr',
            'bdtSymbol',
            'inrSymbol',
            'inrRate'
        ));
    }
}
