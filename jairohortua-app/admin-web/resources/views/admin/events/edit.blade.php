@extends('admin.layouts.app')

@section('content')
    <h1 class="mb-3">Edit event</h1>

    <form method="POST" action="{{ route('admin.events.update', $event) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input class="form-control" name="title" value="{{ old('title', $event->title) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="3">{{ old('description', $event->description) }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Image URL</label>
            <input class="form-control" name="image_url" value="{{ old('image_url', $event->image_url) }}">
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Latitude</label>
                <input class="form-control" name="latitude" value="{{ old('latitude', $event->latitude) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Longitude</label>
                <input class="form-control" name="longitude" value="{{ old('longitude', $event->longitude) }}" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Starts at</label>
                <input class="form-control" type="datetime-local" name="starts_at" value="{{ old('starts_at', optional($event->starts_at)->format('Y-m-d\TH:i')) }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Radius (km)</label>
                <input class="form-control" name="radius_km" value="{{ old('radius_km', $event->radius_km) }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Days window</label>
                <input class="form-control" name="days_window" value="{{ old('days_window', $event->days_window) }}">
            </div>
        </div>
        <button class="btn btn-primary" type="submit">Save</button>
        <a class="btn btn-secondary" href="{{ route('admin.events.index') }}">Cancel</a>
    </form>
@endsection
