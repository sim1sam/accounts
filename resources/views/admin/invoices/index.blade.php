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
                    
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Invoice ID</th>
                                <th>Customer</th>
                                <th>Invoice Value</th>
                                <th>Created At</th>
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
                                    <td>{{ number_format($invoice->invoice_value, 2) }}</td>
                                    <td>{{ $invoice->created_at->format('Y-m-d H:i:s') }}</td>
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
                    
                    <div class="mt-3">
                        {{ $invoices->links() }}
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
