@extends('adminlte::page')

@section('title', 'Staff Details')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Staff Details</h1>
        <div>
            <a href="{{ route('admin.staff.edit', $staff) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('admin.staff.index') }}" class="btn btn-default">Back to List</a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th style="width: 200px;">ID</th>
                    <td>{{ $staff->id }}</td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td>{{ $staff->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $staff->email ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $staff->phone ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Position</th>
                    <td>{{ $staff->position ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge badge-{{ $staff->status == 'active' ? 'success' : 'danger' }}">
                            {{ ucfirst($staff->status) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $staff->created_at->format('Y-m-d H:i:s') }}</td>
                </tr>
                <tr>
                    <th>Updated At</th>
                    <td>{{ $staff->updated_at->format('Y-m-d H:i:s') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Customers Managed by This Staff</h3>
        </div>
        <div class="card-body">
            @if($staff->customers->count() > 0)
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff->customers as $customer)
                            <tr>
                                <td>{{ $customer->id }}</td>
                                <td>{{ $customer->name }}</td>
                                <td>{{ $customer->email ?? 'N/A' }}</td>
                                <td>{{ $customer->mobile ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No customers are currently managed by this staff member.</p>
            @endif
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop
