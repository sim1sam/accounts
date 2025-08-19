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
            <!-- Month-wise Graphs -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Invoices vs Payments vs Expenses</h3>
                    <div class="form-inline">
                        <label for="monthPicker" class="mr-2 mb-0">Month</label>
                        <input type="month" id="monthPicker" class="form-control form-control-sm mr-3" value="{{ now()->format('Y-m') }}" />
                        <label for="dayPicker" class="mr-2 mb-0">Day</label>
                        <input type="date" id="dayPicker" class="form-control form-control-sm" />
                    </div>
                </div>
                <div class="card-body position-relative">
                    <div id="financeChartLoading" class="text-center text-info" style="padding: 25px;">
                        <i class="fas fa-spinner fa-spin"></i> Loading chart data...
                    </div>
                    <div id="financeChartEmpty" class="text-center text-muted" style="display:none; padding: 25px;">No data for the selected period.</div>
                    <canvas id="financeChart" height="160" style="width:100%; display:none;"></canvas>
                </div>
            </div>
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
            
            <!-- Top Staff Sales (This Month) -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Top Staff Sales (This Month)</h3>
                    <a href="{{ route('admin.reports.staff_sales') }}" class="btn btn-sm btn-outline-primary">View Full Report</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0">
                            <thead>
                                <tr>
                                    <th>Staff</th>
                                    <th class="text-right">Invoice</th>
                                    <th class="text-right">Cancel</th>
                                    <th class="text-right">Sale</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($staffSales ?? []) as $row)
                                    <tr>
                                        <td>
                                            @if(!empty($row['staff_id']))
                                                <a href="{{ route('admin.staff.show', $row['staff_id']) }}">{{ $row['staff_name'] }}</a>
                                            @else
                                                <span class="text-muted">{{ $row['staff_name'] }} (Unassigned)</span>
                                            @endif
                                        </td>
                                        <td class="text-right">৳ {{ number_format($row['invoice_total'], 2) }}</td>
                                        <td class="text-right text-danger">৳ {{ number_format($row['cancel_total'], 2) }}</td>
                                        <td class="text-right font-weight-bold">৳ {{ number_format($row['sale_total'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No sales yet this month.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
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
                                    ৳ {{ number_format($bank->current_balance ?? 0, 2) }}
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
    <!-- Chart.js CDN with fallback -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        // Fallback for Chart.js
        if (typeof Chart === 'undefined') {
            console.warn('Primary Chart.js CDN failed, loading fallback...');
            document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"><\/script>');
        }
    </script>
    <script>
        $(function () {
            'use strict'
            
            // Set up CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // jQuery UI sortable removed - not essential for dashboard functionality
            // If you need sortable widgets, include jQuery UI library
            
            console.log('Accounts Dashboard loaded successfully!');
            console.log('jQuery version:', $.fn.jquery);
            console.log('Chart.js available:', typeof Chart !== 'undefined');

            // Initialize month picker to current month
            const now = new Date();
            const ym = now.toISOString().slice(0,7);
            const $month = $('#monthPicker');
            if (!$month.val()) { $month.val(ym); }
            const $day = $('#dayPicker');
            
            console.log('Initial month picker value:', $month.val());
            console.log('Dashboard data route URL:', '{{ route('admin.dashboard.data') }}');

            let chartInstance = null;
            
            console.log('About to initialize chart functions...');

            function loadDashboardData(params) {
                let url = '{{ route('admin.dashboard.data') }}';
                const qs = [];
                if (params.date) qs.push('date=' + encodeURIComponent(params.date));
                else if (params.month) qs.push('month=' + encodeURIComponent(params.month));
                if (qs.length) url += '?' + qs.join('&');
                
                console.log('Loading dashboard data from URL:', url);
                console.log('Request params:', params);
                
                return $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    timeout: 10000, // 10 second timeout
                    success: function(data) {
                        console.log('Successfully loaded dashboard data:', data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Failed to load dashboard data:', {
                            url: url,
                            status: status,
                            error: error,
                            response: xhr.responseText,
                            statusCode: xhr.status,
                            readyState: xhr.readyState
                        });
                    }
                });
            }

            function hasAnyData(payload){
                const sum = (arr) => (arr || []).reduce((a,b)=>a+(+b||0), 0);
                return sum(payload.invoices) > 0 || sum(payload.payments) > 0 || sum(payload.expenses) > 0;
            }

            function renderChart(payload) {
                try {
                    console.log('Rendering chart with payload:', payload);
                    
                    const canvas = document.getElementById('financeChart');
                    if (!canvas) {
                        console.error('Chart canvas not found!');
                        return;
                    }
                    
                    const ctx = canvas.getContext('2d');
                    const $empty = $('#financeChartEmpty');
                    const $loading = $('#financeChartLoading');
                    
                    // Hide loading state
                    $loading.hide();
                    
                    // Toggle empty state
                    if (!hasAnyData(payload)) {
                        console.log('No data to display, showing empty state');
                        $empty.show();
                        canvas.style.display = 'none';
                        return;
                    } else {
                        $empty.hide();
                        canvas.style.display = 'block';
                    }
                    
                    // Check if Chart.js is loaded
                    if (typeof Chart === 'undefined') {
                        console.error('Chart.js is not loaded!');
                        return;
                    }
                const data = {
                    labels: payload.labels,
                    datasets: [
                        {
                            label: 'Invoices',
                            data: payload.invoices,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            backgroundColor: 'rgba(54, 162, 235, 0.1)',
                            tension: 0.25,
                        },
                        {
                            label: 'Payments',
                            data: payload.payments,
                            borderColor: 'rgba(40, 167, 69, 1)',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            tension: 0.25,
                        },
                        {
                            label: 'Expenses',
                            data: payload.expenses,
                            borderColor: 'rgba(220, 53, 69, 1)',
                            backgroundColor: 'rgba(220, 53, 69, 0.1)',
                            tension: 0.25,
                        }
                    ]
                };

                const options = {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: (v) => '৳ ' + Number(v).toLocaleString() }
                        }
                    },
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(ctx){
                                    const val = ctx.parsed.y || 0;
                                    return ctx.dataset.label + ': ৳ ' + Number(val).toLocaleString();
                                }
                            }
                        }
                    }
                };

                if (chartInstance) { chartInstance.destroy(); }
                chartInstance = new Chart(ctx, { type: 'line', data, options });
                console.log('Chart rendered successfully');
                
                } catch (error) {
                    console.error('Error rendering chart:', error);
                    $('#financeChartLoading').hide();
                    $('#financeChartEmpty').show().text('Error loading chart: ' + error.message);
                }
            }

            function refreshChart() {
                const month = $month.val();
                const day = ($day.val() || '').trim();
                const params = day ? { date: day } : { month };
                
                // Show loading state
                $('#financeChartLoading').show();
                $('#financeChartEmpty').hide();
                $('#financeChart').hide();
                
                console.log('Refreshing chart with params:', params);
                
                // Simple approach - just load and render
                loadDashboardData(params)
                    .done(function(payload){
                        console.log('dashboard payload received:', payload);
                        renderChart(payload);
                    })
                    .fail(function(xhr, status, error){
                        console.error('Failed to load dashboard data:', {xhr, status, error});
                        $('#financeChartLoading').hide();
                        $('#financeChartEmpty').show().text('Failed to load chart data: ' + status + ' - ' + error);
                    });
            }

            async function tryLoadRecentMonthWithData(maxBack) {
                // Start from selected month, go back one-by-one
                let cur = $month.val();
                if (!cur) return false;
                let [y, m] = cur.split('-').map(Number);
                for (let i=0;i<maxBack;i++) {
                    // step back one month
                    m -= 1;
                    if (m === 0) { m = 12; y -= 1; }
                    const probe = y + '-' + String(m).padStart(2, '0');
                    try {
                        const payload = await loadDashboardData({ month: probe });
                        if (payload && hasAnyData(payload)) {
                            $month.val(probe);
                            renderChart(payload);
                            return true;
                        }
                    } catch (e) {
                        console.warn('probe month failed', probe, e);
                    }
                }
                return false;
            }

            $month.on('change', function(){
                // If month changes, clear day filter
                $day.val('');
                refreshChart();
            });
            $day.on('change', refreshChart);
            
            console.log('About to call refreshChart() for the first time...');
            refreshChart();
            console.log('Dashboard initialization complete!');
        });
    </script>
@stop
