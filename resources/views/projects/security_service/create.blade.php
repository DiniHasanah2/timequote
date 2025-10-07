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
    
       <form method="POST" action="{{ route('versions.security_service.store', $version->id) }}">
            @csrf
                <input type="hidden" name="section" value="cloud">
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

            <!-- Security Service Table -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th colspan="4">Managed Services</th>
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
                            <td colspan="2">Managed Services 1</td>
                          <td>
                                    <div class="input-group">
                                       <select name="kl_managed_services_1" class="form-select">
    <option value="None" @selected(old('kl_managed_services_1', $security_service->kl_managed_services_1 ?? '') == 'None')>None</option>
    <option value="Managed Operating System" @selected(old('kl_managed_services_1', $security_service->kl_managed_services_1 ?? '') == 'Managed Operating System')>Managed Operating System</option>
    <option value="Managed Backup and Restore" @selected(old('kl_managed_services_1', $security_service->kl_managed_services_1 ?? '') == 'Managed Backup and Restore')>Managed Backup and Restore</option>
    <option value="Managed Patching" @selected(old('kl_managed_services_1', $security_service->kl_managed_services_1 ?? '') == 'Managed Patching')>Managed Patching</option>
    <option value="Managed DR" @selected(old('kl_managed_services_1', $security_service->kl_managed_services_1 ?? '') == 'Managed DR')>Managed DR</option>
</select>
                                    </div>
                                </td>


                                <td>

                                <div class="input-group">
                                       <select name="cyber_managed_services_1" class="form-select">
    <option value="None" @selected(old('cyber_managed_services_1', $security_service->cyber_managed_services_1 ?? '') == 'None')>None</option>
    <option value="Managed Operating System" @selected(old('cyber_managed_services_1', $security_service->cyber_managed_services_1 ?? '') == 'Managed Operating System')>Managed Operating System</option>
    <option value="Managed Backup and Restore" @selected(old('cyber_managed_services_1', $security_service->cyber_managed_services_1 ?? '') == 'Managed Backup and Restore')>Managed Backup and Restore</option>
    <option value="Managed Patching" @selected(old('cyber_managed_services_1', $security_service->cyber_managed_services_1 ?? '') == 'Managed Patching')>Managed Patching</option>
    <option value="Managed DR" @selected(old('cyber_managed_services_1', $security_service->cyber_managed_services_1 ?? '') == 'Managed DR')>Managed DR</option>
</select>
                                    </div>
                                    
                                </td>


                        </tr>




                         <tr>
                            <td colspan="2">Managed Services 2</td>
                          <td>
                                    <div class="input-group">
                                       <select name="kl_managed_services_2" class="form-select">
    <option value="None" @selected(old('kl_managed_services_2', $security_service->kl_managed_services_2 ?? '') == 'None')>None</option>
    <option value="Managed Operating System" @selected(old('kl_managed_services_2', $security_service->kl_managed_services_2 ?? '') == 'Managed Operating System')>Managed Operating System</option>
    <option value="Managed Backup and Restore" @selected(old('kl_managed_services_2', $security_service->kl_managed_services_2 ?? '') == 'Managed Backup and Restore')>Managed Backup and Restore</option>
    <option value="Managed Patching" @selected(old('kl_managed_services_2', $security_service->kl_managed_services_2 ?? '') == 'Managed Patching')>Managed Patching</option>
    <option value="Managed DR" @selected(old('kl_managed_services_2', $security_service->kl_managed_services_2 ?? '') == 'Managed DR')>Managed DR</option>
</select>
                                    </div>
                                </td>


                                <td>

                                <div class="input-group">
                                       <select name="cyber_managed_services_2" class="form-select">
    <option value="None" @selected(old('cyber_managed_services_2', $security_service->cyber_managed_services_2 ?? '') == 'None')>None</option>
    <option value="Managed Operating System" @selected(old('cyber_managed_services_2', $security_service->cyber_managed_services_2 ?? '') == 'Managed Operating System')>Managed Operating System</option>
    <option value="Managed Backup and Restore" @selected(old('cyber_managed_services_2', $security_service->cyber_managed_services_2 ?? '') == 'Managed Backup and Restore')>Managed Backup and Restore</option>
    <option value="Managed Patching" @selected(old('cyber_managed_services_2', $security_service->cyber_managed_services_2 ?? '') == 'Managed Patching')>Managed Patching</option>
    <option value="Managed DR" @selected(old('cyber_managed_services_2', $security_service->cyber_managed_services_2 ?? '') == 'Managed DR')>Managed DR</option>
