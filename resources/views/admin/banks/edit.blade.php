@extends('adminlte::page')

@section('title', 'Edit Bank Account')

@section('content_header')
    <h1>Edit Bank Account</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.banks.update', $bank->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Bank Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $bank->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="account_name">Account Name</label>
                            <input type="text" name="account_name" id="account_name" class="form-control @error('account_name') is-invalid @enderror" value="{{ old('account_name', $bank->account_name) }}">
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
                            <input type="text" name="account_number" id="account_number" class="form-control @error('account_number') is-invalid @enderror" value="{{ old('account_number', $bank->account_number) }}" required>
                            @error('account_number')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="branch">Branch</label>
                            <input type="text" name="branch" id="branch" class="form-control @error('branch') is-invalid @enderror" value="{{ old('branch', $bank->branch) }}">
                            @error('branch')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                    
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="currency_id">Currency <span class="text-danger">*</span></label>
                            <select name="currency_id" id="currency_id" class="form-control select2 @error('currency_id') is-invalid @enderror" required>
                                <option value="">Select Currency</option>
                                @foreach(\App\Models\Currency::all() as $currency)
                                    <option value="{{ $currency->id }}" data-rate="{{ $currency->conversion_rate }}" {{ old('currency_id', $bank->currency_id) == $currency->id ? 'selected' : '' }}>
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
                
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="custom-control custom-switch mt-4">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', $bank->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Initial Balance</label>
                            <div class="form-control-static p-2 bg-light rounded">
                                @if($bank->currency && $bank->currency->code != 'BDT')
                                    <strong>{{ $bank->currency->symbol }} {{ number_format($bank->initial_balance, 2) }}</strong>
                                    <div class="text-muted mt-1">৳ {{ number_format($bank->initial_balance * ($bank->currency->conversion_rate ?? 1), 2) }} (BDT)</div>
                                @else
                                    <strong>৳ {{ number_format($bank->initial_balance, 2) }}</strong>
                                @endif
                            </div>
                            <small class="text-muted mt-1 d-block">Initial balance cannot be changed after creation.</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Current Balance</label>
                            <div class="form-control-static p-2 bg-light rounded">
                                @if($bank->currency && $bank->currency->code != 'BDT')
                                    <strong>{{ $bank->currency->symbol }} {{ number_format($bank->current_balance, 2) }}</strong>
                                    <div class="text-muted mt-1">৳ {{ number_format($bank->amount_in_bdt ?? ($bank->current_balance * ($bank->currency->conversion_rate ?? 1)), 2) }} (BDT)</div>
                                @else
                                    <strong>৳ {{ number_format($bank->current_balance, 2) }}</strong>
                                @endif
                            </div>
                            <small class="text-muted mt-1 d-block">Use the balance adjustment feature to change the current balance.</small>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 text-center mt-4">
                        <button type="submit" class="btn btn-primary mr-2">Update Bank Account</button>
                        <a href="{{ route('admin.banks.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>
        .form-control-static {
            border: 1px solid rgba(0,0,0,.125);
        }
        .input-group-text {
            min-width: 40px;
            justify-content: center;
        }
        #balance_in_bdt {
            font-weight: 500;
            color: #6c757d;
            margin-top: 5px;
            display: block;
        }
    </style>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Initialize select2 for better dropdown experience
            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });
            
            // Calculate BDT equivalent when currency changes
            function updateCurrencyInfo() {
                const selectedOption = $('#currency_id option:selected');
                const currencyCode = selectedOption.text().split(' ')[0] || '';
                const rate = parseFloat(selectedOption.data('rate')) || 1;
                
                // Update display of balances when currency changes
                if (currencyCode) {
                    console.log('Currency changed to: ' + currencyCode + ' with rate: ' + rate);
                }
            }
            
            $('#currency_id').on('change', updateCurrencyInfo);
            
            // Initial update
            updateCurrencyInfo();
        });
    </script>
@stop
