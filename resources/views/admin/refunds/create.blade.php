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
                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
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
                                    <input type="hidden" id="customer_id_0" name="refunds[0][customer_id]" required>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="refund_amount_0">Refund Amount</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="currencyPrefix_0">BDT</span>
                                        </div>
                                        <input type="number" class="form-control @error('refunds.0.refund_amount') is-invalid @enderror" id="refund_amount_0" name="refunds[0][refund_amount]" value="{{ old('refunds.0.refund_amount') }}" placeholder="Enter amount" step="0.01" required>
                                        @error('refund_amount')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <small class="text-muted" id="amountBdtPreview_0" style="display:block;">≈ BDT 0.00</small>
                                <small class="text-muted" id="nativeTotalPreview_0" style="display:block;">After refund: 0.00</small>
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
                                    <label for="bank_id">Bank</label>
                                    <select class="form-control @error('refunds.0.bank_id') is-invalid @enderror" id="bank_id_0" name="refunds[0][bank_id]" required>
                                        <option value="">-- Select Bank --</option>
                                        @foreach($banks as $bank)
                                            <option value="{{ $bank->id }}" data-currency="{{ optional($bank->currency)->code ?? 'BDT' }}" data-rate="{{ optional($bank->currency)->conversion_rate ?? 1 }}" data-native-balance="{{ $bank->current_balance ?? 0 }}" {{ old('refunds.0.bank_id') == $bank->id ? 'selected' : '' }}>
                                                {{ $bank->name }} - {{ $bank->account_number }} ({{ optional($bank->currency)->code ?? 'BDT' }}) Balance: {{ number_format($bank->current_balance, 2) }}
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
            
            // Form submission handling
            $('#refundForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission
                console.log('Form submission attempted');
                
                // Count the number of refund entries
                const entryCount = $('.refund-entry').length;
                console.log('Number of refund entries:', entryCount);
                
                // Create an array to hold all refund data
                let refundsData = [];
                
                // Check if all required fields are filled
                let hasEmptyRequiredFields = false;
                
                // Collect data from each refund entry
                $('.refund-entry').each(function(index) {
                    const entryId = $(this).data('entry-id');
                    console.log(`Processing entry #${index+1} (ID: ${entryId})`);
                    
                    // Get values for this entry
                    const customerId = $(`#customer_id_${entryId}`).val();
                    const bankId = $(`#bank_id_${entryId}`).val();
                    const refundAmount = $(`#refund_amount_${entryId}`).val();
                    const refundDate = $(`#refund_date_${entryId}`).val();
                    const remarks = $(`#remarks_${entryId}`).val();
                    
                    console.log(`Entry #${index+1} data:`, {
                        customer_id: customerId,
                        bank_id: bankId,
                        refund_amount: refundAmount,
                        refund_date: refundDate,
                        remarks: remarks
                    });
                    
                    // Validate required fields for this entry
                    if (!customerId) {
                        console.error(`Missing customer_id for entry #${index+1} (ID: ${entryId})`);
                        $(`#customerDropdown_${entryId}`).addClass('is-invalid');
                        hasEmptyRequiredFields = true;
                    } else {
                        $(`#customerDropdown_${entryId}`).removeClass('is-invalid');
                    }
                    
                    if (!bankId) {
                        console.error(`Missing bank_id for entry #${index+1} (ID: ${entryId})`);
                        $(`#bank_id_${entryId}`).addClass('is-invalid');
                        hasEmptyRequiredFields = true;
                    } else {
                        $(`#bank_id_${entryId}`).removeClass('is-invalid');
                    }
                    
                    if (!refundAmount) {
                        console.error(`Missing refund_amount for entry #${index+1} (ID: ${entryId})`);
                        $(`#refund_amount_${entryId}`).addClass('is-invalid');
                        hasEmptyRequiredFields = true;
                    } else {
                        $(`#refund_amount_${entryId}`).removeClass('is-invalid');
                    }
                    
                    if (!refundDate) {
                        console.error(`Missing refund_date for entry #${index+1} (ID: ${entryId})`);
                        $(`#refund_date_${entryId}`).addClass('is-invalid');
                        hasEmptyRequiredFields = true;
                    } else {
                        $(`#refund_date_${entryId}`).removeClass('is-invalid');
                    }
                    
                    // Add this entry's data to the refundsData array
                    if (customerId && bankId && refundAmount && refundDate) {
                        refundsData.push({
                            customer_id: customerId,
                            bank_id: bankId,
                            refund_amount: refundAmount,
                            refund_date: refundDate,
                            remarks: remarks
                        });
                    }
                });
                
                if (hasEmptyRequiredFields) {
                    console.error('Form has empty required fields');
                    alert('Please fill in all required fields');
                    return false;
                }
                
                console.log('Complete refunds data:', refundsData);
                
                if (refundsData.length === 0) {
                    console.error('No valid refund entries found');
                    alert('Please add at least one valid refund entry');
                    return false;
                }
                
                // Create a form data object
                const formData = new FormData(document.getElementById('refundForm'));
                
                // Remove existing refunds inputs to avoid duplicates
                $('#refundForm').find('input[name^="refunds"]').remove();
                
                // Add each refund as a hidden input
                refundsData.forEach((refund, index) => {
                    for (const [key, value] of Object.entries(refund)) {
                        $('<input>').attr({
                            type: 'hidden',
                            name: `refunds[${index}][${key}]`,
                            value: value
                        }).appendTo('#refundForm');
                    }
                });
                
                // Log the final form data for debugging
                console.log('Final form structure:');
                $('#refundForm').find('input[type="hidden"]').each(function() {
                    console.log($(this).attr('name') + ' = ' + $(this).val());
                });
                
                // Submit the form
                console.log('Form submission proceeding with refunds data');
                this.submit();
            });
            
            // Add click handler for submit button to ensure form submission
            $('#submitBtn').on('click', function(e) {
                e.preventDefault();
                console.log('Submit button clicked');
                $('#refundForm').trigger('submit');
            });
            
            // Handle customer search for all entries
            $(document).on('keyup', '.customer-search', function(e) {
                e.stopPropagation();
                const searchText = $(this).val().toLowerCase();
                
                // Get the entry ID from the closest refund-entry or from the input ID
                let entryId = 0;
                const refundEntry = $(this).closest('.refund-entry');
                if (refundEntry.length) {
                    entryId = refundEntry.data('entry-id');
                } else {
                    // Try to get entry ID from the input ID (customer_search_X)
                    const inputId = $(this).attr('id');
                    if (inputId) {
                        const idMatch = inputId.match(/customer_search_(\d+)/);
                        if (idMatch && idMatch[1]) {
                            entryId = parseInt(idMatch[1]);
                        }
                    }
                }
                
                console.log('Searching in entry ID:', entryId);
                
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
                
                // Get the entry ID from the closest refund-entry
                // If we can't find it, default to 0 (first entry)
                let entryId = 0;
                const refundEntry = $(this).closest('.refund-entry');
                if (refundEntry.length) {
                    entryId = refundEntry.data('entry-id');
                } else {
                    // If we can't find the entry ID from the refund-entry, try to get it from the customerOptions div
                    const customerOptionsDiv = $(this).closest('[id^="customerOptions_"]');
                    if (customerOptionsDiv.length) {
                        const idMatch = customerOptionsDiv.attr('id').match(/customerOptions_(\d+)/);
                        if (idMatch && idMatch[1]) {
                            entryId = parseInt(idMatch[1]);
                        }
                    }
                }
                
                console.log('Selected customer for entry ID:', entryId);
                
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
                
                // Initialize Select2 for the new dropdowns
                $(`#customer_id_${entryCounter}, #bank_id_${entryCounter}`).select2({
                    theme: 'bootstrap4',
                    placeholder: '-- Select --',
                    width: '100%'
                });
                
                // Initialize the new dropdown
                $(`#customerDropdown_${entryCounter}`).dropdown();
                
                // Add a divider between entries
                if (entryCounter > 0) {
                    $(`<hr class="my-4">`).insertBefore(`#refund-entry-${entryCounter}`);
                }

                // Initialize currency prefix and BDT preview for the new entry
                setTimeout(function(){
                    $(`#bank_id_${entryCounter}`).trigger('change');
                    $(`#refund_amount_${entryCounter}`).trigger('input');
                }, 0);
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
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="currencyPrefix_${index}">BDT</span>
                                    </div>
                                    <input type="number" class="form-control" id="refund_amount_${index}" name="refunds[${index}][refund_amount]" placeholder="Enter amount" step="0.01" required>
                                </div>
                                <small class="text-muted" id="amountBdtPreview_${index}" style="display:block;">≈ BDT 0.00</small>
                                <small class="text-muted" id="nativeTotalPreview_${index}" style="display:block;">After refund: 0.00</small>
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
                                <label for="bank_id_${index}">Bank</label>
                                <select class="form-control" id="bank_id_${index}" name="refunds[${index}][bank_id]" required>
                                    <option value="">-- Select Bank --</option>
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->id }}" data-currency="{{ optional($bank->currency)->code ?? 'BDT' }}" data-rate="{{ optional($bank->currency)->conversion_rate ?? 1 }}" data-native-balance="{{ $bank->current_balance ?? 0 }}">
                                            {{ $bank->name }} - {{ $bank->account_number }} ({{ optional($bank->currency)->code ?? 'BDT' }}) Balance: {{ number_format($bank->current_balance, 2) }}
                                        </option>
                                    @endforeach
                                </select>
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
            
            // Ensure the first entry has the correct data-entry-id attribute
            $('.refund-entry').first().attr('data-entry-id', '0');
            
            // Make sure the customer search input has the correct class
            $('#customer_search_0').addClass('customer-search');
            
            // Log the structure of the first entry for debugging
            console.log('First entry structure:', {
                entryId: $('.refund-entry').first().data('entry-id'),
                customerId: $('#customer_id_0').val(),
                customerSearch: $('#customer_search_0').length,
                customerOptions: $('#customerOptions_0').length,
                customerDropdown: $('#customerDropdown_0').length
            });
            
            // No need to update these IDs as they're already set correctly in the HTML
            // $('#customerOptions_0');
            // $('#customerDropdown_0');
            // $('#selectedCustomerText_0');
            // $('#customerDetails_0');
            // $('#customerName_0');
            // $('#customerMobile_0');
            // $('#customerEmail_0');
            // $('#customerAddress_0');
            // $('#customerDeliveryClass_0');
            // $('#customerKAM_0');

            // Helpers to manage currency prefix and BDT preview
            function getIndexFromId(id, prefix) {
                const m = id.match(new RegExp('^' + prefix + '_(\\d+)$'));
                return m && m[1] ? parseInt(m[1], 10) : 0;
            }

            function updateCurrencyUI(index) {
                const $bank = $(`#bank_id_${index}`);
                const code = ($bank.find('option:selected').data('currency') || 'BDT').toString().toUpperCase();
                const rate = parseFloat($bank.find('option:selected').data('rate') || 1);
                const nativeBal = parseFloat($bank.find('option:selected').data('native-balance') || 0);
                $(`#currencyPrefix_${index}`).text(code);
                const amount = parseFloat($(`#refund_amount_${index}`).val() || 0);
                const bdt = code === 'BDT' ? amount : amount * (isFinite(rate) && rate > 0 ? rate : 1);
                $(`#amountBdtPreview_${index}`).text(`≈ BDT ${bdt.toFixed(2)}`);
                const newNative = (nativeBal || 0) - (amount || 0);
                $(`#nativeTotalPreview_${index}`).text(`After refund: ${code} ${newNative.toFixed(2)}`);
            }

            // React to bank change
            $(document).on('change', 'select[id^="bank_id_"]', function() {
                const idx = getIndexFromId(this.id, 'bank_id');
                updateCurrencyUI(idx);
            });

            // React to amount typing
            $(document).on('input', 'input[id^="refund_amount_"]', function() {
                const idx = getIndexFromId(this.id, 'refund_amount');
                updateCurrencyUI(idx);
            });

            // Initialize for first entry on page load
            setTimeout(function(){
                $('#bank_id_0').trigger('change');
                $('#refund_amount_0').trigger('input');
            }, 0);
        });
    </script>
@stop
