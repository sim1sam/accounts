@extends('adminlte::page')

@include('admin.profit_loss.personal_expense_helper')

@section('title', 'Profit & Loss Statement')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Profit & Loss Statement</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Profit & Loss</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <!-- Date Range Filter -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter by Date Range</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profit-loss.index') }}" method="GET" class="form-inline">
                        <div class="form-group mr-3">
                            <label for="start_date" class="mr-2">Start Date:</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                        </div>
                        <div class="form-group mr-3">
                            <label for="end_date" class="mr-2">End Date:</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                    </form>
                </div>
                <div class="card-footer">
                    <div class="text-muted">
                        <i class="fas fa-info-circle"></i> Showing data from <strong>{{ $startDate }}</strong> to <strong>{{ $endDate }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Main P&L Summary -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Profit & Loss Overview</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> All amounts are displayed in both BDT and INR currencies (BDT / INR format)
                            </div>
                        </div>
                    </div>
                    
                    <!-- Summary Boxes -->
                    <div class="row">
                        <!-- Revenue Box -->
                        <div class="col-md-4">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Revenue</span>
                                    <span class="info-box-number">{{ $bdtSymbol }} {{ number_format($totalRevenue, 2) }} / {{ $inrSymbol }} {{ number_format($totalRevenueInr, 2) }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: 100%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Expenses Box -->
                        <div class="col-md-4">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Expenses</span>
                                    <span class="info-box-number">{{ $bdtSymbol }} {{ number_format($totalExpenses, 2) }} / {{ $inrSymbol }} {{ number_format($totalExpensesInr, 2) }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $totalRevenue > 0 ? ($totalExpenses / $totalRevenue * 100) : 0 }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        COGS: {{ $bdtSymbol }} {{ number_format($cogsTotal, 2) }} / {{ $inrSymbol }} {{ number_format($cogsTotalInr, 2) }}<br>
                                        Overhead: {{ $bdtSymbol }} {{ number_format($overheadTotal, 2) }} / {{ $inrSymbol }} {{ number_format($overheadTotalInr, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Gross Profit Box -->
                        <div class="col-md-4">
                            <div class="info-box {{ ($totalRevenue - $totalExpenses) >= 0 ? 'bg-info' : 'bg-warning' }}">
                                <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Gross Profit</span>
                                    <span class="info-box-number">{{ $bdtSymbol }} {{ number_format(($totalRevenue - $totalExpenses), 2) }} / {{ $inrSymbol }} {{ number_format(($totalRevenueInr - $totalExpensesInr), 2) }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $totalRevenue > 0 ? abs((($totalRevenue - $totalExpenses) / $totalRevenue * 100)) : 0 }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        Profit Margin: {{ $totalRevenue > 0 ? number_format((($totalRevenue - $totalExpenses) / $totalRevenue * 100), 2) : 0 }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Revenue Details -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Revenue</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Account Name</th>
                                    <th class="text-right">Current Amount (BDT / INR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paymentAccounts as $account)
                                    <tr>
                                        <td>{{ $account->name }}</td>
                                        <td class="text-right">{{ $bdtSymbol }} {{ number_format($account->getAmountInBDT(), 2) }} / {{ $inrSymbol }} {{ number_format($account->getAmountInBDT() / $inrRate, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">No revenue accounts found</td>
                                    </tr>
                                @endforelse
                                <tr class="bg-success">
                                    <th>Total Revenue</th>
                                    <th class="text-right">{{ $bdtSymbol }} {{ number_format($totalRevenue, 2) }} / {{ $inrSymbol }} {{ number_format($totalRevenueInr, 2) }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- COGS Details -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cost of Goods Sold (COGS)</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Account Name</th>
                                    <th class="text-right">Current Amount (BDT / INR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseAccounts as $account)
                                    <tr>
                                        <td>{{ $account->name }}</td>
                                        <td class="text-right">{{ $bdtSymbol }} {{ number_format($account->getAmountInBDT(), 2) }} / {{ $inrSymbol }} {{ number_format($account->getAmountInBDT() / $inrRate, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">No COGS accounts found</td>
                                    </tr>
                                @endforelse
                                <tr class="bg-warning">
                                    <th>Total COGS</th>
                                    <th class="text-right">{{ $bdtSymbol }} {{ number_format($cogsTotal, 2) }} / {{ $inrSymbol }} {{ number_format($cogsTotalInr, 2) }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Overhead Details -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Overhead Expenses</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Account Name</th>
                                    <th class="text-right">Current Amount (BDT / INR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($overheadAccounts as $account)
                                    <tr>
                                        <td>{{ $account->name }}</td>
                                        <td class="text-right">{{ $bdtSymbol }} {{ number_format($account->getAmountInBDT(), 2) }} / {{ $inrSymbol }} {{ number_format($account->getAmountInBDT() / $inrRate, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">No overhead accounts found</td>
                                    </tr>
                                @endforelse
                                <tr class="bg-danger">
                                    <th>Total Overhead</th>
                                    <th class="text-right">{{ $bdtSymbol }} {{ number_format($overheadTotal, 2) }} / {{ $inrSymbol }} {{ number_format($overheadTotalInr, 2) }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Personal Expense Details -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Personal Expenses</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Account Name</th>
                                    <th class="text-right">Current Amount (BDT / INR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($personalExpenseAccounts as $account)
                                    @php
                                        $paidAmount = getPersonalExpensePaidAmount($account, $personalExpensePaidAmounts, $inrRate);
                                    @endphp
                                    <tr>
                                        <td>{{ $account->name }}</td>
                                        <td class="text-right">{{ $bdtSymbol }} {{ number_format($paidAmount['bdt'], 2) }} / {{ $inrSymbol }} {{ number_format($paidAmount['inr'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">No personal expense accounts found</td>
                                    </tr>
                                @endforelse
                                @php
                                    $totalPaidPersonalExpenses = getTotalPaidPersonalExpenses($personalExpenseAccounts, $personalExpensePaidAmounts, $inrRate);
                                @endphp
                                <tr class="bg-warning">
                                    <th>Total Personal Expenses (Paid Only)</th>
                                    <th class="text-right">{{ $bdtSymbol }} {{ number_format($totalPaidPersonalExpenses['bdt'], 2) }} / {{ $inrSymbol }} {{ number_format($totalPaidPersonalExpenses['inr'], 2) }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profit & Loss Summary -->
    <div class="row">
        <div class="col-md-6"></div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">Profit & Loss Summary</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td>Total Revenue</td>
                                    <td class="text-right">{{ $bdtSymbol }} {{ number_format($totalRevenue, 2) }} / {{ $inrSymbol }} {{ number_format($totalRevenueInr, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Total COGS</td>
                                    <td class="text-right">{{ $bdtSymbol }} {{ number_format($cogsTotal, 2) }} / {{ $inrSymbol }} {{ number_format($cogsTotalInr, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Total Overhead</td>
                                    <td class="text-right">{{ $bdtSymbol }} {{ number_format($overheadTotal, 2) }} / {{ $inrSymbol }} {{ number_format($overheadTotalInr, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Personal Expenses</td>
                                    <td class="text-right">{{ $bdtSymbol }} {{ number_format($totalPaidPersonalExpenses['bdt'], 2) }} / {{ $inrSymbol }} {{ number_format($totalPaidPersonalExpenses['inr'], 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Tax</td>
                                    <td class="text-right">{{ $bdtSymbol }} {{ number_format($taxTotal, 2) }} / {{ $inrSymbol }} {{ number_format($taxTotalInr, 2) }}</td>
                                </tr>
                                <tr class="bg-light">
                                    <th>Total Expenses</th>
                                    <th class="text-right">{{ $bdtSymbol }} {{ number_format($totalExpenses, 2) }} / {{ $inrSymbol }} {{ number_format($totalExpensesInr, 2) }}</th>
                                </tr>
                                <tr class="{{ ($totalRevenue - $totalExpenses) >= 0 ? 'bg-info' : 'bg-warning' }}">
                                    <th>Gross Profit</th>
                                    <td class="text-right">{{ $bdtSymbol }} {{ number_format(($totalRevenue - $totalExpenses), 2) }} / {{ $inrSymbol }} {{ number_format(($totalRevenueInr - $totalExpensesInr), 2) }}</td>
                                </tr>
                                <tr class="{{ ($totalRevenue - $totalExpenses) >= 0 ? 'bg-info' : 'bg-warning' }}">
                                    <th>Gross Profit Margin</th>
                                    <td class="text-right">{{ $totalRevenue > 0 ? number_format((($totalRevenue - $totalExpenses) / $totalRevenue * 100), 2) : 0 }}%</td>
                                </tr>
                                <tr>
                                    <td>Operating Profit</td>
                                    <td class="text-right">{{ $bdtSymbol }} {{ number_format($operatingProfit, 2) }} / {{ $inrSymbol }} {{ number_format($operatingProfitInr, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Operating Profit Margin</td>
                                    <td class="text-right">{{ number_format($operatingProfitMargin, 2) }}%</td>
                                </tr>
                                <tr class="{{ (($totalRevenue - $totalExpenses) - $taxTotal) >= 0 ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                    <th>Net Profit</th>
                                    <td class="text-right">{{ $bdtSymbol }} {{ number_format((($totalRevenue - $totalExpenses) - $taxTotal), 2) }} / {{ $inrSymbol }} {{ number_format((($totalRevenueInr - $totalExpensesInr) - $taxTotalInr), 2) }}</td>
                                </tr>
                                <tr class="{{ (($totalRevenue - $totalExpenses) - $taxTotal) >= 0 ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                    <th>Net Profit Margin</th>
                                    <td class="text-right">{{ $totalRevenue > 0 ? number_format(((($totalRevenue - $totalExpenses) - $taxTotal) / $totalRevenue * 100), 2) : 0 }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Tax Details -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tax Expenses</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Account Name</th>
                                    <th class="text-right">Current Amount (BDT / INR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($taxAccounts as $account)
                                    <tr>
                                        <td>{{ $account->name }}</td>
                                        <td class="text-right">{{ $bdtSymbol }} {{ number_format($account->getAmountInBDT(), 2) }} / {{ $inrSymbol }} {{ number_format($account->getAmountInBDT() / $inrRate, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">No tax accounts found</td>
                                    </tr>
                                @endforelse
                                <tr class="bg-secondary">
                                    <th>Total Tax</th>
                                    <th class="text-right">{{ $bdtSymbol }} {{ number_format($taxTotal, 2) }} / {{ $inrSymbol }} {{ number_format($taxTotalInr, 2) }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Account Listings -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Accounts by Category</h3>
                </div>
                <div class="card-body">
                    <div class="accordion" id="accountsAccordion">
                        <!-- Payment Accounts -->
                        <div class="card">
                            <div class="card-header" id="headingPayment">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapsePayment" aria-expanded="true" aria-controls="collapsePayment">
                                        Payment Accounts
                                    </button>
                                </h2>
                            </div>
                            <div id="collapsePayment" class="collapse show" aria-labelledby="headingPayment" data-parent="#accountsAccordion">
                                <div class="card-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Account Name</th>
                                                <th>Currency</th>
                                                <th class="text-right">Amount (BDT)</th>
                                                <th class="text-right">Amount (INR)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($paymentAccounts as $account)
                                                <tr>
                                                    <td>{{ $account->name }}</td>
                                                    <td>{{ $account->currency->code }}</td>
                                                    <td class="text-right">{{ $bdtSymbol }} {{ number_format($account->getAmountInBDT(), 2) }}</td>
                                                    <td class="text-right">{{ $inrSymbol }} {{ number_format($account->getAmountInBDT() / $inrRate, 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">No payment accounts found</td>
                                                </tr>
                                            @endforelse
                                            <tr class="bg-success">
                                                <th colspan="2">Total</th>
                                                <th class="text-right">{{ $bdtSymbol }} {{ number_format($totalRevenue, 2) }}</th>
                                                <th class="text-right">{{ $inrSymbol }} {{ number_format($totalRevenueInr, 2) }}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Purchase (COGS) Accounts -->
                        <div class="card">
                            <div class="card-header" id="headingPurchase">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapsePurchase" aria-expanded="false" aria-controls="collapsePurchase">
                                        Purchase (COGS) Accounts
                                    </button>
                                </h2>
                            </div>
                            <div id="collapsePurchase" class="collapse" aria-labelledby="headingPurchase" data-parent="#accountsAccordion">
                                <div class="card-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Account Name</th>
                                                <th>Currency</th>
                                                <th class="text-right">Amount (BDT)</th>
                                                <th class="text-right">Amount (INR)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($purchaseAccounts as $account)
                                                <tr>
                                                    <td>{{ $account->name }}</td>
                                                    <td>{{ $account->currency->code }}</td>
                                                    <td class="text-right">{{ $bdtSymbol }} {{ number_format($account->getAmountInBDT(), 2) }}</td>
                                                    <td class="text-right">{{ $inrSymbol }} {{ number_format($account->getAmountInBDT() / $inrRate, 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">No purchase accounts found</td>
                                                </tr>
                                            @endforelse
                                            <tr class="bg-warning">
                                                <th colspan="2">Total</th>
                                                <th class="text-right">{{ $bdtSymbol }} {{ number_format($cogsTotal, 2) }}</th>
                                                <th class="text-right">{{ $inrSymbol }} {{ number_format($cogsTotalInr, 2) }}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Overhead Accounts -->
                        <div class="card">
                            <div class="card-header" id="headingOverhead">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseOverhead" aria-expanded="false" aria-controls="collapseOverhead">
                                        Overhead Accounts
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseOverhead" class="collapse" aria-labelledby="headingOverhead" data-parent="#accountsAccordion">
                                <div class="card-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Account Name</th>
                                                <th>Currency</th>
                                                <th class="text-right">Amount (BDT)</th>
                                                <th class="text-right">Amount (INR)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($overheadAccounts as $account)
                                                <tr>
                                                    <td>{{ $account->name }}</td>
                                                    <td>{{ $account->currency->code }}</td>
                                                    <td class="text-right">{{ $bdtSymbol }} {{ number_format($account->getAmountInBDT(), 2) }}</td>
                                                    <td class="text-right">{{ $inrSymbol }} {{ number_format($account->getAmountInBDT() / $inrRate, 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">No overhead accounts found</td>
                                                </tr>
                                            @endforelse
                                            <tr class="bg-danger">
                                                <th colspan="2">Total</th>
                                                <th class="text-right">{{ $bdtSymbol }} {{ number_format($overheadTotal, 2) }}</th>
                                                <th class="text-right">{{ $inrSymbol }} {{ number_format($overheadTotalInr, 2) }}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Personal Expense Accounts -->
                        <div class="card">
                            <div class="card-header" id="headingPersonalExpense">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapsePersonalExpense" aria-expanded="false" aria-controls="collapsePersonalExpense">
                                        Personal Expense Accounts
                                    </button>
                                </h2>
                            </div>
                            <div id="collapsePersonalExpense" class="collapse" aria-labelledby="headingPersonalExpense" data-parent="#accountsAccordion">
                                <div class="card-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Account Name</th>
                                                <th>Currency</th>
                                                <th class="text-right">Amount (BDT)</th>
                                                <th class="text-right">Amount (INR)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($personalExpenseAccounts as $account)
                                                @php
                                                    $paidAmount = getPersonalExpensePaidAmount($account, $personalExpensePaidAmounts, $inrRate);
                                                @endphp
                                                <tr>
                                                    <td>{{ $account->name }}</td>
                                                    <td>{{ $account->currency->code }}</td>
                                                    <td class="text-right">{{ $bdtSymbol }} {{ number_format($paidAmount['bdt'], 2) }}</td>
                                                    <td class="text-right">{{ $inrSymbol }} {{ number_format($paidAmount['inr'], 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">No personal expense accounts found</td>
                                                </tr>
                                            @endforelse
                                            @php
                                                $totalPaidPersonalExpenses = getTotalPaidPersonalExpenses($personalExpenseAccounts, $personalExpensePaidAmounts, $inrRate);
                                            @endphp
                                            <tr class="bg-warning">
                                                <th colspan="2">Total (Paid Only)</th>
                                                <th class="text-right">{{ $bdtSymbol }} {{ number_format($totalPaidPersonalExpenses['bdt'], 2) }}</th>
                                                <th class="text-right">{{ $inrSymbol }} {{ number_format($totalPaidPersonalExpenses['inr'], 2) }}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tax Accounts -->
                        <div class="card">
                            <div class="card-header" id="headingTax">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTax" aria-expanded="false" aria-controls="collapseTax">
                                        Tax Accounts
                                    </button>
                                </h2>
                            </div>
                            <div id="collapseTax" class="collapse" aria-labelledby="headingTax" data-parent="#accountsAccordion">
                                <div class="card-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Account Name</th>
                                                <th>Currency</th>
                                                <th class="text-right">Amount (BDT)</th>
                                                <th class="text-right">Amount (INR)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($taxAccounts as $account)
                                                <tr>
                                                    <td>{{ $account->name }}</td>
                                                    <td>{{ $account->currency->code }}</td>
                                                    <td class="text-right">{{ $bdtSymbol }} {{ number_format($account->getAmountInBDT(), 2) }}</td>
                                                    <td class="text-right">{{ $inrSymbol }} {{ number_format($account->getAmountInBDT() / $inrRate, 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">No tax accounts found</td>
                                                </tr>
                                            @endforelse
                                            <tr class="bg-secondary">
                                                <th colspan="2">Total</th>
                                                <th class="text-right">{{ $bdtSymbol }} {{ number_format($taxTotal, 2) }}</th>
                                                <th class="text-right">{{ $inrSymbol }} {{ number_format($taxTotalInr, 2) }}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .info-box-number {
            font-size: 1.5rem;
        }
        .info-box .progress-description {
            font-size: 12px;
        }
    </style>
@stop

@section('js')
    <script>
        $(function () {
            console.log('Profit & Loss Menu loaded successfully!');
        });
    </script>
@stop
