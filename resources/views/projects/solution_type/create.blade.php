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
    $solType  = old('solution_type', $solution_type->solution_type ?? null);
    $drRegion = old('dr_region', $solution_type->dr_region ?? null);

    // Tunjuk DR link hanya jika:
    // - BUKAN MP-DRaaS Only
    // - DAN dr_region wujud & bukan "None"
    $showDrCrumb = ($solType !== 'MP-DRaaS Only') 
                   && !empty($drRegion) 
                   && strcasecmp($drRegion, 'None') !== 0;
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
              <!---@if(($solution_type->solution_type ?? '') !== 'MP-DRaaS Only')
           
            <a href="{{ route('versions.region.dr.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.region.dr.create' ? 'active-link' : '' }}">DR Settings</a>
            <span class="breadcrumb-separator">»</span>
            @endif--->


            {{-- DR Settings crumb --}}
<span id="crumb-dr-item" class="{{ $showDrCrumb ? '' : 'd-none' }}">
    <a href="{{ route('versions.region.dr.create', $version->id) }}" 
       class="breadcrumb-link {{ Route::currentRouteName() === 'versions.region.dr.create' ? 'active-link' : '' }}">
       DR Settings
    </a>
    <span class="breadcrumb-separator">»</span>
</span>




















              @if(($solution_type->solution_type ?? '') !== 'TCS Only')
            <a href="{{ route('versions.mpdraas.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.mpdraas.create' ? 'active-link' : '' }}">MP-DRaaS</a>
            <span class="breadcrumb-separator">»</span>
            @endif
            <a href="{{ route('versions.security_service.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.security_service.create' ? 'active-link' : '' }}">Managed Services & Cloud Security</a>
            <span class="breadcrumb-separator">»</span>
               <a href="{{ route('versions.security_service.time.create', $version->id) }}"
   class="breadcrumb-link {{ Route::currentRouteName() === 'versions.security_service.time.create' ? 'active-link' : '' }}">
  Time Security Services
</a>
<span class="breadcrumb-separator">»</span>


   <a href="{{ route('versions.non_standard_offerings.create', $version->id) }}"
   class="breadcrumb-link {{ Route::currentRouteName() === 'versions.non_standard_offerings.create' ? 'active-link' : '' }}">
  Standard Services
</a>
<span class="breadcrumb-separator">»</span>

            <a href="{{ route('versions.non_standard_items.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.non_standard_items.create' ? 'active-link' : '' }}">3rd Party (Non-Standard)</a>
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



        
    
       <form method="POST" action="{{ route('versions.solution_type.store', $version->id) }}">
            @csrf
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
              <fieldset @disabled($isLocked)>
            
            <!-- Solution Type Table -->
            <div class="table-responsive mb-4">
               
                <table class="table table-bordered">
                    


                    <thead class="table-dark">
                        <tr>
                            <th colspan="5">Solution Type</th>  
                        </tr>
                    </thead>                    
                    <tbody>
                        <tr>
    <td>Select Solution Type</td>
    <td colspan="4"> 
        <div class="input-group">
            <select name="solution_type" id="solution_type" class="form-select">
                <option value="TCS Only" @selected(old('solution_type', $solution_type->solution_type ?? 'TCS Only') == 'TCS Only')>TCS Only</option>
                <option value="MP-DRaaS Only" @selected(old('solution_type', $solution_type->solution_type ?? '') == 'MP-DRaaS Only')>MP-DRaaS Only</option>
                <option value="Both" @selected(old('solution_type', $solution_type->solution_type ?? '') == 'Both')>Both</option>
            </select>
        </div>


       
    </td>

<input type="hidden" name="dr_region" id="dr_region_hidden">

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hidden = document.getElementById('dr_region_hidden');
    function syncHidden() {
        const val = (function() {
            const tcs = document.querySelector('#tcs_only_table:not(.d-none) select[data-name="dr_region"]');
            const both = document.querySelector('#both_table:not(.d-none) select[data-name="dr_region"]');
            return (tcs && tcs.value) || (both && both.value) || '';
        })();
        if (hidden) hidden.value = val;
    }

    document.querySelectorAll('select[data-name="dr_region"]').forEach(sel => {
        sel.addEventListener('change', syncHidden);
    });

    // juga sync bila solution type tukar (jadual switch)
    const solTypeSelect = document.getElementById('solution_type');
    if (solTypeSelect) solTypeSelect.addEventListener('change', syncHidden);

    syncHidden();
});
</script>










