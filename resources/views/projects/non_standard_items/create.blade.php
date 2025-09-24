{{-- resources/views/projects/non_standard_items/create.blade.php --}}
@extends('layouts.app')

@php use Illuminate\Support\Facades\Storage; @endphp
@php
    // ====== Extra models used on this page (no controller change needed) ======
    use App\Models\Category;
    use App\Models\Service;
    use App\Models\NonStandardOffering;

    // For breadcrumbs
    $solution_type = $solution_type ?? $version->solution_type ?? null;

    // Dropdown data
    $categories = Category::orderBy('name')->get();
    $services   = Service::orderBy('name')->get(); // select * to avoid missing-column errors

    // Normalize services for the JS (unit + price resolution with safe fallbacks)
    $servicesJson = $services->map(function($s){
        $unitPrice = $s->v_rate_card_price_per_unit
            ?? $s->rate_card_price_per_unit
            ?? $s->v_price_per_unit
            ?? $s->price_per_unit
            ?? $s->monthly_rate
            ?? 0;
        return [
            'id'          => $s->id,
            'name'        => $s->name,
            'code'        => $s->code ?? null,
            'category_id' => $s->category_id,
            'unit'        => $s->measurement_unit ?? 'Unit',
            'unit_price'  => (float) $unitPrice,
        ];
    });

    // Saved offerings list
    $offering_items = NonStandardOffering::where('version_id', $version->id)->latest()->get();
@endphp

@section('content')

