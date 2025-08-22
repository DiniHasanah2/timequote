@extends('layouts.app')

@section('content')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

 @if (in_array(auth()->user()->role, ['admin', 'product']))

{{-- Add Product Button --}}
<div class="d-flex justify-content-end mb-3">
    <a href="#" class="btn btn-pink me-2" onclick="document.getElementById('addForm').style.display='block'; return false;"><i class=""></i> Add Product</a>
    
    <!---<a href="#" class="btn btn-pink me-2" data-bs-toggle="modal" data-bs-target="#importModal">
        <i class="bi bi-upload"></i> Import Data
    </a>--->
    <!---<a href="#" class="btn btn-pink" onclick="">
        <i class=""></i> Export
    </a>--->
</div>

{{-- Product Table --}}
<div class="card shadow-sm">
    <div class="card-body p-3">
        <table class="table table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>Product Unique ID</th>
                    <th>Quotation ID</th>
                    <th>Services Code</th>
                    <th>Quantity</th>
                    <th>Price Per Unit (RM)</th>
                    <th>Total Price (RM)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <tbody>
                @forelse($products as $product)
                    <tr>

                      <td class="text-break w-auto" style="max-width: 300px;">{{ $product->id }}</td>
                     <!--- <td>{{ substr((string) $product->id, 0, 8) }}</td>--->
     
        <!---<td>{{ $product->quotation_id }}</td>--->
        <td>
    @if ($product->quotation && $product->quotation->version)
        <a href="{{ route('versions.quotation.preview', $product->quotation->version->id) }}">
            {{ $product->quotation_id }}
        </a>
    @else
        {{ $product->quotation_id }}
    @endif
</td>





        <td>{{ $product->service->code ?? '-' }}</td>
        <td>{{ $product->quantity }}</td>
        <td>{{ number_format($product->priceperunit, 2) }}</td>
        <td>{{ number_format($product->totalprice, 2) }}</td>
                      <td>
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">
    <i class="bi bi-pencil"></i>
</a>

                        </td>
 
                        
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted" >No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Add Product Form --}}
<div id="addForm" class="card mt-4 shadow-sm" style="display:none;">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Add New Product</h5>
        <button type="button" class="btn-close" onclick="document.getElementById('addForm').style.display='none'"></button>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('products.store') }}">
            @csrf


<!---<div class="mb-3">
    <label for="customer_id" class="form-label">Customer Name</label>
    <select name="customer_id" id="customer_id" class="form-select" required>
        <option value="">-- Select Customer --</option>
        @foreach($customers as $customer)
            <option 
                value="{{ $customer->id }}" 
                data-department="{{ $customer->department }}"
            >
                {{ $customer->name }}
            </option>
        @endforeach
    </select>
</div>

<script>
    function filterCustomers() {
    const selectedDepartment = document.getElementById('department').value;
    const customerSelect = document.getElementById('customer_id');
    const hiddenDept = document.getElementById('filter_department');
    
    // Update hidden input value (for backend if needed)
    hiddenDept.value = selectedDepartment;

    const allOptions = customerSelect.querySelectorAll('option');

    allOptions.forEach(option => {
        const dept = option.getAttribute('data-department');
        if (!selectedDepartment || !dept || dept === selectedDepartment || option.value === '') {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });

    customerSelect.value = ""; // reset customer selection
}

</script>--->


 
            <div class="mb-3">
               <label for="quotation_id" class="form-label">Quotation</label>
<select name="quotation_id" id="quotation_id" class="form-select" required>
    <option value="">-- Select Quotation --</option>
    @foreach($availableQuotations as $q)
        <option 
            value="{{ $q->id }}" 
            data-customer="{{ $q->project->customer->id }}"
        >
            {{ $q->project->name }} - {{ $q->version->version_name }} - {{ $q->project->customer->name }}
        </option>
    @endforeach
</select>


            </div>

    <script>
document.getElementById('customer_id').addEventListener('change', function () {
    const selectedCustomerId = this.value;
    const quotationSelect = document.getElementById('quotation_id');
    const options = quotationSelect.querySelectorAll('option');

    options.forEach(option => {
        const customerId = option.getAttribute('data-customer');
        if (!selectedCustomerId || option.value === '' || customerId === selectedCustomerId) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });

    // Reset selection
    quotationSelect.value = '';
});
</script>


<div class="mb-3">
       <label for="services_id" class="form-label">Service Code</label>

    <select name="services_id" id="service_code" class="form-select" required>
    <option value="">-- Select Service Code --</option>
    @foreach($services as $service)
        <option value="{{ $service->id }}">{{ $service->code }} - {{ $service->name }}</option>
    @endforeach
</select>

</div>



<div class="mb-3">
    <label for="quantity" class="form-label">Quantity</label>
     <td><input type="number" name="quantity" class="form-control" min="0"></td>
</div>


<div class="mb-3">
    <label for="priceperunit " class="form-label">Price Per Unit</label>
     <td><input type="number" name="priceperunit" class="form-control" min="0"></td>
</div>


<div class="mb-3">
    <label for="totalprice" class="form-label">Total Price</label>
     <td><input type="number" name="totalprice" class="form-control" min="0"></td>
</div>

            
                <div>
                    <button type="submit" class="btn btn-pink">
                        <i class=""></i> Save Product
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


@endif

{{-- Auto-show form if URL has #add --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.location.hash === '#add') {
            document.getElementById('addForm').style.display = 'block';
        }
    });
</script>
{{-- Import Modal --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('products.import') }}" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Import Product (CSV)</h5>
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
                
                <a href="{{ asset('assets/products_template.csv') }}" class="btn btn-pink mb-4" download>
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