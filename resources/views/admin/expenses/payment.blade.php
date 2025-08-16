@extends('adminlte::page')

@section('title', 'Process Payment')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Process Payment</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.expenses.index') }}">Expenses</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.expenses.show', $expense) }}">Details</a></li>
                <li class="breadcrumb-item active">Payment</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Details</h3>
                </div>
                <form action="{{ route('admin.expenses.payment.process', $expense) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="bank_id">Select Bank <span class="text-danger">*</span></label>
                            <select class="form-control @error('bank_id') is-invalid @enderror" id="bank_id" name="bank_id" required>
                                <option value="">Select Bank</option>
                                @foreach($banks as $bank)
                                    <option value="{{ $bank->id }}" data-balance="{{ $bank->current_balance }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                        {{ $bank->name }} (Balance: ৳ {{ number_format($bank->current_balance, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('bank_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="payment_amount">Payment Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" class="form-control @error('payment_amount') is-invalid @enderror" id="payment_amount" name="payment_amount" value="{{ old('payment_amount', $expense->amount_in_bdt) }}" required>
                            @error('payment_amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="form-text text-muted">
                                Expense amount: ৳ {{ number_format($expense->amount_in_bdt, 2) }}
                            </small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-credit-card"></i> Process Payment
                        </button>
                        <a href="{{ route('admin.expenses.show', $expense) }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Expense Summary</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-6">Account:</dt>
                        <dd class="col-6">{{ $expense->account->name }}</dd>

                        <dt class="col-6">Amount:</dt>
                        <dd class="col-6">
                            {{ $expense->account->currency->symbol }} {{ number_format($expense->amount, 2) }}
                            @if($expense->account->currency->code !== 'BDT')
                                <br><small class="text-muted">৳ {{ number_format($expense->amount_in_bdt, 2) }}</small>
                            @endif
                        </dd>

                        <dt class="col-6">Status:</dt>
                        <dd class="col-6">
                            <span class="badge badge-warning">Pending Payment</span>
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Important Notes</h3>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><i class="fas fa-info-circle text-info"></i> Payment will be recorded in transaction history</li>
                        <li><i class="fas fa-bank text-primary"></i> Bank balance will be deducted</li>
                        <li><i class="fas fa-check text-success"></i> Expense status will be marked as paid</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop
