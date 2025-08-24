@extends('adminlte::page')

@section('title', 'Payments')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Payments</h1>
        <a href="{{ route('admin.payments.create') }}" class="btn btn-primary">Add New Payment</a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment List</h3>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <form method="GET" action="{{ route('admin.payments.index') }}" class="mb-3">
                        <div class="form-row">
                            <div class="col-md-4 mb-2">
                                <label for="q" class="sr-only">Search</label>
                                <input type="text" name="q" id="q" value="{{ request('q') }}" class="form-control" placeholder="Search customer name or mobile">
                            </div>
                            <div class="col-md-3 mb-2">
                                <select name="bank_id" id="bank_id" class="form-control">
                                    <option value="">-- All Banks --</option>
                                    @isset($banks)
                                        @foreach($banks as $b)
                                            @php $code = optional($b->currency)->code; @endphp
                                            <option value="{{ $b->id }}" {{ (string)request('bank_id') === (string)$b->id ? 'selected' : '' }}>
                                                {{ $b->name }} @if($code) ({{ strtoupper($code) }}) @endif
                                            </option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="date" name="payment_date_from" id="payment_date_from" value="{{ request('payment_date_from') }}" class="form-control" placeholder="From">
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="date" name="payment_date_to" id="payment_date_to" value="{{ request('payment_date_to') }}" class="form-control" placeholder="To">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-2 mb-2">
                                <input type="number" step="0.01" name="min_amount" id="min_amount" value="{{ request('min_amount') }}" class="form-control" placeholder="Min amount">
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="number" step="0.01" name="max_amount" id="max_amount" value="{{ request('max_amount') }}" class="form-control" placeholder="Max amount">
                            </div>
                            <div class="col-md-4 mb-2 d-flex align-items-center">
                                <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-search"></i> Search</button>
                                <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
                            </div>
                        </div>
                    </form>
                    
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Payment Date</th>
                                <th>Bank</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td>{{ $payment->id }}</td>
                                    <td>
                                        @if($payment->customer)
                                            {{ $payment->customer->name }} ({{ $payment->customer->mobile }})
                                        @else
                                            Customer not found
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $code = optional(optional($payment->bank)->currency)->code ?? 'BDT';
                                            $rate = (float) (optional(optional($payment->bank)->currency)->conversion_rate ?? 1);
                                            if ($rate <= 0) { $rate = 1; }
                                            // payment->amount is stored in BDT; convert to native when non-BDT
                                            $native = strtoupper($code) === 'BDT' ? (float) $payment->amount : ((float) $payment->amount / $rate);
                                        @endphp
                                        {{ $code }} {{ number_format($native, 2) }}
                                        @if (strtoupper($code) !== 'BDT')
                                            <small class="text-muted">(≈ BDT {{ number_format($payment->amount, 2) }})</small>
                                        @endif
                                    </td>
                                    <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                                    <td>
                                        @if($payment->bank)
                                            {{ $payment->bank->name }}
                                        @else
                                            Bank not found
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('admin.payments.edit', $payment->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.payments.destroy', $payment->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this payment?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No payments found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <div class="mt-3">
                        {{ $payments->links() }}
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
