@extends('adminlte::page')

@section('title', 'Account Transaction History - ' . $account->name)

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">{{ $account->name }} - Transaction History</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.accounts.index') }}">Accounts</a></li>
                <li class="breadcrumb-item active">Transaction History</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Transaction History</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                    @if($transactions->count() > 0)
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Balance After</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $transaction->type === 'expense' ? 'danger' : ($transaction->type === 'income' ? 'success' : 'info') }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $transaction->description }}</td>
                                    <td>
                                        @if(strpos($transaction->description, 'pending') !== false)
                                            <span class="text-warning">+৳ {{ number_format($transaction->amount, 2) }}</span>
                                        @elseif(strpos($transaction->description, 'paid') !== false)
                                            <span class="text-danger">-৳ {{ number_format($transaction->amount, 2) }}</span>
                                        @else
                                            <span class="text-{{ $transaction->type === 'expense' ? 'danger' : 'success' }}">
                                                {{ $transaction->type === 'expense' ? '-' : '+' }}৳ {{ number_format($transaction->amount, 2) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>৳ {{ number_format($transaction->balance_after, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center p-4">
                            <p>No transactions found for this account.</p>
                        </div>
                    @endif
                </div>
                <!-- /.card-body -->
                @if($transactions->hasPages())
                <div class="card-footer clearfix">
                    {{ $transactions->links() }}
                </div>
                @endif
            </div>
            <!-- /.card -->
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Account Summary</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-6">Account:</dt>
                        <dd class="col-6">{{ $account->name }}</dd>

                        <dt class="col-6">Currency:</dt>
                        <dd class="col-6">{{ $account->currency->name }} ({{ $account->currency->code }})</dd>

                        <dt class="col-6">Initial Amount:</dt>
                        <dd class="col-6">{{ $account->currency->symbol }} {{ number_format($account->initial_amount, 2) }}</dd>

                        <dt class="col-6">Current Balance:</dt>
                        <dd class="col-6">
                            <strong>{{ $account->currency->symbol }} {{ number_format($account->current_amount, 2) }}</strong>
                            @if($account->currency->code !== 'BDT')
                                <br><small class="text-muted">৳ {{ number_format($account->getAmountInBDT(), 2) }}</small>
                            @endif
                        </dd>

                        <dt class="col-6">Status:</dt>
                        <dd class="col-6">
                            <span class="badge badge-{{ $account->is_active ? 'success' : 'secondary' }}">
                                {{ $account->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.accounts.index') }}" class="btn btn-default btn-block">
                        <i class="fas fa-arrow-left"></i> Back to Accounts
                    </a>
                    <a href="{{ route('admin.account-transactions.index') }}" class="btn btn-info btn-block">
                        <i class="fas fa-list"></i> All Account Transactions
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop
