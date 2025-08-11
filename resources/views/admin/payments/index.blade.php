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
                    
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Payment Date</th>
                                <th>Account No</th>
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
                                    <td>{{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                                    <td>{{ $payment->account_no }}</td>
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
