@extends('adminlte::page')

@section('title', 'Create Budget')

@section('content_header')
    <h1>Create Monthly Budget</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.budgets.store') }}" method="POST" id="budget-form">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="month">Month</label>
                            <input type="month" name="month" id="month" class="form-control" value="{{ old('month', now()->format('Y-m')) }}" required>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <input type="text" name="remarks" id="remarks" class="form-control" value="{{ old('remarks') }}" placeholder="Optional">
                        </div>
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Budget Lines</h5>
                    <button type="button" id="add-row" class="btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Line</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="items-table">
                        <thead>
                            <tr>
                                <th style="width:40%">Account (Budget Name)</th>
                                <th style="width:20%">Currency</th>
                                <th style="width:20%">Amount</th>
                                <th style="width:15%">≈ BDT</th>
                                <th style="width:5%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Rows injected by JS -->
                        </tbody>
                    </table>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary">Save Budget</button>
                </div>
            </form>
        </div>
    </div>
@stop

@push('js')
<script>
(function(){
    @php
        $accountsData = $accounts->map(function($a){
            return [
                'id' => $a->id,
                'name' => $a->name,
                'code' => optional($a->currency)->code ?? 'BDT',
                'rate' => (float) (optional($a->currency)->conversion_rate ?? 1),
            ];
        })->values();
    @endphp
    const accounts = @json($accountsData);
    @php
        $currencyData = $currencies->map(function($c){
            return [
                'id' => $c->id,
                'code' => $c->code,
                'rate' => (float) ($c->conversion_rate ?? 1),
            ];
        })->values();
    @endphp
    const currencies = @json($currencyData);

    let idx = 0;
    const tbody = document.querySelector('#items-table tbody');
    const addBtn = document.getElementById('add-row');

    function formatBDT(val){
        return Number(val || 0).toFixed(2);
    }

    function createAccountSelect(name){
        const sel = document.createElement('select');
        sel.className = 'form-control account-select';
        sel.name = name;
        sel.required = true;
        const opt0 = document.createElement('option');
        opt0.value = '';
        opt0.textContent = 'Select Account';
        sel.appendChild(opt0);
        accounts.forEach(acc => {
            const o = document.createElement('option');
            o.value = acc.id;
            o.textContent = acc.name + ' (' + (acc.code || 'BDT') + ')';
            o.dataset.code = (acc.code || 'BDT');
            o.dataset.rate = acc.rate > 0 ? acc.rate : 1;
            sel.appendChild(o);
        });
        return sel;
    }

    function createCurrencySelect(name){
        const sel = document.createElement('select');
        sel.className = 'form-control currency-select';
        sel.name = name;
        sel.required = true;
        const opt0 = document.createElement('option');
        opt0.value = '';
        opt0.textContent = 'Select Currency';
        sel.appendChild(opt0);
        currencies.forEach(cur => {
            const o = document.createElement('option');
            o.value = cur.id;
            o.textContent = cur.code;
            o.dataset.code = cur.code;
            o.dataset.rate = cur.rate > 0 ? cur.rate : 1;
            sel.appendChild(o);
        });
        return sel;
    }

    function addRow(defaults = {}){
        const tr = document.createElement('tr');

        const tdAcc = document.createElement('td');
        const accountSelect = createAccountSelect(`items[${idx}][account_id]`);
        tdAcc.appendChild(accountSelect);

        const tdCur = document.createElement('td');
        const currencySelect = createCurrencySelect(`items[${idx}][currency_id]`);
        tdCur.appendChild(currencySelect);

        const tdAmt = document.createElement('td');
        const inputGroup = document.createElement('div');
        inputGroup.className = 'input-group';
        const prefix = document.createElement('div');
        prefix.className = 'input-group-prepend';
        prefix.innerHTML = `<span class="input-group-text cur-code">BDT</span>`;
        const input = document.createElement('input');
        input.type = 'number';
        input.min = '0';
        input.step = '0.01';
        input.className = 'form-control amount-input';
        input.name = `items[${idx}][amount]`;
        input.required = true;
        input.value = defaults.amount || '';
        inputGroup.appendChild(prefix);
        inputGroup.appendChild(input);
        tdAmt.appendChild(inputGroup);

        const tdBdt = document.createElement('td');
        tdBdt.innerHTML = '<span class="bdt-val">0.00</span>';

        const tdAct = document.createElement('td');
        const rm = document.createElement('button');
        rm.type = 'button';
        rm.className = 'btn btn-sm btn-danger';
        rm.innerHTML = '<i class="fas fa-trash"></i>';
        tdAct.appendChild(rm);

        tr.appendChild(tdAcc); tr.appendChild(tdCur); tr.appendChild(tdAmt); tr.appendChild(tdBdt); tr.appendChild(tdAct);
        tbody.appendChild(tr);

        function recalc(){
            const curOpt = currencySelect.options[currencySelect.selectedIndex];
            const code = curOpt && curOpt.dataset.code ? curOpt.dataset.code.toUpperCase() : 'BDT';
            const rate = curOpt && curOpt.dataset.rate ? parseFloat(curOpt.dataset.rate) : 1;
            const amt = parseFloat(input.value || 0);
            tr.querySelector('.cur-code').textContent = code;
            const validRate = rate > 0 ? rate : 1;
            const bdt = code === 'BDT' ? amt : (amt * validRate);
            tr.querySelector('.bdt-val').textContent = formatBDT(bdt);
        }
        accountSelect.addEventListener('change', recalc);
        currencySelect.addEventListener('change', recalc);
        input.addEventListener('input', recalc);
        rm.addEventListener('click', () => { tr.remove(); });
        idx++;
        recalc();
    }

    // initial one row
    addRow();

    addBtn.addEventListener('click', () => addRow());
})();
</script>
@endpush
