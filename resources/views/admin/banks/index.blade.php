@extends('adminlte::page')

@section('title', 'Bank Accounts')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Bank Accounts</h1>
        <a href="{{ route('admin.banks.create') }}" class="btn btn-primary">Create Bank Account</a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Bank Name</th>
                        <th>Account Name</th>
                        <th>Account Number</th>
                        <th>Branch</th>
                        <th>Currency</th>
                        <th>Initial Balance</th>
                        <th>Current Balance</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banks as $bank)
                        <tr>
                            <td>{{ $bank->id }}</td>
                            <td>{{ $bank->name }}</td>
                            <td>{{ $bank->account_name ?? 'N/A' }}</td>
                            <td>{{ $bank->account_number }}</td>
                            <td>{{ $bank->branch ?? 'N/A' }}</td>
                            <td>{{ $bank->currency->code ?? 'BDT' }}</td>
                            <td>
                                @if($bank->currency && $bank->currency->code != 'BDT')
                                    {{ $bank->currency->symbol }} {{ number_format($bank->initial_balance, 2) }}
                                    <small class="text-muted d-block">৳ {{ number_format($bank->initial_balance * ($bank->currency->conversion_rate ?? 1), 2) }}</small>
                                @else
                                    ৳ {{ number_format($bank->initial_balance, 2) }}
                                @endif
                            </td>
                            <td>
                                @if($bank->currency && $bank->currency->code != 'BDT')
                                    {{ $bank->currency->symbol }} {{ number_format($bank->current_balance, 2) }}
                                    <small class="text-muted d-block">৳ {{ number_format($bank->amount_in_bdt ?? ($bank->current_balance * ($bank->currency->conversion_rate ?? 1)), 2) }}</small>
                                @else
                                    ৳ {{ number_format($bank->current_balance, 2) }}
                                @endif
                            </td>
                            <td>
                                @if($bank->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.banks.show', $bank->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.banks.edit', $bank->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.banks.destroy', $bank->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this bank account?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No bank accounts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $banks->links() }}
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Initialize DataTable if needed
            // $('.table').DataTable();
        });
    </script>
@stop
