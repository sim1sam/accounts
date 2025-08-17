@extends('adminlte::page')

@section('title', 'Create User')

@section('content_header')
    <h1>Create User</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="form-group">
                    <label for="name">Name</label>
                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                    <small class="form-text text-muted">To create the super admin, use: {{ config('permissions.super_admin_email', 'admin@gmail.com') }}</small>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" type="password" class="form-control" name="password" required>
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" class="form-control">
                        <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="supervisor" {{ old('role') === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (all menus)</option>
                    </select>
                    <small class="form-text text-muted">Only the configured super admin email will be set as Admin; others will use Staff/Supervisor.</small>
                </div>

                <div id="menu-perms" class="form-group">
                    <label>Menu Access</label>
                    <div class="row">
                        @foreach($menuKeys as $key)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="m_{{ md5($key) }}" name="menus[]" value="{{ $key }}" {{ in_array($key, (array) old('menus', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="m_{{ md5($key) }}">{{ $key }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <small class="form-text text-muted">Menu access applies to Staff and Supervisor. Admin sees all menus.</small>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
<script>
    function togglePerms(){
        const role = document.getElementById('role').value;
        const section = document.getElementById('menu-perms');
        section.style.display = role === 'admin' ? 'none' : 'block';
    }
    document.getElementById('role').addEventListener('change', togglePerms);
    togglePerms();
</script>
@endsection
