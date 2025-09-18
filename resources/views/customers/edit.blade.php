@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Edit Customer</h5>
    <button type="button" class="btn-close" onclick="window.location.href='{{ route('customers.index') }}'"></button>
  </div>

  <div class="card-body">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if(!$canEdit && auth()->user()->role === 'presale')
      <div class="alert alert-info">
        You can only view this customer because it belongs to another presale.
      </div>
    @endif

    @php
      $auth = auth()->user();
      $isPresale = $auth->role === 'presale';

      // Canonicalize division value to 'Enterprise' | 'Wholesale'
      $divValue = old('division', $customer->division);
      if ($divValue === 'Enterprise & Public Sector Business') { $divValue = 'Enterprise'; }
    @endphp

    <form method="POST" action="{{ route('customers.update', $customer->id) }}">
      @csrf
      @method('PUT')

      {{-- Division --}}
      <div class="mb-3">
        <label for="division" class="form-label">Division</label>

        @if($isPresale)
          {{-- Presale: cannot change division (locked) --}}
          <select id="division_display" class="form-select" disabled>
            <option value="Enterprise" {{ $divValue === 'Enterprise' ? 'selected' : '' }}>
              Enterprise & Public Sector Business
            </option>
            <option value="Wholesale" {{ $divValue === 'Wholesale' ? 'selected' : '' }}>
              Wholesale
            </option>
          </select>
          <input type="hidden" name="division" id="division" value="{{ $divValue }}">
        @else
          {{-- Admin/Product: can change --}}
          <select name="division" id="division" class="form-select" required>
            <option value="">-- Select Division --</option>
            <option value="Enterprise" {{ $divValue === 'Enterprise' ? 'selected' : '' }}>
              Enterprise & Public Sector Business
            </option>
            <option value="Wholesale" {{ $divValue === 'Wholesale' ? 'selected' : '' }}>
              Wholesale
            </option>
          </select>
        @endif
      </div>

      {{-- Department (populated via JS) --}}
      <div class="mb-3">
        <label for="department" class="form-label">Department</label>
        <select name="department" id="department" class="form-select" required>
          <option value="">-- Select Department --</option>
        </select>
      </div>

      {{-- Customer Name --}}
      <div class="mb-3">
        <label for="name" class="form-label">Customer Name</label>
        @if($canEdit && !$isPresale) {{-- Admin/Product boleh tukar nama --}}
          <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $customer->name) }}" required>
        @else
          <div class="form-control bg-light py-2">{{ $customer->name }}</div>
          <input type="hidden" name="name" value="{{ $customer->name }}">
        @endif
      </div>

      {{-- Client Manager (datalist by name) --}}
      <div class="mb-3">
        <label for="client_manager_id" class="form-label">Client Manager</label>
        @if($canEdit)
          <input
            list="client_managers"
            name="client_manager_id"
            id="client_manager_id"
            class="form-control"
            required
            placeholder="Search client manager"
            value="{{ old('client_manager_id', $customer->client_manager) }}"
          />
          <datalist id="client_managers">
            @foreach($clientManagers as $manager)
              <option value="{{ $manager->name }}"></option>
            @endforeach
          </datalist>
        @else
          <div class="form-control bg-light py-2">{{ $customer->client_manager }}</div>
          <input type="hidden" name="client_manager_id" value="{{ $customer->client_manager }}">
        @endif
      </div>

      {{-- Presale Assignment (Admin/Product only) --}}
      @if($canEdit && !$isPresale)
        <div class="mb-3">
          <label for="presale_id" class="form-label">Assign to Presale</label>
          <select name="presale_id" id="presale_id" class="form-select" required>
            @foreach($presales as $presale)
              <option value="{{ $presale->id }}" {{ (old('presale_id', $customer->presale_id) == $presale->id) ? 'selected' : '' }}>
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
{{-- Populate Department list based on Division --}}
<script>
  const departments = {
    Enterprise: [
      "Financial Services","Manufacturing & Automotive","State Government",
      "Region-Southern & Eastern","Hospitality & Healthcare","GLC","Education",
      "Banks","Enterprise Technology","Oil & Gas","Public Sector","Region-Northern",
      "Federal Government","Enterprise & Public Sector Business","Retail & Media"
    ],
    Wholesale: ["Wholesale","OTT","ASP","Global","Domestic"]
  };

  function canonDivision() {
    // Presale (hidden), or Admin/Product (select). Also read disabled display if needed.
    const hidden = document.getElementById('division');
    const select = document.querySelector('select#division');
    const display = document.getElementById('division_display');
    if (hidden && hidden.value) return hidden.value;
    if (select && select.value) return select.value;
    if (display && display.value) return display.value;
    return '';
  }

  function fillDepartments(division, selected) {
    const sel = document.getElementById('department');
    if (!sel) return;
    sel.innerHTML = '<option value="">-- Select Department --</option>';
    (departments[division] || []).forEach(d => {
      const opt = document.createElement('option');
      opt.value = d; opt.textContent = d;
      if (selected && d === selected) opt.selected = true;
      sel.appendChild(opt);
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    const selectedDept = @json(old('department', $customer->department));
    const divVal = canonDivision(); // 'Enterprise' | 'Wholesale'
    if (divVal) fillDepartments(divVal, selectedDept);
  });

  // When Admin/Product changes division
  document.addEventListener('change', function (e) {
    if (e.target && e.target.id === 'division') {
      fillDepartments(e.target.value, null);
    }
  });
</script>
@endif
@endsection
