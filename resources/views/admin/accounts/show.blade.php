@extends('adminlte::page')

@section('title', 'Account Details')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Account Details</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.accounts.index') }}">Accounts</a></li>
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
                    <h3 class="card-title">{{ $account->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.account-transactions.show', $account) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-history"></i> Transaction History
                        </a>
                        <a href="{{ route('admin.accounts.edit', $account->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-wallet"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Account Name</span>
                                    <span class="info-box-number">{{ $account->name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-tag"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Category</span>
                                    <span class="info-box-number">{{ $account->category ?? 'Not categorized' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-money-bill-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Currency</span>
                                    <span class="info-box-number">{{ $account->currency->name }} ({{ $account->currency->code }})</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-coins"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Initial Amount</span>
                                    <span class="info-box-number">{{ $account->currency->symbol }} {{ number_format($account->initial_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-balance-scale"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Current Amount</span>
                                    <span class="info-box-number">{{ $account->currency->symbol }} {{ number_format($account->current_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($account->currency->code !== 'BDT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-exchange-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Conversion Rate to BDT</span>
                                    <span class="info-box-number">1 {{ $account->currency->code }} = {{ number_format($account->currency->conversion_rate, 5) }} BDT</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-money-bill"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Amount in BDT</span>
                                    <span class="info-box-number">৳ {{ number_format($account->getAmountInBDT(), 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-check-circle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Status</span>
                                    <span class="info-box-number">
                                        @if($account->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-secondary"><i class="fas fa-calendar"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Created At</span>
                                    <span class="info-box-number">{{ $account->created_at->format('d M Y, h:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Balance Adjustment Form -->
                    <div class="card card-primary mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Adjust Balance</h3>
                        </div>
                        <form action="{{ route('admin.accounts.adjust-balance', $account->id) }}" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="adjustment_type">Adjustment Type</label>
                                            <select class="form-control" id="adjustment_type" name="adjustment_type" required>
                                                <option value="increase">Increase Balance</option>
                                                <option value="decrease">Decrease Balance</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="amount">Amount ({{ $account->currency->code }})</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">{{ $account->currency->symbol }}</span>
                                                </div>
                                                <input type="number" step="0.01" min="0.01" class="form-control" id="amount" name="amount" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <button type="submit" class="btn btn-primary btn-block">Apply Adjustment</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                    <div class="text-right">
                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>
@stop
