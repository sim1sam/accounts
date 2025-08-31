@extends('adminlte::page')

@section('title', 'Process Payment')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Process Payment</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.expenses.index') }}">Expenses</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.expenses.show', $expense) }}">Details</a></li>
                <li class="breadcrumb-item active">Payment</li>
            </ol>
        </div>
    </div>
@stop

@push('js')
<script>
    (function(){
        const bankSelect = document.getElementById('bank_id');
        const input = document.getElementById('payment_amount');
        const codeBadge = document.getElementById('bank-code');
        const bdtHelper = document.getElementById('helper-bdt');
        const nativeHelper = document.getElementById('helper-native');
        // Remaining due in BDT (expense total - prior payments)
        // Exclude initial transaction when calculating paid amount
        @php
            $initialTransaction = $expense->accountTransactions()->where('type', 'expense')->orderBy('created_at')->first();
            $initialTransactionId = $initialTransaction ? $initialTransaction->id : 0;
            $paidBDT = (float) $expense->accountTransactions()->where('type', 'expense')->where('id', '!=', $initialTransactionId)->sum('amount');
            $remainingBDT = max(((float)$expense->amount_in_bdt) - $paidBDT, 0);
        @endphp
        const expenseBDTRemaining = {{ json_encode($remainingBDT) }};

        function recalc(){
            const opt = bankSelect.options[bankSelect.selectedIndex];
            const code = opt && opt.dataset.code ? opt.dataset.code : 'BDT';
            const rate = opt && opt.dataset.rate ? parseFloat(opt.dataset.rate) : 1;
            codeBadge.textContent = code;
            // Auto-fill expected native amount for the selected bank based on remaining due
            // For INR specifically, use the correct conversion (1 BDT = 0.69 INR, so 1 INR = 1/0.69 BDT)
            let expectedNative;
            if (code === 'INR') {
                // For INR, 1 BDT = 0.69 INR (or 1 INR = 1.45 BDT)
                // Therefore, to convert BDT to INR: BDT * 0.69
                expectedNative = expenseBDTRemaining * 0.69;
            } else {
                expectedNative = code === 'BDT' ? expenseBDTRemaining : (rate > 0 ? (expenseBDTRemaining / rate) : expenseBDTRemaining);
            }
            if (!input.value || parseFloat(input.value) <= 0) {
                input.value = expectedNative.toFixed(2);
            }
            // Show helper: native -> BDT
            const entered = parseFloat(input.value || 0);
            let enteredBDT;
            
            // Special handling for INR conversion
            if (code === 'INR') {
                // For INR, 1 INR = 1.45 BDT (as per requirement: INR 13.80 = BDT 20)
                enteredBDT = entered * 1.45;
            } else {
                enteredBDT = code === 'BDT' ? entered : (entered * (rate > 0 ? rate : 1));
            }
            
            // Show remaining due in both currencies
            let remainingNative;
            if (code === 'INR') {
                remainingNative = expenseBDTRemaining * 0.69;
                bdtHelper.textContent = 'Remaining due: BDT ' + expenseBDTRemaining.toFixed(2) + ' = INR ' + remainingNative.toFixed(2);
            } else if (code !== 'BDT') {
                remainingNative = rate > 0 ? (expenseBDTRemaining / rate) : expenseBDTRemaining;
                bdtHelper.textContent = 'Remaining due: BDT ' + expenseBDTRemaining.toFixed(2) + ' = ' + code + ' ' + remainingNative.toFixed(2);
            } else {
                bdtHelper.textContent = 'Remaining due: BDT ' + expenseBDTRemaining.toFixed(2);
            }
            
            // Show payment amount in both currencies
            nativeHelper.textContent = 'You will pay: ' + code + ' ' + (entered.toFixed(2)) + ' = BDT ' + enteredBDT.toFixed(2);
            
            // Update hidden BDT amount field
            document.getElementById('payment_amount_bdt').value = enteredBDT.toFixed(2);
        }

        bankSelect.addEventListener('change', () => { 
            // When bank changes, automatically calculate and set the converted amount
            const opt = bankSelect.options[bankSelect.selectedIndex];
            const code = opt && opt.dataset.code ? opt.dataset.code : 'BDT';
            const rate = opt && opt.dataset.rate ? parseFloat(opt.dataset.rate) : 1;
            
            // Get current BDT value (either from hidden field or calculate from current input)
            let currentBDTValue;
            const hiddenBDTField = document.getElementById('payment_amount_bdt');
            
            if (hiddenBDTField.value && parseFloat(hiddenBDTField.value) > 0) {
                // Use the stored BDT value if available
                currentBDTValue = parseFloat(hiddenBDTField.value);
            } else if (input.value && parseFloat(input.value) > 0) {
                // Calculate BDT from current input value
                const prevCode = codeBadge.textContent;
                if (prevCode === 'INR') {
                    currentBDTValue = parseFloat(input.value) * (1/0.69);
                } else if (prevCode !== 'BDT') {
                    const prevRate = bankSelect.options[bankSelect.selectedIndex].dataset.rate || 1;
                    currentBDTValue = parseFloat(input.value) * parseFloat(prevRate);
                } else {
                    currentBDTValue = parseFloat(input.value);
                }
            } else {
                // Default to remaining BDT if no value is set
                currentBDTValue = expenseBDTRemaining;
            }
            
            // Auto-fill with converted amount based on the selected currency
            if (code === 'INR') {
                // Convert BDT to INR (BDT * 0.69) - for INR 13.80 = BDT 20
                input.value = (expenseBDTRemaining * 0.69).toFixed(2);
            } else if (code !== 'BDT') {
                // For other currencies, use the rate from the bank
                input.value = (rate > 0 ? (expenseBDTRemaining / rate) : expenseBDTRemaining).toFixed(2);
            } else {
                // For BDT, use the BDT amount directly
                input.value = expenseBDTRemaining.toFixed(2);
            }
            
            // Update hidden BDT field
            hiddenBDTField.value = currentBDTValue.toFixed(2);
            
            recalc();
        });
        input.addEventListener('input', recalc);
        // init
        recalc();
    })();