@if($errors->any())
    <div class="alert alert-danger">
        <h5>Error!</h5>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<head>
    <style>
        table th, table td { position: relative; min-width: 100px; }
        table th { cursor: ew-resize; position: relative; }
        .resize-handle { position: absolute; right: 0; top: 0; width: 10px; height: 100%; cursor: ew-resize; }
        .ratio > iframe { width: 100%; height: 100%; }
        .ns-upload { max-width: 460px; }
        @media (max-width: 576px) { .ns-upload .btn { width: 100%; } }

        .breadcrumb-link { color:rgb(105,103,103); text-decoration:none; }
        .breadcrumb-link:hover { text-decoration: underline; }
        .active-link { font-weight: 700; color:#FF82E6 !important; text-decoration: underline; }
        .breadcrumb-separator { color:#999; }
    </style>
</head>

<div class="card shadow-sm">
    <div class="card-header d-flex justify-between align-items-center">
        <div class="breadcrumb-text">
            <a href="{{ route('versions.solution_type.create', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.solution_type.create' ? 'active-link' : '' }}">Solution Type</a>
            <span class="breadcrumb-separator">»</span>

            @if(($solution_type->solution_type ?? '') !== 'MP-DRaaS Only')
                <a href="{{ route('versions.region.create', $version->id) }}"
                   class="breadcrumb-link {{ Route::currentRouteName() === 'versions.region.create' ? 'active-link' : '' }}">Professional Services</a>
                <span class="breadcrumb-separator">»</span>
            @endif

            <a href="{{ route('versions.region.network.create', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.region.network.create' ? 'active-link' : '' }}">Network & Global Services</a>
            <span class="breadcrumb-separator">»</span>

            @if(($solution_type->solution_type ?? '') !== 'MP-DRaaS Only')
                <a href="{{ route('versions.backup.create', $version->id) }}"
                   class="breadcrumb-link {{ Route::currentRouteName() === 'versions.backup.create' ? 'active-link' : '' }}">ECS & Backup</a>
                <span class="breadcrumb-separator">»</span>
            @endif

            @if(($solution_type->solution_type ?? '') !== 'MP-DRaaS Only')
                <a href="{{ route('versions.region.dr.create', $version->id) }}"
                   class="breadcrumb-link {{ Route::currentRouteName() === 'versions.region.dr.create' ? 'active-link' : '' }}">DR Settings</a>
                <span class="breadcrumb-separator">»</span>
            @endif

            @if(($solution_type->solution_type ?? '') !== 'TCS Only')
                <a href="{{ route('versions.mpdraas.create', $version->id) }}"
                   class="breadcrumb-link {{ Route::currentRouteName() === 'versions.mpdraas.create' ? 'active-link' : '' }}">MP-DRaaS</a>
                <span class="breadcrumb-separator">»</span>
            @endif

            <a href="{{ route('versions.security_service.create', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.security_service.create' ? 'active-link' : '' }}">Cloud Security</a>
            <span class="breadcrumb-separator">»</span>

            <a href="{{ route('versions.security_service.time.create', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.security_service.time.create' ? 'active-link' : '' }}">Time Security Services</a>
            <span class="breadcrumb-separator">»</span>

            <a href="{{ route('versions.non_standard_items.create', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.non_standard_items.create' ? 'active-link' : '' }}">Non-Standard Services</a>
            <span class="breadcrumb-separator">»</span>

            <a href="{{ route('versions.internal_summary.show', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.internal_summary.show' ? 'active-link' : '' }}">Internal Summary</a>
            <span class="breadcrumb-separator">»</span>

            <a href="{{ route('versions.quotation.ratecard', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.quotation.ratecard' ? 'active-link' : '' }}">Breakdown Price</a>
            <span class="breadcrumb-separator">»</span>

            <a href="{{ route('versions.quotation.preview', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.quotation.preview' ? 'active-link' : '' }}">Quotation (Monthly)</a>
            <span class="breadcrumb-separator">»</span>

            <a href="{{ route('versions.quotation.annual', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.quotation.annual' ? 'active-link' : '' }}">Quotation (Annual)</a>
            <span class="breadcrumb-separator">»</span>

            <a href="{{ route('versions.download_zip', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.download_zip' ? 'active-link' : '' }}">Download Zip File</a>
        </div>

        <button type="button" class="btn-close" style="margin-left: auto;"
                onclick="window.location.href='{{ route('projects.index') }}'"></button>
    </div>

    <div class="card-body">
        <div class="mb-4">
            <h6 class="fw-bold">Project</h6>
            <div class="mb-3">
                <input type="text" class="form-control bg-light" value="{{ $project->name }}" readonly>
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <input type="hidden" name="version_id" value="{{ $version->id }}">
                <input type="hidden" name="customer_id" value="{{ $project->customer_id }}">
            </div>
        </div>




      
        <!---<div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <strong>Standard Services (Non-Standard Offering)</strong>
                <small class="text-muted">Pick Category → Service, then set Quantity & Months</small>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('versions.non_standard_offerings.store', $version->id) }}" class="row g-3">
                    @csrf

                 
<div >
  <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
    <div class="row g-3">
      <div class="col-12">
        <label class="form-label">Category</label>
        <select id="off_cat" name="category_id" class="form-select" required>
          <option value="">— Select —</option>
          @foreach($categories as $c)
            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->category_code }})</option>
          @endforeach
        </select>
      </div>

      <div class="col-12">
        <label class="form-label">Service</label>
        <select id="off_svc" name="service_id" class="form-select" required></select>
        <div class="form-text">List filters by Category.</div>
      </div>

        <div class="col-md-2">
                        <label class="form-label">Unit</label>
                        <input type="text" id="off_unit" name="unit" class="form-control bg-light" readonly value="Unit">
                    </div>
    </div>
  </div>
