@extends('adminlte::page')

@section('title', 'Edit Delivery')

@section('content_header')
    <h1>Edit Delivery</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.deliveries.update', $delivery) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="customer_select">Select Customer by Mobile</label>
                            <select id="customer_select" class="form-control select2" style="width: 100%;">
                                <option value="">Search by mobile number or name</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" data-mobile="{{ $customer->mobile }}" 
                                        {{ $delivery->customer_id == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->mobile }} - {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="customer_id" id="customer_id" value="{{ $delivery->customer_id }}">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Customer Name</label>
                            <input type="text" class="form-control" id="customer_name" value="{{ $delivery->customer->name }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Customer Mobile</label>
                            <input type="text" class="form-control" id="customer_mobile" value="{{ $delivery->customer->mobile }}" readonly>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Customer Address</label>
                            <input type="text" class="form-control" id="customer_address" value="{{ $delivery->customer->address }}" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="delivery_value">Delivery Value</label>
                            <input type="number" name="delivery_value" id="delivery_value" class="form-control @error('delivery_value') is-invalid @enderror" step="0.01" required value="{{ $delivery->delivery_value }}">
                            @error('delivery_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="delivery_date">Delivery Date</label>
                            <input type="date" name="delivery_date" id="delivery_date" class="form-control @error('delivery_date') is-invalid @enderror" required value="{{ $delivery->delivery_date->format('Y-m-d') }}">
                            @error('delivery_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="shipment_no">Shipment No</label>
                            <input type="text" name="shipment_no" id="shipment_no" class="form-control @error('shipment_no') is-invalid @enderror" required value="{{ $delivery->shipment_no }}">
                            @error('shipment_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update Delivery</button>
                    <a href="{{ route('admin.deliveries.index') }}" class="btn btn-default">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Search by mobile number or name",
                allowClear: true
            });
            
            $('#customer_select').on('change', function() {
                var customerId = $(this).val();
                if (customerId) {
                    var selectedOption = $(this).find('option:selected');
                    var mobile = selectedOption.data('mobile');
                    
                    // Set the hidden customer_id field
                    $('#customer_id').val(customerId);
                    
                    // Fetch customer details
                    $.ajax({
                        url: "{{ route('admin.find.customer') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            mobile: mobile
                        },
                        success: function(response) {
                            if (response.success) {
                                var customer = response.customer;
                                $('#customer_name').val(customer.name);
                                $('#customer_mobile').val(customer.mobile);
                                $('#customer_address').val(customer.address);
                            }
                        },
                        error: function(xhr) {
                            console.log('Error fetching customer details');
                        }
                    });
                } else {
                    // Clear customer details
                    $('#customer_id').val('');
                    $('#customer_name').val('');
                    $('#customer_mobile').val('');
                    $('#customer_address').val('');
                }
            });
        });
    </script>
@stop
