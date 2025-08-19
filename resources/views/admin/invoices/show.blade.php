@extends('adminlte::page')

@section('title', 'Invoice Details')

@section('content_header')
    <h1>Invoice Details</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Invoice #{{ $invoice->invoice_id }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Invoice ID</label>
                                <p class="form-control-plaintext">{{ $invoice->invoice_id }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Invoice Value</label>
                                <p class="form-control-plaintext">{{ number_format($invoice->invoice_value, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h4>Customer</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name</label>
                                <p class="form-control-plaintext">{{ optional($invoice->customer)->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mobile</label>
                                <p class="form-control-plaintext">{{ optional($invoice->customer)->mobile ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <p class="form-control-plaintext">{{ optional($invoice->customer)->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Address</label>
                                <p class="form-control-plaintext">{{ optional($invoice->customer)->address ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <h4>Staff</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Name</label>
                                <p class="form-control-plaintext">{{ optional($invoice->staff)->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <p class="form-control-plaintext">{{ optional($invoice->staff)->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Created At</label>
                                <p class="form-control-plaintext">{{ $invoice->created_at }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Updated At</label>
                                <p class="form-control-plaintext">{{ $invoice->updated_at }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('admin.invoices.index') }}" class="btn btn-default">Back</a>
                    <a href="{{ route('admin.invoices.edit', $invoice) }}" class="btn btn-primary">Edit</a>
                </div>
            </div>
        </div>
    </div>
@stop