</div>



                    

                  

                    <div class="col-md-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" id="off_qty" name="quantity" class="form-control" value="1" min="1" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Months</label>
                        <input type="number" id="off_months" name="months" class="form-control" value="1" min="1" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Unit Price / month (RM)</label>
                        <input type="number" id="off_unit_price" name="unit_price_per_month" class="form-control" step="0.0001" min="0" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Markup %</label>
                        <input type="number" id="off_markup" name="mark_up" class="form-control" value="0" min="0">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Total (Auto)</label>
                        <input type="text" id="off_total_preview" class="form-control bg-light" readonly>
                    </div>

                    <div class="col-12">
                        <button class="btn btn-pink" type="submit">Add to Offering</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===================== OFFERING LIST (SEPARATE TABLE) ===================== --}}
        <div class="card mb-4">
            <div class="card-header"><strong>Non-Standard Offering (Saved)</strong></div>
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Category</th>
                            <th>Service</th>
                            <th>Unit</th>
                            <th>Qty</th>
                            <th>Months</th>
                            <th>Unit Price/Month (RM)</th>
                            <th>Markup %</th>
                            <th>Selling Price (RM)</th>
                            <th>Action</th>
                        </tr>
                        
                    </thead>
                    <tbody>
                        @forelse($offering_items as $i => $o)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $o->category_name }} ({{ $o->category_code }})</td>
                                <td>{{ $o->service_name }} <small class="text-muted">{{ $o->service_code }}</small></td>
                                <td>{{ $o->unit }}</td>
                                <td>{{ $o->quantity }}</td>
                                <td>{{ $o->months }}</td>
                                <td>{{ number_format($o->unit_price_per_month, 4) }}</td>
                                <td>{{ number_format($o->mark_up, 2) }}</td>
                                <td>{{ number_format($o->selling_price, 2) }}</td>
                                <td class="d-flex gap-2">
                                    <a class="btn btn-sm btn-pink"
                                       href="{{ route('versions.non_standard_offerings.edit', [$version->id, $o->id]) }}">Edit</a>
                                    <form method="POST"
                                          action="{{ route('versions.non_standard_offerings.destroy', [$version->id, $o->id]) }}"
                                          onsubmit="return confirm('Delete this offering?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center text-muted">No offering added.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>--->

        <a href="#"
   class="btn me-2 mb-3 fw-semibold shadow-sm"
   style="
     --bs-btn-color:#fff;
     --bs-btn-bg:#FF82E6;
     --bs-btn-border-color:#FF82E6;
     --bs-btn-hover-color:#fff;
     --bs-btn-hover-bg:#e66fd5;
     --bs-btn-hover-border-color:#e66fd5;
     --bs-btn-focus-shadow-rgb:255,130,230;
     --bs-btn-active-color:#fff;
     --bs-btn-active-bg:#d95fc3;
     --bs-btn-active-border-color:#d95fc3;
     --bs-btn-disabled-color:#fff;
     --bs-btn-disabled-bg:#f2b7eb;
     --bs-btn-disabled-border-color:#f2b7eb;"
     
   onclick="toggleOfferingForm(true); return false;">
  Add New (Standard Services)
</a>



        {{-- ===================== OFFERING LIST (SEPARATE TABLE) ===================== --}}
<div class="table-responsive mb-4" style="overflow-x:auto; white-space:nowrap;">
  <table class="table table-bordered" style="min-width: 2000px;">
    <thead class="table-dark">
      <tr>
        <th colspan="10">Standard Services (Non-Standard Offering)</th>
      </tr>
      <tr>
        <th>No</th>
        <th>Category</th>
        <th>Service</th>
        <th>Unit</th>
        <th>Qty</th>
        <th>Months</th>
        <th>Unit Price/Month (RM)</th>
        <th>Markup %</th>
        <th>Selling Price (RM)</th>
        <th>Action</th>
      </tr>
    </thead>

    <tbody>
      @forelse($offering_items as $i => $o)
        <tr>
          <td>{{ $i + 1 }}</td>
          <td>{{ $o->category_name }} ({{ $o->category_code }})</td>
          <td>
            {{ $o->service_name }}
            <small class="text-muted">{{ $o->service_code }}</small>
          </td>
          <td>{{ $o->unit }}</td>
          <td>{{ $o->quantity }}</td>
         
          <td>{{ (int) $o->months }}</td>

          <td>{{ number_format($o->unit_price_per_month, 4) }}</td>
          <td>{{ number_format($o->mark_up, 2) }}</td>
          <td>{{ number_format($o->selling_price, 2) }}</td>
          <td class="d-flex gap-2">
            <a class="btn btn-sm btn-pink"
               href="{{ route('versions.non_standard_offerings.edit', [$version->id, $o->id]) }}">
              <i class="bi bi-pencil"></i> Edit
            </a>
            <form method="POST"
                  action="{{ route('versions.non_standard_offerings.destroy', [$version->id, $o->id]) }}"
                  onsubmit="return confirm('Delete this offering?')">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-danger">Delete</button>
            </form>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="10" class="text-center text-muted">No offering added.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>



