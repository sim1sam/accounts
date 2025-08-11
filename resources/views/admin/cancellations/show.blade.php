@extends('adminlte::page')

@section('title', 'View Cancellation')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Cancellation Details</h1>
        <div>
            <a href="{{ route('admin.cancellations.edit', $cancellation->id) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('admin.cancellations.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cancellation #{{ $cancellation->id }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Cancellation Information</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 30%">Cancellation ID</th>
                                            <td>{{ $cancellation->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Cancellation Value</th>
                                            <td>{{ number_format($cancellation->cancellation_value, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Cancellation Date</th>
                                            <td>{{ $cancellation->cancellation_date->format('d-m-Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created At</th>
                                            <td>{{ $cancellation->created_at->format('d-m-Y H:i:s') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Updated At</th>
                                            <td>{{ $cancellation->updated_at->format('d-m-Y H:i:s') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Customer Information</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 30%">Name</th>
                                            <td>{{ $cancellation->customer->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Mobile</th>
                                            <td>{{ $cancellation->customer->mobile }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ $cancellation->customer->email ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Address</th>
                                            <td>{{ $cancellation->customer->address ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Delivery Class</th>
                                            <td>{{ $cancellation->customer->delivery_class ?? 'N/A' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card card-secondary">
                                <div class="card-header">
                                    <h3 class="card-title">Remarks</h3>
                                </div>
                                <div class="card-body">
                                    <p>{{ $cancellation->remarks ?? 'No remarks provided.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <form action="{{ route('admin.cancellations.destroy', $cancellation->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this cancellation?');" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Cancellation</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
