@extends('admin.layouts.app')

@section('content')
    <h1 class="mb-3">Edit banner</h1>

    <form method="POST" action="{{ route('admin.banners.update', $banner) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Image URL</label>
            <input class="form-control" name="image_url" value="{{ old('image_url', $banner->image_url) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Target URL</label>
            <input class="form-control" name="target_url" value="{{ old('target_url', $banner->target_url) }}">
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Order</label>
                <input class="form-control" name="order" value="{{ old('order', $banner->order) }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Active</label>
                <select class="form-select" name="is_active">
                    <option value="1" @selected($banner->is_active)>Yes</option>
                    <option value="0" @selected(!$banner->is_active)>No</option>
                </select>
            </div>
        </div>
        <button class="btn btn-primary" type="submit">Save</button>
        <a class="btn btn-secondary" href="{{ route('admin.banners.index') }}">Cancel</a>
    </form>
@endsection
