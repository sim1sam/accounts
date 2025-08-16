@extends('adminlte::page')

@section('title', 'Create New Account')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Create New Account</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.accounts.index') }}">Accounts</a></li>
                <li class="breadcrumb-item active">Create</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Account Details</h3>
                </div>
                <!-- /.card-header -->
                <form action="{{ route('admin.accounts.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="name">Account Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="initial_amount">Initial Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" class="form-control @error('initial_amount') is-invalid @enderror" id="initial_amount" name="initial_amount" value="{{ old('initial_amount', 0) }}" required>
                            @error('initial_amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="currency_id">Currency <span class="text-danger">*</span></label>
                            <select class="form-control @error('currency_id') is-invalid @enderror" id="currency_id" name="currency_id" required>
                                <option value="">Select Currency</option>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}" data-symbol="{{ $currency->symbol }}" data-code="{{ $currency->code }}" data-rate="{{ $currency->conversion_rate }}">
                                        {{ $currency->name }} ({{ $currency->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('currency_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="row" id="amount-display">
                            <div class="col-md-6" id="currency-amount-section">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fas fa-money-bill"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Amount in Selected Currency</span>
                                        <span class="info-box-number" id="currency-amount-display">0.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" id="bdt-amount-section">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="fas fa-exchange-alt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Amount in BDT</span>
                                        <span class="info-box-number" id="bdt-amount-display">0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Create Account</button>
                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
            <!-- /.card -->
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        // Initially hide the amount display
        $('#amount-display').hide();
        
        // Function to update amount displays
        function updateAmountDisplays() {
            const amount = parseFloat($('#initial_amount').val()) || 0;
            const currencySelect = $('#currency_id');
            const selectedOption = currencySelect.find('option:selected');
            
            if (selectedOption.val()) {
                const symbol = selectedOption.data('symbol');
                const code = selectedOption.data('code');
                const rate = parseFloat(selectedOption.data('rate')) || 0;
                
                // Show/hide sections based on currency
                if (code === 'BDT') {
                    $('#currency-amount-section').hide();
                    $('#bdt-amount-section').show();
                    $('#bdt-amount-display').text('৳ ' + amount.toFixed(2));
                } else {
                    $('#currency-amount-section').show();
                    $('#bdt-amount-section').show();
                    $('#currency-amount-display').text(symbol + ' ' + amount.toFixed(2));
                    $('#bdt-amount-display').text('৳ ' + (amount * rate).toFixed(2));
                }
                
                $('#amount-display').show();
            } else {
                $('#amount-display').hide();
            }
        }
        
        // Event listeners
        $('#initial_amount, #currency_id').on('change keyup', updateAmountDisplays);
    });
</script>
@stop
