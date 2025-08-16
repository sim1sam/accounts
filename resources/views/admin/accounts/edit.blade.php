@extends('adminlte::page')

@section('title', 'Edit Account')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Edit Account</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.accounts.index') }}">Accounts</a></li>
                <li class="breadcrumb-item active">Edit</li>
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
                <form action="{{ route('admin.accounts.update', $account->id) }}" method="POST">
                    @csrf
                    @method('PUT')
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
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $account->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Initial Amount</label>
                            <input type="text" class="form-control" value="{{ $account->currency->symbol }} {{ number_format($account->initial_amount, 2) }}" disabled>
                            <small class="text-muted">Initial amount cannot be changed after creation</small>
                        </div>

                        <div class="form-group">
                            <label>Current Amount</label>
                            <input type="text" class="form-control" value="{{ $account->currency->symbol }} {{ number_format($account->current_amount, 2) }}" disabled>
                            <small class="text-muted">Use the balance adjustment feature to change the current amount</small>
                        </div>

                        <div class="form-group">
                            <label for="currency_id">Currency <span class="text-danger">*</span></label>
                            <select class="form-control @error('currency_id') is-invalid @enderror" id="currency_id" name="currency_id" required>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}" data-symbol="{{ $currency->symbol }}" data-code="{{ $currency->code }}" data-rate="{{ $currency->conversion_rate }}" {{ $account->currency_id == $currency->id ? 'selected' : '' }}>
                                        {{ $currency->name }} ({{ $currency->code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('currency_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="text-warning">Warning: Changing the currency will convert the current amount based on exchange rates.</small>
                        </div>

                        <div class="row" id="amount-display">
                            <div class="col-md-6" id="currency-amount-section">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info"><i class="fas fa-money-bill"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Amount in Selected Currency</span>
                                        <span class="info-box-number" id="currency-amount-display">
                                            {{ $account->currency->symbol }} {{ number_format($account->current_amount, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" id="bdt-amount-section">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success"><i class="fas fa-exchange-alt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Amount in BDT</span>
                                        <span class="info-box-number" id="bdt-amount-display">
                                            ৳ {{ number_format($account->getAmountInBDT(), 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', $account->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update Account</button>
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
        // Function to update amount displays when currency changes
        $('#currency_id').on('change', function() {
            const currentAmount = {{ $account->current_amount }};
            const oldRate = {{ $account->currency->conversion_rate }};
            const selectedOption = $(this).find('option:selected');
            
            if (selectedOption.val()) {
                const symbol = selectedOption.data('symbol');
                const code = selectedOption.data('code');
                const newRate = parseFloat(selectedOption.data('rate')) || 0;
                
                // Convert amount: first to BDT, then to new currency
                const amountInBDT = currentAmount * oldRate;
                const newAmount = code === 'BDT' ? amountInBDT : amountInBDT / newRate;
                
                // Show/hide sections based on currency
                if (code === 'BDT') {
                    $('#currency-amount-section').hide();
                    $('#bdt-amount-section').show();
                    $('#bdt-amount-display').text('৳ ' + amountInBDT.toFixed(2));
                } else {
                    $('#currency-amount-section').show();
                    $('#bdt-amount-section').show();
                    $('#currency-amount-display').text(symbol + ' ' + newAmount.toFixed(2));
                    $('#bdt-amount-display').text('৳ ' + amountInBDT.toFixed(2));
                }
            }
        });
        
        // Initialize display based on current currency
        const currentCode = $('#currency_id').find('option:selected').data('code');
        if (currentCode === 'BDT') {
            $('#currency-amount-section').hide();
        }
    });
</script>
@stop
