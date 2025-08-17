@extends('adminlte::page')

@section('title', 'Payment Details')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Payment Details</h1>
        <div>
            <a href="{{ route('admin.payments.edit', $payment->id) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment #{{ $payment->id }}</h3>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Customer</label>
                                <p class="form-control-static">
                                    @if($payment->customer)
                                        {{ $payment->customer->name }} ({{ $payment->customer->mobile }})
                                    @else
                                        Customer not found
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Amount</label>
                                @php
                                    $code = optional(optional($payment->bank)->currency)->code ?? 'BDT';
                                    $rate = (float) (optional(optional($payment->bank)->currency)->conversion_rate ?? 1);
                                    if ($rate <= 0) { $rate = 1; }
                                    // amount is stored in BDT; convert to native for display when non-BDT
                                    $native = strtoupper($code) === 'BDT' ? (float) $payment->amount : ((float) $payment->amount / $rate);
                                @endphp
                                <p class="form-control-static">
                                    {{ $code }} {{ number_format($native, 2) }}
                                    @if (strtoupper($code) !== 'BDT')
                                        <small class="text-muted">(≈ BDT {{ number_format($payment->amount, 2) }})</small>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Date</label>
                                <p class="form-control-static">{{ $payment->payment_date->format('Y-m-d') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Bank</label>
                                <p class="form-control-static">
                                    @if($payment->bank)
                                        {{ $payment->bank->name }} - {{ $payment->bank->account_name }} ({{ $payment->bank->account_number }})
                                    @else
                                        Bank not found
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Created At</label>
                                <p class="form-control-static">{{ $payment->created_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Updated At</label>
                                <p class="form-control-static">{{ $payment->updated_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    @if($payment->customer)
                    <div class="card card-info mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Customer Details</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Customer Name</label>
                                        <p class="form-control-static">{{ $payment->customer->name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Mobile</label>
                                        <p class="form-control-static">{{ $payment->customer->mobile }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <p class="form-control-static">{{ $payment->customer->email ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Address</label>
                                        <p class="form-control-static">{{ $payment->customer->address ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Delivery Class</label>
                                        <p class="form-control-static">{{ $payment->customer->delivery_class ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>KAM/Staff Responsible</label>
                                        <p class="form-control-static">
                                            @if($payment->customer->staff)
                                                {{ $payment->customer->staff->name }}
                                            @else
                                                Not Assigned
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
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
