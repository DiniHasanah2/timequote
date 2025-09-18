@extends('layouts.app')

@section('content')
@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $solution_type = $solution_type ?? $version->solution_type ?? null;
@endphp

@if($isLocked)
  <div class="alert alert-warning d-flex align-items-center" role="alert">
    <span class="me-2">🔒</span>
    <div>
      This version was locked at
      <strong>{{ optional($lockedAt)->format('d M Y, H:i') }}</strong>.
      All fields are read-only.
    </div>
  </div>
@endif







<div class="card shadow-sm">
    <!---<div class="card-header d-flex justify-between align-items-center">--->
        <div class="card-header d-flex justify-content-between align-items-center">

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
            <a href="{{ route('versions.security_service.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.security_service.create' ? 'active-link' : '' }}">Cloud Security</a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.security_service.time.create', $version->id) }}"
   class="breadcrumb-link {{ Route::currentRouteName() === 'versions.security_service.time.create' ? 'active-link' : '' }}">
  Time Security Services
</a>
<span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.non_standard_items.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.non_standard_items.create' ? 'active-link' : '' }}">Non-Standard Services</a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.internal_summary.show', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.internal_summary.show' ? 'active-link' : '' }}">Internal Summary</a>
              <span class="breadcrumb-separator">»</span>
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
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif


        
    
       <form method="POST" action="{{ route('versions.security_service.store', $version->id) }}">
            @csrf
             <input type="hidden" name="section" value="time">
            @if(isset($region) && $region)
                @method('PUT')
            @endif

          
           
 <div class="mb-4">
                <h6 class="fw-bold">Project</h6>
                <div class="mb-3">
                    <input type="text" class="form-control bg-light" value="{{ $project->name }}" readonly>
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="version_id" value="{{ $version->id }}">
<input type="hidden" name="customer_id" value="{{ $project->customer_id }}">



                   
            </div>
              


                            
    <table class="table table-bordered w-auto">

         <tr>
            <td class="bg-light fw-bold text-center" style="font-size:14px;">Production</td>
           <td>
    <div class="input-group" style="width:135px;">
        <input name="region" 
               class="form-control bg-white border-0 auto-save"  
               data-field="region" 
               data-version-id="{{ $version->id }}" 
               value="{{ $solution_type->production_region ?? '' }}" 
               readonly style="font-size:14px;">
        <input type="hidden" name="region" value="{{ $solution_type->production_region ?? '' }}">
    </div>
</td>

        </tr>
         <tr>
            <td class="bg-light fw-bold text-center" style="font-size:14px;">DR</td>
             <td>
                <div class="input-group" style="width:135px;">
        <input name="region" 
               class="form-control bg-white border-0 auto-save"  
               data-field="region" 
               data-version-id="{{ $version->id }}" 
               value="{{ $solution_type->dr_region ?? '' }}" 
               readonly style="font-size:14px;">
        <input type="hidden" name="region" value="{{ $solution_type->dr_region ?? '' }}">
    </div>
             </td>
        </tr>
    </table>

                   
            
        <fieldset @disabled($isLocked)>

            
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                  
                      
                        


                       
                
                          <thead class="table-dark">
                        <tr>
                            <th colspan="4">Monitoring</th>
                        </tr>
                    </thead>
                    <thead class="table-light">
                        <tr>
                            <th colspan="2"></th>
                           
                            <th>KL</th>
                            <th>Cyber</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>TIME Security Advanced Monitoring (TSAM)</td>
                            <td>EPS</td>
                            <td>
                                <div class="input-group">
                                    <input name="" 
                                           class="form-control bg-light text-muted" 
                                           value=""
                                           disabled
                                           style="cursor: not-allowed;">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input name="" 
                                           class="form-control bg-light text-muted" 
                                           value=""
                                           disabled
                                           style="cursor: not-allowed;">
                                </div>
                            </td>
                        </tr>
                        <tr>
                           <td>{{ $pricing['CMON-TIS-NOD-STD']['name'] }}
                                <br>
                                 <small class="text-muted">This service is undergoing costing evaluation by TDC, not to be offered as standard</small>
                            </td>
                           


                            
    <td>{{ $pricing['CMON-TIS-NOD-STD']['measurement_unit'] }}</td>
                           
                          <td>
                                    <div class="input-group">
                                       <select name="kl_insight_vmonitoring" class="form-select">
    <option value="No" @selected(old('kl_insight_vmonitoring', $security_service->kl_insight_vmonitoring ?? '') == 'No')>No</option>
    <option value="Yes" @selected(old('kl_insight_vmonitoring', $security_service->kl_insight_vmonitoring ?? '') == 'Yes')>Yes</option>
