@extends('adminlte::page')

@section('title', 'Bank Account Details')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Bank Account Details</h1>
        <div>
            <a href="{{ route('admin.banks.edit', $bank->id) }}" class="btn btn-warning">Edit</a>
            <a href="{{ route('admin.banks.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Account Information</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">Bank Name</th>
                            <td>{{ $bank->name }}</td>
                        </tr>
                        <tr>
                            <th>Account Name</th>
                            <td>{{ $bank->account_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Account Number</th>
                            <td>{{ $bank->account_number }}</td>
                        </tr>
                        <tr>
                            <th>Branch</th>
                            <td>{{ $bank->branch ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Initial Balance</th>
                            <td>{{ number_format($bank->initial_balance, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Current Balance</th>
                            <td><strong>{{ number_format($bank->current_balance, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($bank->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $bank->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated</th>
                            <td>{{ $bank->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Adjust Balance</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.banks.adjust-balance', $bank->id) }}" method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="amount">Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" step="0.01" min="0.01" required>
                            @error('amount')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label>Transaction Type <span class="text-danger">*</span></label>
                            <div class="d-flex">
                                <div class="custom-control custom-radio mr-4">
                                    <input class="custom-control-input" type="radio" id="increase" name="type" value="increase" checked>
                                    <label for="increase" class="custom-control-label">Increase</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input class="custom-control-input" type="radio" id="decrease" name="type" value="decrease">
                                    <label for="decrease" class="custom-control-label">Decrease</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Adjust Balance</button>
                        </div>
                    </form>
                </div>
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
            // Any JavaScript initialization if needed
        });
    </script>
@stop
