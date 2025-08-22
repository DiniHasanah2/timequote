@extends('layouts.app')

@section('content')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

{{-- Action Buttons --}}
<div class="d-flex justify-content-end mb-3 gap-2">
    {{-- Add Solution Button --}}
    <a href="#" class="btn btn-pink" onclick="document.getElementById('solutionForm').style.display='block'; setFormAction('{{ route('solutions.store') }}', 'Add Solution'); return false;">
        <i></i> Add Solution
    </a>

    {{-- View Solutions Button (if needed) --}}
    <a href="{{ route('solutions.index') }}" class="btn btn-pink">
        <i></i> View Solution
    </a>
</div>
         
{{-- Solution Table --}}
<div class="card shadow-sm">
    <div class="card-body p-3">
        <table class="table table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>Customer Name</th>
                    <th>Project Name</th>
                    <th>Version Name</th>
                    <th>Status</th>
                    <th>Quotation Project ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
@forelse ($solutions as $solution)
    <tr>
        <td>{{ $solution->customer_name }}</td>
        <td>{{ $solution->project_name }}</td>
        <td>{{ $solution->version_name }}</td>
        <td>{{ $solution->status }}</td>
        <td>{{ $solution->quotation_id }}</td>
        <td>
    <!---<a href="{{ route('versions.quotation.preview', $solution->version_id) }}" class="btn btn-sm btn-outline-primary" title="View Quotation">
        <i class="bi bi-eye"></i> View
    </a>--->

    <a href="{{ route('versions.quotation.preview', ['version' => $solution->version_id, 'viewonly' => 1]) }}" 
   class="btn btn-sm btn-outline-primary" title="View Quotation">
    <i class="bi bi-eye"></i> View
</a>

</td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center">No solutions found.</td>
    </tr>
@endforelse
</tbody>


        </table>
    </div>
</div>

{{-- Solution Form (Reusable for Add/Edit) --}}
<div id="solutionForm" class="card mt-4 shadow-sm" style="display:none;">
    <div class="card-header">
        <h5 class="mb-0" id="formTitle">Solution Form</h5>
    </div>
    <div class="card-body">
        <form id="dynamicForm" method="POST">
            @csrf
            <div id="formMethod"></div> {{-- For method spoofing on edit --}}
            
            <div class="mb-3">
               <label for="quotation_id" class="form-label">Select Quotation</label>
<select name="quotation_id" id="quotation_id" class="form-select" required>
    <option value="">-- Select Quotation --</option>
    @foreach($availableQuotations as $q)
        <option value="{{ $q->id }}">
            {{ $q->project->name }} - {{ $q->version->version_name }} - {{ $q->project->customer->name }}
        </option>
    @endforeach
</select>

            </div>

            {{-- Show presale dropdown for admin only --}}
            @if(auth()->user()->role === 'admin')
            <div class="mb-3">
                <label for="presale_id" class="form-label">Assign to Presale</label>
                <select name="presale_id" id="presale_id" class="form-control" required>
                    <option value="">Select Presale</option>
                    @foreach($presales as $presale)
                        <option value="{{ $presale->id }}">{{ $presale->name }}</option>
                    @endforeach
                </select>
            </div>
            @else
                <input type="hidden" name="presale_id" value="{{ Auth::id() }}">
            @endif

            <div class="d-flex justify-content-end mt-4">
                <button type="button" class="btn btn-secondary me-2" 
                        onclick="document.getElementById('solutionForm').style.display='none'">
                    Cancel
                </button>
                <button type="submit" class="btn btn-pink">Save Solution</button>
            </div>
        </form>
    </div>
</div>

{{-- Auto-show form if URL has #add --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show form if URL has #add
        if (window.location.hash === '#add') {
            document.getElementById('solutionForm').style.display = 'block';
            setFormAction("{{ route('solutions.store') }}", "Add Solution");
        }
        
        // Initialize form elements
        initSolutionForm();
    });

    function setFormAction(action, title, method = 'POST') {
        const form = document.getElementById('dynamicForm');
        const titleElement = document.getElementById('formTitle');
        const methodInput = document.getElementById('formMethod');
        
        form.action = action;
        titleElement.textContent = title;
        
        // Clear previous method input
        methodInput.innerHTML = '';
        
        // For edit forms, add method spoofing
        if (method !== 'POST') {
            methodInput.innerHTML = '@method("PUT")';
        }
    }

    function showEditForm(solutionId, updateUrl) {
        // Fetch solution data via AJAX and populate form
        fetch(`/solutions/${solutionId}/edit`)
            .then(response => response.json())
            .then(data => {
                // Populate form fields with data
                document.getElementById('industry').value = data.industry;
                document.getElementById('presale_id').value = data.presale_id;
                
                // Update form action
                setFormAction(updateUrl, "Edit Solution", "PUT");
                
                // Show form
                document.getElementById('solutionForm').style.display = 'block';
            });
    }

    function initSolutionForm() {
        // Initialize solution options
        const solutionSelect = document.getElementById('industry');
        
        // Add sample options (replace with your actual data)
        const options = [
            {value: 'cloud', text: 'Cloud Solution'},
            {value: 'security', text: 'Security Solution'},
            {value: 'network', text: 'Network Solution'},
        ];
        
        options.forEach(option => {
            const opt = document.createElement('option');
            opt.value = option.value;
            opt.textContent = option.text;
            solutionSelect.appendChild(opt);
        });
    }
</script>
@endsection

@push('styles')
<style>
    /* Pink Button Styles */
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

    .btn-outline-pink {
        color: #FF82E6;
        border-color: #FF82E6;
    }

    .btn-outline-pink:hover {
        background-color: #FF82E6;
        color: white;
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