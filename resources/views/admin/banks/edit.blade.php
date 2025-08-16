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
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="branch">Branch</label>
                            <input type="text" name="branch" id="branch" class="form-control @error('branch') is-invalid @enderror" value="{{ old('branch', $bank->branch) }}">
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
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', $bank->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Initial Balance</label>
                            <p class="form-control-static">
                                @if($bank->currency && $bank->currency->code != 'BDT')
                                    {{ $bank->currency->symbol }} {{ number_format($bank->initial_balance, 2) }}
                                    <small class="text-muted d-block">৳ {{ number_format($bank->initial_balance * ($bank->currency->conversion_rate ?? 1), 2) }} (BDT)</small>
                                @else
                                    ৳ {{ number_format($bank->initial_balance, 2) }}
                                @endif
                            </p>
                            <small class="text-muted">Initial balance cannot be changed after creation.</small>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Current Balance</label>
                            <p class="form-control-static">
                                @if($bank->currency && $bank->currency->code != 'BDT')
                                    {{ $bank->currency->symbol }} {{ number_format($bank->current_balance, 2) }}
                                    <small class="text-muted d-block">৳ {{ number_format($bank->amount_in_bdt ?? ($bank->current_balance * ($bank->currency->conversion_rate ?? 1)), 2) }} (BDT)</small>
                                @else
                                    ৳ {{ number_format($bank->current_balance, 2) }}
                                @endif
                            </p>
                            <small class="text-muted">Use the balance adjustment feature to change the current balance.</small>
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
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Calculate BDT equivalent when currency changes
            function updateCurrencyInfo() {
                const selectedOption = $('#currency_id option:selected');
                const currencyCode = selectedOption.text().split(' ')[0] || '';
                
                // Update display of balances when currency changes
                if (currencyCode) {
                    console.log('Currency changed to: ' + currencyCode);
                }
            }
            
            $('#currency_id').on('change', updateCurrencyInfo);
        });
    </script>
@stop
