@extends('admin.layouts.app')

@section('content')
    <h1 class="mb-3">Create user</h1>

    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input class="form-control" name="username" value="{{ old('username') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input class="form-control" name="email" type="email" value="{{ old('email') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input class="form-control" name="password" type="password" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Role</label>
            <select class="form-select" name="role" required>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn btn-primary" type="submit">Create</button>
        <a class="btn btn-secondary" href="{{ route('admin.users.index') }}">Cancel</a>
    </form>
@endsection
