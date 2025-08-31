@extends('adminlte::page')

@section('title', 'Ledger Report')

@section('content_header')
    <h1>Ledger Report</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filter Options</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.reports.ledger') }}" method="GET">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="end_date">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="customer_id">Customer</label>
                        <select name="customer_id" id="customer_id" class="form-control select2">
                            <option value="">All Customers</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} ({{ $customer->mobile }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="bank_id">Bank</label>
                        <select name="bank_id" id="bank_id" class="form-control select2">
                            <option value="">All Banks</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank->id }}" {{ request('bank_id') == $bank->id ? 'selected' : '' }}>
                                    {{ $bank->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="record_type">Record Type</label>
                        <select name="record_type" id="record_type" class="form-control">
                            <option value="all" {{ request('record_type', 'all') == 'all' ? 'selected' : '' }}>All Records</option>
                            <option value="invoice" {{ request('record_type') == 'invoice' ? 'selected' : '' }}>Invoices</option>
                            <option value="payment" {{ request('record_type') == 'payment' ? 'selected' : '' }}>Payments</option>
                            <option value="delivery" {{ request('record_type') == 'delivery' ? 'selected' : '' }}>Deliveries</option>
                            <option value="cancellation" {{ request('record_type') == 'cancellation' ? 'selected' : '' }}>Cancellations</option>
                            <option value="refund" {{ request('record_type') == 'refund' ? 'selected' : '' }}>Refunds</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <input type="hidden" name="filter" value="1">
                        <button type="submit" class="btn btn-primary mr-2">Apply Filters</button>
                        <a href="{{ route('admin.reports.ledger') }}" class="btn btn-default">Reset</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@if(request('filter'))
    <!-- Print Button -->
    @if(request()->has('filter'))
        <a href="{{ route('admin.reports.ledger.print', request()->all()) }}" target="_blank" class="btn btn-info ml-2">
            <i class="fas fa-print"></i> Print Report
        </a>
    @endif
    <!-- Customer Summary Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Customer Summary</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th class="text-right">Invoice Amount</th>
                        <th class="text-right">Payment Amount</th>
                        <th class="text-right">Delivery Amount</th>
                        <th class="text-right">Cancellation Amount</th>
                        <th class="text-right">Refund Amount</th>
                        <th class="text-right">Customer Balance/Due</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ledgerData as $data)
                        <tr>
                            <td>{{ $data['customer']->name }} ({{ $data['customer']->mobile }})</td>
                            <td class="text-right">{{ number_format($data['invoice_amount'], 2) }}</td>
                            <td class="text-right">{{ number_format($data['payment_amount'], 2) }}</td>
                            <td class="text-right">{{ number_format($data['delivery_amount'], 2) }}</td>
                            <td class="text-right">{{ number_format($data['cancellation_amount'], 2) }}</td>
                            <td class="text-right">{{ number_format($data['refund_amount'], 2) }}</td>
                            <td class="text-right font-weight-bold {{ $data['balance'] >= 0 ? 'text-danger' : 'text-success' }}">
                                {{ $data['balance'] >= 0 ? 'Customer Balance: ' : 'Customer Due: ' }} {{ number_format(abs($data['balance']), 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No records found</td>
                        </tr>
                    @endforelse
                    
                    <!-- Totals row -->
                    <tr class="bg-light font-weight-bold">
                        <td>TOTAL</td>
                        <td class="text-right">{{ number_format($totals['invoice'], 2) }}</td>
                        <td class="text-right">{{ number_format($totals['payment'], 2) }}</td>
                        <td class="text-right">{{ number_format($totals['delivery'], 2) }}</td>
                        <td class="text-right">{{ number_format($totals['cancellation'], 2) }}</td>
                        <td class="text-right">{{ number_format($totals['refund'], 2) }}</td>
                        <td class="text-right {{ $totals['balance'] >= 0 ? 'text-danger' : 'text-success' }}">
                            {{ $totals['balance'] >= 0 ? 'Customer Balance: ' : 'Customer Due: ' }} {{ number_format(abs($totals['balance']), 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detailed Records -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detailed Records</h3>
        </div>
        <div class="card-body">
            @forelse($ledgerData as $data)
                <div class="mb-4">
                    <h5>{{ $data['customer']->name }} ({{ $data['customer']->mobile }})</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Details</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['records'] as $record)
                                    <tr>
                                        <td>{{ date('Y-m-d', strtotime($record['date'])) }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($record['type'] == 'invoice') badge-primary
                                                @elseif($record['type'] == 'payment') badge-success
                                                @elseif($record['type'] == 'delivery') badge-info
                                                @elseif($record['type'] == 'cancellation') badge-warning
                                                @elseif($record['type'] == 'refund') badge-danger
                                                @endif
                                            ">
                                                {{ ucfirst($record['type']) }}
                                            </span>
                                        </td>
                                        <td>{{ $record['details'] }}</td>
                                        <td class="text-right">
                                            @if(in_array($record['type'], ['payment', 'cancellation', 'refund']))
                                                <span class="text-success">-{{ number_format($record['amount'], 2) }}</span>
                                            @else
                                                <span class="text-danger">{{ number_format($record['amount'], 2) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No records found</td>
                                    </tr>
                                @endforelse
                                <tr class="bg-light font-weight-bold">
                                    <td colspan="3" class="text-right">Balance:</td>
                                    <td class="text-right {{ $data['balance'] >= 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($data['balance'], 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    No records found for the selected filters.
                </div>
            @endforelse
        </div>
    </div>
@endif
@stop

@section('css')
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
    }
    
    /* Print styles */
    @media print {
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        
        .print-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .print-header h1 {
            margin: 0;
            font-size: 24px;
        }
        
        .print-header p {
            margin: 5px 0 0;
            font-size: 14px;
        }
        
        .customer-details h3,
        .customer-summary h3,
        .detailed-records h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 18px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        .print-customer-table,
        .print-summary-table,
        .print-details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .print-customer-table th,
        .print-customer-table td {
            padding: 5px;
            text-align: left;
            border: none;
        }
        
        .print-customer-table th {
            width: 120px;
            font-weight: bold;
        }
        
        .print-summary-table th,
        .print-summary-table td,
        .print-details-table th,
        .print-details-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }
        
        .print-summary-table th,
        .print-details-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        
        .print-details-table td:nth-child(1),
        .print-details-table td:nth-child(2),
        .print-details-table td:nth-child(3) {
            text-align: left;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        
        .text-danger {
            color: #dc3545;
        }
        
        .text-success {
            color: #28a745;
        }
        
        .page-break {
            page-break-after: always;
        }
    }
</style>
@stop


@section('js')
<script>
    $(document).ready(function() {
        $('.select2').select2();
        
        // Set default dates if not already set
        if (!$('#start_date').val() && !$('#end_date').val()) {
            // Set default to current month
            var date = new Date();
            var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
            var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
            
            var firstDayFormatted = firstDay.toISOString().slice(0, 10);
            var lastDayFormatted = lastDay.toISOString().slice(0, 10);
            
            $('#start_date').val(firstDayFormatted);
            $('#end_date').val(lastDayFormatted);
        }
        
        // No print functionality needed here as we use a dedicated print route
    });
</script>
@stop
