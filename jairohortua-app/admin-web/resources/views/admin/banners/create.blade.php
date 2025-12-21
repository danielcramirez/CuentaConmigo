@extends('admin.layouts.app')

@section('content')
    <h1 class="mb-3">Create banner</h1>

    <form method="POST" action="{{ route('admin.banners.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Image URL</label>
            <input class="form-control" name="image_url" value="{{ old('image_url') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Target URL</label>
            <input class="form-control" name="target_url" value="{{ old('target_url') }}">
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Order</label>
                <input class="form-control" name="order" value="{{ old('order', 0) }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Active</label>
                <select class="form-select" name="is_active">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
        </div>
        <button class="btn btn-primary" type="submit">Create</button>
        <a class="btn btn-secondary" href="{{ route('admin.banners.index') }}">Cancel</a>
    </form>
@endsection
