@extends('layouts.app')

@section('content')
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif


{{-- Add Category Button and Action Buttons --}}

<div class="d-flex justify-content-end mb-3">

{{-- Only show if user role is "product" --}}
    <!---@if (auth()->user()->role === 'product')
        <a href="#" class="btn btn-pink me-2" onclick="document.getElementById('addForm').style.display='block'; return false;">
            <i class=""></i> Add New Category
        </a>
    @endif--->


    @if (in_array(auth()->user()->role, ['admin', 'product']))
    <a href="#" class="btn btn-pink me-2" onclick="document.getElementById('addForm').style.display='block'; return false;">
        <i class=""></i> Add New Category
    </a>
@endif







    <a href="#" class="btn btn-pink me-2" data-bs-toggle="modal" data-bs-target="#importModal">
    <i class="bi bi-upload"></i> Import
</a>
  

    <a href="{{ route('categories.export') }}" class="btn btn-pink">
    <i class="bi bi-download"></i> Export
</a>

</div>

{{-- Category Table --}}
<div class="card shadow-sm">
    <div class="card-body p-3">
        <table class="table table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>Category Name</th>
                    <th>Category Code</th>
                    <th>Category Unique ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
        
                @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->category_code }}</td>
                        <td>{{ $category->id }}</td>
                        <td>

                        @if (in_array(auth()->user()->role, ['admin', 'product']))
    <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-pink">
        <i class="bi bi-pencil"></i> Edit
    </a>

    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Sure to delete?')">
            <i class="bi bi-trash"></i> Delete
        </button>
    </form>
@endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No categories found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


<!-- Upload Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('categories.import') }}" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Import Category (CSV)</h5>
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
                
                <a href="{{ asset('assets/categories_template.csv') }}" class="btn btn-pink mb-4" download>
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







{{-- Add Category Form --}}
<div id="addForm" class="card mt-4 shadow-sm" style="display:none;">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Add New Product Category</h5>
        <button type="button" class="btn-close" onclick="document.getElementById('addForm').style.display='none'"></button>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('categories.store') }}">
            @csrf

    <div class="mb-3">
    <label for="category_name" class="form-label">Category Name</label>
    <input type="text" name="name" id="category_name" class="form-control" required>
</div>

<div class="mb-3">
    <label for="category_code" class="form-label">Category Code</label>
    <input type="text" name="code" id="category_code" class="form-control" required>
</div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-pink">Save Category</button>
                
                
            </div>
        </form>
    </div>


<!---<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.location.hash === '#add') {
            document.getElementById('addForm').style.display = 'block';
        }
    });

        function setCategoryCode() {
        const mapping = {
            "Cloud Professional Services": "CPFS",
            "Cloud Managed Service": "CMNS",
            "Cloud Network": "CNET",
            "Compute": "CMPT",
            "Licenses": "CLIC",
            "Storage": "STRG",
            "Cloud Backup and DR": "CBDR",
            "Cloud Security": "CSEC",
            "Cloud Data Protection": "CDPT",
            "Cloud Monitoring": "CMON",
            "Security": "SECT",
            "Other Services (3rd Party)": "C3PP",
            "Multi-Platform DRaaS": "CMDR"
        };

        const selectedName = document.getElementById('category_name').value;
        const codeField = document.getElementById('category_code');

        codeField.value = mapping[selectedName] || '';
    }

    

</script>--->



@endsection

@push('styles')
<style>
    .btn-pink {
        background-color: #FF82E6 !important;
        color: white !important;
        border: none !important;
    }
    .btn-pink:hover {
        background-color: #e76ccf !important;
    }
</style>
@endpush