@extends('adminlte::page')

@section('title', 'View Refund')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Refund Details</h1>
        <div>
            <a href="{{ route('admin.refunds.edit', $refund->id) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('admin.refunds.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Refund #{{ $refund->id }}</h3>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Refund Information</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 30%">Refund ID</th>
                                            <td>{{ $refund->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Refund Amount</th>
                                            @php
                                                $code = optional(optional($refund->bank)->currency)->code ?? 'BDT';
                                                $rate = (float) (optional(optional($refund->bank)->currency)->conversion_rate ?? 1);
                                                if ($rate <= 0) { $rate = 1; }
                                                // refund_amount is stored in BDT; convert to native for display when non-BDT
                                                $native = strtoupper($code) === 'BDT' ? (float) $refund->refund_amount : ((float) $refund->refund_amount / $rate);
                                            @endphp
                                            <td>
                                                {{ $code }} {{ number_format($native, 2) }}
                                                @if (strtoupper($code) !== 'BDT')
                                                    <small class="text-muted">(≈ BDT {{ number_format($refund->refund_amount, 2) }})</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Refund Date</th>
                                            <td>{{ $refund->refund_date->format('Y-m-d') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Bank</th>
                                            <td>{{ $refund->bank ? $refund->bank->name . ' (' . $refund->bank->account_number . ')' : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Remarks</th>
                                            <td>{{ $refund->remarks ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created At</th>
                                            <td>{{ $refund->created_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Customer Information</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 30%">Customer Name</th>
                                            <td>{{ $refund->customer->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Mobile</th>
                                            <td>{{ $refund->customer->mobile }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ $refund->customer->email ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Address</th>
                                            <td>{{ $refund->customer->address ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Delivery Class</th>
                                            <td>{{ $refund->customer->delivery_class }}</td>
                                        </tr>
                                        <tr>
                                            <th>KAM/Staff</th>
                                            <td>{{ $refund->customer->staff->name ?? 'Not Assigned' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <form action="{{ route('admin.refunds.destroy', $refund->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this refund?');" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
