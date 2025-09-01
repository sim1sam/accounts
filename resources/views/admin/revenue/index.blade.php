@extends('adminlte::page')

@section('title', 'Revenue Menu')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Revenue Menu</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Revenue Menu</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Main Revenue Summary -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Revenue Summary</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> All amounts are displayed in both BDT and INR currencies (BDT / INR format)
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-box bg-success">
                                <span class="info-box-icon"><i class="fas fa-money-bill-wave"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">All Payments</span>
                                    <span class="info-box-number">{{ $bdtSymbol }} {{ number_format($netPayments, 2) }} / {{ $inrSymbol }} {{ number_format($netPaymentsInr, 2) }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: 100%"></div>
                                    </div>
                                    <span class="progress-description">
                                        Payments: {{ $bdtSymbol }} {{ number_format($totalPayments, 2) }} / {{ $inrSymbol }} {{ number_format($totalPaymentsInr, 2) }}<br>
                                        Refunds: {{ $bdtSymbol }} {{ number_format($totalRefunds, 2) }} / {{ $inrSymbol }} {{ number_format($totalRefundsInr, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-warning">
                                <span class="info-box-icon"><i class="fas fa-shopping-cart"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">COGS</span>
                                    <span class="info-box-number">{{ $bdtSymbol }} {{ number_format($cogsTotal, 2) }} / {{ $inrSymbol }} {{ number_format($cogsTotalInr, 2) }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $netPayments > 0 ? ($cogsTotal / $netPayments * 100) : 0 }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        Purchase Category Accounts
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-danger">
                                <span class="info-box-icon"><i class="fas fa-building"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Overhead</span>
                                    <span class="info-box-number">{{ $bdtSymbol }} {{ number_format($overheadTotal, 2) }} / {{ $inrSymbol }} {{ number_format($overheadTotalInr, 2) }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $netPayments > 0 ? ($overheadTotal / $netPayments * 100) : 0 }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        Overhead Category Accounts
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box bg-info">
                                <span class="info-box-icon"><i class="fas fa-chart-line"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Assets</span>
                                    <span class="info-box-number">{{ $bdtSymbol }} {{ number_format($assetTotal, 2) }} / {{ $inrSymbol }} {{ number_format($assetTotalInr, 2) }}</span>
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $netPayments > 0 ? ($assetTotal / $netPayments * 100) : 0 }}%"></div>
                                    </div>
                                    <span class="progress-description">
                                        Tangible & Intangible Assets
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
        <!-- Purchase Accounts (COGS) -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Purchase Accounts (COGS)</h3>
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
                                        <td colspan="2" class="text-center">No purchase accounts found</td>
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

        <!-- Overhead Accounts -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Overhead Accounts</h3>
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
    </div>

    <div class="row">
        <!-- Tangible Asset Accounts -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tangible Asset Accounts</h3>
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
                                @forelse($tangibleAssetAccounts as $account)
                                    <tr>
                                        <td>{{ $account->name }}</td>
                                        <td class="text-right">{{ $bdtSymbol }} {{ number_format($account->getAmountInBDT(), 2) }} / {{ $inrSymbol }} {{ number_format($account->getAmountInBDT() / $inrRate, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">No tangible asset accounts found</td>
                                    </tr>
                                @endforelse
                                <tr class="bg-info">
                                    <th>Total Tangible Assets</th>
                                    <th class="text-right">{{ $bdtSymbol }} {{ number_format($tangibleAssetTotal, 2) }} / {{ $inrSymbol }} {{ number_format($tangibleAssetTotalInr, 2) }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Intangible Asset Accounts -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Intangible Asset Accounts</h3>
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
                                @forelse($intangibleAssetAccounts as $account)
                                    <tr>
                                        <td>{{ $account->name }}</td>
                                        <td class="text-right">{{ $bdtSymbol }} {{ number_format($account->getAmountInBDT(), 2) }} / {{ $inrSymbol }} {{ number_format($account->getAmountInBDT() / $inrRate, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center">No intangible asset accounts found</td>
                                    </tr>
                                @endforelse
                                <tr class="bg-info">
                                    <th>Total Intangible Assets</th>
                                    <th class="text-right">{{ $bdtSymbol }} {{ number_format($intangibleAssetTotal, 2) }} / {{ $inrSymbol }} {{ number_format($intangibleAssetTotalInr, 2) }}</th>
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
            font-size: 24px;
            font-weight: bold;
        }
        .info-box .progress {
            height: 5px;
            margin: 5px 0;
        }
        .info-box .progress-description {
            font-size: 12px;
        }
    </style>
@stop

@section('js')
    <script>
        $(function () {
            console.log('Revenue Menu loaded successfully!');
        });
    </script>
@stop
