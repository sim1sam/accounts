@extends('adminlte::page')

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
    <div class="row">
        <!-- Main P&L Summary -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Profit & Loss Summary</h3>
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
                            <div class="info-box {{ $grossProfit >= 0 ? 'bg-info' : 'bg-warning' }}">
                                <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Gross Profit</span>
                                    <span class="info-box-number">{{ $bdtSymbol }} {{ number_format($grossProfit, 2) }} / {{ $inrSymbol }} {{ number_format($grossProfitInr, 2) }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ abs($grossProfitMargin) }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        Profit Margin: {{ number_format($grossProfitMargin, 2) }}%
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
                                    <th class="text-right">{{ $bdtSymbol }} {{ number_format($taxAmount, 2) }} / {{ $inrSymbol }} {{ number_format($taxAmountInr, 2) }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- P&L Summary -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Profit & Loss Summary</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tbody>
                                <tr class="bg-success">
                                    <th>Total Revenue</th>
                                    <td class="text-right">{{ $bdtSymbol }} {{ number_format($totalRevenue, 2) }} / {{ $inrSymbol }} {{ number_format($totalRevenueInr, 2) }}</td>
                                </tr>
                                <tr class="bg-warning">
                                    <th>Total COGS</th>
                                    <td class="text-right">{{ $bdtSymbol }} {{ number_format($cogsTotal, 2) }} / {{ $inrSymbol }} {{ number_format($cogsTotalInr, 2) }}</td>
                                </tr>
                                <tr class="bg-danger">
                                    <th>Total Overhead</th>
                                    <td class="text-right">{{ $bdtSymbol }} {{ number_format($overheadTotal, 2) }} / {{ $inrSymbol }} {{ number_format($overheadTotalInr, 2) }}</td>
                                </tr>
                                <tr class="bg-danger">
                                    <th>Total Expenses</th>
                                    <td class="text-right">{{ $bdtSymbol }} {{ number_format($totalExpenses, 2) }} / {{ $inrSymbol }} {{ number_format($totalExpensesInr, 2) }}</td>
                                </tr>
                                <tr class="{{ $grossProfit >= 0 ? 'bg-info' : 'bg-warning' }}">
                                    <th>Gross Profit</th>
                                    <td class="text-right">{{ $bdtSymbol }} {{ number_format($grossProfit, 2) }} / {{ $inrSymbol }} {{ number_format($grossProfitInr, 2) }}</td>
                                </tr>
                                <tr class="{{ $grossProfit >= 0 ? 'bg-info' : 'bg-warning' }}">
                                    <th>Gross Profit Margin</th>
                                    <td class="text-right">{{ number_format($grossProfitMargin, 2) }}%</td>
                                </tr>
                                <tr class="bg-secondary">
                                    <th>Tax</th>
                                    <td class="text-right">{{ $bdtSymbol }} {{ number_format($taxAmount, 2) }} / {{ $inrSymbol }} {{ number_format($taxAmountInr, 2) }}</td>
                                </tr>
                                <tr class="{{ $netProfit >= 0 ? 'bg-success' : 'bg-danger' }}">
                                    <th>Net Profit</th>
                                    <td class="text-right">{{ $bdtSymbol }} {{ number_format($netProfit, 2) }} / {{ $inrSymbol }} {{ number_format($netProfitInr, 2) }}</td>
                                </tr>
                                <tr class="{{ $netProfit >= 0 ? 'bg-success' : 'bg-danger' }}">
                                    <th>Net Profit Margin</th>
                                    <td class="text-right">{{ number_format($netProfitMargin, 2) }}%</td>
                                </tr>
                            </tbody>
                        </table>
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