</tr>


            
                    </tbody>

                            
                </table>




                   {{-- Region header --}}
   

@php
    $selectedType = old('solution_type', $solution_type->solution_type ?? '');
@endphp

{{-- TCS ONLY TABLE --}}
<div id="tcs_only_table" class="solution-table d-none">
    <table class="table table-bordered">


 <thead class="table-dark">
                        <tr>
                            <th colspan="4">Region</th>  
                        </tr>
                    </thead>  
        <thead class="table-light">
            <tr>
                <th>Solution Type</th>
                <th>Production</th>
                <th>DR</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>TCS Only</td>
             <td>
                    <select data-name="production_region" class="form-select">
                <option value="None" @selected(old('production_region', $solution_type->production_region ?? '') == 'None')>None</option>
                <option value="Kuala Lumpur" @selected(old('production_region', $solution_type->production_region ?? '') == 'Kuala Lumpur')>Kuala Lumpur</option>
                <option value="Cyberjaya" @selected(old('production_region', $solution_type->production_region ?? '') == 'Cyberjaya')>Cyberjaya</option>
            </select>
                </td>
                <td>
                    <select data-name="dr_region" class="form-select">
                <option value="None" @selected(old('dr_region', $solution_type->dr_region ?? '') == 'None')>None</option>
                <option value="Kuala Lumpur" @selected(old('dr_region', $solution_type->dr_region ?? '') == 'Kuala Lumpur')>Kuala Lumpur</option>
                <option value="Cyberjaya" @selected(old('dr_region', $solution_type->dr_region ?? '') == 'Cyberjaya')>Cyberjaya</option>
            </select>
                </td>
                
               



            </tr>
        </tbody>
    </table>
</div>

{{-- MP-DRaaS ONLY TABLE --}}
<div id="mpdr_only_table" class="solution-table d-none">
    <table class="table table-bordered">


 <thead class="table-dark">
                        <tr>
                            <th colspan="4">Region</th>  
                        </tr>
                    </thead>  
        <thead class="table-light">
            <tr>
               <th>Solution Type</th>
                <th>MP-DRaaS</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>MP-DRaaS Only</td>
               <td>
                    <select data-name="mpdraas_region" class="form-select">
                <option value="None" @selected(old('mpdraas_region', $solution_type->mpdraas_region ?? '') == 'None')>None</option>
                <option value="Kuala Lumpur" @selected(old('mpdraas_region', $solution_type->mpdraas_region ?? '') == 'Kuala Lumpur')>Kuala Lumpur</option>
                <option value="Cyberjaya" @selected(old('mpdraas_region', $solution_type->mpdraas_region ?? '') == 'Cyberjaya')>Cyberjaya</option>
            </select>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- BOTH TABLE --}}
<div id="both_table" class="solution-table d-none">
    <table class="table table-bordered">

 <thead class="table-dark">
                        <tr>
                            <th colspan="5">Region</th>  
                        </tr>
                    </thead>  
        <thead class="table-light">
            <tr>
                <th>Solution Type</th>
                <th>Production</th>
                <th>DR</th>
                <th>MP-DRaaS</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Both</td>
                <td>
                    <select data-name="production_region" class="form-select">
                <option value="None" @selected(old('production_region', $solution_type->production_region ?? '') == 'None')>None</option>
                <option value="Kuala Lumpur" @selected(old('production_region', $solution_type->production_region ?? '') == 'Kuala Lumpur')>Kuala Lumpur</option>
                <option value="Cyberjaya" @selected(old('production_region', $solution_type->production_region ?? '') == 'Cyberjaya')>Cyberjaya</option>
            </select>
                </td>
                <td>
                    <select data-name="dr_region" class="form-select">
                <option value="None" @selected(old('dr_region', $solution_type->dr_region ?? '') == 'None')>None</option>
                <option value="Kuala Lumpur" @selected(old('dr_region', $solution_type->dr_region ?? '') == 'Kuala Lumpur')>Kuala Lumpur</option>
                <option value="Cyberjaya" @selected(old('dr_region', $solution_type->dr_region ?? '') == 'Cyberjaya')>Cyberjaya</option>
            </select>
                </td>
                <td>
                    <select data-name="mpdraas_region" class="form-select">
                <option value="None" @selected(old('mpdraas_region', $solution_type->mpdraas_region ?? '') == 'None')>None</option>
                <option value="Kuala Lumpur" @selected(old('mpdraas_region', $solution_type->mpdraas_region ?? '') == 'Kuala Lumpur')>Kuala Lumpur</option>
                <option value="Cyberjaya" @selected(old('mpdraas_region', $solution_type->mpdraas_region ?? '') == 'Cyberjaya')>Cyberjaya</option>
            </select>
                </td>
            </tr>
        </tbody>
    </table>
