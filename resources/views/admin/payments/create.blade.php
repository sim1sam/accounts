@extends('adminlte::page')

@section('title', 'Create Payment')

@section('content_header')
    <h1>Create Payment</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Payment Details</h3>
                </div>
                
                <form action="{{ route('admin.payments.store') }}" method="POST">
                    <div class="card-body">
                        @csrf
                        
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="customer_id">Customer</label>
                                    <div class="dropdown">
                                        <button class="btn btn-default dropdown-toggle form-control text-left" type="button" id="customerDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span id="selectedCustomerText">Select Customer</span>
                                        </button>
                                        <div class="dropdown-menu w-100" aria-labelledby="customerDropdown">
                                            <input type="text" class="form-control" id="customer_search" placeholder="Search by mobile or name">
                                            <div class="dropdown-divider"></div>
                                            @foreach($customers as $customer)
                                                <a class="dropdown-item customer-option" href="#" data-id="{{ $customer->id }}" data-mobile="{{ $customer->mobile }}" data-name="{{ $customer->name }}">
                                                    {{ $customer->mobile }} - {{ $customer->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                    <input type="hidden" id="customer_id" name="customer_id" required>
                                    @error('customer_id')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="amount">Amount</label>
                                    <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" placeholder="Enter amount" required>
                                    @error('amount')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="payment_date">Payment Date</label>
                                    <input type="date" class="form-control @error('payment_date') is-invalid @enderror" id="payment_date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                                    @error('payment_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="account_no">Account No</label>
                                    <input type="text" class="form-control @error('account_no') is-invalid @enderror" id="account_no" name="account_no" value="{{ old('account_no') }}" placeholder="Enter account number" required>
                                    @error('account_no')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div id="customerDetails" style="display: none;">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Customer Details</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Customer Name</label>
                                                <p id="customerName" class="form-control-static"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Mobile</label>
                                                <p id="customerMobile" class="form-control-static"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <p id="customerEmail" class="form-control-static"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Address</label>
                                                <p id="customerAddress" class="form-control-static"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Delivery Class</label>
                                                <p id="customerDeliveryClass" class="form-control-static"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>KAM/Staff Responsible</label>
                                                <p id="customerKAM" class="form-control-static"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Timestamps are automatically saved in the database -->
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Create Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .dropdown-toggle::after {
            float: right;
            margin-top: 10px;
        }
        #customer_search {
            margin: 5px;
            width: calc(100% - 10px);
        }
        .customer-option {
            white-space: normal;
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Fix dropdown initialization
            $('.dropdown-toggle').dropdown();
            
            // Handle customer search
            $('#customer_search').on('keyup', function(e) {
                e.stopPropagation();
                const searchText = $(this).val().toLowerCase();
                $('.customer-option').each(function() {
                    const optionText = $(this).text().toLowerCase();
                    if (optionText.indexOf(searchText) > -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
            
            // Prevent dropdown from closing when clicking on search input
            $('#customer_search').on('click', function(e) {
                e.stopPropagation();
            });
            
            // Handle customer selection
            $(document).on('click', '.customer-option', function(e) {
                e.preventDefault();
                const customerId = $(this).data('id');
                const customerMobile = $(this).data('mobile');
                const customerName = $(this).data('name');
                
                // Update the button text and hidden input
                $('#selectedCustomerText').text(customerMobile + ' - ' + customerName);
                $('#customer_id').val(customerId);
                
                // Close the dropdown
                $('#customerDropdown').dropdown('toggle');
                
                // Trigger the customer selection event
                selectCustomer(customerMobile);
            });
            
            // Function to select a customer and fetch details
            function selectCustomer(customerMobile) {
                // Get full customer details via AJAX
                $.ajax({
                    url: "{{ route('admin.find.customer') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        mobile: customerMobile
                    },
                    success: function(response) {
                        if (response.success) {
                            // Display customer details
                            $('#customerDetails').show();
                            $('#customerName').text(response.customer.name);
                            $('#customerMobile').text(response.customer.mobile);
                            $('#customerEmail').text(response.customer.email || 'N/A');
                            $('#customerAddress').text(response.customer.address || 'N/A');
                            $('#customerDeliveryClass').text(response.customer.delivery_class || 'N/A');
                            $('#customerKAM').text(response.kam || 'Not Assigned');
                            
                            // Enable submit button
                            $('#submitBtn').prop('disabled', false);
                        }
                    },
                    error: function() {
                        alert('Error fetching customer details');
                    }
                });
            }
        });
    </script>
@stop
