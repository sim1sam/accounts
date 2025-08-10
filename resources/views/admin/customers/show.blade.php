@extends('adminlte::page')

@section('title', 'Customer Details')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Customer Details</h1>
        <div>
            <a href="{{ route('admin.customers.edit', $customer) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('admin.customers.index') }}" class="btn btn-default">Back to List</a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">Name</th>
                            <td>{{ $customer->name }}</td>
                        </tr>
                        <tr>
                            <th>Mobile</th>
                            <td>{{ $customer->mobile }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $customer->email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Date of Birth</th>
                            <td>{{ $customer->dob ? $customer->dob->format('d M, Y') : 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">Address</th>
                            <td>{{ $customer->address ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Delivery Class</th>
                            <td>{{ $customer->delivery_class ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>KAM</th>
                            <td>{{ $customer->keyAccountManager ? $customer->keyAccountManager->name : 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $customer->created_at->format('d M, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="mt-4">
                <form action="{{ route('admin.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this customer?');" style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Customer</button>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop
