@extends('adminlte::page')

@section('title', 'Create New Expense')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Create New Expense</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.expenses.index') }}">Expenses</a></li>
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
                    <h3 class="card-title">Expense Details</h3>
                </div>
                <!-- /.card-header -->
                <form action="{{ route('admin.expenses.store') }}" method="POST">
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
                            <label for="amount">Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" required>
                            @error('amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="row" id="amount-display" style="display: none;">
                            <div class="col-md-6" id="currency-amount-section">
                                <div class="info-box">
                                    <span class="info-box-icon bg-warning"><i class="fas fa-money-bill"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Amount in Selected Currency</span>
                                        <span class="info-box-number" id="currency-amount-display">0.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" id="bdt-amount-section">
                                <div class="info-box">
                                    <span class="info-box-icon bg-danger"><i class="fas fa-exchange-alt"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Amount in BDT</span>
                                        <span class="info-box-number" id="bdt-amount-display">0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="account_id">Account <span class="text-danger">*</span></label>
                            <select class="form-control @error('account_id') is-invalid @enderror" id="account_id" name="account_id" required>
                                <option value="">Select Account</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" data-balance="{{ $account->current_amount }}" data-currency="{{ $account->currency->symbol }}" data-rate="{{ $account->currency->conversion_rate }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }} ({{ $account->currency->symbol }} {{ number_format($account->current_amount, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('account_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="expense_date">Date</label>
                            <input type="date" class="form-control @error('expense_date') is-invalid @enderror" id="expense_date" name="expense_date" value="{{ old('expense_date', now()->toDateString()) }}">
                            @error('expense_date')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks" rows="3">{{ old('remarks') }}</textarea>
                            @error('remarks')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Create Expense</button>
                        <a href="{{ route('admin.expenses.index') }}" class="btn btn-default">Cancel</a>
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
        // Function to update amount displays
        function updateAmountDisplays() {
            const amount = parseFloat($('#amount').val()) || 0;
            const accountSelect = $('#account_id');
            const selectedOption = accountSelect.find('option:selected');
            
            if (selectedOption.val() && amount > 0) {
                const symbol = selectedOption.data('currency');
                const rate = parseFloat(selectedOption.data('rate')) || 1;
                
                // Always show both currency amounts
                $('#currency-amount-section').show();
                $('#bdt-amount-section').show();
                $('#currency-amount-display').text(symbol + ' ' + amount.toFixed(2));
                
                // Calculate and show BDT amount based on account currency rate
                const bdtAmount = amount * rate;
                $('#bdt-amount-display').text('৳ ' + bdtAmount.toFixed(2));
                
                $('#amount-display').show();
            } else {
                $('#amount-display').hide();
            }
        }
        
        // Event listeners
        $('#amount, #account_id').on('change keyup', updateAmountDisplays);
    });
</script>
@stop
