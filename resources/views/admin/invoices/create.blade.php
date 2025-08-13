@extends('adminlte::page')

@section('title', 'Create Invoice')

@section('content_header')
    <h1>Create Invoice</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Invoice Entry</h3>
                </div>
                
                <form action="{{ route('admin.invoices.store') }}" method="POST" id="invoiceForm">
                    @csrf
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div id="invoice-entries">
                            <div class="invoice-entry" data-entry-id="0" id="invoice-entry-0">
                                <div class="d-flex justify-content-between mb-2">
                                    <h4>Invoice Entry #1</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="customer_select_0">Select Customer</label>
                                            <div class="dropdown">
                                                <button class="btn btn-default dropdown-toggle form-control text-left" type="button" id="customerDropdown_0" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: white; border: 1px solid #ced4da; text-align: left;">
                                                    <span id="selectedCustomerText_0">-- Select Customer --</span>
                                                    <span class="caret float-right mt-1"></span>
                                                </button>
                                                <div class="dropdown-menu w-100" aria-labelledby="customerDropdown_0" style="max-height: 300px; overflow-y: auto;">
                                                    <input type="text" class="form-control mb-2 mx-2 customer-search" id="customer_search_0" placeholder="Search by mobile number" style="width: 95%;">
                                                    <div class="dropdown-divider"></div>
                                                    <div id="customerOptions_0">
                                                        @foreach($customers as $customer)
                                                            <a class="dropdown-item customer-option" href="#" data-id="{{ $customer->id }}" data-mobile="{{ $customer->mobile }}" data-name="{{ $customer->name }}">
                                                                {{ $customer->mobile }} - {{ $customer->name }}
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" id="customer_id_0" name="invoices[0][customer_id]" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="invoice_id_0">Invoice ID</label>
                                            <input type="text" class="form-control @error('invoices.0.invoice_id') is-invalid @enderror" id="invoice_id_0" name="invoices[0][invoice_id]" value="{{ old('invoices.0.invoice_id') }}" placeholder="Enter a custom invoice number" required>
                                            <small class="text-muted">Enter a custom invoice number</small>
                                            @error('invoices.0.invoice_id')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="invoice_value_0">Invoice Value</label>
                                            <input type="number" step="0.01" class="form-control @error('invoices.0.invoice_value') is-invalid @enderror" id="invoice_value_0" name="invoices[0][invoice_value]" value="{{ old('invoices.0.invoice_value') }}" placeholder="Enter invoice value" required>
                                            @error('invoices.0.invoice_value')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="customerDetails_0" style="display: none;">
                                    <div class="card card-info">
                                        <div class="card-header">
                                            <h3 class="card-title">Customer Details</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Name</label>
                                                        <p id="customerName_0" class="form-control-static"></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mobile</label>
                                                        <p id="customerMobile_0" class="form-control-static"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Email</label>
                                                        <p id="customerEmail_0" class="form-control-static"></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Address</label>
                                                        <p id="customerAddress_0" class="form-control-static"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Delivery Class</label>
                                                        <p id="customerDeliveryClass_0" class="form-control-static"></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>KAM/Staff Responsible</label>
                                                        <p id="customerKAM_0" class="form-control-static"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                            </div>
                        </div>    
                        <!-- Invoice Value field is already defined above -->
                        
                        <!-- Timestamps are automatically saved in the database -->
                    </div>
                    
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" id="submitBtn" class="btn btn-primary">Submit</button>
                                <a href="{{ route('admin.invoices.index') }}" class="btn btn-default">Cancel</a>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" id="addInvoiceBtn" class="btn btn-success">+ Add Another Invoice</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize entry counter
            let entryCounter = 0;
            
            // Fix dropdown initialization
            $('.dropdown-toggle').dropdown();
            
            // Handle customer search for the first entry
            $(document).on('keyup', '.customer-search', function(e) {
                e.stopPropagation();
                const searchText = $(this).val().toLowerCase();
                const entryId = $(this).closest('.invoice-entry').data('entry-id');
                
                $(`#customerOptions_${entryId} .customer-option`).each(function() {
                    const optionText = $(this).text().toLowerCase();
                    if (optionText.indexOf(searchText) > -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
            
            // Prevent dropdown from closing when clicking on search input
            $(document).on('click', '.customer-search', function(e) {
                e.stopPropagation();
            });
            
            // Handle customer selection
            $(document).on('click', '.customer-option', function(e) {
                e.preventDefault();
                const customerId = $(this).data('id');
                const customerMobile = $(this).data('mobile');
                const customerName = $(this).data('name');
                
                // Find the closest invoice entry
                const invoiceEntry = $(this).closest('.invoice-entry');
                const entryId = invoiceEntry.data('entry-id');
                
                // Update the button text and hidden input
                $(`#selectedCustomerText_${entryId}`).text(customerMobile + ' - ' + customerName);
                $(`#customer_id_${entryId}`).val(customerId);
                
                // Close the dropdown
                $(`#customerDropdown_${entryId}`).dropdown('toggle');
                
                // Trigger the customer selection event
                selectCustomer(customerMobile, entryId);
            });
            
            // Function to select a customer and fetch details
            function selectCustomer(customerMobile, entryId) {
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
                            $(`#customerDetails_${entryId}`).show();
                            $(`#customerName_${entryId}`).text(response.customer.name);
                            $(`#customerMobile_${entryId}`).text(response.customer.mobile);
                            $(`#customerEmail_${entryId}`).text(response.customer.email || 'N/A');
                            $(`#customerAddress_${entryId}`).text(response.customer.address || 'N/A');
                            $(`#customerDeliveryClass_${entryId}`).text(response.customer.delivery_class || 'N/A');
                            $(`#customerKAM_${entryId}`).text(response.kam || 'Not Assigned');
                        }
                    },
                    error: function() {
                        alert('Error fetching customer details');
                    }
                });
            }
            
            // Add new invoice entry
            $('#addInvoiceBtn').on('click', function() {
                console.log('Add button clicked');
                entryCounter++;
                console.log('Creating new entry with counter:', entryCounter);
                
                // Create a new entry
                const newEntryHtml = `
                    <div class="invoice-entry" data-entry-id="${entryCounter}" id="invoice-entry-${entryCounter}">
                        <div class="d-flex justify-content-between mb-2">
                            <h4>Invoice Entry #${entryCounter + 1}</h4>
                            <button type="button" class="btn btn-sm btn-danger remove-entry-btn" data-entry-id="${entryCounter}">Remove</button>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="customer_select_${entryCounter}">Select Customer</label>
                                    <div class="dropdown">
                                        <button class="btn btn-default dropdown-toggle form-control text-left" type="button" id="customerDropdown_${entryCounter}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: white; border: 1px solid #ced4da; text-align: left;">
                                            <span id="selectedCustomerText_${entryCounter}">-- Select Customer --</span>
                                            <span class="caret float-right mt-1"></span>
                                        </button>
                                        <div class="dropdown-menu w-100" aria-labelledby="customerDropdown_${entryCounter}" style="max-height: 300px; overflow-y: auto;">
                                            <input type="text" class="form-control mb-2 mx-2 customer-search" id="customer_search_${entryCounter}" placeholder="Search by mobile number" style="width: 95%;">
                                            <div class="dropdown-divider"></div>
                                            <div id="customerOptions_${entryCounter}">
                                                @foreach($customers as $customer)
                                                    <a class="dropdown-item customer-option" href="#" data-id="{{ $customer->id }}" data-mobile="{{ $customer->mobile }}" data-name="{{ $customer->name }}">
                                                        {{ $customer->mobile }} - {{ $customer->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="customer_id_${entryCounter}" name="invoices[${entryCounter}][customer_id]" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="invoice_id_${entryCounter}">Invoice ID</label>
                                    <input type="text" class="form-control" id="invoice_id_${entryCounter}" name="invoices[${entryCounter}][invoice_id]" placeholder="Enter a custom invoice number" required>
                                    <small class="text-muted">Enter a custom invoice number</small>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="invoice_value_${entryCounter}">Invoice Value</label>
                                    <input type="number" step="0.01" class="form-control" id="invoice_value_${entryCounter}" name="invoices[${entryCounter}][invoice_value]" placeholder="Enter invoice value" required>
                                </div>
                            </div>
                        </div>
                        
                        <div id="customerDetails_${entryCounter}" style="display: none;">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Customer Details</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <p id="customerName_${entryCounter}" class="form-control-static"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Mobile</label>
                                                <p id="customerMobile_${entryCounter}" class="form-control-static"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <p id="customerEmail_${entryCounter}" class="form-control-static"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Address</label>
                                                <p id="customerAddress_${entryCounter}" class="form-control-static"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Delivery Class</label>
                                                <p id="customerDeliveryClass_${entryCounter}" class="form-control-static"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>KAM/Staff Responsible</label>
                                                <p id="customerKAM_${entryCounter}" class="form-control-static"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>
                `;
                
                // Append the new entry to the container
                $('#invoice-entries').append(newEntryHtml);
                
                // Initialize the dropdown for the new entry
                $(`#customerDropdown_${entryCounter}`).dropdown();
            });
            
            // Remove invoice entry
            $(document).on('click', '.remove-entry-btn', function() {
                const entryId = $(this).data('entry-id');
                $(`#invoice-entry-${entryId}`).remove();
            });
        });
    </script>
@stop
