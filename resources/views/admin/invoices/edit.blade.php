@extends('adminlte::page')

@section('title', 'Edit Invoice')

@section('content_header')
    <h1>Edit Invoice</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Invoice #{{ $invoice->invoice_id }}</h3>
                </div>

                <form action="{{ route('admin.invoices.update', $invoice) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="invoice_id">Invoice ID</label>
                                    <input type="text" class="form-control @error('invoice_id') is-invalid @enderror" id="invoice_id" name="invoice_id" value="{{ old('invoice_id', $invoice->invoice_id) }}" required>
                                    @error('invoice_id')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="invoice_value">Invoice Value</label>
                                    <input type="number" step="0.01" class="form-control @error('invoice_value') is-invalid @enderror" id="invoice_value" name="invoice_value" value="{{ old('invoice_value', $invoice->invoice_value) }}" required>
                                    @error('invoice_value')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="customer_id">Customer</label>
                                    <select class="form-control @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id">
                                        <option value="">-- Select Customer --</option>
                                        @foreach($customers as $c)
                                            <option value="{{ $c->id }}" {{ (string)old('customer_id', $invoice->customer_id) === (string)$c->id ? 'selected' : '' }}>
                                                {{ $c->mobile }} - {{ $c->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="staff_id">Staff</label>
                                    <select class="form-control @error('staff_id') is-invalid @enderror" id="staff_id" name="staff_id">
                                        <option value="">-- Select Staff --</option>
                                        @foreach($staff as $s)
                                            <option value="{{ $s->id }}" {{ (string)old('staff_id', $invoice->staff_id) === (string)$s->id ? 'selected' : '' }}>
                                                {{ $s->name }} @if($s->phone) - {{ $s->phone }} @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('staff_id')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('admin.invoices.index') }}" class="btn btn-default">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
