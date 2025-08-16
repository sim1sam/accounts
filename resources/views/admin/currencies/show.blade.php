@extends('adminlte::page')

@section('title', 'Currency Details')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Currency Details</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.currencies.index') }}">Currencies</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $currency->name }} ({{ $currency->code }})</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.currencies.edit', $currency->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.currencies.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-money-bill"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Currency Name</span>
                                    <span class="info-box-number">{{ $currency->name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-code"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Currency Code</span>
                                    <span class="info-box-number">{{ $currency->code }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-dollar-sign"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Currency Symbol</span>
                                    <span class="info-box-number">{{ $currency->symbol }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-exchange-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Conversion Rate to BDT</span>
                                    <span class="info-box-number">{{ number_format($currency->conversion_rate, 5) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Default Currency</span>
                                    <span class="info-box-number">
                                        @if($currency->is_default)
                                            <span class="badge badge-primary">Yes</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-toggle-on"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Status</span>
                                    <span class="info-box-number">
                                        @if($currency->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Currency Conversion Examples</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>{{ $currency->code }} Amount</th>
                                                    <th>BDT Equivalent</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{ $currency->symbol }} 1.00</td>
                                                    <td>৳ {{ number_format($currency->conversion_rate * 1, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ $currency->symbol }} 10.00</td>
                                                    <td>৳ {{ number_format($currency->conversion_rate * 10, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ $currency->symbol }} 100.00</td>
                                                    <td>৳ {{ number_format($currency->conversion_rate * 100, 2) }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ $currency->symbol }} 1,000.00</td>
                                                    <td>৳ {{ number_format($currency->conversion_rate * 1000, 2) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <div class="text-right">
                        <a href="{{ route('admin.currencies.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
@stop

@section('css')
    <style>
        .info-box-number {
            font-size: 1.5rem;
        }
    </style>
@stop
