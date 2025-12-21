@extends('admin.layouts.app')

@section('content')
    <h1 class="mb-3">Edit module</h1>

    <form method="POST" action="{{ route('admin.modules.update', $module) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input class="form-control" name="name" value="{{ old('name', $module->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Key</label>
            <input class="form-control" name="key" value="{{ old('key', $module->key) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="2">{{ old('description', $module->description) }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Icon</label>
            <input class="form-control" name="icon" value="{{ old('icon', $module->icon) }}">
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Route</label>
                <input class="form-control" name="route" value="{{ old('route', $module->route) }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Order</label>
                <input class="form-control" name="order" value="{{ old('order', $module->order) }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Active</label>
                <select class="form-select" name="is_active">
                    <option value="1" @selected($module->is_active)>Yes</option>
                    <option value="0" @selected(!$module->is_active)>No</option>
                </select>
            </div>
        </div>
        <button class="btn btn-primary" type="submit">Save</button>
        <a class="btn btn-secondary" href="{{ route('admin.modules.index') }}">Cancel</a>
    </form>
@endsection