</select>
                                    </div>
                                </td>

                                <td>
                                    <div class="input-group">
                                       <select name="cyber_insight_vmonitoring" class="form-select">
    <option value="No" @selected(old('cyber_insight_vmonitoring', $security_service->cyber_insight_vmonitoring ?? '') == 'No')>No</option>
    <option value="Yes" @selected(old('cyber_insight_vmonitoring', $security_service->cyber_insight_vmonitoring ?? '') == 'Yes')>Yes</option>
</select>
                                    </div>
                                </td>


                        </tr>
                    
                          
                        <thead class="table-dark">
                            <tr>
                                <th colspan="4">Security Service</th>
                                
                            </tr>
                        </thead>
                        <thead class="table-light">
                            <tr>
                                <th colspan="2"></th>
                                <th>KL</th>
                                <th>Cyber</th>
                            </tr>
                        </thead>

                        <tr>
                            <!---<td>Cloud Vulnerability Assessment (Per IP)</td>
                            <td>GB</td>--->

                             <td>{{ $pricing['SECT-VAS-EIP-STD']['name'] }}</td>
    <td>{{ $pricing['SECT-VAS-EIP-STD']['measurement_unit'] }}</td>
    
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_cloud_vulnerability" class="form-control"
                                     value="{{ old('kl_cloud_vulnerability', $security_service->kl_cloud_vulnerability ?? '') }}" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="cyber_cloud_vulnerability" class="form-control"
                                     value="{{ old('cyber_cloud_vulnerability', $security_service->cyber_cloud_vulnerability ?? '') }}" min="0">
                                </div>
                            </td>
                        </tr>

                
                    </tbody>
                </table>
            </div>
              </fieldset>




            <div class="d-flex justify-content-between gap-3"> 

            <a href="{{ route('versions.security_service.create', $version->id) }}" class="btn btn-secondary" role="button">
        <i class="bi bi-arrow-left"></i> Previous Step
    </a>
      
  <div class="d-flex flex-column align-items-centre gap-2">
    

            <div class="d-flex justify-content-end gap-3"> 
                <button type="submit" class="btn btn-pink"  @disabled($isLocked)>Save Time Security Service</button>

                  <a href="{{ route('versions.non_standard_items.create', $version->id) }}" class="btn btn-secondary me-2" role="button">Next: Non-Standard Services<i class="bi bi-arrow-right"></i></a>

               
              
            </div>
                 <div class="alert alert-danger py-1 px-2 small mb-0" role="alert" style="font-size: 0.8rem;">
            ⚠️ Ensure you click <strong>Save</strong> before continuing to the next step!
    </div>

    </div>
        </form>
    </div>
</div>

<br>

        {{-- ===================== Any-file upload for reference ===================== --}}
        <form action="{{ route('versions.security_service.time.files.upload', $version->id) }}"
              method="POST" enctype="multipart/form-data" class="mb-3">
            @csrf
            <label class="form-label mb-1">Attach Files (PDF/CSV/TXT/Excel/Word/PPT/Images)</label>
            <div class="input-group" style="max-width: 680px;">
                <input type="file" name="ref_file" class="form-control" required
                       accept=".pdf,.csv,.txt,.xlsx,.xls,.doc,.docx,.ppt,.pptx,.png,.jpg,.jpeg,.webp">
                <button type="submit" class="btn btn-pink">
                    <i class="bi bi-upload"></i> Upload
                </button>
            </div>
        </form>

        @if(($ref_files ?? collect())->count())
            <div class="card mb-4">
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
                                                   href="{{ route('versions.security_service.time.files.download', [$version->id, $f->id]) }}">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <form action="{{ route('versions.security_service.time.files.delete', [$version->id, $f->id]) }}"
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

        <br>

@if($isLocked)
<script>
document.addEventListener('DOMContentLoaded', () => {
  
  document.querySelectorAll('.auto-save').forEach(el => {
    el.addEventListener('change', e => e.preventDefault(), true);
    el.addEventListener('input',  e => e.preventDefault(), true);
  });
});
</script>
@endif



@endsection
@push('styles')
<style>
  .breadcrumb-text .breadcrumb-link {
    color: rgb(105, 103, 103);
    text-decoration: none;
  }
  .breadcrumb-text .breadcrumb-link:hover {
    text-decoration: underline;
  }

  /* Active = PINK */
  .breadcrumb-text .breadcrumb-link.active-link {
    font-weight: 700;
    color: #FF82E6 !important; /* pink highlight */
    text-decoration: underline;
  }

  .breadcrumb-text .breadcrumb-separator {
    color: #999;
  }
</style>
@endpush













