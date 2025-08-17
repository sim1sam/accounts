@extends('adminlte::page')

@section('title', 'Transaction Details')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Transaction Details</h1>
        <div>
            <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Transaction #{{ $transaction->id }}</h3>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Transaction Date</label>
                                <p class="form-control-static">{{ $transaction->transaction_date->format('Y-m-d') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Amount</label>
                                @php
                                    $code = optional(optional($transaction->bank)->currency)->code ?? 'BDT';
                                    $rate = (float) (optional(optional($transaction->bank)->currency)->conversion_rate ?? 1);
                                    if ($rate <= 0) { $rate = 1; }
                                    $native = strtoupper($code) === 'BDT' ? (float) $transaction->amount : ((float) $transaction->amount / $rate);
                                @endphp
                                <p class="form-control-static">
                                    {{ $code }} {{ number_format($native, 2) }}
                                    @if (strtoupper($code) !== 'BDT')
                                        <small class="text-muted">(≈ BDT {{ number_format($transaction->amount, 2) }})</small>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type</label>
                                <p class="form-control-static">
                                    @if($transaction->type == 'credit')
                                        <span class="badge badge-success">Credit</span>
                                    @else
                                        <span class="badge badge-danger">Debit</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bank</label>
                                <p class="form-control-static">
                                    @if($transaction->bank)
                                        {{ $transaction->bank->name }} - {{ $transaction->bank->account_name }} ({{ $transaction->bank->account_number }})
                                    @else
                                        Bank not found
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <p class="form-control-static">{{ $transaction->description }}</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($transaction->payment)
                    <div class="card card-info mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Related Payment</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Payment ID</label>
                                        <p class="form-control-static">
                                            <a href="{{ route('admin.payments.show', $transaction->payment->id) }}">
                                                #{{ $transaction->payment->id }}
                                            </a>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Payment Amount</label>
                                        <p class="form-control-static">{{ number_format($transaction->payment->amount, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Payment Date</label>
                                        <p class="form-control-static">{{ $transaction->payment->payment_date->format('Y-m-d') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Customer</label>
                                        <p class="form-control-static">
                                            @if($transaction->payment->customer)
                                                {{ $transaction->payment->customer->name }} ({{ $transaction->payment->customer->mobile }})
                                            @else
                                                Customer not found
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Created At</label>
                                <p class="form-control-static">{{ $transaction->created_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Updated At</label>
                                <p class="form-control-static">{{ $transaction->updated_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Initialize any plugins or scripts if needed
        });
    </script>
@stop
