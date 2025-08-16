@extends('adminlte::page')

@section('title', 'Create Bank Account')

@section('content_header')
    <h1>Create Bank Account</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.banks.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Bank Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="account_name">Account Name</label>
                            <input type="text" name="account_name" id="account_name" class="form-control @error('account_name') is-invalid @enderror" value="{{ old('account_name') }}">
                            @error('account_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="account_number">Account Number <span class="text-danger">*</span></label>
                            <input type="text" name="account_number" id="account_number" class="form-control @error('account_number') is-invalid @enderror" value="{{ old('account_number') }}" required>
                            @error('account_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="branch">Branch</label>
                            <input type="text" name="branch" id="branch" class="form-control @error('branch') is-invalid @enderror" value="{{ old('branch') }}">
                            @error('branch')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="currency_id">Currency <span class="text-danger">*</span></label>
                            <select name="currency_id" id="currency_id" class="form-control @error('currency_id') is-invalid @enderror" required>
                                <option value="">Select Currency</option>
                                @foreach(\App\Models\Currency::all() as $currency)
                                    <option value="{{ $currency->id }}" data-rate="{{ $currency->conversion_rate }}" {{ old('currency_id') == $currency->id ? 'selected' : '' }}>
                                        {{ $currency->code }} ({{ $currency->symbol }})
                                    </option>
                                @endforeach
                            </select>
                            @error('currency_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="initial_balance">Initial Balance <span class="text-danger">*</span></label>
                            <input type="number" name="initial_balance" id="initial_balance" class="form-control @error('initial_balance') is-invalid @enderror" value="{{ old('initial_balance', 0) }}" step="0.01" min="0" required>
                            <small class="form-text text-muted" id="balance_in_bdt"></small>
                            @error('initial_balance')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 text-center mt-4">
                        <button type="submit" class="btn btn-primary mr-2">Create Bank Account</button>
                        <a href="{{ route('admin.banks.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Calculate BDT equivalent when currency or amount changes
            function updateBDTAmount() {
                const amount = parseFloat($('#initial_balance').val()) || 0;
                const selectedOption = $('#currency_id option:selected');
                const rate = parseFloat(selectedOption.data('rate')) || 1;
                const currencyCode = selectedOption.text().split(' ')[0] || '';
                
                if (currencyCode && currencyCode !== 'BDT') {
                    const amountInBDT = amount * rate;
                    $('#balance_in_bdt').html(`Equivalent in BDT: ৳ ${amountInBDT.toFixed(2)}`);
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'amount_in_bdt',
                        value: amountInBDT.toFixed(2)
                    }).appendTo('form');
                } else if (currencyCode === 'BDT') {
                    $('#balance_in_bdt').html('');
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'amount_in_bdt',
                        value: amount.toFixed(2)
                    }).appendTo('form');
                } else {
                    $('#balance_in_bdt').html('');
                }
            }
            
            $('#currency_id, #initial_balance').on('change keyup', updateBDTAmount);
            
            // Initial calculation
            updateBDTAmount();
        });
    </script>
@stop
