@extends('layouts.app')

@section('content')

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
{{-- Add Project and Version Buttons --}}
<div class="d-flex justify-content-end mb-3">
    <a href="#" class="btn btn-pink me-2" onclick="toggleForm('addProjectForm'); return false;">
        <i class=""></i> Add Project
    </a>
    


    @if($projects->isNotEmpty())
    <a href="{{ route('projects.versions.create', $projects->first()->id) }}" class="btn btn-pink">
        <i class=""></i> Add Version
    </a>
@else
    <a href="#" class="btn btn-pink disabled">
        <i class=""></i> Add Version
    </a>
@endif
</div>

{{-- Project Table --}}
<div class="card shadow-sm">
    <div class="card-body p-3">
        <table class="table table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>Customer Name</th>
                    <th>Project Name</th>
                    <th>Project Version</th>
                    <th>Project Type</th>
                    <th>Date Created</th>
                    <th>Last Edited Date</th>
                    <th>Quotation Value (RM)</th>
                    
                     <th>Assigned Presale</th>
                    <th style="padding-left: 55px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                    @foreach($project->versions as $version)
                    <tr>
                        <td>{{ $project->customer->name }}</td>
                        <td>{{ $project->name }}</td>
                        <td>{{ $version->version_name }} (v{{ $version->version_number }})</td>
                        <td></td>
                        <td>{{ $version->created_at->format('d/m/Y') }}</td>
                        <td>{{ $version->updated_at->format('d/m/Y') }}</td>
                        <!---<td>{{ number_format($version->quotations->sum('total_amount'), 2) }}</td>--->

                        @php
    $quotation = $version->quotations->first();
@endphp
<td>
    {{ $quotation ? number_format($quotation->total_amount, 2) : '0.00' }}
</td>


                        
                      
    <td>
    @if($project->assigned_presales->isEmpty())
        <span class="text-muted">No presale assigned</span>
    @else
        @foreach ($project->assigned_presales as $presale)
            {{ $presale->username }}<br>
        @endforeach
    @endif
</td>


                        
                        <td>
                            <div class="d-flex gap-2">

                        @if(auth()->user()->role === 'admin')
<a href="{{ route('projects.assignPresalesForm', $project->id) }}" class="btn btn-sm btn-outline-primary">
    Assign To
</a>
@endif

                           
                        <a href="{{ route('versions.solution_type.create', $version->id) }}" 
   class="btn btn-sm btn-pink">
   <i class="bi bi-pencil"></i> Edit
</a>


                            <form action="{{ route('versions.destroy', $version->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Sure to delete this version?')">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted">No projects found.</td>
                </tr>
                @endforelse
            </tbody>
     
        </table>
    </div>
</div>

{{-- Add Project Form --}}
<div id="addProjectForm" class="card mt-4 shadow-sm" style="display:none;">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Add Project</h5>
        <button type="button" class="btn-close" aria-label="Close" onclick="toggleForm('addProjectForm')"></button>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('projects.store') }}">
            @csrf
            <!---<div class="mb-3">
    <label for="division" class="form-label">Division</label>
    <select name="division" id="division" class="form-select" required onchange="updateDepartments()">
         <option value="">-- Select Division --</option>
        <option value="Enterprise">Enterprise & Public Sector Business</option>
        <option value="Wholesale">Wholesale</option>
    </select>
</div>--->

<!---<div class="mb-3">
    <label for="department" class="form-label">Department</label>
    <select name="department" id="department" class="form-select" required onchange="filterCustomers()">
        <option value="">-- Select Department --</option>
    </select>
</div>
<input type="hidden" name="filter_department" id="filter_department">--->


<script>
    const departments = {
        Enterprise: [
            "Financial Services",
            "Manufacturing & Automotive",
            "State Government",
            "Region-Southern & Eastern",
            "Hospitality & Healthcare",
            //"Enterprise Product",
            "GLC",
            //"Sales Operations",
            "Education",
            //"Presales Consulting",
            //"Strategic Partnership",
            "Banks",
            "Enterprise Technology",
            //"Marketing",
            "Oil & Gas",
            "Public Sector",
            "Region-Northern",
            "Federal Government",
            "Enterprise & Public Sector Business",
            //"Insight & Value Management",
            //"Business Development",
            "Retail & Media"
        ],
        Wholesale: [
            "Wholesale",
            "OTT",
            "ASP",
            "Global",
            //"Strategy Management",
            //"Business Operations",
            //"Product & Marketing",
            "Domestic",
            //"Site Acquisition, Operations and Maintenance"
        ]
    };

    function updateDepartments() {
    const division = document.getElementById('division').value;
    const departmentSelect = document.getElementById('department');

    // Reset
    departmentSelect.innerHTML = '<option value="">-- Select Department --</option>';

    if (departments[division]) {
        departments[division].forEach(dept => {
            const option = document.createElement('option');
            option.value = dept;
            option.textContent = dept;
            departmentSelect.appendChild(option);
        });
    }

    // Trigger reset filter
    filterCustomers();
}

