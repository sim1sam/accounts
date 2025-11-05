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
            <form method="GET" action="{{ route('admin.deliveries.index') }}" class="mb-3">
                <div class="form-row">
                    <div class="col-md-4 mb-2">
                        <label for="q" class="sr-only">Search</label>
                        <input type="text" name="q" id="q" value="{{ request('q') }}" class="form-control" placeholder="Search shipment no, customer name or mobile">
                    </div>
                    <div class="col-md-3 mb-2">
                        <select name="staff_id" id="staff_id" class="form-control">
                            <option value="">-- All Staff (KAM) --</option>
                            @isset($staff)
                                @foreach($staff as $s)
                                    <option value="{{ $s->id }}" {{ (string)request('staff_id') === (string)$s->id ? 'selected' : '' }}>{{ $s->name }} @if($s->phone) - {{ $s->phone }} @endif</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <input type="date" name="delivery_date_from" id="delivery_date_from" value="{{ request('delivery_date_from') }}" class="form-control" placeholder="From">
                    </div>
                    <div class="col-md-2 mb-2">
                        <input type="date" name="delivery_date_to" id="delivery_date_to" value="{{ request('delivery_date_to') }}" class="form-control" placeholder="To">
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
                        <a href="{{ route('admin.deliveries.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
                    </div>
                </div>
            </form>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Mobile</th>
                        <th>KAM/Staff</th>
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
                            <td>{{ optional($delivery->customer->keyAccountManager)->name ?? '-' }}</td>
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

            @if($deliveries->hasPages())
            <div class="card-footer clearfix">
                {{ $deliveries->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
@stop
