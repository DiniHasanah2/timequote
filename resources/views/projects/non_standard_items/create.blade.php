@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@php
    $solution_type = $solution_type ?? $version->solution_type ?? null;
@endphp



@section('content')

@if($errors->any())
    <div class="alert alert-danger">
        <h5>Error!</h5>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<head>
    <style>
        /* Enable column resizing */
        table th, table td {
            position: relative;
            min-width: 100px; /* Adjust the minimum width as needed */
        }

        /* Add a resize handle on the right side of each table header */
        table th {
            cursor: ew-resize; /* Shows resize cursor on hover */
            position: relative;
        }

        .resize-handle {
            position: absolute;
            right: 0;
            top: 0;
            width: 10px;
            height: 100%;
            cursor: ew-resize;
        }

        .ratio > iframe { width: 100%; height: 100%; }

        /* hadkan lebar borang upload supaya tak panjang sangat */
.ns-upload { max-width: 460px; }

/* optional: kalau nak button full width kat mobile sahaja */
@media (max-width: 576px) {
  .ns-upload .btn { width: 100%; }
}


    </style>
</head>





<div class="card shadow-sm">
    <div class="card-header d-flex justify-between align-items-center">
        <div class="breadcrumb-text">
             <a href="{{ route('versions.solution_type.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.solution_type.create' ? 'active-link' : '' }}">Solution Type</a>
            <span class="breadcrumb-separator">»</span>
            @if(($solution_type->solution_type ?? '') !== 'MP-DRaaS Only')
            <a href="{{ route('versions.region.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.region.create' ? 'active-link' : '' }}">Professional Services</a>
            <span class="breadcrumb-separator">»</span>
            @endif
            <a href="{{ route('versions.region.network.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.region.network.create' ? 'active-link' : '' }}">Network & Global Services</a>
            <span class="breadcrumb-separator">»</span>
             <!---@if(($solution_type->solution_type ?? '') !== 'MP-DRaaS Only')
            <a href="{{ route('versions.ecs_configuration.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.ecs_configuration.create' ? 'active-link' : '' }}">ECS Configuration</a>
            <span class="breadcrumb-separator">»</span>
            @endif--->
              @if(($solution_type->solution_type ?? '') !== 'MP-DRaaS Only')
             <a href="{{ route('versions.backup.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.backup.create' ? 'active-link' : '' }}">ECS & Backup</a>
    <span class="breadcrumb-separator">»</span>
            @endif
             @if(($solution_type->solution_type ?? '') !== 'MP-DRaaS Only')
            <a href="{{ route('versions.region.dr.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.region.dr.create' ? 'active-link' : '' }}">DR Settings</a>
            <span class="breadcrumb-separator">»</span>
            @endif
              @if(($solution_type->solution_type ?? '') !== 'TCS Only')
            <a href="{{ route('versions.mpdraas.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.mpdraas.create' ? 'active-link' : '' }}">MP-DRaaS</a>
            <span class="breadcrumb-separator">»</span>
            @endif
            <a href="{{ route('versions.security_service.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.security_service.create' ? 'active-link' : '' }}">Security Services</a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.non_standard_items.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.non_standard_items.create' ? 'active-link' : '' }}">Other Services</a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.internal_summary.show', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.internal_summary.show' ? 'active-link' : '' }}">Internal Summary</a>
              <span class="breadcrumb-separator">»</span>


<!---<a href="{{ route('versions.internal_summary.save', ['version' => $version->id]) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.internal_summary.show' ? 'active-link' : '' }}">Internal Summary</a>
              <span class="breadcrumb-separator">»</span>--->










            <a href="{{ route('versions.quotation.ratecard', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.quotation.ratecard' ? 'active-link' : '' }}">Breakdown Price</a>
              <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.quotation.preview', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.quotation.preview' ? 'active-link' : '' }}">Quotation (Monthly)</a>
              <span class="breadcrumb-separator">»</span>
               <a href="{{ route('versions.quotation.annual', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.quotation.annual' ? 'active-link' : '' }}">Quotation (Annual)</a>
              <span class="breadcrumb-separator">»</span>
            <a href=" {{ route('versions.download_zip', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.download_zip' ? 'active-link' : '' }}">Download Zip File</a>
        </div>
        <button type="button" class="btn-close" style="margin-left: auto;" onclick="window.location.href='{{ route('projects.index') }}'"></button>

    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif


         @if($errors->any())
    <div class="alert alert-danger">
        <h5>Error!</h5>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif



     <div class="mb-4">
                <h6 class="fw-bold">Project</h6>
                <div class="mb-3">
                    <input type="text" class="form-control bg-light" value="{{ $project->name }}" readonly>
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="version_id" value="{{ $version->id }}">
<input type="hidden" name="customer_id" value="{{ $project->customer_id }}">
<br>




  


                   
            </div>

            
            

  

