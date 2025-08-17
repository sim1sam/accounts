@extends('adminlte::page')

@section('title', 'Users & Permissions')

@section('content_header')
    <h1>Users & Permissions</h1>
@endsection

@section('content')
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <div></div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Create User
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Protected</th>
                        <th style="width:180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @php
                                $roleClass = match($user->role){
                                    'admin' => 'primary',
                                    'supervisor' => 'info',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge badge-{{ $roleClass }}">{{ strtoupper($user->role) }}</span>
                        </td>
                        <td>
                            @if($user->is_protected)
                                <span class="badge badge-warning">Yes</span>
                            @else
                                <span class="text-muted">No</span>
                            @endif
                        </td>
                        <td>
                            <a class="btn btn-sm btn-info" href="{{ route('admin.users.permissions.edit', $user) }}">
                                <i class="fas fa-user-shield"></i> Edit Permissions
                            </a>
                            <form class="d-inline" action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" {{ $user->is_protected ? 'disabled' : '' }}>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No users found.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
