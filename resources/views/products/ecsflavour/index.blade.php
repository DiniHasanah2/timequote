@extends('layouts.app')

@section('content')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif




<div class="modal fade" id="servicePickerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pick Service (Category: Compute)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-2 mb-3">
          <div class="col-md-6">
            <input id="svc-search" type="text" class="form-control" placeholder="Search by name, code, description…">
          </div>
        </div>

        <div class="table-responsive" style="max-height:60vh; overflow:auto;">
          <table class="table table-sm align-middle">
            <thead class="table-light">
              <tr>
                <th style="min-width:160px;">Service Code</th>
                <th style="min-width:220px;">Product Name</th>
                <th>Description</th>
                <th style="min-width:120px;">Unit</th>
                <th style="min-width:140px;">Charge Duration</th>
                <th style="min-width:120px;">Action</th>
              </tr>
            </thead>
            <tbody id="svc-body">
              @foreach($computeServices as $svc)
                <tr>
                  <td><code>{{ $svc->code }}</code></td>
                  <td>{{ $svc->name }}</td>
                  <td class="text-muted">{{ $svc->description }}</td>
                  <td>{{ $svc->measurement_unit }}</td>
                  <td>{{ $svc->charge_duration }}</td>
                  <td>
                   
                

<button type="button"
  class="btn btn-sm btn-pink pick-service-btn"
  data-code="{{ $svc->code }}"
  data-name="{{ $svc->name }}"
  data-bs-dismiss="modal">Use</button>


                  </td>
                </tr>
              @endforeach
              @if($computeServices->isEmpty())
                <tr><td colspan="6" class="text-center text-muted">
                  No Compute services found. Add them in Products → Services first.
                </td></tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {
  // Bila klik "Use": isi field & modal auto-close (sebab ada data-bs-dismiss="modal")
  document.querySelectorAll('.pick-service-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const code = btn.dataset.code ?? '';
      const name = btn.dataset.name ?? '';

      const codeInput = document.getElementById('ecs_code');
      const nameInput = document.getElementById('flavour_name');
      if (codeInput) codeInput.value = code;
      if (nameInput) nameInput.value = name;

      // Kalau form Add masih hidden, tunjukkan
      const addForm = document.getElementById('addForm');
      if (addForm && addForm.style.display === 'none') addForm.style.display = 'block';

      // Fokus ke field pertama yang boleh edit
      document.getElementById('vCPU')?.focus();
    });
  });

  // Carian pantas dalam modal
  const q = document.getElementById('svc-search');
  const tbody = document.getElementById('svc-body');
  q?.addEventListener('input', function(){
    const term = (this.value || '').toLowerCase();
    tbody?.querySelectorAll('tr').forEach(tr => {
      tr.style.display = tr.innerText.toLowerCase().includes(term) ? '' : 'none';
    });
  });

  // (Optional) Bersih fallback kalau backdrop tertinggal
  document.addEventListener('hidden.bs.modal', () => {
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('padding-right');
  });
});
</script>


<div class="d-flex justify-content-end mb-3">
     <a href="#" class="btn btn-pink me-2" onclick="document.getElementById('addForm').style.display='block'; return false;">
        <i class=""></i> Add New
    </a>

  <!-- Upload Button to Open Modal -->
<a href="#" class="btn btn-pink me-2" data-bs-toggle="modal" data-bs-target="#importModal">
    <i class="bi bi-upload"></i> Import
</a>


    <a href="{{ route('ecs-flavours.export') }}" class="btn btn-pink">
    <i class="bi bi-download"></i> Export
</a>

   
</div>

<form method="GET" action="{{ route('ecs-flavours.index') }}" class="row g-3 mb-3">

    <div class="col-md-2">
        <label class="form-label">Type</label>
        <select name="type" class="form-select" onchange="this.form.submit()">
            <option value="">-- All Types --</option>
            @foreach ($allTypes as $type)
                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label">Generation</label>
        <select name="generation" class="form-select" onchange="this.form.submit()">
            <option value="">-- All Gen --</option>
            @foreach ($allGenerations as $gen)
                <option value="{{ $gen }}" {{ request('generation') == $gen ? 'selected' : '' }}>{{ $gen }}</option>
            @endforeach
        </select>
    </div>


    <div class="col-md-4">
        <label class="form-label">Search ECS Code</label>
        <input type="text" name="ecs_code" class="form-control" value="{{ request('ecs_code') }}" placeholder="e.g. CMPT-ECS-SHR">
    </div>

    <div class="col-md-2 align-self-end">
        <button type="submit" class="btn btn-pink w-100">Filter</button>
    </div>
</form>


{{-- ECS Flavour Table --}}
<div class="card shadow-sm">
    <div class="card-body p-3">
        <table class="table table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>ECS Service Code</th>
                    <th>Product Name</th>
                    <th>vCPU</th>
                    <th>RAM</th>
                    <th>Type</th>
                    <th>Generation</th>
                    <th>Memory Label</th>
                    <th>Windows License Count</th>
                    <th>Red Hat Enterprise License Count</th>
                    <th>DR</th>
                    <th>Pin</th>
                    <th>GPU</th>
                    <th>Dedicated Host</th>
                    <th>Microsoft SQL License Count</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <tbody>
                @forelse($ecs_flavours as $ecs_flavour)
                    <tr>
                        
                          <td>{{ $loop->iteration }}</td>
                        <td>{{ $ecs_flavour->ecs_code }}</td>
                        <td>{{ $ecs_flavour->flavour_name }}</td>
                        <td>{{ $ecs_flavour->vCPU }}</td>
                        <td>{{ $ecs_flavour->RAM }}</td>
                        <td>{{ $ecs_flavour->type }}</td>
                        <td>{{ $ecs_flavour->generation }}</td>
                        <td>{{ $ecs_flavour->memory_label }}</td>
                        <td>{{ $ecs_flavour->windows_license_count }}</td>
                        <td>{{ $ecs_flavour->red_hat_enterprise_license_count }}</td>
                        <td>
    @if ($ecs_flavour->dr)
        <span class="badge bg-success">✓</span>
    @else
        <span class="badge border border-secondary">&nbsp;</span>
    @endif
