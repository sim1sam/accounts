@extends('adminlte::page')

@section('title', 'Budgets')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Budgets</h1>
        <a href="{{ route('admin.budgets.create') }}" class="btn btn-primary">Create Budget</a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Monthly Budgets</h3>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Month</th>
                        <th>Status</th>
                        <th>Items</th>
                        <th>Total (≈ BDT)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($budgets as $budget)
                        @php
                            // lazy load items sum in BDT when needed
                            $totalBdt = $budget->items()->sum('amount_in_bdt');
                        @endphp
                        <tr>
                            <td>{{ $budget->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($budget->month)->format('Y-m') }}</td>
                            <td>
                                @if($budget->status === 'converted')
                                    <span class="badge badge-success">Converted</span>
                                @else
                                    <span class="badge badge-secondary">Planned</span>
                                @endif
                            </td>
                            <td>{{ $budget->items_count }}</td>
                            <td>BDT {{ number_format($totalBdt, 2) }}</td>
                            <td>
                                <a href="{{ route('admin.budgets.show', $budget->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No budgets found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">{{ $budgets->links() }}</div>
        </div>
    </div>
@stop