</select>
                                    </div>
                                    
                                </td>


                        </tr>


                         <tr>
                            <td colspan="2">Managed Services 3</td>
                          <td>
                                    <div class="input-group">
                                       <select name="kl_managed_services_3" class="form-select">
    <option value="None" @selected(old('kl_managed_services_3', $security_service->kl_managed_services_3 ?? '') == 'None')>None</option>
    <option value="Managed Operating System" @selected(old('kl_managed_services_3', $security_service->kl_managed_services_3 ?? '') == 'Managed Operating System')>Managed Operating System</option>
    <option value="Managed Backup and Restore" @selected(old('kl_managed_services_3', $security_service->kl_managed_services_3 ?? '') == 'Managed Backup and Restore')>Managed Backup and Restore</option>
    <option value="Managed Patching" @selected(old('kl_managed_services_3', $security_service->kl_managed_services_3 ?? '') == 'Managed Patching')>Managed Patching</option>
    <option value="Managed DR" @selected(old('kl_managed_services_3', $security_service->kl_managed_services_3 ?? '') == 'Managed DR')>Managed DR</option>
</select>
                                    </div>
                                </td>


                                <td>

                                <div class="input-group">
                                       <select name="cyber_managed_services_3" class="form-select">
    <option value="None" @selected(old('cyber_managed_services_3', $security_service->cyber_managed_services_3 ?? '') == 'None')>None</option>
    <option value="Managed Operating System" @selected(old('cyber_managed_services_3', $security_service->cyber_managed_services_3 ?? '') == 'Managed Operating System')>Managed Operating System</option>
    <option value="Managed Backup and Restore" @selected(old('cyber_managed_services_3', $security_service->cyber_managed_services_3 ?? '') == 'Managed Backup and Restore')>Managed Backup and Restore</option>
    <option value="Managed Patching" @selected(old('cyber_managed_services_3', $security_service->cyber_managed_services_3 ?? '') == 'Managed Patching')>Managed Patching</option>
    <option value="Managed DR" @selected(old('cyber_managed_services_3', $security_service->cyber_managed_services_3 ?? '') == 'Managed DR')>Managed DR</option>
</select>
                                    </div>
                                    
                                </td>


                        </tr>

                         <tr>
                            <td colspan="2">Managed Services 4</td>
                          <td>
                                    <div class="input-group">
                                       <select name="kl_managed_services_4" class="form-select">
    <option value="None" @selected(old('kl_managed_services_4', $security_service->kl_managed_services_4 ?? '') == 'None')>None</option>
    <option value="Managed Operating System" @selected(old('kl_managed_services_4', $security_service->kl_managed_services_4 ?? '') == 'Managed Operating System')>Managed Operating System</option>
    <option value="Managed Backup and Restore" @selected(old('kl_managed_services_4', $security_service->kl_managed_services_4 ?? '') == 'Managed Backup and Restore')>Managed Backup and Restore</option>
    <option value="Managed Patching" @selected(old('kl_managed_services_4', $security_service->kl_managed_services_4 ?? '') == 'Managed Patching')>Managed Patching</option>
    <option value="Managed DR" @selected(old('kl_managed_services_4', $security_service->kl_managed_services_4 ?? '') == 'Managed DR')>Managed DR</option>
</select>
                                    </div>
                                </td>


                                <td>

                                <div class="input-group">
                                       <select name="cyber_managed_services_4" class="form-select">
<option value="None" @selected(old('cyber_managed_services_4', $security_service->cyber_managed_services_4 ?? '') == 'None')>None</option>
    <option value="Managed Operating System" @selected(old('cyber_managed_services_4', $security_service->cyber_managed_services_4 ?? '') == 'Managed Operating System')>Managed Operating System</option>
    <option value="Managed Backup and Restore" @selected(old('cyber_managed_services_4', $security_service->cyber_managed_services_4 ?? '') == 'Managed Backup and Restore')>Managed Backup and Restore</option>
    <option value="Managed Patching" @selected(old('cyber_managed_services_4', $security_service->cyber_managed_services_4 ?? '') == 'Managed Patching')>Managed Patching</option>
    <option value="Managed DR" @selected(old('cyber_managed_services_4', $security_service->cyber_managed_services_4 ?? '') == 'Managed DR')>Managed DR</option>