<form method="POST" action="{{ route('versions.non_standard_items.import', $version->id) }}" enctype="multipart/form-data" class="mb-3">
    @csrf
    <div class="d-flex gap-3 align-items-end">
        <div>










   <label class="form-label" >Import from Excel (.xlsx)</label>
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




 



        
        <form method="POST" action="{{ route('versions.non_standard_items.store', $version->id) }}">
            @csrf

<br>
        
            <!-- Table -->
            <div class="table-responsive mb-4" style="overflow-x: auto; white-space: nowrap;">
                <table class="table table-bordered" style="min-width: 2000px;">
                    <thead class="table-dark">

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
        

    

@php
    $allItems = $non_standard_items;
@endphp


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
                        <a href="{{ route('non_standard_items.edit', [$version->id, $item['id']]) }}" class="btn btn-sm btn-pink">
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
            <td colspan="8" class="text-muted text-center">No non-standard items found.</td>
        </tr>
    @endforelse
</tbody>




        </table>

        
<a href="#" class="btn btn-pink me-2" onclick="document.getElementById('addForm').style.display='block'; return false;">
        <i class=""></i> Add New Item
    </a>
  
  
  </div>




{{-- Add Item Form --}}
<div id="addForm" class="card mt-4 shadow-sm" style="display:none;">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Add New Item</h5>
        <button type="button" class="btn-close" onclick="document.getElementById('addForm').style.display='none'"></button>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('versions.non_standard_items.store', $version->id) }}">
            @csrf

            


<div class="mb-3">
    <label for="item_name" class="form-label">Item Name</label>
    <input type="text" name="item_name" id="item_name" class="form-control" required min="0">
</div>

    <div class="mb-3">
    <label for="unit" class="form-label">Unit</label>
    <select name="unit" id="unit" class="form-select" required>
            @foreach (['Unit', 'GB', 'Mbps', 'Pair', 'Domain', 'VM', 'Hours', 'Days'] as $opt)
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
    <input type="number" name="cost" id="cost" class="form-control" required min="0">
</div>

<div class="mb-3">
    <label for="mark_up" class="form-label">Markup (%)</label>
    <input type="number" name="mark_up" id="mark_up" class="form-control" required min="0">
</div>


<div class="mb-3">
    <label for="selling_price" class="form-label">Selling Price</label>
   <input step="any" name="selling_price" id="selling_price" class="form-control" readonly style="background-color: black; color: white;">
</div>
         
                <button type="submit" class="btn btn-pink">Save Item</button>
                
                
            </div>
            
        </form>

        
    </div>

    
</div>





       
    </div>



            

    

               <!---<div class="d-flex justify-content-between gap-3 p-2"> 

     
   
        
    <a href="{{ route('versions.security_service.create', $version->id) }}" class="btn btn-secondary" role="button">
        <i class="bi bi-arrow-left"></i> Previous Step
    </a>

            <div class="d-flex justify-content-end gap-3 p-2">
             

                

                <a href="{{ route('versions.internal_summary.show', $version->id) }}"   
                   class="btn btn-secondary me-2" 
                   role="button">
                   View Internal Summary <i class="bi bi-arrow-right"></i>
                </a> 


              
            </div>--->



                    
    
    <!---{{-- ============ Any Files Upload (PDF/CSV/TXT/IMG/Office) ============ --}} <form action="{{ route('versions.non_standard_items.files.upload', $version->id) }}" method="POST" enctype="multipart/form-data" class="mb-3 mx-auto" style="max-width: 560px;"> @csrf <label class="form-label">Import Files (PDF/CSV/TXT/Excel/PPT)</label> <div class="input-group"> <input type="file" name="ref_file" class="form-control" required accept=".pdf,.csv,.txt,.xlsx,.xls,.doc,.docx,.ppt,.pptx,.png,.jpg,.jpeg,.webp"> <button type="submit" class="btn btn-pink"> <i class="bi bi-upload"></i> Import Any Files </button> </div> </form>
    {{-- ============ Reference Files Preview ============ --}} @if(($ref_files ?? collect())->count()) <div class="card mb-4"> <div class="card-header"><strong>Reference Files</strong></div> <div class="card-body"> <div class="row g-3"> @foreach($ref_files as $f) @php $url = Storage::url($f->stored_path); $isPdf = str_starts_with($f->mime_type, 'application/pdf'); $isImg = str_starts_with($f->mime_type, 'image/'); $isCsv = ($f->ext === 'csv') || ($f->mime_type === 'text/csv'); $isTxt = ($f->ext === 'txt') || ($f->mime_type === 'text/plain'); $isOffice= in_array($f->ext, ['doc','docx','ppt','pptx','xls','xlsx']); @endphp <div class="col-12 col-md-6 col-xl-4"> <div class="card h-100 shadow-sm"> <div class="card-body"> <div class="d-flex justify-content-between align-items-start mb-2"> <div style="max-width:70%"> <div class="fw-bold text-truncate" title="{{ $f->original_name }}">{{ $f->original_name }}</div> <div class="text-muted small">{{ strtoupper($f->ext) }} • {{ number_format($f->size_bytes/1024,1) }} KB</div> </div> <div class="d-flex gap-2"> <a class="btn btn-sm btn-outline-secondary" href="{{ route('versions.non_standard_items.files.download', [$version->id, $f->id]) }}"> <i class="bi bi-download"></i> </a> <form action="{{ route('versions.non_standard_items.files.delete', [$version->id, $f->id]) }}" method="POST" onsubmit="return confirm('Delete this file?')"> @csrf @method('DELETE') <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button> </form> </div> </div> {{-- PREVIEW UI --}} @if($isImg) <img src="{{ $url }}" alt="" class="img-fluid rounded" style="max-height:220px;object-fit:cover;"> @elseif($isPdf) <div class="ratio ratio-4x3 border rounded"> <iframe src="{{ $url }}" title="PDF preview" style="border:0;"></iframe> </div> @elseif($isCsv) <details> <summary class="small mb-2">Preview CSV</summary> <object data="{{ $url }}" type="text/csv" style="width:100%;height:180px;border:1px solid #eee;"></object> <div class="small text-muted mt-1">Open full file via 
        Download.</div> </details> @elseif($isTxt) <details> <summary class="small mb-2">Preview Text</summary> <iframe src="{{ $url }}" style="width:100%;height:180px;border:1px solid #eee;"></iframe> </details> @elseif($isOffice) <div class="alert alert-info py-2 small mb-0"> Preview not supported here. Use <strong>Download</strong> to open {{ strtoupper($f->ext) }} file. </div> @else <div class="alert alert-secondary py-2 small mb-0"> Unsupported preview. Please download to view. </div> @endif </div> </div> </div> @endforeach </div> </div> </div> @endif--->



