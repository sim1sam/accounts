@extends('adminlte::page')

@section('title', 'Import Customers')

@section('content_header')
    <h1>Import Customers</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Upload CSV</h3>
            </div>
            <form action="{{ route('admin.customers.import.preview') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="file">CSV File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" name="file" id="file" accept=".csv,text/csv">
                        @error('file')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <small class="form-text text-muted">Max 2MB. CSV with header row.</small>
                    </div>

                    <button class="btn btn-primary"><i class="fas fa-eye"></i> Preview</button>
                    <a href="{{ route('admin.customers.import.sample') }}" class="btn btn-success">
                        <i class="fas fa-download"></i> Download sample CSV
                    </a>
                    <a href="#" class="btn btn-link" onclick="event.preventDefault(); document.getElementById('sample-csv').classList.toggle('d-none');">View sample CSV</a>
                </div>
            </form>
        </div>

        <div id="sample-csv" class="card d-none">
            <div class="card-header"><h3 class="card-title">Sample CSV</h3></div>
            <div class="card-body">
<pre class="mb-0"><code>name,mobile,email,address,dob,delivery_class,kam
Acme Ltd,01700111222,contact@acme.test,"12 Street, City",,A,1
John Doe,01899000111,,"House 7, Road 3, City",1990-05-01,,
</code></pre>
                <small class="text-muted">Note: name is required. At least one of mobile/email is required. dob format: YYYY-MM-DD.</small>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Instructions</h3></div>
            <div class="card-body">
                <ul>
                    <li><strong>Required</strong>: name; mobile or email.</li>
                    <li><strong>Duplicates</strong>: matched first by mobile, else email. Existing records are updated.</li>
                    <li><strong>Header</strong>: include the first row with column names.</li>
                    <li><strong>Large files</strong>: split into smaller CSVs if needed.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@stop
