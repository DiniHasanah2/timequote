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
                        <td>{{ $network_mapping->id }}</td>
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
        
                <label for="network_code" class="form-label">Network Service Code</label>
                <input type="text" name="network_code" id="network_code" class="form-control" required>
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