{{-- ===================== IMPORT (BETUL-BETUL ATAS BUTTONS) ===================== --}}
<form action="{{ route('versions.non_standard_items.files.upload', $version->id) }}"
      method="POST" enctype="multipart/form-data"
      class="mb-3 mx-3"> {{-- tambah mx-3 --}}
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

{{-- ===================== REFERENCE FILES PREVIEW (ATAS) ===================== --}}
@if(($ref_files ?? collect())->count())
  <div class="card mb-4 mx-3"> {{-- tambah mx-3 --}}
    <div class="card-header"><strong>Reference Files</strong></div>
    <div class="card-body">
      <div class="row g-3">
        @foreach($ref_files as $f)
          @php
            $url    = Storage::url($f->stored_path);
            $isPdf  = str_starts_with($f->mime_type, 'application/pdf');
            $isImg  = str_starts_with($f->mime_type, 'image/');
            $isCsv  = ($f->ext === 'csv') || ($f->mime_type === 'text/csv');
            $isTxt  = ($f->ext === 'txt') || ($f->mime_type === 'text/plain');
            $isOffice = in_array($f->ext, ['doc','docx','ppt','pptx','xls','xlsx']);
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
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                  </div>
                </div>

                {{-- PREVIEW TYPE --}}
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
      </div>
    </div>
  </div>
@endif

{{-- ===================== NAVIGATION BUTTONS (BAWAH) ===================== --}}
<div class="d-flex justify-content-between align-items-center mt-4 p-2 mx-3"> {{-- tambah mx-3 --}}
  {{-- Previous di kiri --}}
  <a href="{{ route('versions.security_service.create', $version->id) }}"
     class="btn btn-secondary" role="button">
    <i class="bi bi-arrow-left"></i> Previous Step
  </a>

  {{-- Next di kanan --}}
  <a href="{{ route('versions.internal_summary.show', $version->id) }}"
     class="btn btn-secondary" role="button">
    View Internal Summary <i class="bi bi-arrow-right"></i>
  </a>
</div>



        </form>


        

        
    </div>

    {{-- Auto-show form if URL has #add --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.location.hash === '#add') {
            document.getElementById('addForm').style.display = 'block';
        }
    });
</script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const costInput = document.querySelector('input[name="cost"]');
    const markUpInput = document.querySelector('input[name="mark_up"]');
    const sellingPriceInput = document.querySelector('input[name="selling_price"]');

    function calculateSellingPrice() {
        const cost = parseFloat(costInput.value) || 0;
        const markup = parseFloat(markUpInput.value) || 0;
        const sellingPrice = cost + (cost * (markup / 100));
        sellingPriceInput.value = sellingPrice.toFixed(2); // 2 decimal places
    }

    costInput.addEventListener('input', calculateSellingPrice);
    markUpInput.addEventListener('input', calculateSellingPrice);
});
</script>




@endsection

@push('styles')
<style>
	.breadcrumb-link {
    	color:rgb(105, 103, 103);
    	text-decoration: none;
	}

	.breadcrumb-link:hover {
    	text-decoration: underline;
	}

	.active-link {
    	font-weight: bold;
    	color: #FF82E6 !important; /* pink highlight */
    	text-decoration: underline;
	}

	.breadcrumb-separator {
    	color: #999;
	}
</style>
@endpush