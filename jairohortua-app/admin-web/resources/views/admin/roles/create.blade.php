@extends('admin.layouts.app')

@section('content')
    <h1 class="mb-3">Create role</h1>

    <form method="POST" action="{{ route('admin.roles.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input class="form-control" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Permissions</label>
            <div class="row">
                @foreach ($permissions as $permission)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}">
                            <label class="form-check-label">{{ $permission->name }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <button class="btn btn-primary" type="submit">Create</button>
        <a class="btn btn-secondary" href="{{ route('admin.roles.index') }}">Cancel</a>
    </form>
@endsection
