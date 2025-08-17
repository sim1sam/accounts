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
                        
                        <div id="payment-entries">
                            <div class="payment-entry" data-entry-id="0" id="payment-entry-0">
                                <div class="d-flex justify-content-between mb-2">
                                    <h4>Payment Entry #1</h4>
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
                                            <input type="hidden" id="customer_id_0" name="payments[0][customer_id]" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="payment_date_0">Payment Date</label>
                                            <input type="date" class="form-control" id="payment_date_0" name="payments[0][payment_date]" value="{{ date('Y-m-d') }}" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="amount_0">Amount</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="currencyPrefix_0">BDT</span>
                                                </div>
                                                <input type="number" step="0.01" class="form-control" id="amount_0" name="payments[0][amount]" placeholder="Enter amount" required>
                                            </div>
                                            <small class="text-muted" id="amountBdtPreview_0" style="display:block;">≈ BDT 0.00</small>
                                            <small class="text-muted" id="nativeTotalPreview_0" style="display:block;">After payment: 0.00</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bank_id_0">Bank</label>
                                            <select class="form-control" id="bank_id_0" name="payments[0][bank_id]" required>
                                                <option value="">Select Bank</option>
                                                @foreach($banks as $bank)
                                                    <option value="{{ $bank->id }}" data-currency="{{ optional($bank->currency)->code ?? 'BDT' }}" data-rate="{{ optional($bank->currency)->conversion_rate ?? 1 }}" data-native-balance="{{ $bank->current_balance ?? 0 }}">
                                                        {{ $bank->name }} - {{ $bank->account_name }} ({{ $bank->account_number }}) ({{ optional($bank->currency)->code ?? 'BDT' }}) Balance: {{ number_format($bank->current_balance, 2) }}
                                                    </option>
                                                @endforeach
                                            </select>
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
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-default">Cancel</a>
                        <button type="button" class="btn btn-success" id="addPaymentBtn">+ Add Another Payment</button>
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
            // Initialize entry counter
            let entryCounter = 0;
            
            // Fix dropdown initialization
            $('.dropdown-toggle').dropdown();
            
            // Handle customer search for the first entry
            $(document).on('keyup', '.customer-search', function(e) {
                e.stopPropagation();
                const searchText = $(this).val().toLowerCase();
                const entryId = $(this).closest('.payment-entry').data('entry-id');
                
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
                
                // Find the closest payment entry
                const paymentEntry = $(this).closest('.payment-entry');
                const entryId = paymentEntry.data('entry-id');
                
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
            
            // Add new payment entry
            $('#addPaymentBtn').on('click', function() {
                console.log('Add button clicked');
                entryCounter++;
                console.log('Creating new entry with counter:', entryCounter);
                
                // Create a new entry
                const newEntryHtml = `
                    <div class="payment-entry" data-entry-id="${entryCounter}" id="payment-entry-${entryCounter}">
                        <div class="d-flex justify-content-between mb-2">
                            <h4>Payment Entry #${entryCounter + 1}</h4>
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
                                    <input type="hidden" id="customer_id_${entryCounter}" name="payments[${entryCounter}][customer_id]" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_date_${entryCounter}">Payment Date</label>
                                    <input type="date" class="form-control" id="payment_date_${entryCounter}" name="payments[${entryCounter}][payment_date]" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="amount_${entryCounter}">Amount</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="currencyPrefix_${entryCounter}">BDT</span>
                                        </div>
                                        <input type="number" step="0.01" class="form-control" id="amount_${entryCounter}" name="payments[${entryCounter}][amount]" placeholder="Enter amount" required>
                                    </div>
                                    <small class="text-muted" id="amountBdtPreview_${entryCounter}" style="display:block;">≈ BDT 0.00</small>
                                    <small class="text-muted" id="nativeTotalPreview_${entryCounter}" style="display:block;">After payment: 0.00</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bank_id_${entryCounter}">Bank</label>
                                    <select class="form-control" id="bank_id_${entryCounter}" name="payments[${entryCounter}][bank_id]" required>
                                        <option value="">Select Bank</option>
                                        @foreach($banks as $bank)
                                            <option value="{{ $bank->id }}" data-currency="{{ optional($bank->currency)->code ?? 'BDT' }}" data-rate="{{ optional($bank->currency)->conversion_rate ?? 1 }}" data-native-balance="{{ $bank->current_balance ?? 0 }}">
                                                {{ $bank->name }} - {{ $bank->account_name }} ({{ $bank->account_number }}) ({{ optional($bank->currency)->code ?? 'BDT' }}) Balance: {{ number_format($bank->current_balance, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
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
                $('#payment-entries').append(newEntryHtml);
                
                // Initialize the dropdown for the new entry
                $(`#customerDropdown_${entryCounter}`).dropdown();

                // Initialize currency UI for the new entry
                updateCurrencyUI(entryCounter);
            });
            
            // Remove payment entry
            $(document).on('click', '.remove-entry-btn', function() {
                const entryId = $(this).data('entry-id');
                $(`#payment-entry-${entryId}`).remove();
            });

            // ---- Currency helpers for payment entries ----
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
                const amount = parseFloat($(`#amount_${index}`).val() || 0);
                const bdt = code === 'BDT' ? amount : amount * (isFinite(rate) && rate > 0 ? rate : 1);
                $(`#amountBdtPreview_${index}`).text(`≈ BDT ${bdt.toFixed(2)}`);
                const newNative = (nativeBal || 0) + (amount || 0); // Payment increases native balance
                $(`#nativeTotalPreview_${index}`).text(`After payment: ${code} ${newNative.toFixed(2)}`);
            }

            // React to bank change
            $(document).on('change', 'select[id^="bank_id_"]', function() {
                const idx = getIndexFromId(this.id, 'bank_id');
                updateCurrencyUI(idx);
            });

            // React to amount typing
            $(document).on('input', 'input[id^="amount_"]', function() {
                const idx = getIndexFromId(this.id, 'amount');
                updateCurrencyUI(idx);
            });

            // Initialize first entry
            updateCurrencyUI(0);
        });
    </script>
@stop
