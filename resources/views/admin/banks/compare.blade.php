@extends('adminlte::page')

@section('title', 'Bank Compare')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Bank Compare (System vs Physical)</h1>
        <a href="{{ route('admin.banks.index') }}" class="btn btn-secondary">Back to Banks</a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.banks.compare.store') }}" method="POST">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" class="form-control" value="{{ $date }}">
                    </div>
                    <div class="col-md-8 d-flex align-items-end justify-content-end">
                        <button type="submit" class="btn btn-primary">Save Daily Balances</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Bank</th>
                                <th>Currency</th>
                                <th class="text-right">System Balance</th>
                                <th class="text-right">System (BDT)</th>
                                <th style="width:220px">Physical Amount ({{ "" }})</th>
                                <th>Note</th>
                                <th class="text-right">Saved Physical (BDT)</th>
                                <th class="text-right">Difference (BDT)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($banks as $idx => $bank)
                                @php
                                    $code = $bank->currency->code ?? 'BDT';
                                    $symbol = $bank->currency->symbol ?? '৳';
                                    $rate = $bank->currency->conversion_rate ?? 1;
                                    $systemNative = (float)($bank->current_balance ?? 0);
                                    $systemBDT = (float)($bank->amount_in_bdt ?? ($systemNative * $rate));
                                    $row = $existing[$bank->id] ?? null;
                                @endphp
                                <tr>
                                    <td>{{ $idx + 1 }}</td>
                                    <td>{{ $bank->name }}<br><small class="text-muted">{{ $bank->account_number }}</small></td>
                                    <td>{{ $code }} ({{ $symbol }})</td>
                                    <td class="text-right">
                                        @if($code !== 'BDT')
                                            {{ $symbol }} {{ number_format($systemNative, 2) }}
                                        @else
                                            ৳ {{ number_format($systemNative, 2) }}
                                        @endif
                                    </td>
                                    <td class="text-right">৳ {{ number_format($systemBDT, 2) }}</td>
                                    <td>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">{{ $symbol }}</span>
                                            </div>
                                            <input type="number" step="0.01" min="0" name="banks[{{ $bank->id }}][physical_amount]" class="form-control" placeholder="Enter physical" value="{{ old('banks.'.$bank->id.'.physical_amount', $row->physical_amount ?? '') }}">
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="banks[{{ $bank->id }}][note]" class="form-control" placeholder="Optional note" value="{{ old('banks.'.$bank->id.'.note', $row->note ?? '') }}">
                                    </td>
                                    <td class="text-right">{{ $row ? '৳ '.number_format($row->physical_amount_bdt, 2) : '-' }}</td>
                                    <td class="text-right">
                                        @if($row)
                                            @php
                                                // Live difference: physical (BDT) - system (BDT)
                                                // Requirement: if bank (system) > physical => show negative; if bank < physical => show positive
                                                $liveDiff = (float)$row->physical_amount_bdt - (float)$systemBDT;
                                            @endphp
                                            <span class="badge {{ $liveDiff == 0.0 ? 'badge-success' : ($liveDiff > 0 ? 'badge-warning' : 'badge-danger') }}">
                                                {{ $liveDiff > 0 ? '+' : '' }}{{ number_format($liveDiff, 2) }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
@stop

@section('css')
    <style>
        .table td, .table th { vertical-align: middle; }
    </style>
@stop

@section('js')
    <script>
        // Auto reload page when date changes to show previously saved values
        document.getElementById('date').addEventListener('change', function() {
            const params = new URLSearchParams(window.location.search);
            params.set('date', this.value);
            window.location.href = '{{ route('admin.banks.compare') }}' + '?' + params.toString();
        });
    </script>
@stop
