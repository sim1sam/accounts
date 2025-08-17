@extends('adminlte::page')

@section('title', 'Budget Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Budget: {{ \Carbon\Carbon::parse($budget->month)->format('Y-m') }}</h1>
        <div>
            <a href="{{ route('admin.budgets.index') }}" class="btn btn-secondary mr-2">Back</a>
            @if($budget->status !== 'converted')
                <form action="{{ route('admin.budgets.convert', $budget->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Convert all budget items to expenses?')">
                        <i class="fas fa-exchange-alt"></i> Convert to Expenses
                    </button>
                </form>
            @else
                <span class="badge badge-success">Converted</span>
            @endif
        </div>
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
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-3">
                <strong>Status:</strong>
                @if($budget->status === 'converted')
                    <span class="badge badge-success">Converted</span>
                @else
                    <span class="badge badge-secondary">Planned</span>
                @endif
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Account</th>
                            <th>Amount (Native)</th>
                            <th>≈ BDT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalBdt = 0; @endphp
                        @forelse($budget->items as $i => $item)
                            @php
                                $code = strtoupper(optional($item->currency)->code ?? 'BDT');
                                $native = (float) $item->amount;
                                $bdt = (float) $item->amount_in_bdt;
                                $totalBdt += $bdt;
                            @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $item->account->name }}</td>
                                <td>{{ $code }} {{ number_format($native, 2) }}</td>
                                <td>BDT {{ number_format($bdt, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center">No items</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Total ≈ BDT</th>
                            <th>BDT {{ number_format($totalBdt, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@stop