</select>
                                    </div>
                                    
                                </td>


                        </tr>
                       


                    
                    
                          
                       

                        
                         <thead class="table-dark">
                        <tr>
                            <th colspan="4">Cloud Security</th>
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
                             <td>{{ $pricing['CSEC-VFW-DDT-FG']['name'] }}</td>
    <td>{{ $pricing['CSEC-VFW-DDT-FG']['measurement_unit'] }}</td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_firewall_fortigate" class="form-control"
                                    value="{{ old('kl_firewall_fortigate', $security_service->kl_firewall_fortigate ?? '') }}" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="cyber_firewall_fortigate" class="form-control"
                                    value="{{ old('cyber_firewall_fortigate', $security_service->cyber_firewall_fortigate ?? '') }}" min="0">
                                </div>
                            </td>
                        </tr>
                        <tr>
                           <td>{{ $pricing['CSEC-VFW-DDT-OS']['name'] }}</td>
    <td>{{ $pricing['CSEC-VFW-DDT-OS']['measurement_unit'] }}</td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_firewall_opnsense" class="form-control"
                                    value="{{ old('kl_firewall_opnsense', $security_service->kl_firewall_opnsense ?? '') }}" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="cyber_firewall_opnsense" class="form-control"
                                     value="{{ old('cyber_firewall_opnsense', $security_service->cyber_firewall_opnsense ?? '') }}" min="0">
                                </div>
                            </td>
                        </tr>

                        <tr>
                           <td>{{ $pricing['CSEC-WAF-SHR-HA']['name'] }}</td>
    <td>{{ $pricing['CSEC-WAF-SHR-HA']['measurement_unit'] }}</td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_shared_waf" class="form-control"
                                    value="{{ old('kl_shared_waf', $security_service->kl_shared_waf ?? '') }}" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="cyber_shared_waf" class="form-control"
                                     value="{{ old('cyber_shared_waf', $security_service->cyber_shared_waf ?? '') }}" min="0">
                                </div>
                            </td>
                        </tr>



                        <tr>
                          <td>{{ $pricing['CSEC-EDR-NOD-STD']['name'] }}</td>
    <td>{{ $pricing['CSEC-EDR-NOD-STD']['measurement_unit'] }}</td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_antivirus" class="form-control"
                                    value="{{ old('kl_antivirus', $security_service->kl_antivirus ?? '') }}" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="cyber_antivirus" class="form-control"
                                     value="{{ old('cyber_antivirus', $security_service->cyber_antivirus ?? '') }}" min="0">
                                </div>
                            </td>
                        </tr>



                           <thead class="table-dark">
                            <tr>
                                <th colspan="4">Other Services</th>
                                
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
                             <td>{{ $pricing['CNET-GLB-SHR-DOMAIN']['name'] }}</td>
    <td>{{ $pricing['CNET-GLB-SHR-DOMAIN']['measurement_unit'] }}</td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_gslb" class="form-control"
                                     value="{{ old('kl_gslb', $security_service->kl_gslb ?? '') }}">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="cyber_gslb" class="form-control"
                                     value="{{ old('cyber_gslb', $security_service->cyber_gslb ?? '') }}">
                                </div>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
  </fieldset>

            <div class="d-flex justify-content-between gap-3"> 

            @if(($solution_type->solution_type ?? '') === 'TCS Only')
            <a href="{{ route('versions.region.dr.create', $version->id) }}" class="btn btn-secondary" role="button">
        <i class="bi bi-arrow-left"></i> Previous Step
    </a>
        @else
    <a href="{{ route('versions.mpdraas.create', $version->id) }}" class="btn btn-secondary" role="button">
        <i class="bi bi-arrow-left"></i> Previous<br>Step
    </a>
    @endif
  <div class="d-flex flex-column align-items-centre gap-2">
    

            <div class="d-flex justify-content-end gap-3"> <!-- Added gap-3 for spacing -->
                <button type="submit" class="btn btn-pink" @disabled($isLocked)>Save Managed Services & Cloud Security</button>

                  <a href="{{ route('versions.security_service.time.create', $version->id) }}" class="btn btn-secondary me-2" role="button">Next:  Time Security Services <i class="bi bi-arrow-right"></i></a>

               
              
            </div>
                 <div class="alert alert-danger py-1 px-2 small mb-0" role="alert" style="font-size: 0.8rem;">
            ⚠️ Ensure you click <strong>Save</strong> before continuing to the next step!
    </div>

    </div>
        </form>
    </div>
</div>




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