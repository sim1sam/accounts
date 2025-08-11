@extends('adminlte::page')

@section('title', 'View Delivery')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Delivery Details</h1>
        <div>
            <a href="{{ route('admin.deliveries.edit', $delivery) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('admin.deliveries.index') }}" class="btn btn-default">Back to List</a>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h4>Delivery Information</h4>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">Delivery ID</th>
                            <td>{{ $delivery->id }}</td>
                        </tr>
                        <tr>
                            <th>Delivery Value</th>
                            <td>{{ $delivery->delivery_value }}</td>
                        </tr>
                        <tr>
                            <th>Delivery Date</th>
                            <td>{{ $delivery->delivery_date->format('Y-m-d') }}</td>
                        </tr>
                        <tr>
                            <th>Shipment No</th>
                            <td>{{ $delivery->shipment_no }}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $delivery->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $delivery->updated_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h4>Customer Information</h4>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">Customer Name</th>
                            <td>{{ $delivery->customer->name }}</td>
                        </tr>
                        <tr>
                            <th>Mobile</th>
                            <td>{{ $delivery->customer->mobile }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $delivery->customer->email }}</td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td>{{ $delivery->customer->address }}</td>
                        </tr>
                        <tr>
                            <th>Delivery Class</th>
                            <td>{{ $delivery->customer->delivery_class }}</td>
                        </tr>
                        <tr>
                            <th>KAM</th>
                            <td>{{ $delivery->customer->staff ? $delivery->customer->staff->name : 'Not Assigned' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="mt-3">
                <form action="{{ route('admin.deliveries.destroy', $delivery) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this delivery?');" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Delivery</button>
                </form>
            </div>
        </div>
    </div>
@stop
