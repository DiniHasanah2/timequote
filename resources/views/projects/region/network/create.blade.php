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
    
       <form method="POST" action="{{ route('versions.region.network.store', $version->id) }}">
            @csrf
          


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
            <td class="bg-light fw-bold text-center">Production</td>
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
            <td class="bg-light fw-bold text-center">DR</td>
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

            <!-- Network Table -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th colspan="4">Network (24/7 Support)</th>
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
                            <td>Bandwidth</td>
                            <td>{{ $pricing['CNET-BWS-CIA-80']['measurement_unit'] }}</td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_bandwidth" class="form-control auto-save"  data-field="kl_bandwidth" 
       data-version-id="{{ $version->id }}" value="{{ old('kl_bandwidth', $region->kl_bandwidth ?? '') }}" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="cyber_bandwidth" class="form-control auto-save"  data-field="cyber_bandwidth" 
       data-version-id="{{ $version->id }}" value="{{ old('cyber_bandwidth', $region->cyber_bandwidth ?? '') }}" min="0">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Bandwidth with Anti-DDoS</td>
                             <td>{{ $pricing['CNET-BWD-CIA-100']['measurement_unit'] }}</td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_bandwidth_with_antiddos" class="form-control auto-save"  data-field="kl_bandwidth_with_antiddos" 
       data-version-id="{{ $version->id }}" value="{{ old('kl_bandwidth_with_antiddos', $region->kl_bandwidth_with_antiddos ?? '') }}" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="cyber_bandwidth_with_antiddos" class="form-control auto-save"  data-field="cyber_bandwidth_with_antiddos" 
       data-version-id="{{ $version->id }}" value="{{ old('cyber_bandwidth_with_antiddos', $region->cyber_bandwidth_with_antiddos ?? '') }}" min="0">
                                </div>
                            </td>
                        </tr>
                     
                        <tr>
    <td>Included Elastic IP (FOC)
        
    </td>
    
    <td>Unit</td>

    <td>
    <div class="input-group">
        {{-- DISPLAY ONLY --}}
        <input name="kl_included_elastic_ip_display" 
               class="form-control bg-light"
               value="{{ old('kl_included_elastic_ip', $region->kl_included_elastic_ip ?? '0') }}"
               readonly>

        {{-- HIDDEN INPUT FOR DB (auto-save akan trigger yang ni) --}}
        <input type="hidden" name="kl_included_elastic_ip"
               class="auto-save"
               data-field="kl_included_elastic_ip"
               data-version-id="{{ $version->id }}"
               value="{{ old('kl_included_elastic_ip', $region->kl_included_elastic_ip ?? '0') }}">
    </div>
</td>


<td>
    <div class="input-group">
        <input name="cyber_included_elastic_ip_display" 
               class="form-control bg-light"
               value="{{ old('cyber_included_elastic_ip', $region->cyber_included_elastic_ip ?? '0') }}"
               readonly>

        <input type="hidden" name="cyber_included_elastic_ip"
               class="auto-save"
               data-field="cyber_included_elastic_ip"
               data-version-id="{{ $version->id }}"
               value="{{ old('cyber_included_elastic_ip', $region->cyber_included_elastic_ip ?? '0') }}">
    </div>
