<form method="POST" action="{{ route('projects.store') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Customer</label>
        <select name="customer_id" class="form-control" required>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
            @endforeach
        </select>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Project Name</label>
        <input type="text" name="project_name" class="form-control" required>
    </div>
    
    <div class="mb-3">
        <label class="form-label">Version Name</label>
        <input type="text" name="version_name" class="form-control" required>
    </div>
    
    
    <button type="submit" class="btn btn-pink">Submit Project</button>
</form>