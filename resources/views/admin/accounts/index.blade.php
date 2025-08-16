@extends('adminlte::page')

@section('title', 'Accounts Management')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Accounts Management</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Accounts</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Accounts</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.accounts.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add New Account
                        </a>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Initial Amount</th>
                                    <th>Current Amount</th>
                                    <th>Currency</th>
                                    <th>Amount in BDT</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($accounts as $account)
                                    <tr>
                                        <td>{{ $account->id }}</td>
                                        <td>{{ $account->name }}</td>
                                        <td>{{ $account->currency->symbol }} {{ number_format($account->initial_amount, 2) }}</td>
                                        <td>{{ $account->currency->symbol }} {{ number_format($account->current_amount, 2) }}</td>
                                        <td>{{ $account->currency->code }}</td>
                                        <td>৳ {{ number_format($account->getAmountInBDT(), 2) }}</td>
                                        <td>
                                            @if($account->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.accounts.show', $account->id) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.accounts.edit', $account->id) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.accounts.destroy', $account->id) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this account?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/vendor/datatables/css/dataTables.bootstrap4.min.css">
@stop

@section('js')
    <script src="/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="/vendor/datatables/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.datatable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
@stop
