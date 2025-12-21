@extends('admin.layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Events</h1>
        <a class="btn btn-primary" href="{{ route('admin.events.create') }}">Create event</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Starts at</th>
                <th>Radius</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($events as $event)
                <tr>
                    <td>{{ $event->id }}</td>
                    <td>{{ $event->title }}</td>
                    <td>{{ $event->starts_at }}</td>
                    <td>{{ $event->radius_km ?? 'default' }}</td>
                    <td class="d-flex gap-2">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.events.edit', $event) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.events.destroy', $event) }}">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
