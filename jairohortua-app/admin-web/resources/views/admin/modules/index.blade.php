@extends('admin.layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Modules</h1>
        <a class="btn btn-primary" href="{{ route('admin.modules.create') }}">Create module</a>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Key</th>
                <th>Route</th>
                <th>Order</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($modules as $module)
                <tr>
                    <td>{{ $module->id }}</td>
                    <td>{{ $module->name }}</td>
                    <td>{{ $module->key }}</td>
                    <td>{{ $module->route }}</td>
                    <td>{{ $module->order }}</td>
                    <td>{{ $module->is_active ? 'Yes' : 'No' }}</td>
                    <td class="d-flex gap-2">
                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.modules.edit', $module) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.modules.destroy', $module) }}">
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