</td>


    <!---<td>
        <div class="input-group">
            <input name="kl_included_elastic_ip_display" 
                   class="form-control bg-light auto-save" data-field="kl_included_elastic_ip_display" 
                    data-version-id="{{ $version->id }}"
                   value="{{ old('kl_included_elastic_ip', $region->kl_included_elastic_ip ?? '0') }}"
                   readonly>
            <input type="hidden" name="kl_included_elastic_ip" 
                   value="{{ old('kl_included_elastic_ip', $region->kl_included_elastic_ip ?? '0') }}">

                   <input type="hidden" name="kl_included_elastic_ip"
       class="auto-save"
       data-field="kl_included_elastic_ip"
       data-version-id="{{ $version->id }}"
       value="{{ old('kl_included_elastic_ip', $region->kl_included_elastic_ip ?? '0') }}">

    
    </div>
    </td>
    <td>
        <div class="input-group">
            <input name="cyber_included_elastic_ip_display" 
                    class="form-control bg-light auto-save" data-field="cyber_included_elastic_ip_display" 
                    data-version-id="{{ $version->id }}"
                   value="{{ old('cyber_included_elastic_ip', $region->cyber_included_elastic_ip ?? '0') }}"
                   readonly>
            <input type="hidden" name="cyber_included_elastic_ip" 
                   value="{{ old('cyber_included_elastic_ip', $region->cyber_included_elastic_ip ?? '0') }}">
                   <input type="hidden" name="cyber_included_elastic_ip"
       class="auto-save"
       data-field="cyber_included_elastic_ip"
       data-version-id="{{ $version->id }}"
       value="{{ old('cyber_included_elastic_ip', $region->cyber_included_elastic_ip ?? '0') }}">

        </div>

       
    </td>--->
