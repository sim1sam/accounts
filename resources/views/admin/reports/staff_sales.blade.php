@extends('adminlte::page')

@section('title', 'Staff Sales Report')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Staff-wise Sales</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <form class="form-inline" method="get" action="{{ route('admin.reports.staff_sales') }}">
            <div class="form-group mr-2">
                <label for="from" class="mr-2">From</label>
                <input type="date" class="form-control" id="from" name="from" value="{{ request('from', $start) }}">
            </div>
            <div class="form-group mr-2">
                <label for="to" class="mr-2">To</label>
                <input type="date" class="form-control" id="to" name="to" value="{{ request('to', $end) }}">
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Staff</th>
                        <th class="text-right">Invoice Amount</th>
                        <th class="text-right">Cancel Amount</th>
                        <th class="text-right">Sale Amount</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $i => $row)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                @if(!empty($row->staff_id))
                                    <a href="{{ route('admin.staff.show', $row->staff_id) }}">{{ $row->staff_name }}</a>
                                @else
                                    <span class="text-muted">{{ $row->staff_name }} (Unassigned)</span>
                                @endif
                            </td>
                            <td class="text-right">৳ {{ number_format($row->invoice_total, 2) }}</td>
                            <td class="text-right text-danger">৳ {{ number_format($row->cancel_total, 2) }}</td>
                            <td class="text-right font-weight-bold">৳ {{ number_format($row->sale_total, 2) }}</td>
                            <td class="text-right">
                                @if(!empty($row->staff_id))
                                    <a href="{{ route('admin.invoices.index', ['staff_id' => $row->staff_id]) }}" class="btn btn-xs btn-outline-primary">Invoices</a>
                                    <a href="{{ route('admin.cancellations.index', ['staff_id' => $row->staff_id]) }}" class="btn btn-xs btn-outline-warning">Cancellations</a>
                                @else
                                    <button class="btn btn-xs btn-outline-secondary" disabled>Invoices</button>
                                    <button class="btn btn-xs btn-outline-secondary" disabled>Cancellations</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No data for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($rows))
                <tfoot>
                    @php
                        $sumInv = $rows->sum('invoice_total');
                        $sumCan = $rows->sum('cancel_total');
                        $sumSale = $rows->sum('sale_total');
                    @endphp
                    <tr>
                        <th colspan="2" class="text-right">Total</th>
                        <th class="text-right">৳ {{ number_format($sumInv, 2) }}</th>
                        <th class="text-right text-danger">৳ {{ number_format($sumCan, 2) }}</th>
                        <th class="text-right">৳ {{ number_format($sumSale, 2) }}</th>
                        <th></th>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@stop