<div id="offeringForm" class="card mb-4" style="display:none;">
  <div class="card-header d-flex justify-content-between align-items-center">
    <strong>Standard Services (Non-Standard Offering)</strong>
    <small class="text-muted">Pick Category → Service, then set Quantity &amp; Months</small>
  </div>

  <div class="card-body">
    <form method="POST" action="{{ route('versions.non_standard_offerings.store', $version->id) }}">
      @csrf

      {{-- Limit width + center --}}
      <div class="row justify-content">
        <div class="col-12 col-sm-10 col-md-9 col-lg-8 col-xl-7">

          {{-- Category (full width) --}}
        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-10">
            <label class="form-label">Category</label>
            <select id="off_cat" name="category_id" class="form-select" required>
              <option value="">— Select —</option>
              @foreach($categories as $c)
                <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->category_code }})</option>
              @endforeach
            </select>
          </div>
            </div>
            <br>

          {{-- Service + Unit (sebelah-sebelah pada md+) --}}
          <div class="row g-3 align-items-end">
            <div class="col-12 col-md-10">
              <label class="form-label">Service</label>
              <select id="off_svc" name="service_id" class="form-select" required></select>
              <div class="form-text">List filters by Category.</div>
            </div>
           
          </div>

          {{-- Qty / Months / Unit Price / Markup / Total --}}
          <div class="row g-3 mt-1">

            <div class="col-6 col-md-3">
              <label class="form-label">Unit</label>
              <input type="text" id="off_unit" name="unit" class="form-control bg-light" readonly value="Unit">
            </div>

            <div class="col-6 col-md-3">
              <label class="form-label">Quantity</label>
              <input type="number" id="off_qty" name="quantity" class="form-control" value="1" min="1" required>
            </div>
            <div class="col-6 col-md-3">
              <label class="form-label">Months</label>
        
              <input type="number" id="off_months" name="months" class="form-control" value="1" min="1" step="1" required>

              
            </div>
            <div class="col-12 col-md-3">
              <label class="form-label">Unit Price / month (RM)</label>
              <input type="number" id="off_unit_price" name="unit_price_per_month" class="form-control" step="0.0001" min="0" required>
            </div>
            <div class="col-12 col-md-3">
              <label class="form-label">Markup %</label>
              <input type="number" id="off_markup" name="mark_up" class="form-control" value="0" min="0">
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label">Total (Auto)</label>
              <input type="text" id="off_total_preview" class="form-control bg-light" readonly>
            </div>
          </div>

          <div class="d-flex gap-2 mt-4">
            <button class="btn btn-pink" type="submit">Add to Offering</button>
            <button type="button" class="btn btn-outline-secondary"
                    onclick="toggleOfferingForm(false)">Cancel</button>
          </div>

        </div>
      </div>
    </form>
  </div>
</div>

<script>
  function toggleOfferingForm(show) {
    const el = document.getElementById('offeringForm');
    el.style.display = show ? 'block' : 'none';
    if (show) {
      // optional: fokus pada Category
      setTimeout(() => document.getElementById('off_cat')?.focus(), 0);
    } else {
      // optional: reset field kalau nak
      // document.querySelector('#offeringForm form')?.reset();
    }
  }
</script>





<br><br>


         {{-- ===================== IMPORT EXCEL (for Non-Standard Items) ===================== --}}
        <form method="POST" action="{{ route('versions.non_standard_items.import', $version->id) }}"
              enctype="multipart/form-data" class="mb-4">
            @csrf
            <div class="d-flex gap-3 align-items-end">
                <div>
                    <label class="form-label">Import Non-Standard Items from Excel (.xlsx)</label>
                    <input type="file" name="import_file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-pink me-2">
                    <i class="bi bi-upload"></i> Import Excel
                </button>
                <a href="{{ asset('storage/Non_Standard_Template.xlsx') }}" class="btn btn-pink me-2">
                    <i class="bi bi-download"></i> Download Template
                </a>
            </div>
        </form>

         
        {{-- ===================== NON-STANDARD ITEMS (EXISTING TABLE) ===================== --}}
        <div class="table-responsive mb-4" style="overflow-x:auto; white-space:nowrap;">

           <a href="#"
   class="btn me-2 mb-3 fw-semibold shadow-sm"
   style="
     --bs-btn-color:#fff;
     --bs-btn-bg:#FF82E6;
     --bs-btn-border-color:#FF82E6;
     --bs-btn-hover-color:#fff;
     --bs-btn-hover-bg:#e66fd5;
     --bs-btn-hover-border-color:#e66fd5;
     --bs-btn-focus-shadow-rgb:255,130,230;
     --bs-btn-active-color:#fff;
     --bs-btn-active-bg:#d95fc3;
     --bs-btn-active-border-color:#d95fc3;
     --bs-btn-disabled-color:#fff;
     --bs-btn-disabled-bg:#f2b7eb;
     --bs-btn-disabled-border-color:#f2b7eb;
   "
   onclick="document.getElementById('addForm').style.display='block'; return false;">
  Add New (Non-Standard Item)
</a>


           
            <table class="table table-bordered" style="min-width: 2000px;">
                
                <thead class="table-dark">
                      <tr>
                        <th colspan="8">3rd Party (Non-Standard Items)</th>
                       
                    </tr>
                    <tr>
                        <th>No</th>
                        <th>Item Name</th>
                        <th>Unit</th>
                        <th>Quantity</th>
                        <th>Cost (RM)</th>
                        <th>Markup (%)</th>
                        <th>Selling Price (RM)</th>
                        <th>Action</th>
                    </tr>
                  
                </thead>
                @php $allItems = $non_standard_items; @endphp
                <tbody>
                    @forelse ($allItems as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['item_name'] ?? '' }}</td>
                            <td>{{ $item['unit'] ?? '' }}</td>
                            <td>{{ $item['quantity'] ?? '' }}</td>
                            <td>{{ number_format($item['cost'] ?? 0, 2) }}</td>
                            <td>{{ (int) ($item['mark_up'] ?? 0) }}</td>
                            <td>{{ number_format($item['selling_price'] ?? 0, 2) }}</td>
                            <td>
                                @if(isset($item['id']))
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('non_standard_items.edit', [$version->id, $item['id']]) }}"
                                           class="btn btn-sm btn-pink">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('non_standard_items.destroy', ['version' => $version->id, 'item' => $item['id']]) }}"
                                              method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="badge bg-secondary">Imported</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-muted text-center">No non-standard items added.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>

        {{-- ===================== ADD ITEM FORM (EXISTING FLOW) ===================== --}}
        <div id="addForm" class="card mt-4 shadow-sm" style="display:none;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Add New Item</h5>
                <button type="button" class="btn-close"
                        onclick="document.getElementById('addForm').style.display='none'"></button>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('versions.non_standard_items.store', $version->id) }}">
                    @csrf

                    <div class="mb-3">
                        <label for="item_name" class="form-label">Item Name</label>
                        <input type="text" name="item_name" id="item_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="unit" class="form-label">Unit</label>
                        <select name="unit" id="unit" class="form-select" required>
                            @foreach (['Unit','GB','Mbps','Pair','Domain','VM','Hours','Days'] as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" required min="0">
                    </div>

                    <div class="mb-3">
                        <label for="cost" class="form-label">Cost (RM)</label>
                        <input type="number" name="cost" id="cost" class="form-control" required min="0" step="0.01">
                    </div>

                    <div class="mb-3">
                        <label for="mark_up" class="form-label">Markup (%)</label>
                        <input type="number" name="mark_up" id="mark_up" class="form-control" required min="0" step="0.01">
                    </div>

                    <div class="mb-3">
                        <label for="selling_price" class="form-label">Selling Price</label>
                        <input step="any" name="selling_price" id="selling_price" class="form-control" readonly
                               style="background-color: black; color: white;">
                    </div>

                    <button type="submit" class="btn btn-pink">Save Item</button>
                </form>
            </div>
        </div>

        <br>

        
   
        {{-- ===================== NAVIGATION BUTTONS ===================== --}}
        <div class="d-flex justify-content-between align-items-center mt-4 p-2 mx-3">
            <a href="{{ route('versions.security_service.time.create', $version->id) }}"
               class="btn btn-secondary" role="button">
                <i class="bi bi-arrow-left"></i> Previous Step
            </a>

            <a href="{{ route('versions.internal_summary.show', $version->id) }}"
               class="btn btn-secondary" role="button">
                View Internal Summary <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        
    </div> {{-- card-body --}}

        
        {{-- ===================== Any-file upload for reference ===================== --}}
        <form action="{{ route('versions.non_standard_items.files.upload', $version->id) }}"
              method="POST" enctype="multipart/form-data" class="mb-3 mx-3">
            @csrf
            <label class="form-label mb-1">Import Files (PDF/CSV/TXT/Excel/PPT)</label>
            <div class="input-group" style="max-width: 640px;">
                <input type="file" name="ref_file" class="form-control" required
                       accept=".pdf,.csv,.txt,.xlsx,.xls,.doc,.docx,.ppt,.pptx,.png,.jpg,.jpeg,.webp">
                <button type="submit" class="btn btn-pink">
                    <i class="bi bi-upload"></i> Import Any Files
                </button>
            </div>
        </form>

        @if(($ref_files ?? collect())->count())
            <div class="card mb-4 mx-3">
                <div class="card-header"><strong>Reference Files</strong></div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($ref_files as $f)
                            @php
                                $url     = Storage::url($f->stored_path);
                                $isPdf   = str_starts_with($f->mime_type, 'application/pdf');
                                $isImg   = str_starts_with($f->mime_type, 'image/');
                                $isCsv   = ($f->ext === 'csv') || ($f->mime_type === 'text/csv');
                                $isTxt   = ($f->ext === 'txt') || ($f->mime_type === 'text/plain');
                                $isOffice= in_array($f->ext, ['doc','docx','ppt','pptx','xls','xlsx']);
                            @endphp
                            <div class="col-12 col-md-6 col-xl-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div style="max-width:70%">
                                                <div class="fw-bold text-truncate" title="{{ $f->original_name }}">{{ $f->original_name }}</div>
                                                <div class="text-muted small">{{ strtoupper($f->ext) }} • {{ number_format($f->size_bytes/1024,1) }} KB</div>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <a class="btn btn-sm btn-outline-secondary"
                                                   href="{{ route('versions.non_standard_items.files.download', [$version->id, $f->id]) }}">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <form action="{{ route('versions.non_standard_items.files.delete', [$version->id, $f->id]) }}"
                                                      method="POST" onsubmit="return confirm('Delete this file?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </div>

                                        @if($isImg)
                                            <img src="{{ $url }}" alt="" class="img-fluid rounded" style="max-height:220px;object-fit:cover;">
                                        @elseif($isPdf)
                                            <div class="ratio ratio-4x3 border rounded">
                                                <iframe src="{{ $url }}" title="PDF preview" style="border:0;"></iframe>
                                            </div>
                                        @elseif($isCsv)
                                            <details>
                                                <summary class="small mb-2">Preview CSV</summary>
                                                <object data="{{ $url }}" type="text/csv" style="width:100%;height:180px;border:1px solid #eee;"></object>
                                                <div class="small text-muted mt-1">Open full file via Download.</div>
                                            </details>
                                        @elseif($isTxt)
                                            <details>
                                                <summary class="small mb-2">Preview Text</summary>
                                                <iframe src="{{ $url }}" style="width:100%;height:180px;border:1px solid #eee;"></iframe>
                                            </details>
                                        @elseif($isOffice)
                                            <div class="alert alert-info py-2 small mb-0">
                                                Preview not supported here. Use <strong>Download</strong> to open {{ strtoupper($f->ext) }} file.
                                            </div>
                                        @else
                                            <div class="alert alert-secondary py-2 small mb-0">
                                                Unsupported preview. Please download to view.
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div> {{-- row --}}
                </div>
            </div>
        @endif

</div> {{-- card --}}

{{-- ===================== Scripts ===================== --}}
<script>
    // Auto-show Add form if URL has #add
    document.addEventListener('DOMContentLoaded', function () {
        if (window.location.hash === '#add') {
            const f = document.getElementById('addForm');
            if (f) f.style.display = 'block';
        }
    });
</script>

<script>
    // Auto-calc Selling Price for Add New Item (existing flow)
    document.addEventListener('DOMContentLoaded', function() {
        const costInput = document.querySelector('input[name="cost"]');
        const markUpInput = document.querySelector('input[name="mark_up"]');
        const sellingPriceInput = document.querySelector('input[name="selling_price"]');
        if (!costInput || !markUpInput || !sellingPriceInput) return;

        function calculateSellingPrice() {
            const cost = parseFloat(costInput.value) || 0;
            const markup = parseFloat(markUpInput.value) || 0;
            const sellingPrice = cost + (cost * (markup / 100));
            sellingPriceInput.value = sellingPrice.toFixed(2);
        }
        costInput.addEventListener('input', calculateSellingPrice);
        markUpInput.addEventListener('input', calculateSellingPrice);
    });
</script>

<script>
    // Non-Standard Offering dynamic dropdown + total calculator
    document.addEventListener('DOMContentLoaded', function() {
        const services = @json($servicesJson);

        const $cat    = document.getElementById('off_cat');
        const $svc    = document.getElementById('off_svc');
        const $unit   = document.getElementById('off_unit');
        const $qty    = document.getElementById('off_qty');
        const $months = document.getElementById('off_months');
        const $price  = document.getElementById('off_unit_price');
        const $markup = document.getElementById('off_markup');
        const $total  = document.getElementById('off_total_preview');

        function num(x){ return isFinite(+x) ? +x : 0; }

        function computeTotal() {
            const base = num($qty?.value) * num($months?.value) * num($price?.value);
            const tot  = base * (1 + num($markup?.value)/100);
            if ($total) $total.value = tot.toFixed(2);
        }

        function filterServices() {
            const catId = $cat?.value;
            const list = (services || []).filter(s => s.category_id === catId);
            if (!$svc) return;

            if (!list.length) {
                $svc.innerHTML = '<option value="">— No services —</option>';
                if ($unit)  $unit.value  = 'Unit';
                if ($price) $price.value = (0).toFixed(4);
                computeTotal();
                return;
            }

            $svc.innerHTML = list.map(s =>
                `<option value="${s.id}" data-unit="${s.unit}" data-price="${s.unit_price}">
                    ${s.name}${s.code ? ' ('+s.code+')' : ''}
                 </option>`
            ).join('');

            // default select first
            if ($unit)  $unit.value  = list[0].unit || 'Unit';
            if ($price) $price.value = Number(list[0].unit_price || 0).toFixed(4);
            computeTotal();
        }

        function onServiceChange() {
            const opt = $svc?.selectedOptions?.[0];
            if (!opt) return;
            if ($unit)  $unit.value  = opt.dataset.unit || 'Unit';
            if ($price) $price.value = Number(opt.dataset.price || 0).toFixed(4);
            computeTotal();
        }

        if ($cat)   $cat.addEventListener('change', filterServices);
        if ($svc)   $svc.addEventListener('change', onServiceChange);
        [$qty,$months,$price,$markup].forEach(el => el && el.addEventListener('input', computeTotal));

        // init
        if ($cat) filterServices();
    });
</script>
@endsection
