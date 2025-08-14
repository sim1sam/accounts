@extends('adminlte::page')

@section('title', 'Edit Refund')

@section('content_header')
    <h1>Edit Refund</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Refund Entry</h3>
                </div>
                
                <form action="{{ route('admin.refunds.update', $refund->id) }}" method="POST" id="refundForm">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="customer_select">Select Customer</label>
                                    <div class="dropdown">
                                        <button class="btn btn-default dropdown-toggle form-control text-left" type="button" id="customerDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: white; border: 1px solid #ced4da; text-align: left;">
                                            <span id="selectedCustomerText">{{ $refund->customer->mobile }} - {{ $refund->customer->name }}</span>
                                            <span class="caret float-right mt-1"></span>
                                        </button>
                                        <div class="dropdown-menu w-100" aria-labelledby="customerDropdown" style="max-height: 300px; overflow-y: auto;">
                                            <input type="text" class="form-control mb-2 mx-2" id="customer_search" placeholder="Search by mobile number" style="width: 95%;">
                                            <div class="dropdown-divider"></div>
                                            <div id="customerOptions">
                                                @foreach($customers as $customer)
                                                    <a class="dropdown-item customer-option" href="#" data-id="{{ $customer->id }}" data-mobile="{{ $customer->mobile }}" data-name="{{ $customer->name }}">
                                                        {{ $customer->mobile }} - {{ $customer->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="customer_id" name="customer_id" value="{{ $refund->customer_id }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="refund_amount">Refund Amount</label>
                                    <input type="number" class="form-control @error('refund_amount') is-invalid @enderror" id="refund_amount" name="refund_amount" value="{{ old('refund_amount', $refund->refund_amount) }}" placeholder="Enter refund amount" step="0.01" required>
                                    @error('refund_amount')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="refund_date">Refund Date</label>
                                    <input type="date" class="form-control @error('refund_date') is-invalid @enderror" id="refund_date" name="refund_date" value="{{ old('refund_date', $refund->refund_date->format('Y-m-d')) }}" required>
                                    @error('refund_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bank_id">Bank</label>
                                    <select class="form-control @error('bank_id') is-invalid @enderror" id="bank_id" name="bank_id" required>
                                        <option value="">-- Select Bank --</option>
                                        @foreach($banks as $bank)
                                            <option value="{{ $bank->id }}" {{ old('bank_id', $refund->bank_id) == $bank->id ? 'selected' : '' }}>
                                                {{ $bank->name }} - {{ $bank->account_number }} (Balance: {{ number_format($bank->current_balance, 2) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('bank_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="remarks">Remarks</label>
                                    <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks" rows="3" placeholder="Enter remarks">{{ old('remarks', $refund->remarks) }}</textarea>
                                    @error('remarks')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div id="customerDetails">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Customer Details</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Name:</label>
                                                <p id="customerName" class="form-control-static">{{ $refund->customer->name }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Mobile:</label>
                                                <p id="customerMobile" class="form-control-static">{{ $refund->customer->mobile }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Email:</label>
                                                <p id="customerEmail" class="form-control-static">{{ $refund->customer->email ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Address:</label>
                                                <p id="customerAddress" class="form-control-static">{{ $refund->customer->address ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Delivery Class:</label>
                                                <p id="customerDeliveryClass" class="form-control-static">{{ $refund->customer->delivery_class }}</p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>KAM/Staff:</label>
                                                <p id="customerKAM" class="form-control-static">{{ $refund->customer->staff->name ?? 'Not Assigned' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" id="submitBtn">Update</button>
                        <a href="{{ route('admin.refunds.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .select2-container .select2-selection--single {
            height: 38px;
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px;
            padding-left: 0;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            padding: 8px;
            border-radius: 4px;
        }
        .select2-dropdown {
            border: 1px solid #ced4da;
        }
    </style>
@stop

@section('js')
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