</div>

</fieldset>


                
            
       <div class="d-flex justify-content-between">
    <!-- Button left side -->
    <div>
    </div> 
    <div class="d-flex flex-column align-items-centre gap-2">    

             <div class="d-flex justify-content-end gap-3"> <!-- Added gap-3 for spacing -->
                <button type="submit" class="btn btn-pink" @disabled($isLocked)>Save Solution Type</button>

            
        

                     @if(($solution_type->solution_type ?? '') === 'MP-DRaaS Only')
        <a href="{{ route('versions.region.network.create', $version->id) }}"   
                   class="btn btn-secondary me-2" 
                   role="button">
                   Next: Network <i class="bi bi-arrow-right"></i>
                </a> 
    @else
                
                
                <a href="{{ route('versions.region.create', $version->id) }}"   
                   class="btn btn-secondary me-2" 
                   role="button">
                   Next: Professional Services <i class="bi bi-arrow-right"></i>
                </a> 
                @endif

            </div>
              <div class="alert alert-danger py-1 px-2 small mb-0" role="alert" style="font-size: 0.8rem;">
            ⚠️ Ensure you click <strong>Save</strong> before continuing to the next step!
    </div>

    </div>
                         

           
        </form>
    </div>
</div>







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

@push('scripts')
<script>
    function toggleSolutionTable() {
        const selected = document.getElementById('solution_type').value;

        // Sembunyikan semua
        document.querySelectorAll('.solution-table').forEach(el => el.classList.add('d-none'));

        // Tunjukkan ikut value
        if (selected === 'TCS Only') {
            document.getElementById('tcs_only_table').classList.remove('d-none');
        } else if (selected === 'MP-DRaaS Only') {
            document.getElementById('mpdr_only_table').classList.remove('d-none');
        } else if (selected === 'Both') {
            document.getElementById('both_table').classList.remove('d-none');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        toggleSolutionTable(); // bila page load
        document.getElementById('solution_type').addEventListener('change', toggleSolutionTable); // bila dropdown tukar
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    function updateNamesBasedOnSolutionType() {
        const selectedType = document.getElementById('solution_type').value;

        // Buang semua name attribute dulu
        document.querySelectorAll('.solution-table select').forEach(select => {
            select.removeAttribute('name');
        });

        if (selectedType === 'TCS Only') {
            document.querySelector('#tcs_only_table select[data-name="production_region"]')?.setAttribute('name', 'production_region');
            document.querySelector('#tcs_only_table select[data-name="dr_region"]')?.setAttribute('name', 'dr_region');
        } else if (selectedType === 'MP-DRaaS Only') {
            document.querySelector('#mpdr_only_table select[data-name="mpdraas_region"]')?.setAttribute('name', 'mpdraas_region');
        } else if (selectedType === 'Both') {
            document.querySelector('#both_table select[data-name="production_region"]')?.setAttribute('name', 'production_region');
            document.querySelector('#both_table select[data-name="dr_region"]')?.setAttribute('name', 'dr_region');
            document.querySelector('#both_table select[data-name="mpdraas_region"]')?.setAttribute('name', 'mpdraas_region');
        }
    }

    // Run sekali masa page load
    updateNamesBasedOnSolutionType();

    // Bila tukar dropdown
    document.getElementById('solution_type').addEventListener('change', function () {
        // Tunjuk/hide table ikut pilihan
        document.querySelectorAll('.solution-table').forEach(div => div.classList.add('d-none'));

        if (this.value === 'TCS Only') {
            document.getElementById('tcs_only_table').classList.remove('d-none');
        } else if (this.value === 'MP-DRaaS Only') {
            document.getElementById('mpdr_only_table').classList.remove('d-none');
        } else {
            document.getElementById('both_table').classList.remove('d-none');
        }

        updateNamesBasedOnSolutionType();
    });
});
</script>




@endpush

