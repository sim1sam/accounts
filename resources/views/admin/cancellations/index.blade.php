@extends('adminlte::page')

@section('title', 'Cancellations')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Cancellations</h1>
        <a href="{{ route('admin.cancellations.create') }}" class="btn btn-primary">Create Cancellation</a>
    </div>
@stop

@section('content')
    <div class="card">
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
                        <th>Customer</th>
                        <th>Cancellation Value</th>
                        <th>Cancellation Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cancellations as $cancellation)
                        <tr>
                            <td>{{ $cancellation->id }}</td>
                            <td>{{ $cancellation->customer->name }} ({{ $cancellation->customer->mobile }})</td>
                            <td>{{ number_format($cancellation->cancellation_value, 2) }}</td>
                            <td>{{ $cancellation->cancellation_date->format('d-m-Y') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.cancellations.show', $cancellation->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.cancellations.edit', $cancellation->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.refunds.create', ['from_cancellation' => $cancellation->id]) }}" class="btn btn-sm btn-success" title="Transfer to Refund">
                                        <i class="fas fa-exchange-alt"></i>
                                    </a>
                                    <form action="{{ route('admin.cancellations.destroy', $cancellation->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this cancellation?');" style="display: inline;">
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
                            <td colspan="5" class="text-center">No cancellations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    <style>
        .btn-group .btn {
            margin-right: 5px;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Initialize DataTable if needed
            // $('.table').DataTable();
        });
    </script>
@stop
