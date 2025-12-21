@extends('admin.layouts.app')

@section('content')
    <h1 class="mb-3">Settings</h1>

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Notification radius (km)</label>
                <input class="form-control" name="notification_radius_km" value="{{ old('notification_radius_km', optional($settings->get('notification_radius_km'))->value) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Notification days window</label>
                <input class="form-control" name="notification_days_window" value="{{ old('notification_days_window', optional($settings->get('notification_days_window'))->value) }}" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Facebook URL</label>
                <input class="form-control" name="social_facebook_url" value="{{ old('social_facebook_url', optional($settings->get('social_facebook_url'))->value) }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Instagram URL</label>
                <input class="form-control" name="social_instagram_url" value="{{ old('social_instagram_url', optional($settings->get('social_instagram_url'))->value) }}">
            </div>
        </div>
        <button class="btn btn-primary" type="submit">Save</button>
    </form>
@endsection
