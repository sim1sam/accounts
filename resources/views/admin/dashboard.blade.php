@extends('adminlte::page')

@section('title', 'Accounts Dashboard')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <!-- Info boxes -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-2">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Customers</span>
                    <span class="info-box-number">{{ \App\Models\Customer::count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-2">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-file-invoice"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Invoices</span>
                    <span class="info-box-number">{{ \App\Models\Invoice::count() }}</span>
                </div>
            </div>
        </div>

        <div class="clearfix hidden-md-up"></div>

        <div class="col-12 col-sm-6 col-md-2">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-money-bill-wave"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Payments</span>
                    <span class="info-box-number">{{ \App\Models\Payment::count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-2">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-ban"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Cancellations</span>
                    <span class="info-box-number">{{ \App\Models\Cancellation::count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-2">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-undo"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Refunds</span>
                    <span class="info-box-number">{{ \App\Models\Refund::count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-2">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-truck"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Deliveries</span>
                    <span class="info-box-number">{{ \App\Models\Delivery::count() }}</span>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->

    <div class="row">
        <!-- Left col -->
        <div class="col-md-8">
            <!-- Financial Summary -->
            <div class="card">
                <div class="card-header border-transparent">
                    <h3 class="card-title">Financial Summary</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Count</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><a href="{{ route('admin.invoices.index') }}">Invoices</a></td>
                                    <td>{{ \App\Models\Invoice::count() }}</td>
                                    <td>৳ {{ number_format(\App\Models\Invoice::sum('invoice_value'), 2) }}</td>
                                    <td><span class="badge badge-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td><a href="{{ route('admin.payments.index') }}">Payments</a></td>
                                    <td>{{ \App\Models\Payment::count() }}</td>
                                    <td>৳ {{ number_format(\App\Models\Payment::sum('amount'), 2) }}</td>
                                    <td><span class="badge badge-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td><a href="{{ route('admin.deliveries.index') }}">Deliveries</a></td>
                                    <td>{{ \App\Models\Delivery::count() }}</td>
                                    <td>৳ {{ number_format(\App\Models\Delivery::sum('delivery_value'), 2) }}</td>
                                    <td><span class="badge badge-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td><a href="{{ route('admin.cancellations.index') }}">Cancellations</a></td>
                                    <td>{{ \App\Models\Cancellation::count() }}</td>
                                    <td>৳ {{ number_format(\App\Models\Cancellation::sum('cancellation_value'), 2) }}</td>
                                    <td><span class="badge badge-warning">Pending</span></td>
                                </tr>
                                <tr>
                                    <td><a href="{{ route('admin.refunds.index') }}">Refunds</a></td>
                                    <td>{{ \App\Models\Refund::count() }}</td>
                                    <td>৳ {{ number_format(\App\Models\Refund::sum('refund_amount'), 2) }}</td>
                                    <td><span class="badge badge-warning">Pending</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer clearfix">
                    <a href="{{ route('admin.reports.ledger') }}" class="btn btn-sm btn-info float-left">View Ledger Report</a>
                    <a href="{{ route('admin.transactions.index') }}" class="btn btn-sm btn-secondary float-right">View All Transactions</a>
                </div>
                <!-- /.card-footer -->
            </div>
            <!-- /.card -->

            <!-- Recent Customers -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recently Added Customers</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">
                        @foreach(\App\Models\Customer::latest()->take(5)->get() as $customer)
                        <li class="nav-item">
                            <a href="{{ route('admin.customers.show', $customer->id) }}" class="nav-link">
                                <i class="fas fa-user"></i> {{ $customer->name }}
                                <span class="float-right text-primary">{{ $customer->mobile }}</span>
                            </a>
                            <p class="text-muted ml-3 mb-2 small">{{ $customer->address }}</p>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <!-- /.card-body -->
                <div class="card-footer text-center">
                    <a href="{{ route('admin.customers.index') }}" class="uppercase">View All Customers</a>
                </div>
                <!-- /.card-footer -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->

        <div class="col-md-4">
            <!-- Bank Info -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Bank Information</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">
                        @foreach(\App\Models\Bank::all() as $bank)
                        <li class="nav-item">
                            <a href="{{ route('admin.banks.show', $bank->id) }}" class="nav-link">
                                <i class="fas fa-university"></i> {{ $bank->name }}
                                <span class="float-right text-{{ $bank->current_balance > 0 ? 'success' : 'danger' }}">
                                    ৳ {{ number_format($bank->current_balance, 2) }}
                                </span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <!-- /.card-body -->
                <div class="card-footer text-center">
                    <a href="{{ route('admin.banks.index') }}" class="uppercase">View All Banks</a>
                </div>
                <!-- /.card-footer -->
            </div>
            <!-- /.card -->

            <!-- Staff Info -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Staff Information</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-tool" data-card-widget="remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">
                        @foreach(\App\Models\Staff::take(5)->get() as $staff)
                        <li class="nav-item">
                            <a href="{{ route('admin.staff.show', $staff->id) }}" class="nav-link">
                                <i class="fas fa-user-tie"></i> {{ $staff->name }}
                                <span class="float-right text-primary">
                                    {{ $staff->designation }}
                                </span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <!-- /.card-body -->
                <div class="card-footer text-center">
                    <a href="{{ route('admin.staff.index') }}" class="uppercase">View All Staff</a>
                </div>
                <!-- /.card-footer -->
            </div>
            <!-- /.card -->

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="btn-group w-100 mb-2">
                        <a class="btn btn-info" href="{{ route('admin.invoices.create') }}">New Invoice</a>
                        <a class="btn btn-primary" href="{{ route('admin.payments.create') }}">New Payment</a>
                        <a class="btn btn-success" href="{{ route('admin.deliveries.create') }}">New Delivery</a>
                    </div>
                    <div class="btn-group w-100">
                        <a class="btn btn-warning" href="{{ route('admin.cancellations.create') }}">New Cancellation</a>
                        <a class="btn btn-danger" href="{{ route('admin.refunds.create') }}">New Refund</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
@stop

@section('css')
    <style>
        .info-box .info-box-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .products-list .product-img {
            float: left;
            width: 50px;
        }
        .products-list .product-info {
            margin-left: 60px;
        }
    </style>
@stop

@section('js')
    <script>
        $(function () {
            'use strict'
            
            // Make the dashboard widgets sortable using jQuery UI
            $('.connectedSortable').sortable({
                placeholder: 'sort-highlight',
                connectWith: '.connectedSortable',
                handle: '.card-header, .nav-tabs',
                forcePlaceholderSize: true,
                zIndex: 999999
            })
            
            $('.connectedSortable .card-header').css('cursor', 'move')
            
            console.log('Accounts Dashboard loaded successfully!');
        });
    </script>
@stop
