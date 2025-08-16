@extends('adminlte::page')

@section('title', 'Expenses')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Expenses</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Expenses</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Expense List</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Expense
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                    @if($expenses->count() > 0)
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Account</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenses as $expense)
                                <tr>
                                    <td>{{ $expense->id }}</td>
                                    <td>{{ $expense->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <strong>{{ $expense->account->currency->symbol }} {{ number_format($expense->amount, 2) }}</strong>
                                        <br><small class="text-muted">৳ {{ number_format($expense->amount_in_bdt, 2) }}</small>
                                    </td>
                                    <td>{{ $expense->account->name }}</td>
                                    <td>
                                        @if($expense->isPending())
                                            <span class="badge badge-warning">Pending</span>
                                        @else
                                            <span class="badge badge-success">Paid</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('admin.expenses.show', $expense) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($expense->isPending())
                                                <a href="{{ route('admin.expenses.edit', $expense) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.expenses.payment', $expense) }}" class="btn btn-success btn-sm">
                                                    <i class="fas fa-credit-card"></i> Pay
                                                </a>
                                                <form action="{{ route('admin.expenses.destroy', $expense) }}" method="POST" style="display: inline-block;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center p-4">
                            <p>No expenses found.</p>
                            <a href="{{ route('admin.expenses.create') }}" class="btn btn-primary">Create Your First Expense</a>
                        </div>
                    @endif
                </div>
                <!-- /.card-body -->
                @if($expenses->hasPages())
                <div class="card-footer clearfix">
                    {{ $expenses->links() }}
                </div>
                @endif
            </div>
            <!-- /.card -->
        </div>
    </div>
@stop
