@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Project</h5>
        <button type="button" class="btn-close" onclick="window.location.href='{{ route('projects.index') }}'"></button>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('projects.update', $project->id) }}">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label">Project Name</label>
                <input type="text" name="name" class="form-control" 
                       value="{{ $project->name }}" required>
            </div>
            
            @if($project->versions->first())
            <div class="mb-3">
                <label class="form-label">Version Name</label>
                <input type="text" name="version_name" class="form-control" 
                       value="{{ $project->versions->first()->version_name }}" required>
            </div>
            @endif
            
            <button type="submit" class="btn btn-pink">Save Changes</button>
        </form>
    </div>
</div>
@endsection