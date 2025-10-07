@extends('layouts.app')

@section('content')

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif


{{-- Filter + Add (one row) --}}
<div>  <!---class="card mb-3"--->
  <div class="card-body py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-2">

      {{-- LEFT: Search form --}}
      <form method="GET" action="{{ route('customers.index') }}" class="d-flex flex-wrap align-items-end gap-2">
        {{-- OPTIONAL: Department filter (auto-submit) --}}
        @if(!empty($deptOptions))
        <div class="d-flex flex-column">
          <label class="form-label mb-1">Department</label>
          <select name="department" class="form-select" style="min-width:220px" onchange="this.form.submit()">
            <option value="">— All Departments —</option>
            @foreach($deptOptions as $dept)
              <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>
                {{ $dept }}
              </option>
            @endforeach
          </select>
        </div>
        @endif
        
        <div class="d-flex flex-column">
          <label class="form-label mb-1">Search Customer</label>
          <input
            type="text"
            name="q"
            class="form-control"
            style="min-width:260px"
            placeholder="Type customer name..."
            value="{{ request('q', '') }}"
          >
        </div>

        

        <div class="d-flex align-items-end">
          <button type="submit" class="btn btn-pink">Search</button>
        </div>

        @if(request()->filled('q') || request()->filled('department'))
          <div class="d-flex align-items-end">
            <a href="{{ route('customers.index') }}" class="btn btn-pink">Reset</a>
          </div>
        @endif
      </form>

      {{-- RIGHT: Add New Customer --}}
      <div class="d-flex">
        <a href="#" class="btn btn-pink" onclick="openAddForm(); return false;">
          Add New Customer
        </a>
      </div>

    </div>
  </div>
</div>



{{-- Add Customer Button --}}
<!---<div class="d-flex justify-content-end mb-3">
  <a href="#" class="btn btn-pink" onclick="openAddForm(); return false;">
    Add New Customer
  </a>
</div>--->










{{-- Customer Table --}}
<div class="card shadow-sm">
  <div class="card-body p-3">
    <table class="table table-striped mb-0">
      <thead class="table-light">
        <tr>
          <th>Customer Name</th>
          <th>Department</th>
          <th>Client Manager</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($customers as $customer)
          <tr>
            <td>{{ $customer->name }}</td>
            <td>{{ $customer->department }}</td>
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
<div id="addForm" class="card mt-4 shadow-sm" style="display:none; max-width: 10000px; margin: 0 auto;">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Add New Customer</h5>
    <button type="button" class="btn-close" onclick="document.getElementById('addForm').style.display='none'"></button>
  </div>

  <div class="card-body">
    <form method="POST" action="{{ route('customers.store') }}">
      @csrf

      {{-- Division --}}
      <div class="mb-3">
        <label for="division_display" class="form-label">Division</label>
        @php
          $user = $user ?? auth()->user();
          $isPresale = $user && $user->role === 'presale';
          $lockedDivision = $isPresale ? ($user->division) : null; // null = belum lock
        @endphp

        @if($isPresale && $lockedDivision)
          {{-- Presale locked: paparkan select disabled + hidden input untuk submit --}}
          <select id="division_display" class="form-select" disabled>
            <option value="Enterprise" {{ $lockedDivision === 'Enterprise' ? 'selected' : '' }}>
              Enterprise & Public Sector Business
            </option>
            <option value="Wholesale" {{ $lockedDivision === 'Wholesale' ? 'selected' : '' }}>
              Wholesale
            </option>
          </select>
          <input type="hidden" name="division" id="division" value="{{ $lockedDivision }}">
        @else
          {{-- Admin/Product atau Presale belum lock --}}
          <select name="division" id="division" class="form-select" required>
            <option value="">-- Select Division --</option>
            <option value="Enterprise" {{ $isPresale ? 'selected' : '' }}>
              Enterprise & Public Sector Business
            </option>
            <option value="Wholesale">Wholesale</option>
          </select>
        @endif
      </div>

      {{-- Department --}}
      <div class="mb-3">
        <label for="department" class="form-label">Department</label>
        <select name="department" id="department" class="form-select" required>
          <option value="">-- Select Department --</option>
        </select>
      </div>

      {{-- Customer Name --}}
      <div class="mb-3">
        <label for="name" class="form-label">Customer Name</label>
        <input type="text" name="name" id="name" class="form-control" required>
      </div>

      {{-- Client Manager --}}
      <div class="mb-3">
        <label for="client_manager_id" class="form-label">Client Manager</label>
        <input list="client_managers" name="client_manager_id" id="client_manager_id" class="form-control" required placeholder="Search client manager">
        <datalist id="client_managers">
          @foreach($clientManagers as $manager)
            <option value="{{ $manager->name }}"></option>
          @endforeach
        </datalist>
      </div>

      {{-- Business Number --}}
      <div class="mb-3">
        <label for="business_number" class="form-label">Business Number</label>
        <input type="text" name="business_number" id="business_number" class="form-control" required>
      </div>

      {{-- Assign Presale (Admin sahaja) --}}
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

      <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-light" onclick="document.getElementById('addForm').style.display='none'">Cancel</button>
        <button type="submit" class="btn btn-pink">Save Customer</button>
      </div>
    </form>
  </div>
</div>

{{-- ===== Scripts (1 blok sahaja) ===== --}}
<script>
  // --- Data ---
  const departments = {
    Enterprise: [
      "Financial Services","Manufacturing & Automotive","State Government",
      "Region-Southern & Eastern","Hospitality & Healthcare","GLC","Education",
      "Banks","Enterprise Technology","Oil & Gas","Public Sector","Region-Northern",
      "Federal Government","Enterprise & Public Sector Business","Retail & Media"
    ],
    Wholesale: ["Wholesale","OTT","ASP","Global","Domestic"]
  };

  // --- Helpers ---
  function getDivisionValue() {
    // Presale locked: hidden#division; Admin/Product: select#division; UI disabled: #division_display
    const hidden  = document.getElementById('division');
    const select  = document.querySelector('select#division');
    const display = document.getElementById('division_display');
    return (hidden && hidden.value) || (select && select.value) || (display && display.value) || '';
  }

  function fillDepartments(division) {
    const sel = document.getElementById('department');
    if (!sel) return;
    sel.innerHTML = '<option value="">-- Select Department --</option>';
    (departments[division] || []).forEach(d => {
      const opt = document.createElement('option');
      opt.value = d;
      opt.textContent = d;
      sel.appendChild(opt);
    });
  }

  function refreshDepartments() {
    const div = getDivisionValue();
    if (div) fillDepartments(div);
  }

  // Buka form & populate segera (hilangkan rasa "lambat")
  function openAddForm() {
    const addForm = document.getElementById('addForm');
    addForm.style.display = 'block';
    // populate selepas element visible
    requestAnimationFrame(refreshDepartments);
  }

  // Admin/Product tukar division → refresh serta-merta
  document.addEventListener('change', function (e) {
    if (e.target && e.target.id === 'division') refreshDepartments();
  });

  // Auto-buka form jika #add atau session error
  document.addEventListener('DOMContentLoaded', function () {
    @if ($errors->any() || session('error') || request()->getRequestUri() === route('customers.index') . '#add')
      openAddForm();
    @endif
  });
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

@push('scripts')
{{-- Letak select2 js di stack scripts (layout perlu @stack('scripts')) --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const cm = document.getElementById('client_manager_id');
    if (cm && $(cm).select2) {
      $('#client_manager_id').select2({ placeholder: 'Select a Client Manager', allowClear: true });
    }
  });
</script>
@endpush
