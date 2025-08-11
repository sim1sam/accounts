@extends('adminlte::page')

@section('title', 'Deliveries')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Deliveries</h1>
        <a href="{{ route('admin.deliveries.create') }}" class="btn btn-primary">Create Delivery</a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Mobile</th>
                        <th>Delivery Value</th>
                        <th>Delivery Date</th>
                        <th>Shipment No</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($deliveries as $delivery)
                        <tr>
                            <td>{{ $delivery->id }}</td>
                            <td>{{ $delivery->customer->name }}</td>
                            <td>{{ $delivery->customer->mobile }}</td>
                            <td>{{ $delivery->delivery_value }}</td>
                            <td>{{ $delivery->delivery_date->format('Y-m-d') }}</td>
                            <td>{{ $delivery->shipment_no }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.deliveries.show', $delivery) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.deliveries.edit', $delivery) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.deliveries.destroy', $delivery) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this delivery?');" style="display: inline;">
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
                            <td colspan="7" class="text-center">No deliveries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $deliveries->links() }}
            </div>
        </div>
    </div>
@stop
