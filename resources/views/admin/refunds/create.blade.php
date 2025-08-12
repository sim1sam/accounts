@extends('adminlte::page')

@section('title', 'Create Refund')

@section('content_header')
    <h1>Create Refund</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Refund Entry</h3>
                </div>
                
                <form action="{{ route('admin.refunds.store') }}" method="POST" id="refundForm">
                    @csrf
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        <div id="refund-entries">
                            <!-- Refund entries will be added here -->
                            <div class="refund-entry" data-entry-id="0">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="customer_select">Select Customer</label>
                                    <div class="dropdown">
                                        <button class="btn btn-default dropdown-toggle form-control text-left" type="button" id="customerDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: white; border: 1px solid #ced4da; text-align: left;">
                                            <span id="selectedCustomerText">-- Select Customer --</span>
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
                                    <input type="hidden" id="customer_id_0" name="refunds[0][customer_id]" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="refund_amount">Refund Amount</label>
                                    <input type="number" class="form-control @error('refunds.0.refund_amount') is-invalid @enderror" id="refund_amount_0" name="refunds[0][refund_amount]" value="{{ old('refunds.0.refund_amount') }}" placeholder="Enter refund amount" step="0.01" required>
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
                                    <input type="date" class="form-control @error('refunds.0.refund_date') is-invalid @enderror" id="refund_date_0" name="refunds[0][refund_date]" value="{{ old('refunds.0.refund_date', date('Y-m-d')) }}" required>
                                    @error('refunds.0.refund_date')
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
                                    <label for="account">Account</label>
                                    <input type="text" class="form-control @error('refunds.0.account') is-invalid @enderror" id="account_0" name="refunds[0][account]" value="{{ old('refunds.0.account') }}" placeholder="Enter account">
                                    @error('account')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="remarks">Remarks</label>
                                    <textarea class="form-control @error('refunds.0.remarks') is-invalid @enderror" id="remarks_0" name="refunds[0][remarks]" rows="3" placeholder="Enter remarks">{{ old('refunds.0.remarks') }}</textarea>
                                    @error('remarks')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                                <!-- Customer details section for first entry -->
                                <div id="customerDetails_0" style="display: none;">
                                    <div class="card card-info">
                                        <div class="card-header">
                                            <h3 class="card-title">Customer Details</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Name:</label>
                                                        <p id="customerName_0" class="form-control-static"></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Mobile:</label>
                                                        <p id="customerMobile_0" class="form-control-static"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Email:</label>
                                                        <p id="customerEmail_0" class="form-control-static"></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Address:</label>
                                                        <p id="customerAddress_0" class="form-control-static"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Delivery Class:</label>
                                                        <p id="customerDeliveryClass_0" class="form-control-static"></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>KAM/Staff:</label>
                                                        <p id="customerKAM_0" class="form-control-static"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- End of refund-entry -->
                        </div> <!-- End of refund-entries -->
                    </div>
                    
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
                                <a href="{{ route('admin.refunds.index') }}" class="btn btn-default">Cancel</a>
                            </div>
                            <button type="button" class="btn btn-success" id="addRefundBtn">
                                <i class="fas fa-plus"></i> Add Another Refund
                            </button>
                        </div>
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
            // Initialize entry counter
            let entryCounter = 0;
            
            // Fix dropdown initialization
            $('.dropdown-toggle').dropdown();
            
            // Handle customer search for initial entry
            $(document).on('keyup', '.customer-search', function(e) {
                e.stopPropagation();
                const searchText = $(this).val().toLowerCase();
                const entryId = $(this).closest('.refund-entry').data('entry-id');
                
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
                const entryId = $(this).closest('.refund-entry').data('entry-id');
                
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
                            // Display customer details in the specific entry
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
            
            // Add new refund entry
            $('#addRefundBtn').on('click', function() {
                entryCounter++;
                
                // Clone the template
                const newEntry = createNewRefundEntry(entryCounter);
                
                // Append to the container
                $('#refund-entries').append(newEntry);
                
                // Initialize the new dropdown
                $(`#customerDropdown_${entryCounter}`).dropdown();
                
                // Add a divider between entries
                if (entryCounter > 0) {
                    $(`<hr class="my-4">`).insertBefore(`#refund-entry-${entryCounter}`);
                }
            });
            
            // Remove refund entry
            $(document).on('click', '.remove-entry-btn', function() {
                const entryId = $(this).data('entry-id');
                $(`#refund-entry-${entryId}`).prev('hr').remove(); // Remove divider
                $(`#refund-entry-${entryId}`).remove(); // Remove entry
            });
            
            // Function to create a new refund entry
            function createNewRefundEntry(index) {
                return `
                <div class="refund-entry" id="refund-entry-${index}" data-entry-id="${index}">
                    <div class="d-flex justify-content-between mb-2">
                        <h4>Refund Entry #${index + 1}</h4>
                        <button type="button" class="btn btn-danger remove-entry-btn" data-entry-id="${index}">
                            <i class="fas fa-times"></i> Remove
                        </button>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="customer_select_${index}">Select Customer</label>
                                <div class="dropdown">
                                    <button class="btn btn-default dropdown-toggle form-control text-left" type="button" id="customerDropdown_${index}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: white; border: 1px solid #ced4da; text-align: left;">
                                        <span id="selectedCustomerText_${index}">-- Select Customer --</span>
                                        <span class="caret float-right mt-1"></span>
                                    </button>
                                    <div class="dropdown-menu w-100" aria-labelledby="customerDropdown_${index}" style="max-height: 300px; overflow-y: auto;">
                                        <input type="text" class="form-control mb-2 mx-2 customer-search" id="customer_search_${index}" placeholder="Search by mobile number" style="width: 95%;">
                                        <div class="dropdown-divider"></div>
                                        <div id="customerOptions_${index}">
                                            @foreach($customers as $customer)
                                                <a class="dropdown-item customer-option" href="#" data-id="{{ $customer->id }}" data-mobile="{{ $customer->mobile }}" data-name="{{ $customer->name }}">
                                                    {{ $customer->mobile }} - {{ $customer->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="customer_id_${index}" name="refunds[${index}][customer_id]" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="refund_amount_${index}">Refund Amount</label>
                                <input type="number" class="form-control" id="refund_amount_${index}" name="refunds[${index}][refund_amount]" placeholder="Enter refund amount" step="0.01" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="refund_date_${index}">Refund Date</label>
                                <input type="date" class="form-control" id="refund_date_${index}" name="refunds[${index}][refund_date]" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="account_${index}">Account</label>
                                <input type="text" class="form-control" id="account_${index}" name="refunds[${index}][account]" placeholder="Enter account">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="remarks_${index}">Remarks</label>
                                <textarea class="form-control" id="remarks_${index}" name="refunds[${index}][remarks]" rows="3" placeholder="Enter remarks"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div id="customerDetails_${index}" style="display: none;">
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Customer Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name:</label>
                                            <p id="customerName_${index}" class="form-control-static"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Mobile:</label>
                                            <p id="customerMobile_${index}" class="form-control-static"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email:</label>
                                            <p id="customerEmail_${index}" class="form-control-static"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Address:</label>
                                            <p id="customerAddress_${index}" class="form-control-static"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Delivery Class:</label>
                                            <p id="customerDeliveryClass_${index}" class="form-control-static"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>KAM/Staff:</label>
                                            <p id="customerKAM_${index}" class="form-control-static"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
            }
            
            // Update first entry to match the new format
            // Add entry title and ID
            $('.refund-entry').first().attr('id', 'refund-entry-0');
            $('.refund-entry').first().prepend(`
                <div class="d-flex justify-content-between mb-2">
                    <h4>Refund Entry #1</h4>
                </div>
            `);
            
            // Update IDs for the first entry
            $('#customer_search').attr('id', 'customer_search_0').addClass('customer-search');
            $('#customerOptions').attr('id', 'customerOptions_0');
            $('#customerDropdown').attr('id', 'customerDropdown_0');
            $('#selectedCustomerText').attr('id', 'selectedCustomerText_0');
            $('#customerDetails').attr('id', 'customerDetails_0');
            $('#customerName').attr('id', 'customerName_0');
            $('#customerMobile').attr('id', 'customerMobile_0');
            $('#customerEmail').attr('id', 'customerEmail_0');
            $('#customerAddress').attr('id', 'customerAddress_0');
            $('#customerDeliveryClass').attr('id', 'customerDeliveryClass_0');
            $('#customerKAM').attr('id', 'customerKAM_0');
        });
    </script>
@stop
