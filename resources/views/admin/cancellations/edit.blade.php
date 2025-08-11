@extends('adminlte::page')

@section('title', 'Edit Cancellation')

@section('content_header')
    <h1>Edit Cancellation</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">Edit Cancellation #{{ $cancellation->id }}</h3>
                </div>
                
                <form action="{{ route('admin.cancellations.update', $cancellation->id) }}" method="POST" id="cancellationForm">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="customer_id">Customer</label>
                                    <select class="form-control @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" disabled>
                                        <option value="{{ $cancellation->customer_id }}" selected>
                                            {{ $cancellation->customer->mobile }} - {{ $cancellation->customer->name }}
                                        </option>
                                    </select>
                                    <small class="form-text text-muted">Customer cannot be changed. Create a new cancellation if needed.</small>
                                    <input type="hidden" name="customer_id" value="{{ $cancellation->customer_id }}">
                                    @error('customer_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cancellation_value">Cancellation Value</label>
                                    <input type="number" class="form-control @error('cancellation_value') is-invalid @enderror" id="cancellation_value" name="cancellation_value" value="{{ old('cancellation_value', $cancellation->cancellation_value) }}" placeholder="Enter cancellation value" step="0.01" required>
                                    @error('cancellation_value')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cancellation_date">Cancellation Date</label>
                                    <input type="date" class="form-control @error('cancellation_date') is-invalid @enderror" id="cancellation_date" name="cancellation_date" value="{{ old('cancellation_date', $cancellation->cancellation_date->format('Y-m-d')) }}" required>
                                    @error('cancellation_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="remarks">Remarks</label>
                                    <textarea class="form-control @error('remarks') is-invalid @enderror" id="remarks" name="remarks" rows="3" placeholder="Enter cancellation remarks">{{ old('remarks', $cancellation->remarks) }}</textarea>
                                    @error('remarks')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="card card-info">
                            <div class="card-header">
                                <h3 class="card-title">Customer Details</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <p class="form-control-static">{{ $cancellation->customer->name }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Mobile</label>
                                            <p class="form-control-static">{{ $cancellation->customer->mobile }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <p class="form-control-static">{{ $cancellation->customer->email ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Address</label>
                                            <p class="form-control-static">{{ $cancellation->customer->address ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Delivery Class</label>
                                            <p class="form-control-static">{{ $cancellation->customer->delivery_class ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>KAM/Staff Responsible</label>
                                            <p class="form-control-static">{{ $cancellation->customer->staff ? $cancellation->customer->staff->name : 'Not Assigned' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">Update Cancellation</button>
                        <a href="{{ route('admin.cancellations.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
