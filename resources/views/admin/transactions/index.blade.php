@extends('adminlte::page')

@section('title', 'Transactions')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Transactions</h1>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Transaction List</h3>
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
                                <th>Date</th>
                                <th>Bank</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->id }}</td>
                                    <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                                    <td>
                                        @if($transaction->bank)
                                            {{ $transaction->bank->name }}
                                        @else
                                            Bank not found
                                        @endif
                                    </td>
                                    <td>{{ number_format($transaction->amount, 2) }}</td>
                                    <td>
                                        @if($transaction->type == 'credit')
                                            <span class="badge badge-success">Credit</span>
                                        @else
                                            <span class="badge badge-danger">Debit</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->description }}</td>
                                    <td>
                                        <a href="{{ route('admin.transactions.show', $transaction->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No transactions found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <div class="mt-3">
                        {{ $transactions->links() }}
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
