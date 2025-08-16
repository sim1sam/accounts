@extends('adminlte::page')

@section('title', 'Account Transaction History')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Account Transaction History</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Account History</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Transaction History</h3>
                    <div class="card-tools">
                        <form method="GET" action="{{ route('admin.account-transactions.index') }}" class="form-inline">
                            <select name="account_id" class="form-control form-control-sm mr-2" onchange="this.form.submit()">
                                <option value="">All Accounts</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ $selectedAccountId == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                    @if($transactions->count() > 0)
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Account</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Balance Before</th>
                                    <th>Balance After</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                    <td>{{ $transaction->account->name }}</td>
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
                                    <td>৳ {{ number_format($transaction->balance_before, 2) }}</td>
                                    <td>৳ {{ number_format($transaction->balance_after, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center p-4">
                            <p>No account transactions found.</p>
                        </div>
                    @endif
                </div>
                <!-- /.card-body -->
                @if($transactions->hasPages())
                <div class="card-footer clearfix">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
            <!-- /.card -->
        </div>
    </div>
@stop