</script>
            


<div class="mb-3">
    <label for="customer_id" class="form-label">Customer Name</label>
    <select name="customer_id" id="customer_id" class="form-select" required>
        <option value="">-- Select Customer --</option>
        @foreach($customers as $customer)
            <option 
                value="{{ $customer->id }}" 
                data-department="{{ $customer->department }}"
            >
                {{ $customer->name }}
            </option>
        @endforeach
    </select>
</div>

<script>
    function filterCustomers() {
    const selectedDepartment = document.getElementById('department').value;
    const customerSelect = document.getElementById('customer_id');
    const hiddenDept = document.getElementById('filter_department');
    
    // Update hidden input value (for backend if needed)
    hiddenDept.value = selectedDepartment;

    const allOptions = customerSelect.querySelectorAll('option');

    allOptions.forEach(option => {
        const dept = option.getAttribute('data-department');
        if (!selectedDepartment || !dept || dept === selectedDepartment || option.value === '') {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });

    customerSelect.value = ""; // reset customer selection
}

</script>








            <div class="mb-3">
                <label for="name" class="form-label">Project Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="version_name" class="form-label">Version Name</label>
                <input type="text" name="version_name" id="version_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Version Number</label>
                <input type="text" class="form-control" value="Auto-generated" readonly style="background-color: #f0f0f0;">
                <input type="hidden" name="version_number" value="1.0">
            </div>

            @if(auth()->user()->role === 'admin')
            <div class="mb-3">
                <label for="presale_id" class="form-label">Assign to Presale</label>
                <select name="presale_id" id="presale_id" class="form-select" required>
                    @foreach($presales as $presale)
                        <option value="{{ $presale->id }}">{{ $presale->name }}</option>
                    @endforeach
                </select>
            </div>
            @else
                <input type="hidden" name="presale_id" value="{{ Auth::id() }}">
            @endif

            <button type="submit" class="btn btn-pink">Save Project</button>
            @if($projects->isNotEmpty())
            <a href="{{ route('projects.versions.create', $projects->first()->id) }}" class="btn btn-pink">
                <i class=""></i> Edit Existing Versions
            </a>
        @else
            <a href="#" class="btn btn-pink disabled">
                <i class=""></i> Edit Existing Versions
            </a>
        @endif
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize form action with first project
        const projectSelect = document.getElementById('project_id');
        if (projectSelect) {
            updateVersionFormAction(projectSelect.value);
            
            // Add event listener for project selection change
            projectSelect.addEventListener('change', function() {
                updateVersionFormAction(this.value);
                // You could add AJAX here to fetch the next version number
            });
        }

        // Handle hash in URL
        if (window.location.hash === '#add') {
            toggleForm('addProjectForm');
        } else if (window.location.hash === '#addVersion') {
            toggleForm('addVersionForm');
        }
    });

    function updateVersionFormAction(projectId) {
        const form = document.getElementById('versionForm');
        if (form) {
            form.action = `/projects/${projectId}/versions`;
            
            // Here you could add AJAX to fetch the next available version number
            // and update the hidden version_number field
        }
    }

    function toggleForm(formId) {
        const form = document.getElementById(formId);
        if (form.style.display === 'none') {
            // Hide all forms first
            document.querySelectorAll('[id$="Form"]').forEach(el => {
                el.style.display = 'none';
            });
            // Show the requested form
            form.style.display = 'block';
            
            // Update URL hash
            window.location.hash = formId === 'addProjectForm' ? '#add' : '#addVersion';
            
            // Scroll to the form
            form.scrollIntoView({ behavior: 'smooth' });
        } else {
            // Hide form and redirect to clean URL
            form.style.display = 'none';
            window.location.hash = '';
        }
    }
</script>

@endsection

@push('styles')
<style>
    .btn-pink, 
    .btn-pink:focus, 
    .btn-pink:active,
    .btn-primary,
    .btn-primary:focus,
    .btn-primary:active {
        background-color: #FF82E6 !important;
        color: white !important;
        border: none !important;
        box-shadow: none !important;
    }

    .btn-pink:hover,
    .btn-primary:hover {
        background-color: #e76ccf !important;
        color: white !important;
    }

    .btn-pink {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.25rem;
    }
</style>
@endpush
