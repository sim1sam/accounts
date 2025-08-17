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
