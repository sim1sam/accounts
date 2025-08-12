@extends('adminlte::page')

@section('title', 'Create Delivery')

@section('content_header')
    <h1>Create Delivery</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Delivery Entry</h3>
                </div>
                
                <form action="{{ route('admin.deliveries.store') }}" method="POST" id="deliveryForm">
                    @csrf
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        <div id="delivery-entries">
                            <!-- Delivery Entry #1 -->
                            <div class="delivery-entry" data-entry-id="0" id="delivery-entry-0">
                                <div class="d-flex justify-content-between mb-2">
                                    <h4>Delivery Entry #1</h4>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
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
                                            <input type="hidden" id="customer_id_0" name="deliveries[0][customer_id]" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="delivery_date_0">Delivery Date</label>
                                            <input type="date" class="form-control" id="delivery_date_0" name="deliveries[0][delivery_date]" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="shipment_no_0">Shipment No</label>
                                            <input type="text" class="form-control" id="shipment_no_0" name="deliveries[0][shipment_no]" placeholder="Enter shipment number" required>
                                            <small class="text-muted">Enter shipment tracking number</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="delivery_value_0">Delivery Value</label>
                                            <input type="number" class="form-control" id="delivery_value_0" name="deliveries[0][delivery_value]" placeholder="Enter delivery value" step="0.01" required>
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
                            </div>
                    
                    </div> <!-- End of delivery-entries div -->
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
                        <a href="{{ route('admin.deliveries.index') }}" class="btn btn-default">Cancel</a>
                        <button type="button" class="btn btn-success" id="addDeliveryBtn">+ Add Another Delivery</button>
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
            let entryCounter = 0; // Start with entry 0
            
            // Initialize customer search functionality
            $(document).on('input', '.customer-search', function() {
                const searchTerm = $(this).val().toLowerCase();
                const entryId = $(this).attr('id').split('_')[2]; // Extract entry ID from search input ID
                
                $(`#customerOptions_${entryId} .customer-option`).each(function() {
                    const customerText = $(this).text().toLowerCase();
                    if (customerText.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
            
            // Handle customer selection
            $(document).on('click', '.customer-option', function(e) {
                e.preventDefault();
                const customerId = $(this).data('id');
                const customerMobile = $(this).data('mobile');
                const customerName = $(this).data('name');
                
                // Find the closest delivery entry and get its ID
                const deliveryEntry = $(this).closest('.delivery-entry');
                const entryId = deliveryEntry.data('entry-id');
                console.log('Selected customer for entry ID:', entryId);
                
                // Update the button text and hidden input
                $(`#selectedCustomerText_${entryId}`).text(customerMobile + ' - ' + customerName);
                $(`#customer_id_${entryId}`).val(customerId);
                
                // Close the dropdown
                $(`#customerDropdown_${entryId}`).dropdown('toggle');
                
                // Fetch customer details via AJAX
                $.ajax({
                    url: "{{ route('admin.find.customer') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        mobile: customerMobile
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log('Customer details received for entry:', entryId, response);
                            
                            // Display customer details
                            $(`#customerDetails_${entryId}`).show();
                            $(`#customerName_${entryId}`).text(response.customer.name);
                            $(`#customerMobile_${entryId}`).text(response.customer.mobile);
                            $(`#customerEmail_${entryId}`).text(response.customer.email || 'N/A');
                            $(`#customerAddress_${entryId}`).text(response.customer.address || 'N/A');
                            $(`#customerDeliveryClass_${entryId}`).text(response.customer.delivery_class || 'N/A');
                            $(`#customerKAM_${entryId}`).text(response.kam || 'Not Assigned');
                            
                            // Enable submit button
                            $('#submitBtn').prop('disabled', false);
                        } else {
                            console.error('Customer details not found in response');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching customer details:', error);
                        alert('Error fetching customer details');
                    }
                });
            });
            
            // Add new delivery entry
            $('#addDeliveryBtn').on('click', function() {
                console.log('Add button clicked');
                entryCounter++;
                console.log('Creating new entry with counter:', entryCounter);
                
                // Create a new entry using the layout from the screenshot
                const newEntryHtml = `
                    <div class="delivery-entry" data-entry-id="${entryCounter}" id="delivery-entry-${entryCounter}">
                        <div class="d-flex justify-content-between mb-2">
                            <h4>Delivery Entry #${entryCounter + 1}</h4>
                            <button type="button" class="btn btn-sm btn-danger remove-entry-btn" data-entry-id="${entryCounter}">Remove</button>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
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
                                    <input type="hidden" id="customer_id_${entryCounter}" name="deliveries[${entryCounter}][customer_id]" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="delivery_date_${entryCounter}">Delivery Date</label>
                                    <input type="date" class="form-control" id="delivery_date_${entryCounter}" name="deliveries[${entryCounter}][delivery_date]" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipment_no_${entryCounter}">Shipment No</label>
                                    <input type="text" class="form-control" id="shipment_no_${entryCounter}" name="deliveries[${entryCounter}][shipment_no]" placeholder="Enter shipment number" required>
                                    <small class="text-muted">Enter shipment tracking number</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="delivery_value_${entryCounter}">Delivery Value</label>
                                    <input type="number" class="form-control" id="delivery_value_${entryCounter}" name="deliveries[${entryCounter}][delivery_value]" placeholder="Enter delivery value" step="0.01" required>
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
                    </div>
                `;
                
                $('#delivery-entries').append(newEntryHtml);
                console.log('New entry added to DOM with ID:', entryCounter);
                
                // Initialize the new dropdown
                $('.dropdown-toggle').dropdown();
                console.log('Dropdown initialized');
            });
            
            // Remove delivery entry
            $(document).on('click', '.remove-entry-btn', function() {
                const entryId = $(this).data('entry-id');
                $(`#delivery-entry-${entryId}`).remove();
                
                // Update entry numbers
                $('.delivery-entry').each(function(index) {
                    $(this).find('h4').text(`Delivery Entry #${index + 1}`);
                });
            });
            
            // Function to create a new delivery entry HTML
            function createNewEntryHtml(entryId) {
                console.log('Creating HTML for entry ID:', entryId);
                return `
                    <div class="delivery-entry card card-body mb-3" id="deliveryEntry_${entryId}">
                        <div class="text-right mb-2">
                            <button type="button" class="btn btn-sm btn-danger remove-entry" data-entry-id="${entryId}">Remove</button>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="customer_select_${entryId}">Select Customer</label>
                                    <div class="dropdown">
                                        <button class="btn btn-default dropdown-toggle form-control text-left" type="button" id="customerDropdown_${entryId}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: white; border: 1px solid #ced4da; text-align: left;">
                                            <span id="selectedCustomerText_${entryId}">-- Select Customer --</span>
                                            <span class="caret float-right mt-1"></span>
                                        </button>
                                        <div class="dropdown-menu w-100" aria-labelledby="customerDropdown_${entryId}" id="customerDropdownMenu_${entryId}" style="max-height: 300px; overflow-y: auto;">
                                            <input type="text" class="form-control mb-2 mx-2 customer-search" id="customer_search_${entryId}" placeholder="Search by mobile number" style="width: 95%;">
                                            <div class="dropdown-divider"></div>
                                            <div id="customerOptions_${entryId}">
                                                @foreach($customers as $customer)
                                                    <a class="dropdown-item customer-option" href="#" data-id="{{ $customer->id }}" data-mobile="{{ $customer->mobile }}" data-name="{{ $customer->name }}">
                                                        {{ $customer->mobile }} - {{ $customer->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="deliveries[${entryId}][customer_id]" id="customer_id_${entryId}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="shipment_no_${entryId}">Shipment No</label>
                                    <input type="text" class="form-control" id="shipment_no_${entryId}" name="deliveries[${entryId}][shipment_no]" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="delivery_value_${entryId}">Delivery Value</label>
                                    <input type="number" step="0.01" class="form-control" id="delivery_value_${entryId}" name="deliveries[${entryId}][delivery_value]" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="delivery_date_${entryId}">Delivery Date</label>
                                    <input type="date" class="form-control" id="delivery_date_${entryId}" name="deliveries[${entryId}][delivery_date]" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Customer Details Section -->
                        <div class="customer-details mt-3" id="customerDetails_${entryId}" style="display: none;">
                            <h5>Customer Details</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Name:</strong> <span id="customerName_${entryId}"></span></p>
                                    <p><strong>Mobile:</strong> <span id="customerMobile_${entryId}"></span></p>
                                    <p><strong>Email:</strong> <span id="customerEmail_${entryId}"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Address:</strong> <span id="customerAddress_${entryId}"></span></p>
                                    <p><strong>Delivery Class:</strong> <span id="customerDeliveryClass_${entryId}"></span></p>
                                    <p><strong>KAM/Staff:</strong> <span id="customerKAM_${entryId}"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
        });
    </script>
@stop
