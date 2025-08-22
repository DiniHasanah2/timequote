@extends('layouts.app')

@section('content')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if(isset($warning))
    <div class="alert alert-warning">
        {{ $warning }}
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Add Version</h5>
        <button type="button" class="btn-close" onclick="window.location.href='{{ route('projects.index') }}'"></button>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ isset($project) ? route('projects.versions.store', $project->id) : '' }}">
            @csrf

           
            <div class="mb-3">
                <label for="customer_id" class="form-label">Customer</label>
                <select name="customer_id" id="customer_id" class="form-select" required 
                    onchange="updateProjects(this.value)">
                  
                    @foreach($projects->unique('customer_id') as $proj)
                        <option value="{{ $proj->customer->id }}" 
                            @if(isset($project) && $proj->customer_id == $project->customer_id) selected @endif>
                            {{ $proj->customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="project_id" class="form-label">Project</label>
                <select name="project_id" id="project_id" class="form-select" required>
                    @if(isset($project))
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}" {{ $proj->id == $project->id ? 'selected' : '' }}>
                                {{ $proj->name }}
                            </option>
                        @endforeach
                    @else
                        <option value="">Select Project</option>
                    @endif
                </select>
            </div>

            <div class="mb-3">
                <label for="version_name" class="form-label">Version Name</label>
                <input type="text" name="version_name" id="version_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Version Number</label>
                <input type="text" class="form-control" value="Auto-Generated" readonly style="background-color: #f0f0f0;">
            </div>

            <button type="submit" class="btn btn-pink">Save Version</button>
        </form>
    </div>
</div>

<script>
function updateProjects(customerId) {
    fetch(`/api/customers/${customerId}/projects`)
        .then(response => response.json())
        .then(data => {
            const projectSelect = document.getElementById('project_id');
            projectSelect.innerHTML = '';
            
            data.forEach(project => {
                const option = document.createElement('option');
                option.value = project.id;
                option.textContent = project.name;
                projectSelect.appendChild(option);
            });
            
            // Update form action
            if (data.length > 0) {
                document.querySelector('form').action = 
                    `/projects/${data[0].id}/versions`;
            }
        });
}
</script>
@endsection