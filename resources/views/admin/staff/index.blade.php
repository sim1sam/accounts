@extends('adminlte::page')

@section('title', 'Staff Management')

@section('content_header')
    <div class="d-flex justify-content-between">
        <h1>Staff Management</h1>
        <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">Add New Staff</a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($staff as $employee)
                        <tr>
                            <td>{{ $employee->id }}</td>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->email ?? 'N/A' }}</td>
                            <td>{{ $employee->phone ?? 'N/A' }}</td>
                            <td>{{ $employee->position ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-{{ $employee->status == 'active' ? 'success' : 'danger' }}">
                                    {{ ucfirst($employee->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.staff.show', $employee) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.staff.edit', $employee) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.staff.destroy', $employee) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this staff member?');" style="display: inline-block;">
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
                            <td colspan="7" class="text-center">No staff members found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($staff->hasPages())
            <div class="card-footer clearfix">
                {{ $staff->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function() {
            // Auto close alerts after 5 seconds
            setTimeout(function() {
                $(".alert").alert('close');
            }, 5000);
        });
    </script>
@stop
