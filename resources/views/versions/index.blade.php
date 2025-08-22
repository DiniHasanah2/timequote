@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>All Versions</h5>
        <a href="{{ route('versions.create') }}" class="btn btn-pink">
            <i class="bi bi-plus-circle"></i> Add New Version
        </a>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Version ID</th>
                    <th>Version Name</th>
                    <th>Project ID</th>
                    <th>Version List</th>
                    <th>Date created</th>
                    <th>Date updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($versions as $version)
                <tr>
                    <td>{{ $version->id }}</td>
                    <td>{{ $version->project->name }}</td>
                    <td>{{ $project->id }}</td>
                    <td>{{ $version->project_version }}</td>
                    <td>{{ $version->created_at }}</td>
                    <td>{{ $version->updated_at }}</td>
                    <td>
                        <a href="#" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection