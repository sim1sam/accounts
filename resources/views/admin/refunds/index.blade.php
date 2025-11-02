@extends('adminlte::page')

@section('title', 'Refunds')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Refunds</h1>
        <a href="{{ route('admin.refunds.create') }}" class="btn btn-primary">Create Refund</a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <form method="GET" action="{{ route('admin.refunds.index') }}" class="mb-3">
                <div class="form-row">
                    <div class="col-md-4 mb-2">
                        <label for="q" class="sr-only">Search</label>
                        <input type="text" name="q" id="q" value="{{ request('q') }}" class="form-control" placeholder="Search customer name, mobile, or remarks">
                    </div>
                    <div class="col-md-3 mb-2">
                        <select name="bank_id" id="bank_id" class="form-control">
                            <option value="">-- All Banks --</option>
                            @isset($banks)
                                @foreach($banks as $b)
                                    @php $code = optional($b->currency)->code; @endphp
                                    <option value="{{ $b->id }}" {{ (string)request('bank_id') === (string)$b->id ? 'selected' : '' }}>
                                        {{ $b->name }} @if($code) ({{ strtoupper($code) }}) @endif
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <input type="date" name="refund_date_from" id="refund_date_from" value="{{ request('refund_date_from') }}" class="form-control" placeholder="From">
                    </div>
                    <div class="col-md-2 mb-2">
                        <input type="date" name="refund_date_to" id="refund_date_to" value="{{ request('refund_date_to') }}" class="form-control" placeholder="To">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-2 mb-2">
                        <input type="number" step="0.01" name="min_amount" id="min_amount" value="{{ request('min_amount') }}" class="form-control" placeholder="Min amount">
                    </div>
                    <div class="col-md-2 mb-2">
                        <input type="number" step="0.01" name="max_amount" id="max_amount" value="{{ request('max_amount') }}" class="form-control" placeholder="Max amount">
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-center">
                        <button type="submit" class="btn btn-primary mr-2"><i class="fas fa-search"></i> Search</button>
                        <a href="{{ route('admin.refunds.index') }}" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
                    </div>
                </div>
            </form>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Bank</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($refunds as $refund)
                        <tr>
                            <td>{{ $refund->id }}</td>
                            <td>{{ $refund->customer->name }} ({{ $refund->customer->mobile }})</td>
                            <td>
                                @php
                                    $code = optional(optional($refund->bank)->currency)->code ?? 'BDT';
                                    $rate = (float) (optional(optional($refund->bank)->currency)->conversion_rate ?? 1);
                                    if ($rate <= 0) { $rate = 1; }
                                    // refund_amount is stored in BDT; convert to native when non-BDT
                                    $native = strtoupper($code) === 'BDT' ? (float) $refund->refund_amount : ((float) $refund->refund_amount / $rate);
                                @endphp
                                {{ $code }} {{ number_format($native, 2) }}
                                @if (strtoupper($code) !== 'BDT')
                                    <small class="text-muted">(≈ BDT {{ number_format($refund->refund_amount, 2) }})</small>
                                @endif
                            </td>
                            <td>{{ $refund->refund_date->format('Y-m-d') }}</td>
                            <td>{{ $refund->bank ? $refund->bank->name . ' (' . $refund->bank->account_number . ')' : 'N/A' }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.refunds.show', $refund->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.refunds.edit', $refund->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.refunds.destroy', $refund->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this refund?');" style="display: inline;">
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
                            <td colspan="6" class="text-center">No refunds found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($refunds->hasPages())
            <div class="card-footer clearfix">
                {{ $refunds->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('table').DataTable({
                "responsive": true,
                "autoWidth": false,
            });
        });
    </script>
@stop
