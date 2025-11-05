@extends('adminlte::page')

@section('title', 'Invoices')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Invoices</h1>
        <a href="{{ route('admin.invoices.create') }}" class="btn btn-primary">Add New Invoice</a>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Invoice List</h3>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <form method="GET" action="{{ route('admin.invoices.index') }}" class="mb-3">
                        <div class="form-row">
                            <div class="col-md-4 mb-2">
                                <label for="q" class="sr-only">Search</label>
                                <input type="text" name="q" id="q" value="{{ request('q') }}" class="form-control" placeholder="Search invoice ID, customer name or mobile">
                            </div>
                            <div class="col-md-3 mb-2">
                                <select name="staff_id" id="staff_id" class="form-control">
                                    <option value="">-- All Staff --</option>
                                    @isset($staff)
                                        @foreach($staff as $s)
                                            <option value="{{ $s->id }}" {{ (string)request('staff_id') === (string)$s->id ? 'selected' : '' }}>{{ $s->name }} @if($s->phone) - {{ $s->phone }} @endif</option>
                                        @endforeach
                                    @endisset
                                </select>
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="date" name="invoice_date_from" id="invoice_date_from" value="{{ request('invoice_date_from') }}" class="form-control" placeholder="From">
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="date" name="invoice_date_to" id="invoice_date_to" value="{{ request('invoice_date_to') }}" class="form-control" placeholder="To">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-2 mb-2">
                                <input type="number" step="0.01" name="min_value" id="min_value" value="{{ request('min_value') }}" class="form-control" placeholder="Min value">
                            </div>
                            <div class="col-md-2 mb-2">
                                <input type="number" step="0.01" name="max_value" id="max_value" value="{{ request('max_value') }}" class="form-control" placeholder="Max value">
                            </div>
                            <div class="col-md-4 mb-2 d-flex align-items-center">
                                <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-search"></i> Search</button>
                                <a href="{{ route('admin.invoices.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
                            </div>
                        </div>
                    </form>
                    
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Invoice ID</th>
                                <th>Customer</th>
                                <th>Staff</th>
                                <th>Invoice Value</th>
                                <th>Invoice Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->id }}</td>
                                    <td>{{ $invoice->invoice_id }}</td>
                                    <td>
                                        @if($invoice->customer)
                                            {{ $invoice->customer->name }} ({{ $invoice->customer->mobile }})
                                        @else
                                            Customer not found
                                        @endif
                                    </td>
                                    <td>
                                        @if($invoice->staff)
                                            {{ $invoice->staff->name }} @if($invoice->staff->phone) ({{ $invoice->staff->phone }}) @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ number_format($invoice->invoice_value, 2) }}</td>
                                    <td>{{ optional($invoice->invoice_date)->format('Y-m-d') ?? $invoice->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('admin.invoices.show', $invoice->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="{{ route('admin.invoices.edit', $invoice->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.invoices.destroy', $invoice->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this invoice?')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No invoices found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    @if($invoices->hasPages())
                    <div class="card-footer clearfix">
                        {{ $invoices->appends(request()->query())->links() }}
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
