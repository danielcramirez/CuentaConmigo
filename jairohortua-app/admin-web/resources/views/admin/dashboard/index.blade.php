@extends('admin.layouts.app')

@section('content')
    <h1 class="mb-4">Dashboard</h1>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="fw-bold">Users</div>
                    <div class="display-6">{{ $usersCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="fw-bold">Events</div>
                    <div class="display-6">{{ $eventsCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="fw-bold">Banners</div>
                    <div class="display-6">{{ $bannersCount }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="fw-bold">Notifications</div>
                    <div class="display-6">{{ $notificationsCount }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
