@extends('adminlte::page')

@section('title', 'Edit Permissions')

@section('content_header')
    <h1>Edit Permissions - {{ $user->name }}</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.permissions.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" class="form-control">
                        <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="supervisor" {{ $user->role === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin (all menus)</option>
                    </select>
                </div>

                <div id="menu-perms" class="form-group">
                    <label>Menu Access</label>
                    <div class="row">
                        @foreach($menuKeys as $key)
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="m_{{ md5($key) }}" name="menus[]" value="{{ $key }}" {{ in_array($key, $granted) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="m_{{ md5($key) }}">{{ $key }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <small class="form-text text-muted">Menu access applies to Staff and Supervisor. Admin sees all menus.</small>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Save</button>
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