</tr>

                  
                        <tr>
                               <td>{{ $pricing['CNET-EIP-SHR-STD']['name'] }}</td>
    <td>{{ $pricing['CNET-EIP-SHR-STD']['measurement_unit'] }}</td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_elastic_ip" class="form-control auto-save"  data-field="kl_elastic_ip" 
       data-version-id="{{ $version->id }}" value="{{ old('kl_elastic_ip', $region->kl_elastic_ip ?? '') }}" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="cyber_elastic_ip" class="form-control auto-save"  data-field="cyber_elastic_ip" 
       data-version-id="{{ $version->id }}" value="{{ old('cyber_elastic_ip', $region->cyber_elastic_ip ?? '') }}" min="0">
                                </div>
                            </td>
                        </tr>
                        <tr>
                           <td>{{ $pricing['CNET-ELB-SHR-STD']['name'] }}</td>
    <td>{{ $pricing['CNET-ELB-SHR-STD']['measurement_unit'] }}</td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_elastic_load_balancer" class="form-control auto-save"  data-field="kl_elastic_load_balancer" 
       data-version-id="{{ $version->id }}" value="{{ old('kl_elastic_load_balancer', $region->kl_elastic_load_balancer ?? '') }}" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="cyber_elastic_load_balancer" class="form-control auto-save"  data-field="cyber_elastic_load_balancer" 
       data-version-id="{{ $version->id }}" value="{{ old('cyber_elastic_load_balancer', $region->cyber_elastic_load_balancer ?? '') }}" min="0">
                                </div>
                            </td>
                        </tr>
                        <tr>
                              <td>{{ $pricing['CNET-DGW-SHR-EXT']['name'] }}</td>
    <td>{{ $pricing['CNET-DGW-SHR-EXT']['measurement_unit'] }}</td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_direct_connect_virtual" class="form-control auto-save"  data-field="kl_direct_connect_virtual" 
       data-version-id="{{ $version->id }}" value="{{ old('kl_direct_connect_virtual', $region->kl_direct_connect_virtual ?? '') }}" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="cyber_direct_connect_virtual"  class="form-control auto-save"  data-field="cyber_direct_connect_virtual" 
       data-version-id="{{ $version->id }}" value="{{ old('cyber_direct_connect_virtual', $region->cyber_direct_connect_virtual ?? '') }}" min="0">
                                </div>
                            </td>
                        </tr>

                        <tr>
                              <td>{{ $pricing['CNET-L2BR-SHR-EXT']['name'] }}</td>
    <td>{{ $pricing['CNET-L2BR-SHR-EXT']['measurement_unit'] }}</td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_l2br_instance"  class="form-control auto-save"  data-field="kl_l2br_instance" 
       data-version-id="{{ $version->id }}" value="{{ old('kl_l2br_instance', $region->kl_l2br_instance ?? '') }}" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="cyber_l2br_instance" class="form-control auto-save"  data-field="cyber_l2br_instance" 
       data-version-id="{{ $version->id }}" value="{{ old('cyber_l2br_instance', $region->cyber_l2br_instance ?? '') }}" min="0">
                                </div>
                            </td>
                        </tr>
                        <tr>
                                <!---<td>{{ $pricing['CNET-PLL-SHR-100']['name'] }}</td>--->
                                <td>Virtual Private Leased Line (vPLL)</td>
    <td>{{ $pricing['CNET-PLL-SHR-100']['measurement_unit'] }}</td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_virtual_private_leased_line" class="form-control auto-save"  data-field="kl_virtual_private_leased_line" 
       data-version-id="{{ $version->id }}" value="{{ old('kl_virtual_private_leased_line', $region->kl_virtual_private_leased_line ?? '') }}" min="0">
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
                               <td>{{ $pricing['CNET-L2BR-SHR-INT']['name'] }}</td>
    <td>{{ $pricing['CNET-L2BR-SHR-INT']['measurement_unit'] }}</td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_vpll_l2br"  class="form-control auto-save"  data-field="kl_vpll_l2br" 
       data-version-id="{{ $version->id }}" value="{{ old('kl_vpll_l2br', $region->kl_vpll_l2br ?? '') }}" min="0">
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
                              <td>{{ $pricing['CNET-NAT-SHR-S']['name'] }}</td>
    <td>{{ $pricing['CNET-NAT-SHR-S']['measurement_unit'] }}</td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_nat_gateway_small"  class="form-control auto-save"  data-field="kl_nat_gateway_small" 
       data-version-id="{{ $version->id }}" value="{{ old('kl_nat_gateway_small', $region->kl_nat_gateway_small ?? '') }}" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="cyber_nat_gateway_small" class="form-control auto-save"  data-field="cyber_nat_gateway_small" 
       data-version-id="{{ $version->id }}" value="{{ old('cyber_nat_gateway_small', $region->cyber_nat_gateway_small ?? '') }}" min="0">
                                </div>
                            </td>
                        </tr>
                        <tr>
                               <td>{{ $pricing['CNET-NAT-SHR-M']['name'] }}</td>
    <td>{{ $pricing['CNET-NAT-SHR-M']['measurement_unit'] }}</td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_nat_gateway_medium" class="form-control auto-save"  data-field="kl_nat_gateway_medium" 
       data-version-id="{{ $version->id }}" value="{{ old('kl_nat_gateway_medium', $region->kl_nat_gateway_medium ?? '') }}" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="cyber_nat_gateway_medium" class="form-control auto-save"  data-field="cyber_nat_gateway_medium" 
       data-version-id="{{ $version->id }}" value="{{ old('cyber_nat_gateway_medium', $region->cyber_nat_gateway_medium ?? '') }}" min="0">
                                </div>
                            </td>
                        </tr>
                        <tr>
                               <td>{{ $pricing['CNET-NAT-SHR-L']['name'] }}</td>
    <td>{{ $pricing['CNET-NAT-SHR-L']['measurement_unit'] }}</td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_nat_gateway_large" class="form-control auto-save"  data-field="kl_nat_gateway_large" 
       data-version-id="{{ $version->id }}" value="{{ old('kl_nat_gateway_large', $region->kl_nat_gateway_large ?? '') }}" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="cyber_nat_gateway_large" class="form-control auto-save"  data-field="cyber_nat_gateway_large" 
       data-version-id="{{ $version->id }}" value="{{ old('cyber_nat_gateway_large', $region->cyber_nat_gateway_large ?? '') }}" min="0">
                                </div>
                            </td>
                        </tr>
                        <tr>
                               <td>{{ $pricing['CNET-NAT-SHR-XL']['name'] }}</td>
    <td>{{ $pricing['CNET-NAT-SHR-XL']['measurement_unit'] }}</td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_nat_gateway_xlarge" class="form-control auto-save"  data-field="kl_nat_gateway_xlarge" 
       data-version-id="{{ $version->id }}" value="{{ old('kl_nat_gateway_xlarge', $region->kl_nat_gateway_xlarge ?? '') }}" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="cyber_nat_gateway_xlarge" class="form-control auto-save"  data-field="cyber_nat_gateway_xlarge" 
       data-version-id="{{ $version->id }}" value="{{ old('cyber_nat_gateway_xlarge', $region->cyber_nat_gateway_xlarge ?? '') }}" min="0">
                                </div>
                            </td>
                        </tr>



           
            
                        <!---<thead class="table-dark">
                            <tr>
                                <th colspan="4">Global Services (24/7 Support)</th>
                                
                            </tr>
                        </thead>
                        <tr>
                            <td>Content Delivery Network</td>

                             <td>GB</td>


                             <td>
                                <div class="input-group">
                                    <input type="number" name="kl_content_delivery_network" class="form-control auto-save"  data-field="kl_content_delivery_network" 
       data-version-id="{{ $version->id }}" value="{{ old('kl_content_delivery_network', $region->kl_content_delivery_network ?? '') }}" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="cyber_content_delivery_network" class="form-control auto-save"  data-field="cyber_content_delivery_network" 
       data-version-id="{{ $version->id }}" value="{{ old('cyber_content_delivery_network', $region->cyber_content_delivery_network ?? '') }}" min="0">
                                </div>
                            </td>
                           
    
                        </tr>--->

                         <thead class="table-dark">
                            <tr>
                                <th colspan="4">Storage (24/7 Support)</th>
                                
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
                           <td>{{ $pricing['CSTG-SFS-SHR-STD']['name'] }}</td>
    <td>{{ $pricing['CSTG-SFS-SHR-STD']['measurement_unit'] }}</td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_scalable_file_service" class="form-control auto-save"  data-field="kl_scalable_file_service" 
       data-version-id="{{ $version->id }}" value="{{ old('kl_scalable_file_service', $region->kl_scalable_file_service ?? '') }}" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="cyber_scalable_file_service" class="form-control auto-save"  data-field="cyber_scalable_file_service" 
       data-version-id="{{ $version->id }}" value="{{ old('cyber_scalable_file_service', $region->cyber_scalable_file_service ?? '') }}" min="0">
                                </div>
                            </td>
                        </tr>
                        <tr>
                           <td>{{ $pricing['CSTG-OBS-SHR-STD']['name'] }}</td>
    <td>{{ $pricing['CSTG-OBS-SHR-STD']['measurement_unit'] }}</td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_object_storage_service" class="form-control auto-save"  data-field="kl_object_storage_service" 
       data-version-id="{{ $version->id }}" value="{{ old('kl_object_storage_service', $region->kl_object_storage_service ?? '') }}" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="cyber_object_storage_service" class="form-control auto-save"  data-field="cyber_object_storage_service" 
       data-version-id="{{ $version->id }}" value="{{ old('cyber_object_storage_service', $region->cyber_object_storage_service ?? '') }}" min="0">
                                </div>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