</script>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Payment Details</h3>
                </div>
                <form action="{{ route('admin.expenses.payment.process', $expense) }}" method="POST">
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
                            <label for="bank_id">Select Bank <span class="text-danger">*</span></label>
                            <select class="form-control @error('bank_id') is-invalid @enderror" id="bank_id" name="bank_id" required>
                                <option value="">Select Bank</option>
                                @foreach($banks as $bank)
                                    @php
                                        $bCode = strtoupper(optional($bank->currency)->code ?? 'BDT');
                                        $bRate = (float) (optional($bank->currency)->conversion_rate ?? 1);
                                        if ($bRate <= 0) { $bRate = 1; }
                                    @endphp
                                    <option value="{{ $bank->id }}" data-balance="{{ (float) ($bank->current_balance ?? 0) }}" data-code="{{ $bCode }}" data-rate="{{ $bRate }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                        {{ $bank->name }} ({{ $bCode }}) — Balance: {{ $bCode }} {{ number_format((float) ($bank->current_balance ?? 0), 2) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bank_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="payment_amount">Payment Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="bank-code">BDT</span>
                                </div>
                                <input type="number" step="0.01" min="0.01" class="form-control @error('payment_amount') is-invalid @enderror" id="payment_amount" name="payment_amount" value="{{ old('payment_amount') }}" required>
                                <input type="hidden" id="payment_amount_bdt" name="payment_amount_bdt" value="">
                            </div>
                            @error('payment_amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            @php
                                $initialTransaction = $expense->accountTransactions()->where('type', 'expense')->orderBy('created_at')->first();
                                $initialTransactionId = $initialTransaction ? $initialTransaction->id : 0;
                                $paidBDT = (float) $expense->accountTransactions()->where('type', 'expense')->where('id', '!=', $initialTransactionId)->sum('amount');
                                $remainingBDT = max(((float)$expense->amount_in_bdt) - $paidBDT, 0);
                            @endphp
                            <small class="form-text text-muted" id="helper-bdt">Remaining due: BDT {{ number_format($remainingBDT, 2) }}</small>
                            <small class="form-text text-muted" id="helper-native"></small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-credit-card"></i> Process Payment
                        </button>
                        <a href="{{ route('admin.expenses.show', $expense) }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Expense Summary</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-6">Account:</dt>
                        <dd class="col-6">{{ $expense->account->name }}</dd>

                        <dt class="col-6">Amount:</dt>
                        <dd class="col-6">
                            @php
                                $eCurrency = $expense->currency ?? optional($expense->account)->currency;
                                $eCode = strtoupper(optional($eCurrency)->code ?? 'BDT');
                                $eSym = optional($eCurrency)->symbol ?? ($eCode === 'BDT' ? '৳' : '');
                            @endphp
                            {{ $eCode }} {{ $eSym }} {{ number_format((float) $expense->amount, 2) }}
                            @if($eCode !== 'BDT')
                                <br><small class="text-muted">BDT {{ number_format((float) $expense->amount_in_bdt, 2) }}</small>
                            @endif
                        </dd>

                        <dt class="col-6">Status:</dt>
                        <dd class="col-6">
                            @if($remainingBDT <= 0.01)
                                <span class="badge badge-success">Paid</span>
                            @elseif($paidBDT > 0)
                                <span class="badge badge-info">Partial</span>
                                <br><small class="text-muted">Due: BDT {{ number_format($remainingBDT, 2) }}</small>
                            @else
                                <span class="badge badge-warning">Pending</span>
                                <br><small class="text-muted">Due: BDT {{ number_format($remainingBDT, 2) }}</small>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Important Notes</h3>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><i class="fas fa-info-circle text-info"></i> Payment is recorded in transaction history</li>
                        <li><i class="fas fa-bank text-primary"></i> Bank balance is deducted in native currency</li>
                        <li><i class="fas fa-check text-success"></i> Expense is marked partial until fully paid</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop
