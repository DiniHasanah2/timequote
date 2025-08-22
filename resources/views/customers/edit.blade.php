@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Customer</h5>
        <button type="button" class="btn-close" onclick="window.location.href='{{ route('customers.index') }}'"></button>
    </div>
    <div class="card-body">

       @if(!$canEdit && auth()->user()->role === 'presale')
            <div class="alert alert-info">
                You can only view this customer because it belongs to another presale.
            </div>
        @endif

        <form method="POST" action="{{ route('customers.update', $customer->id) }}">
            @csrf
            @method('PUT')

            {{-- Division --}}
            <div class="mb-3">
                <label for="division" class="form-label">Division</label>
                  @if($canEdit)
                    <select name="division" id="division" class="form-select" required onchange="updateDepartments()">
                        <option value="">-- Select Division --</option>
                        <option value="Enterprise & Public Sector Business" {{ (old('division', $customer->division) == 'Enterprise & Public Sector Business') ? 'selected' : '' }}>Enterprise & Public Sector Business</option>
                        <option value="Wholesale" {{ (old('division', $customer->division) == 'Wholesale') ? 'selected' : '' }}>Wholesale</option>
                    </select>
                @else
                    <div class="form-control bg-light py-2">
                        {{ $customer->division }}
                    </div>
                    <input type="hidden" name="division" value="{{ $customer->division }}">
                @endif
            </div>

            {{-- Department --}}
            <div class="mb-3">
                <label for="department" class="form-label">Department</label>
                @if($canEdit)
                    <select name="department" id="department" class="form-select" required>
                        <option value="">-- Select Department --</option>
                       

                        {{-- Enterprise & Public Sector Business --}}
<option value="Financial Services" {{ (old('department', $customer->department) == 'Financial Services') ? 'selected' : '' }}>Financial Services</option>
<option value="Manufacturing & Automotive" {{ (old('department', $customer->department) == 'Manufacturing & Automotive') ? 'selected' : '' }}>Manufacturing & Automotive</option>
<option value="State Government" {{ (old('department', $customer->department) == 'State Government') ? 'selected' : '' }}>State Government</option>
<option value="Region-Southern & Eastern" {{ (old('department', $customer->department) == 'Region-Southern & Eastern') ? 'selected' : '' }}>Region-Southern & Eastern</option>
<option value="Hospitality & Healthcare" {{ (old('department', $customer->department) == 'Hospitality & Healthcare') ? 'selected' : '' }}>Hospitality & Healthcare</option>
<option value="GLC" {{ (old('department', $customer->department) == 'GLC') ? 'selected' : '' }}>GLC</option>
<option value="Education" {{ (old('department', $customer->department) == 'Education') ? 'selected' : '' }}>Education</option>
<option value="Banks" {{ (old('department', $customer->department) == 'Banks') ? 'selected' : '' }}>Banks</option>
<option value="Enterprise Technology" {{ (old('department', $customer->department) == 'Enterprise Technology') ? 'selected' : '' }}>Enterprise Technology</option>
<option value="Oil & Gas" {{ (old('department', $customer->department) == 'Oil & Gas') ? 'selected' : '' }}>Oil & Gas</option>
<option value="Public Sector" {{ (old('department', $customer->department) == 'Public Sector') ? 'selected' : '' }}>Public Sector</option>
<option value="Region-Northern" {{ (old('department', $customer->department) == 'Region-Northern') ? 'selected' : '' }}>Region-Northern</option>
<option value="Federal Government" {{ (old('department', $customer->department) == 'Federal Government') ? 'selected' : '' }}>Federal Government</option>
<option value="Enterprise & Public Sector Business" {{ (old('department', $customer->department) == 'Enterprise & Public Sector Business') ? 'selected' : '' }}>Enterprise & Public Sector Business</option>
<option value="Retail & Media" {{ (old('department', $customer->department) == 'Retail & Media') ? 'selected' : '' }}>Retail & Media</option>

{{-- Wholesale --}}
<option value="Wholesale" {{ (old('department', $customer->department) == 'Wholesale') ? 'selected' : '' }}>Wholesale</option>
<option value="OTT" {{ (old('department', $customer->department) == 'OTT') ? 'selected' : '' }}>OTT</option>
<option value="ASP" {{ (old('department', $customer->department) == 'ASP') ? 'selected' : '' }}>ASP</option>
<option value="Global" {{ (old('department', $customer->department) == 'Global') ? 'selected' : '' }}>Global</option>
<option value="Domestic" {{ (old('department', $customer->department) == 'Domestic') ? 'selected' : '' }}>Domestic</option>

                    </select>
                @else
                    <div class="form-control bg-light py-2">
                        {{ $customer->department }}
                    </div>
                    <input type="hidden" name="department" value="{{ $customer->department }}">
                @endif
            </div>

            {{-- Customer Name --}}
            <div class="mb-3">
                <label for="name" class="form-label">Customer Name</label>
                @if($canEdit && auth()->user()->role === 'admin')
                    <input type="text" name="name" class="form-control" value="{{ $customer->name }}" required>
                @else
                    <div class="form-control bg-light py-2">
                        {{ $customer->name }}
                    </div>
                    <input type="hidden" name="name" value="{{ $customer->name }}">
                @endif
            </div>

            {{-- Client Manager --}}
            <div class="mb-3">
                <label for="client_manager_id" class="form-label">Client Manager</label>
                @if($canEdit)
                    <input list="client_managers" 
                           name="client_manager_id" 
                           id="client_manager_id" 
                           class="form-control" 
                           required 
                           placeholder="Search client manager"
                           value="{{ old('client_manager_id', $customer->client_manager) }}" />
                    <datalist id="client_managers">
                        @foreach($clientManagers as $manager)
                            <option value="{{ $manager->name }}"></option>
                        @endforeach
                    </datalist>
                @else
                    <div class="form-control bg-light py-2">
                        {{ $customer->client_manager }}
                    </div>
                    <input type="hidden" name="client_manager_id" value="{{ $customer->client_manager }}">
                @endif
            </div>

            {{-- Presale Assignment --}}
            @if($canEdit && auth()->user()->role === 'admin')
                <div class="mb-3">
                    <label for="presale_id" class="form-label">Assign to Presale</label>
                    <select name="presale_id" id="presale_id" class="form-select" required>
                        @foreach($presales as $presale)
                            <option value="{{ $presale->id }}" {{ $customer->presale_id == $presale->id ? 'selected' : '' }}>
                                {{ $presale->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if($canEdit)
                <button type="submit" class="btn btn-pink">Save Changes</button>
            @endif
        </form>
    </div>
</div>

 @if($canEdit)
<script>
    const departments = {
        "Enterprise & Public Sector Business": [
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
        "Wholesale": [
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
        const selectedDept = "{{ old('department', $customer->department) }}";

        departmentSelect.innerHTML = '<option value="">-- Select Department --</option>';

        if (departments[division]) {
            departments[division].forEach(dept => {
                const option = document.createElement('option');
                option.value = dept;
                option.textContent = dept;
                if (dept === selectedDept) {
                    option.selected = true;
                }
                departmentSelect.appendChild(option);
            });
        }
    }

    window.addEventListener('DOMContentLoaded', updateDepartments);
</script>
@endif
@endsection
