@extends('adminlte::page')

@section('title', 'Customers')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Customers</h1>
        <div class="btn-group">
            <a href="{{ route('admin.customers.import') }}" class="btn btn-success">
                <i class="fas fa-file-upload"></i> Bulk Upload
            </a>
            <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Customer
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="mb-3">
                <div class="form-row">
                    <div class="col-md-8 mb-2">
                        <label for="q" class="sr-only">Search</label>
                        <input type="text" name="q" id="q" value="{{ request('q') }}" class="form-control" placeholder="Search by name or mobile">
                    </div>
                    <div class="col-md-4 mb-2 d-flex">
                        <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-search"></i> Search</button>
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
                    </div>
                </div>
            </form>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Delivery Class</th>
                        <th>KAM</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td>{{ $customer->id }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->mobile }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->delivery_class }}</td>
                            <td>{{ $customer->keyAccountManager ? $customer->keyAccountManager->name : 'N/A' }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this customer?');" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No customers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($customers->hasPages())
        <div class="card-footer clearfix">
            {{ $customers->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
        </div>
        @endif
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .pagination {
            margin-bottom: 0;
        }
        .pagination .page-link {
            color: #007bff;
            border-color: #dee2e6;
        }
        .pagination .page-item.active .page-link {
            background-color: #007bff;
            border-color: #007bff;
        }
        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #fff;
            border-color: #dee2e6;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Auto close alerts after 5 seconds
            setTimeout(function() {
                $(".alert").alert('close');
            }, 5000);
        });
    </script>
@stop