</fieldset>
          
            <div class="d-flex justify-content-between gap-3"> 
    
   
   

            @if(($solution_type->solution_type ?? '') === 'MP-DRaaS Only')
            <a href="{{ route('versions.solution_type.create', $version->id) }}" class="btn btn-secondary" role="button">
        <i class="bi bi-arrow-left"></i> Previous Step
    </a>
        @else
    <a href="{{ route('versions.region.create', $version->id) }}" class="btn btn-secondary" role="button">
        <i class="bi bi-arrow-left"></i> Previous Step
    </a>
    @endif
  
    

                         

            <div class="d-flex justify-content-end gap-3"> <!-- Added gap-3 for spacing -->
                <!---<button type="submit" class="btn btn-pink">Save Network</button>--->
            
                @if(($solution_type->solution_type ?? '') === 'MP-DRaaS Only')
                  <a href="{{ route('versions.mpdraas.create', $version->id) }}"  
                   class="btn btn-secondary me-2" 
                   role="button">
                   Next: MP-DRaaS<i class="bi bi-arrow-right"></i>
                </a> 

                @else
                <a href="{{ route('versions.backup.create', $version->id) }}"  
                   class="btn btn-secondary me-2" 
                   role="button">
                   Next: ECS & Backup <i class="bi bi-arrow-right"></i>
                </a> 
                @endif
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

