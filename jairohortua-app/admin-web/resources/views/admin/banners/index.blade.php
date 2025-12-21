@extends('admin.layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Banners</h1>
        <a class="btn btn-primary" href="{{ route('admin.banners.create') }}">Create banner</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Target URL</th>
                <th>Order</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($banners as $banner)
                <tr>
                    <td>{{ $banner->id }}</td>
                    <td>{{ $banner->image_url }}</td>
                    <td>{{ $banner->target_url }}</td>
                    <td>{{ $banner->order }}</td>
                    <td>{{ $banner->is_active ? 'Yes' : 'No' }}</td>
                    <td class="d-flex gap-2">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.banners.edit', $banner) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}">
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