</td>

                        <td>
    @if ($ecs_flavour->pin)
        <span class="badge bg-success">✓</span>
    @else
        <span class="badge border border-secondary">&nbsp;</span>
    @endif
</td>

    <td>
    @if ($ecs_flavour->gpu)
        <span class="badge bg-success">✓</span>
    @else
        <span class="badge border border-secondary">&nbsp;</span>
    @endif
</td>

    <td>
    @if ($ecs_flavour->dedicated_host)
        <span class="badge bg-success">✓</span>
    @else
        <span class="badge border border-secondary">&nbsp;</span>
    @endif
</td>
<td>{{ $ecs_flavour->microsoft_sql_license_count }}</td>
                       
<td>
    <div class="d-flex gap-1">
        <a href="{{ route('ecs-flavours.edit', $ecs_flavour->id) }}" 
           class="btn btn-sm btn-outline-primary" title="Edit">
            <i class="bi bi-pencil"></i>
        </a>
        <form action="{{ route('ecs-flavours.destroy', $ecs_flavour->id) }}" 
              method="POST" 
              onsubmit="return confirm('Are you sure you want to delete this flavour?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                <i class="bi bi-trash"></i>
            </button>
        </form>
    </div>
</td>


                        










                    </tr>
                @empty
                    <tr>
                        <td colspan="16" class="text-center text-muted">No ecs flavour found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('ecs-flavours.import') }}" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Import ECS Flavours (CSV)</h5>
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
                
                <a href="{{ asset('assets/ecs_flavour_template.csv') }}" class="btn btn-pink mb-4" download>
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





{{-- Add New Form --}}
    <div id="addForm" class="card mt-4 shadow-sm" style="display:none;">
    <div class="card-header">
        <h5 class="mb-0">Add ECS Flavour</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('ecs-flavours.store') }}">
        @csrf
        

        <div class="mb-3">
  <label class="form-label">Service (from Products → Services: Compute)</label>
  <div class="input-group">
    <input type="text" id="ecs_code" name="ecs_code" class="form-control" placeholder="Pick from Compute services…" required readonly>
    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#servicePickerModal">
      Browse
    </button>
  </div>
  <small class="text-muted">Add a Compute service in Products → Services; it will appear here automatically.</small>
</div>

<div class="mb-3">
  <label class="form-label">Product Name</label>
  <input type="text" id="flavour_name" name="flavour_name" class="form-control bg-light" readonly>
</div>


        <!---<div class="mb-3">
        
                <label for="esc_code" class="form-label">ECS Service Code</label>
                <input type="text" name="ecs_code" id="ecs_code" class="form-control" required>
            </div>
          <div class="mb-3">
        
                <label for="flavour_name" class="form-label">Flavour Name</label>
                <input type="text" name="flavour_name" id="flavour_name" class="form-control" required>
            </div>--->
            <div class="mb-3">
                <label for="vCPU" class="form-label">vCPU</label>
                <input type="number" name="vCPU" id="vCPU" class="form-control" required min="0">
            </div>
            <div class="mb-3">
                <label for="RAM" class="form-label">RAM</label>
                <input type="number" name="RAM" id="RAM" class="form-control" required min="0">
            </div>
            <div class="mb-3">

             <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <!---<select name="name" id="category_name" class="form-select" required>--->
                  <select name="type" id="type" class="form-select" required>

                    <option value="m">m</option>
                    <option value="c">c</option>
                    <option value="r">r</option>
              
                </select>
            </div>

             <div class="mb-3">
        
                <label for="generation" class="form-label">Generation</label>
                <input type="text" name="generation" id="generation" class="form-control" required>
            </div>

              <div class="mb-3">
        
                <label for="memory_label" class="form-label">Memory Label</label>
                <input type="text" name="memory_label" id="memory_label" class="form-control" required>
            </div>

             <div class="mb-3">
                <label for="windows_license_count" class="form-label">Windows License Count</label>
                <input type="number" name="windows_license_count" id="windows_license_count" class="form-control" required min="0">
            </div>

             <div class="mb-3">
                <label for="red_hat_enterprise_license_count" class="form-label">Red Hat Enterprise License Count</label>
                <input type="number" name="red_hat_enterprise_license_count" id="red_hat_enterprise_license_count" class="form-control" required min="0">
            </div>

            <div>
               
                <input type="checkbox" name="dr" class="form-check-input" id="dr">
                <label class="form-check-label" for="pin">DR</label>
            </div>

            <div>
               
                <input type="checkbox" name="pin" class="form-check-input" id="pin">
                <label class="form-check-label" for="pin">Pin</label>
            </div>

            <div>
               
                <input type="checkbox" name="gpu" class="form-check-input" id="gpu">
                <label class="form-check-label" for="gpu">GPU</label>
            </div>

            <div>
               
                <input type="checkbox" name="dedicated_host" class="form-check-input" id="dedicated_host">
                <label class="form-check-label" for="dedicated_host">Dedicated Host</label>
            </div>
            <br>

              <div class="mb-3">
                <label for="microsoft_sql_license_count " class="form-label">Microsoft SQL License Count</label>
                <input type="number" name="microsoft_sql_license_count" id="microsoft_sql_license_count" class="form-control" required min="0">
            </div>


            <button type="submit" class="btn btn-pink">Save</button>
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
</style>
@endpush