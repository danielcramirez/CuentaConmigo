@extends('admin.layouts.app')

@section('content')
    <h1 class="mb-3">Create module</h1>

    <form method="POST" action="{{ route('admin.modules.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input class="form-control" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Key</label>
            <input class="form-control" name="key" value="{{ old('key') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="2">{{ old('description') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Icon</label>
            <input class="form-control" name="icon" value="{{ old('icon') }}">
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Route</label>
                <input class="form-control" name="route" value="{{ old('route') }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Order</label>
                <input class="form-control" name="order" value="{{ old('order', 0) }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Active</label>
                <select class="form-select" name="is_active">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
        </div>
        <button class="btn btn-primary" type="submit">Create</button>
        <a class="btn btn-secondary" href="{{ route('admin.modules.index') }}">Cancel</a>
    </form>
@endsection
