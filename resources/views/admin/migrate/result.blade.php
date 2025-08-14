@extends('adminlte::page')

@section('title', 'Database Migration')

@section('content_header')
    <h1>Database Migration Results</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Migration Output</h3>
                </div>
                <div class="card-body">
                    @if($success)
                        <div class="alert alert-success">
                            <h5><i class="icon fas fa-check"></i> Success!</h5>
                            Migrations have been run successfully.
                        </div>
                    @else
                        <div class="alert alert-danger">
                            <h5><i class="icon fas fa-ban"></i> Error!</h5>
                            There was an issue running the migrations.
                        </div>
                    @endif
                    
                    <div class="mt-4">
                        <h5>Migration Output:</h5>
                        <pre class="p-3 bg-dark text-white">{{ $output }}</pre>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Return to Dashboard</a>
                    </div>
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
        console.log('Migration completed!');
    </script>
@stop
