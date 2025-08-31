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
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
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
                                <th>Status</th>
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
                                    <td>
                                        @php
                                            // Get data from meta if available
                                            $meta = json_decode($transaction->meta ?? '{}', true);
                                            $nativeCurrency = $meta['native_currency'] ?? null;
                                            $nativeAmount = $meta['native_amount'] ?? null;
                                            $bdtAmount = $meta['bdt_amount'] ?? null;
                                            $conversionRate = $meta['conversion_rate'] ?? null;
                                            
                                            // Fallback to calculated values if meta not available
                                            if (!$nativeCurrency) {
                                                $nativeCurrency = strtoupper(optional(optional($transaction->bank)->currency)->code ?? 'BDT');
                                            }
                                            
                                            if ($nativeCurrency === 'BDT') {
                                                $nativeAmount = (float) $transaction->amount;
                                                $bdtAmount = $nativeAmount;
                                            } else if (!$nativeAmount || !$bdtAmount) {
                                                if ($nativeCurrency === 'INR') {
                                                    // Special case for INR
                                                    $conversionRate = 1.45;
                                                    $nativeAmount = (float) $transaction->amount;
                                                    $bdtAmount = $nativeAmount * $conversionRate;
                                                } else {
                                                    $conversionRate = (float) (optional(optional($transaction->bank)->currency)->conversion_rate ?? 1);
                                                    if ($conversionRate <= 0) { $conversionRate = 1; }
                                                    $nativeAmount = (float) $transaction->amount;
                                                    $bdtAmount = $nativeAmount * $conversionRate;
                                                }
                                            }
                                        @endphp
                                        {{ $nativeCurrency }} {{ number_format($nativeAmount, 2) }}
                                        @if ($nativeCurrency !== 'BDT')
                                            <small class="text-muted">(= BDT {{ number_format($bdtAmount, 2) }})</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->type == 'credit')
                                            <span class="badge badge-success">Credit</span>
                                        @else
                                            <span class="badge badge-danger">Debit</span>
                                        @endif
                                    </td>
                                    <td>{{ $transaction->description }}</td>
                                    <td>
                                        @if($transaction->voided_at)
                                            <span class="badge badge-secondary">Voided</span>
                                        @else
                                            <span class="badge badge-success">Active</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.transactions.show', $transaction->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @if(!$transaction->voided_at)
                                        <form action="{{ route('admin.transactions.void', $transaction->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Void this transaction and revert the amount?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning">
                                                <i class="fas fa-ban"></i> Void
                                            </button>
                                        </form>
                                        @else
                                            <button type="button" class="btn btn-sm btn-default" disabled>
                                                <i class="fas fa-ban"></i> Void
                                            </button>
                                        @endif
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