<script>
    document.getElementById('calculate').addEventListener('click', function() {
        // Add your calculation logic here
    });
</script>

<script>
// ===== Included Elastic IP (FOC) calculator =====
document.addEventListener('DOMContentLoaded', function() {
  const IS_LOCKED = @json($isLocked);

  // Rule: total >= 50 → 8, 31–49 → 6, =30 → 4, 2–29 → 2, else 0
  function calculateIncludedEIP(total) {
    if (total >= 50) return 8;   // 50+
    if (total >= 31) return 6;   // 31–49
    if (total >= 30) return 4;   // exactly 30
    if (total >= 2)  return 2;   // 2–29
    return 0;                    // 0–1
  }

  const klBandwidth     = document.querySelector('[name="kl_bandwidth"]');
  const klAntiDDoS      = document.querySelector('[name="kl_bandwidth_with_antiddos"]');
  const cyberBandwidth  = document.querySelector('[name="cyber_bandwidth"]');
  const cyberAntiDDoS   = document.querySelector('[name="cyber_bandwidth_with_antiddos"]');

  const klHidden   = document.querySelector('[name="kl_included_elastic_ip"]');
  const klDisplay  = document.querySelector('[name="kl_included_elastic_ip_display"]');
  const cyHidden   = document.querySelector('[name="cyber_included_elastic_ip"]');
  const cyDisplay  = document.querySelector('[name="cyber_included_elastic_ip_display"]');

  function updateIncludedEIP() {
    // Guna MAX (ikut layout/flow sedia ada). Contoh 10 & 50 → 50 → 8 ✅
    const klTotal    = Math.max(parseInt(klBandwidth?.value)||0, parseInt(klAntiDDoS?.value)||0);
    const cyberTotal = Math.max(parseInt(cyberBandwidth?.value)||0, parseInt(cyberAntiDDoS?.value)||0);

    const klEIP    = calculateIncludedEIP(klTotal);
    const cyberEIP = calculateIncludedEIP(cyberTotal);

    if (klHidden)  klHidden.value = klEIP;
    if (klDisplay) klDisplay.value = klEIP;
    if (!IS_LOCKED && klHidden) klHidden.dispatchEvent(new Event('change')); // trigger autosave bila tak locked

    if (cyHidden)  cyHidden.value = cyberEIP;
    if (cyDisplay) cyDisplay.value = cyberEIP;
    if (!IS_LOCKED && cyHidden) cyHidden.dispatchEvent(new Event('change'));
  }

  [klBandwidth, klAntiDDoS, cyberBandwidth, cyberAntiDDoS].forEach(el => {
    if (!el) return;
    el.addEventListener('input', updateIncludedEIP);
    el.addEventListener('change', updateIncludedEIP);
  });

  // Initial calc
  updateIncludedEIP();

  // Extra guard bila locked
  if (IS_LOCKED) {
    document.querySelectorAll('.auto-save').forEach(el => {
      el.addEventListener('input',  e => e.preventDefault(), true);
      el.addEventListener('change', e => e.preventDefault(), true);
    });
  }
});
</script>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const IS_LOCKED = @json($isLocked);
  // ✅ URL ikut named route kau
  const AUTOSAVE_URL = @json(route('versions.region.autosave', $version->id));

  document.querySelectorAll('.auto-save').forEach(function (element) {
    element.addEventListener('change', function () {
      if (IS_LOCKED || this.disabled) return;

      const field = this.dataset.field;
      const value = this.value;
      if (!field) return;

      fetch(AUTOSAVE_URL, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ [field]: value })
      }).then(r => {
        if (!r.ok) console.error('autosave error', r.status);
      }).catch(err => console.error('autosave failed', err));
    });
  });
});
</script>
@endpush



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