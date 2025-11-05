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
            <form method="GET" action="{{ route('admin.cancellations.index') }}" class="mb-3">
                <div class="form-row">
                    <div class="col-md-4 mb-2">
                        <label for="q" class="sr-only">Search</label>
                        <input type="text" name="q" id="q" value="{{ request('q') }}" class="form-control" placeholder="Search customer name, mobile, or remarks">
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
                        <input type="date" name="cancellation_date_from" id="cancellation_date_from" value="{{ request('cancellation_date_from') }}" class="form-control" placeholder="From">
                    </div>
                    <div class="col-md-2 mb-2">
                        <input type="date" name="cancellation_date_to" id="cancellation_date_to" value="{{ request('cancellation_date_to') }}" class="form-control" placeholder="To">
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
                        <a href="{{ route('admin.cancellations.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
                    </div>
                </div>
            </form>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Mobile</th>
                        <th>Staff</th>
                        <th>Cancellation Value</th>
                        <th>Cancellation Date</th>
                        <th>Remarks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cancellations as $cancellation)
                        <tr>
                            <td>{{ $cancellation->id }}</td>
                            <td>{{ optional($cancellation->customer)->name }}</td>
                            <td>{{ optional($cancellation->customer)->mobile }}</td>
                            <td>{{ optional($cancellation->staff)->name ?? '-' }}</td>
                            <td>{{ number_format($cancellation->cancellation_value, 2) }}</td>
                            <td>{{ $cancellation->cancellation_date->format('d-m-Y') }}</td>
                            <td>{{ $cancellation->remarks }}</td>
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
            @if($cancellations->hasPages())
            <div class="card-footer clearfix">
                {{ $cancellations->appends(request()->query())->links() }}
            </div>
            @endif
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
