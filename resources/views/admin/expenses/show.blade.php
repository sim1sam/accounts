@extends('adminlte::page')

@section('title', 'Expense Details')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Expense Details</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.expenses.index') }}">Expenses</a></li>
                <li class="breadcrumb-item active">Details</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Expense Information</h3>
                    <div class="card-tools">
                        @if($expense->isPending())
                            <a href="{{ route('admin.expenses.edit', $expense) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('admin.expenses.payment', $expense) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-credit-card"></i> Pay Now
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Amount:</dt>
                        <dd class="col-sm-9">
                            <strong>{{ $expense->account->currency->symbol }} {{ number_format($expense->amount, 2) }}</strong>
                            <br><small class="text-muted">Equivalent: ৳ {{ number_format($expense->amount_in_bdt, 2) }}</small>
                        </dd>

                        <dt class="col-sm-3">Account:</dt>
                        <dd class="col-sm-9">{{ $expense->account->name }}</dd>

                        <dt class="col-sm-3">Status:</dt>
                        <dd class="col-sm-9">
                            @if($expense->isPending())
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge badge-success">Paid</span>
                                @if($expense->paid_at)
                                    <br><small class="text-muted">Paid on: {{ $expense->paid_at->format('M d, Y H:i') }}</small>
                                @endif
                            @endif
                        </dd>

                        <dt class="col-sm-3">Created:</dt>
                        <dd class="col-sm-9">{{ $expense->created_at->format('M d, Y H:i') }}</dd>

                        @if($expense->remarks)
                        <dt class="col-sm-3">Remarks:</dt>
                        <dd class="col-sm-9">{{ $expense->remarks }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            @if($expense->transaction)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Information</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Transaction ID:</dt>
                        <dd class="col-sm-9">#{{ $expense->transaction->id }}</dd>

                        <dt class="col-sm-3">Payment Amount:</dt>
                        <dd class="col-sm-9">৳ {{ number_format($expense->transaction->amount, 2) }}</dd>

                        <dt class="col-sm-3">Bank:</dt>
                        <dd class="col-sm-9">{{ $expense->transaction->bank->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-3">Payment Date:</dt>
                        <dd class="col-sm-9">{{ $expense->transaction->created_at->format('M d, Y H:i') }}</dd>
                    </dl>
                </div>
            </div>
            @endif
        </div>

        <div class="col-md-4">
            @if($expense->accountTransactions->count() > 0)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Account Transaction History</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        <tbody>
                            @foreach($expense->accountTransactions as $transaction)
                            <tr>
                                <td>
                                    <strong>{{ ucfirst($transaction->type) }}</strong><br>
                                    <small class="text-muted">{{ $transaction->created_at->format('M d, Y H:i') }}</small>
                                </td>
                                <td class="text-right">
                                    <span class="text-danger">-৳ {{ number_format($transaction->amount, 2) }}</span><br>
                                    <small class="text-muted">Balance: ৳ {{ number_format($transaction->balance_after, 2) }}</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Actions</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.expenses.index') }}" class="btn btn-default btn-block">
                        <i class="fas fa-arrow-left"></i> Back to Expenses
                    </a>
                    @if($expense->isPending())
                        <a href="{{ route('admin.expenses.payment', $expense) }}" class="btn btn-success btn-block">
                            <i class="fas fa-credit-card"></i> Process Payment
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
