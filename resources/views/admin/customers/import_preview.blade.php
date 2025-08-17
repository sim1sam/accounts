@extends('adminlte::page')

@section('title', 'Preview Customer Import')

@section('content_header')
    <h1>Preview Customer Import</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Preview</h3>
                <div>
                    <a href="{{ route('admin.customers.import') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                    <form action="{{ route('admin.customers.import.process') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-upload"></i> Import
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                @if(!empty($errors) && count($errors))
                    <div class="alert alert-warning">
                        <strong>Validation notes:</strong>
                        <ul class="mb-0">
                            @foreach($errors as $msg)
                                <li>{{ $msg }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(empty($rows) || count($rows) === 0)
                    <div class="alert alert-info mb-0">
                        No rows found in the file. Please go back and upload a valid CSV.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-sm">
                            <thead>
                                <tr>
                                    @foreach($headers as $h)
                                        <th class="text-nowrap">{{ $h }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $i => $row)
                                    <tr>
                                        @foreach($headers as $h)
                                            <td>{{ $row[$h] ?? '' }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="text-muted small mb-0">Total rows: {{ count($rows) }}</p>
                @endif
            </div>
            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('admin.customers.import') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <form action="{{ route('admin.customers.import.process') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Import
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
