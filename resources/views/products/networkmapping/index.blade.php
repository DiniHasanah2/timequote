@extends('layouts.app')

@section('content')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="d-flex justify-content-end mb-3">
     <a href="#" class="btn btn-pink me-2" onclick="document.getElementById('addForm').style.display='block'; return false;">
        <i class=""></i> Add New
    </a>
    <a href="#" class="btn btn-pink me-2" data-bs-toggle="modal" data-bs-target="#importModal">
        <i class="bi bi-upload"></i> Import
    </a>

   <a href="{{ route('network-mappings.export') }}" class="btn btn-pink">
    <i class="bi bi-download"></i> Export
</a>

</div>

{{-- Network Mapping Table --}}
<div class="card shadow-sm">
    <div class="card-body p-3">
        <table class="table table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Network Service Code</th>
                    <th>Min Bandwidth</th>
                    <th>Max Bandwidth</th>
                    <th>EIP FOC</th>
                    <th>Anti-DDoS</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <tbody>
                @forelse($network_mappings as $network_mapping)
                    <tr>
                        
                          <td>{{ $loop->iteration }}</td>
                        <td>{{ $network_mapping->network_code }}</td>
                        <td>{{ $network_mapping->min_bw }}</td>
                        <td>{{ $network_mapping->max_bw }}</td>
                        <td>{{ $network_mapping->eip_foc }}</td>

    <td>
    @if ($network_mapping->anti_ddos)
        <span class="badge bg-success">✓</span>
    @else
        <span class="badge border border-secondary">&nbsp;</span>
    @endif
</td>

                       

                        <td>
                            <a href="{{ route('network-mappings.edit', $network_mapping->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No network mapping found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


{{-- Add New Form --}}
    <div id="addForm" class="card mt-4 shadow-sm" style="display:none;">
    <div class="card-header">
        <h5 class="mb-0">Add Network Mapping</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('network-mappings.store') }}">
        @csrf
       

        <div class="mb-3">
  <label for="network_code" class="form-label">Network Service (Cloud Network)</label>
  <div class="input-group">
    <input type="text" name="network_code" id="network_code" class="form-control" placeholder="Pick from Cloud Network services…" required readonly>
    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#netServicePickerModal">
      Browse
    </button>
  </div>
  <small class="text-muted">Add Cloud Network service in Products → Services; it will appear here.</small>
</div>











            <div class="mb-3">
                 <label for="min_bw" class="form-label">min_bw</label>
                <input type="number" name="min_bw" id="min_bw" class="form-control" required min="0">
            </div>
            <div class="mb-3">
                <label for="max_bw" class="form-label">max_bw</label>
                <input type="number" name="max_bw" id="max_bw" class="form-control" placeholder="" required min="0">
            </div>
            <div class="mb-3">
                <label for="eip_foc" class="form-label">eip_foc</label>
                <input type="number" name="eip_foc" id="eip_foc" class="form-control" required min="0">

            </div>
            <div class="mb-3">
               
                <input type="checkbox" name="anti_ddos" class="form-check-input" id="anti_ddos">
                <label class="form-check-label" for="anti_ddos">Anti-DDoS</label>
            </div>

            <button type="submit" class="btn btn-pink">Save</button>


<div class="modal fade" id="netServicePickerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pick Service (Category: Cloud Network)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="row g-2 mb-3">
          <div class="col-md-6">
            <input id="net-svc-search" type="text" class="form-control" placeholder="Search by name, code, description…">
          </div>
        </div>

        <div class="table-responsive" style="max-height:60vh; overflow:auto;">
          <table class="table table-sm align-middle">
            <thead class="table-light">
              <tr>
                <th style="min-width:160px;">Service Code</th>
                <th style="min-width:220px;">Product Name</th>
              
                <th style="min-width:120px;">Unit</th>
                <th style="min-width:140px;">Charge Duration</th>
                <th style="min-width:120px;">Action</th>
              </tr>
            </thead>
            <tbody id="net-svc-body">
              @foreach($networkServices as $svc)
                <tr>
                  <td><code>{{ $svc->code }}</code></td>
                  <td>{{ $svc->name }}</td>
               
                  <td>{{ $svc->measurement_unit }}</td>
                  <td>{{ $svc->charge_duration }}</td>
                  <td>
                    <button type="button"
                            class="btn btn-sm btn-pink pick-net-service-btn"
                            data-code="{{ $svc->code }}"
                            data-bs-dismiss="modal">Use</button>
                  </td>
                </tr>
              @endforeach
              @if($networkServices->isEmpty())
                <tr><td colspan="6" class="text-center text-muted">No Cloud Network services found.</td></tr>
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


















        </form>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', () => {
 
  document.querySelectorAll('.pick-net-service-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.getElementById('network_code').value = btn.dataset.code ?? '';
    
      const addForm = document.getElementById('addForm');
      if (addForm && addForm.style.display === 'none') addForm.style.display = 'block';
      document.getElementById('min_bw')?.focus();
    });
  });

 
  const q = document.getElementById('net-svc-search');
  const tbody = document.getElementById('net-svc-body');
  q?.addEventListener('input', function(){
    const term = (this.value || '').toLowerCase();
    tbody?.querySelectorAll('tr').forEach(tr => {
      tr.style.display = tr.innerText.toLowerCase().includes(term) ? '' : 'none';
    });
  });

  
  document.addEventListener('hidden.bs.modal', () => {
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('padding-right');
  });
});
</script>

{{-- Auto-show form if URL has #add --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.location.hash === '#add') {
            document.getElementById('addForm').style.display = 'block';
        }
    });
</script>


<!-- Upload Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('network-mappings.import') }}" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Import Network Mapping (CSV)</h5>
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
                
                <a href="{{ asset('assets/network_mappings_template.csv') }}" class="btn btn-pink mb-4" download>
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


<script>
    // to toggle import modal
    document.querySelectorAll('[onclick*="Import"]').forEach(btn => {
        btn.onclick = () => new bootstrap.Modal(document.getElementById('importModal')).show();
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