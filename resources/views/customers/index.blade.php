@extends('layouts.app')

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif


{{-- Add Customer Button --}}
<div class="d-flex justify-content-end mb-3">
    <a href="#" class="btn btn-pink" onclick="document.getElementById('addForm').style.display='block'; return false;">
        <i class=""></i> Add New Customer
    </a>
</div>
{{-- Customer Table --}}
<div class="card shadow-sm">
    <div class="card-body p-3">
        <table class="table table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>Department</th>
                    <th>Customer Name</th>
                    <th>Client Manager</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->department }}</td>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->clientManager->name ?? '-' }}</td>
                        <td>
                            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-pink">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Add Customer Form --}}
<div id="addForm" class="card mt-4 shadow-sm" style="display:none;">
    <div class="card-header  d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Add New Customer</h5>
        <button type="button" class="btn-close" onclick="document.getElementById('addForm').style.display='none'"></button>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('customers.store') }}">
            @csrf
            <div class="mb-3">
    <label for="division" class="form-label">Division</label>
    <select name="division" id="division" class="form-select" required onchange="updateDepartments()">
         <option value="">-- Select Division --</option>
        <option value="Enterprise">Enterprise & Public Sector Business</option>
        <option value="Wholesale">Wholesale</option>
    </select>
</div>

<div class="mb-3">
    <label for="department" class="form-label">Department</label>
    <select name="department" id="department" class="form-select" required>
        <option value="">-- Select Department --</option>
    </select>
</div>

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
        const industrySelect = document.getElementById('department');

        // Clear current options
        industrySelect.innerHTML = '<option value="">-- Select Department --</option>';

        if (departments[division]) {
            departments[division].forEach(dept => {
                const option = document.createElement('option');
                option.value = dept;
                option.textContent = dept;
                industrySelect.appendChild(option);
            });
        }
    }
</script>

          
            
            <div class="mb-3">
                <label for="name" class="form-label">Customer Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <!---<div class="mb-3">
    <label for="client_manager_division" class="form-label">Client Manager Division</label>
    <select id="client_manager_division" class="form-select" onchange="filterClientManagers()">
        <option value="">-- Filter by Division --</option>
        <option value="Enterprise">Enterprise & Public Sector Business</option>
        <option value="Wholesale">Wholesale</option>
    </select>
</div>--->
     
          <div class="mb-3">
    <label for="client_manager_id" class="form-label">Client Manager</label>
  <input list="client_managers" name="client_manager_id" id="client_manager_id" class="form-control" required placeholder="Search client manager">
<datalist id="client_managers">
    @foreach($clientManagers as $manager)
        <option value="{{ $manager->name }}"></option>
    @endforeach
</datalist>
</div>

      
      
      
            <div class="mb-3">
                <label for="business_number" class="form-label">Business Number</label>
                <input type="text" name="business_number" id="business_number" class="form-control" required>
            </div>

            {{-- Show presale dropdown for admin only --}}
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

            <button type="submit" class="btn btn-pink">Save Customer</button>
        </form>
    </div>
</div>

{{-- Auto-show form if URL has #add --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.location.hash === '#add') {
            document.getElementById('addForm').style.display = 'block';
        }
    });
</script>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#client_manager_id').select2({
            placeholder: 'Select a Client Manager',
            allowClear: true
        });
    });
</script>
@endpush

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.location.hash === '#add' || '{{ session('error') }}') {
            document.getElementById('addForm').style.display = 'block';
        }
    });
</script>

<script>
function filterClientManagers() {
    const selectedDivision = document.getElementById('client_manager_division').value;
    const datalist = document.getElementById('client_managers');
    const allOptions = [...datalist.options];

    // Clear all options
    datalist.innerHTML = '';

    allOptions.forEach(option => {
        const division = option.getAttribute('data-division');
        if (!selectedDivision || division === selectedDivision) {
            datalist.appendChild(option.cloneNode(true));
        }
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

    .btn-pink:hover,
    .btn-primary:hover {
        background-color: #e76ccf !important;
        color: white !important;
    }

    /* Additional styling for button size */
    .btn-pink {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        line-height: 1.5;
        border-radius: 0.25rem;

        
    }
    .form-select {
    appearance: auto;
    -webkit-appearance: auto;
    -moz-appearance: auto;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
}
    
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush