@extends('layouts.app')

@section('content')
<div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif


{{-- Version Control Section --}}
<div class="card shadow-sm mb-4" style="max-width: 680px;">
  <div class="card-header py-2">
    <h6 class="mb-0">Version Control</h6>
  </div>

  <div class="card-body py-2">
    <div class="row g-2">
      {{-- Current (system) --}}
      <div class="col-md-6">
        <div class="card border-start border-primary vc-card">
          <div class="card-body py-2">
            <h6 class="card-title text-primary mb-1" style="font-size: 0.9rem;">Current Version</h6>
            <p class="mb-0">
              <strong>{{ $currentCatalog->version_name ?? '-' }}</strong>
              @if($currentCatalog?->effective_from)
                - {{ \Carbon\Carbon::parse($currentCatalog->effective_from)->format('F Y') }}
              @endif
            </p>

            {{-- Info: what user is currently viewing --}}
            <!---<small class="text-muted d-block mt-1">
              Viewing: <strong>{{ $catalog->version_name ?? '-' }}</strong>
            </small>--->

            {{-- If viewing ≠ current, show pill + action --}}
            @if(isset($catalog) && !$catalog->is_current)
              <div class="mt-2 d-flex align-items-center gap-2 flex-wrap">
                <span class="badge-pink">You’re viewing a non-current version</span>
                <form action="{{ route('price-catalogs.makeCurrent', $catalog->id) }}" method="POST" class="m-0">
                  @csrf
                  <button class="btn btn-outline-pink btn-sm">Make current</button>
                </form>
              </div>
            @endif
          </div>
        </div>
      </div>

      {{-- Previous --}}
      <div class="col-md-6">
        <div class="card border-start border-secondary vc-card">
          <div class="card-body py-2">
            <h6 class="card-title text-secondary mb-1" style="font-size: 0.9rem;">Last Version</h6>
            <p class="mb-0">
              <strong>{{ $previousCatalog->version_name ?? '-' }}</strong>
              @if($previousCatalog?->effective_from)
                - {{ \Carbon\Carbon::parse($previousCatalog->effective_from)->format('F Y') }}
              @endif
            </p>
          </div>
        </div>
      </div>
    </div>

    <form method="GET" action="{{ route('services.index') }}" class="mt-3">
 <div class="row g-2 align-items-end">
  {{-- Kiri: Viewing Version (select) --}}
  <div class="col-12 col-md-8">
    <label class="form-label">Commit Version</label>
    <select name="catalog" class="form-select" onchange="this.form.submit()">
      @foreach($catalogs as $c)
        <option value="{{ $c->id }}" {{ ($catalog->id ?? null) == $c->id ? 'selected' : '' }}>
          {{ $c->version_name }} @if($c->is_current) (current) @endif
        </option>
      @endforeach
    </select>
  </div>

  {{-- Kanan: Butang (sebelah-sebelah) --}}
  <div class="col-12 col-md-4">
    <div class="justify-content-md-end align-items-center mt-2 mt-md-0">
      {{-- Contoh: Export + New Version (buang salah satu kalau tak perlu) --}}
     

      <button type="button" class="btn btn-outline-pink text-nowrap"
              data-bs-toggle="modal" data-bs-target="#newVersionModal">
        + New Version
      </button>
    </div>
  </div>
</div>
<br>

    <div class="col-12 col-md-4">
      <div>
     <!---class="d-flex flex-sm-nowrap flex-wrap gap-2 justify-content-md-end align-items-center mt-2 mt-md-0"--->
        <a href="{{ route('services.export', ['catalog' => $catalog->id ?? request('catalog')]) }}"
           class="btn btn-pink text-nowrap">
          <i class="bi bi-download me-1"></i>Export This Version
        </a>
      
        
      </div>
    </div>
  </div>

  
</form>

  </div>
</div>

{{-- New Version Modal --}}
<div class="modal fade" id="newVersionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="{{ route('price-catalogs.store') }}" method="POST" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">Create New Price Version</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="mb-2">
          <label class="form-label">Version Name <small class="text-muted">(unique)</small></label>
          <input type="text" name="version_name" class="form-control" placeholder="e.g. v3.2.0" required>
        </div>

        <div class="mb-2">
          <label class="form-label">Title</label>
          <input type="text" name="title" class="form-control" placeholder="August 2025 Release">
        </div>

        <div class="mb-2">
          <label class="form-label">Effective From</label>
          <input type="date" name="effective_from" class="form-control">
        </div>

        <div class="mb-2">
          <label class="form-label">Clone From</label>
          <select name="source_catalog_id" class="form-select">
            <option value="">(Start empty)</option>
            @foreach($catalogs as $c)
              <option value="{{ $c->id }}" {{ ($catalog->id ?? null)==$c->id ? 'selected' : '' }}>
                {{ $c->version_name }} @if($c->is_current) (current) @endif
              </option>
            @endforeach
          </select>
          <small class="text-muted">Choose a source version to copy all prices.</small>
        </div>

        <div class="mb-2">
          <label class="form-label">Adjust % (optional)</label>
          <input type="number" step="any" name="adjust_percent" class="form-control" placeholder="e.g. 5 for +5%, -3 for -3%">
        </div>

        <div class="form-check mt-2">
          <input class="form-check-input" type="checkbox" name="make_current" value="1" id="mkcur">
          <label class="form-check-label" for="mkcur">Make this version current after create</label>
        </div>

        <div class="mb-2">
          <label class="form-label">Notes</label>
          <textarea name="notes" class="form-control" rows="2" placeholder="release note / remark"></textarea>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-pink" type="submit">Create</button>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>








<form method="GET" action="{{ route('services.index') }}" class="row g-3 mb-3">
    <div class="col-md-2">
        <label class="form-label">Filter by Category</label>
        <select name="category" class="form-select" onchange="this.form.submit()">
            <option value="">-- All Categories --</option>
            @foreach ($allCategories as $cat)
                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>
    </div>

   <div class="col-md-2">
    <label class="form-label">Filter by Service Code</label>
    <select name="code" class="form-select" onchange="this.form.submit()">
        <option value="">-- All Service Code --</option>
        @foreach ($allServiceCode as $servicecode)
            <option value="{{ $servicecode }}" {{ request('code') == $servicecode ? 'selected' : '' }}>{{ $servicecode }}</option>
        @endforeach
    </select>
</div>


  

    <div class="col-md-2">
        <label class="form-label">Sort by</label>
        <select name="sort" class="form-select" onchange="this.form.submit()">
            <option value="">-- Default --</option>
            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A → Z)</option>
            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z → A)</option>
            <option value="price_low_high" {{ request('sort') == 'price_low_high' ? 'selected' : '' }}>Price (Low → High)</option>
            <option value="price_high_low" {{ request('sort') == 'price_high_low' ? 'selected' : '' }}>Price (High → Low)</option>
        </select>
    </div>





{{-- Add Service Button and Action Buttons --}}
    <div class="col-md-6 d-flex justify-content-end align-items-center">

     <a href="#" class="btn btn-pink me-2" onclick="document.getElementById('addForm').style.display='block'; return false;">
        <i class=""></i> Add New
    </a>
    <a href="#" class="btn btn-pink me-2" data-bs-toggle="modal" data-bs-target="#importModal">
        <i class="bi bi-upload"></i> Import
    </a>


    <a href="{{ route('services.export', ['catalog' => $catalog->id ?? request('catalog')]) }}" class="btn btn-pink">
    <i class="bi bi-download"></i> Export
</a>
</div>









</form>





{{-- Service Table --}}
<div class="card shadow-sm">
    <div class="card-body p-3">
        <table class="table table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <!---<th>SKU UUID</th>--->
                    <th>ID</th>
                    <th>Product Category</th>
                    <th>Category Code</th>
                    <th>Product Name</th>
                    <th>Service Code</th>
                    <th>Measurement Unit</th>
                    <th  style="min-width: 180px;">Product Description</th>
                    <th  style="min-width: 180px;">Price Per Unit (RM)</th>
                    <th  style="min-width: 180px;">Rate Card Price Per Unit (RM)</th>
                    <th  style="min-width: 180px;">Transfer Price Per Unit (RM)</th>
                    <th>Actions</th>
                </tr>
            </thead>
           
            <tbody>
@forelse($services as $service)
<tr>
<!---<td>
    {{ substr($service->id, 0, 8) }}
</td>--->
<td>
    {{ $loop->iteration }}
</td>


    <td>{{ $service->category_name }}</td>
    <td>{{ $service->category_code }}</td>
    <td>{{ $service->name }}</td>
    <td>{{ $service->code }}</td>
    <td>{{ $service->measurement_unit }}</td>
    <td>{{ $service->description }}</td>
    <td>{{ number_format((float)($service->v_price_per_unit ?? $service->price_per_unit), 4) }}</td>
<td>{{ number_format((float)($service->v_rate_card_price_per_unit ?? $service->rate_card_price_per_unit), 4) }}</td>
<td>{{ number_format((float)($service->v_transfer_price_per_unit ?? $service->transfer_price_per_unit), 4) }}</td>


    <!---<td>{{ number_format($service->price_per_unit, 4) }}</td>
<td>{{ number_format($service->rate_card_price_per_unit, 4) }}</td>
<td>{{ number_format($service->transfer_price_per_unit, 4) }}</td>--->
    <td>
        <a href="{{ route('services.edit', $service->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
            <i class="bi bi-pencil"></i>
        </a>
   


    <form action="{{ route('services.destroy', $service->id) }}" method="POST" style="display:inline-block;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
        <i class="bi bi-trash"></i>
    </button>
</form>
 </td>
    
</tr>

                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted">No services found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Add Service Form --}}
<div id="addForm" class="card mt-4 shadow-sm" style="display:none;">
    <div class="card-header d-flex align-items-center">
    <h5 class="mb-0">Add New Service</h5>
    <button type="button" class="btn-close ms-auto" aria-label="Close"
        onclick="window.location.href='{{ route('services.index') }}'"></button>
</div>

    <div class="card-body">
        <form method="POST" action="{{ route('services.store') }}">
           
            @csrf
             <div class="mb-3">
                <label for="category_name" class="form-label">Category Name</label>
                <select name="category_name" id="category_name" class="form-select" required onchange="setCategoryData()">
    <option value="">-- Select Category --</option>
    @foreach($categories as $category)
        <option 
            value="{{ $category->name }}" 
            data-id="{{ $category->id }}" 
            data-code="{{ $category->category_code }}">
            {{ $category->name }}
        </option>
    @endforeach
</select>
            </div>
           <input type="hidden" name="category_id" id="category_id">

<div class="mb-3">
    <label for="category_code" class="form-label">Category Code</label>
    <input type="text" name="category_code" id="category_code" class="form-control bg-light" readonly required>
</div>


<div class="mb-3">
    <label for="product_name" class="form-label">Product Name</label>
    <input type="text" name="name" id="product_name" class="form-control" required>
</div>

<div class="mb-3">
    <label for="service_code" class="form-label">Service Code</label>
    <input type="text" name="code" id="service_code" class="form-control" required>
</div>

           <div class="mb-3">
    <label for="measurement_unit" class="form-label">Measurement Unit</label>
    <select name="measurement_unit" id="measurement_unit" class="form-select" required>
        <option value="">-- Select Unit --</option>
        <option value="Unit">Unit</option>
        <option value="Cluster">Cluster</option>
        <option value="GB">GB</option>
        <option value="Mbps">Mbps</option>
    </select>
</div>


<div class="mb-3">
    <label for="description" class="form-label">Product Description</label>
    <input type="text" name="description" id="description" class="form-control" required>
</div>

            <div class="mb-3">
    <label for="price_per_unit" class="form-label">Price Per Unit</label>
    <input type="number" name="price_per_unit" id="price_per_unit" class="form-control" required min="0" step="any">
</div>

  <div class="mb-3">
    <label for="rate_card_price_per_unit" class="form-label">Rate Card Price Per Unit</label>
    <input type="number" name="rate_card_price_per_unit" id="rate_card_price_per_unit" class="form-control" required min="0" step="any">
</div>



  <div class="mb-3">
    <label for="transfer_price_per_unit" class="form-label">Transfer Price Per Unit</label>
    <input type="number" name="transfer_price_per_unit" id="transfer_price_per_unit" class="form-control" required min="0" step="any">
</div>

        
            <button type="submit" class="btn btn-pink">Submit</button>
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

     function setCategoryData() {
        const select = document.getElementById('category_name');
        const selectedOption = select.options[select.selectedIndex];

        document.getElementById('category_id').value = selectedOption.getAttribute('data-id') || '';
        document.getElementById('category_code').value = selectedOption.getAttribute('data-code') || '';
    }
</script>

<!-- Upload Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('services.import') }}" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Import Services (CSV)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <p>You can import up to 2000 rows of data at a time, any excess will be ignored.</p>

                <h6>Step 1: Download template</h6>
                <ul>
                    <li>Download the template and fill in the data according to the format</li>
                    <li>Import only the first worksheet</li>
                    <li>Do not change the header of the template to prevent import failure</li>
                </ul>
                
                <a href="{{ asset('assets/services_template.csv') }}" class="btn btn-pink mb-4" download>
    <i class="bi bi-download"></i> Download Template
</a>


                <h6>Step 2: Upload CSV</h6>
                <div class="border rounded p-4 text-center mb-3" style="background-color: #f8f9fa;">
                    <div class="mb-3 text-start">
                        <label for="csv_file" class="form-label">Choose CSV File</label>
                        <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv" required>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-pink">Upload</button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@verbatim
<script>
    document.querySelectorAll('[onclick*="Import"]').forEach(btn => {
        btn.onclick = () => new bootstrap.Modal(document.getElementById('importModal')).show();
    });
</script>
@endverbatim

</div>
@endsection

@push('styles')
<style>
   table {
    table-layout: auto !important;
    width: 100% !important;
}

table td {
    white-space: nowrap;
    overflow-x: auto;
}


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

  /* compact card body */
  .vc-card .card-body { padding: .6rem .8rem; }

  /* Pink outline button (matching .btn-pink fill) */
  .btn-outline-pink {
    background-color: transparent !important;
    color: #FF82E6 !important;
    border: 1px solid #FF82E6 !important;
    box-shadow: none !important;
  }
  .btn-outline-pink:hover {
    background-color: #FF82E6 !important;
    color: #fff !important;
  }

  /* Pink pill/badge */
  .badge-pink {
    display: inline-block;
    padding: .25rem .6rem;
    font-size: .75rem;
    border-radius: 999px;
    background-color: #FFE8F9; /* soft pink */
    color: #C84FBA;           /* text pink-ish */
    border: 1px solid #FF82E6;
    line-height: 1;
  }
  

 

</style>
@endpush