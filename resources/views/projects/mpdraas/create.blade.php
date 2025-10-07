@extends('layouts.app')

@php
    $solution_type = $solution_type ?? $version->solution_type ?? null;
@endphp

@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@section('content')


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
           <!--- @if(($solution_type->solution_type ?? '') !== 'MP-DRaaS Only')
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
          
            <a href="{{ route('versions.mpdraas.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.mpdraas.create' ? 'active-link' : '' }}">MP-DRaaS</a>
            <span class="breadcrumb-separator">»</span>
         
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



    
       <form method="POST" action="{{ route('versions.mpdraas.store', $version->id) }}">
            @csrf
            <input type="hidden" name="main" id="main_input" value="{{ old('main', $mpdraas->main ?? '') }}">
<input type="hidden" name="used" id="used_input" value="{{ old('used', $mpdraas->used ?? '') }}">
<input type="hidden" name="delta" id="delta_input" value="{{ old('delta', $mpdraas->delta ?? '') }}">
<input type="hidden" name="total_replication" id="total_replication_input" value="{{ old('total_replication', $mpdraas->total_replication ?? '') }}">

      


            <div class="mb-4">
                <h6 class="fw-bold">Project</h6>
                <div class="mb-3">
                    <input type="text" class="form-control bg-light" value="{{ $project->name }}" readonly>
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="version_id" value="{{ $version->id }}">
<input type="hidden" name="customer_id" value="{{ $project->customer_id }}">


                


<div class="card-body">
    <div class="d-flex align-items-start"> <!-- flexbox, horizontal row -->

        <!-- Table Kiri -->
        <div class="table-responsive mb-4">
            
                      <table class="table table-bordered w-auto">
                      <body>
                        <thead class="table-dark">
                            <tr>
                                <th colspan="5">MP-DRaaS</th>
                            </tr>
                        </thead>

                          <tr>
                            <td colspan="2">Number of MP-DRaaS Activation Days</td>
                        
                         
@php
    $selected = old('mpdraas_activation_days', $mpdraas->mpdraas_activation_days ?? '');
@endphp

    <td>
                           
    <div class="input-group">
        <select name="mpdraas_activation_days" class="form-select">
            <option value="15" {{ $selected == 15 ? 'selected' : '' }}>15</option>
            <option value="30" {{ $selected == 30 ? 'selected' : '' }}>30</option>
            <option value="45" {{ $selected == 45 ? 'selected' : '' }}>45</option>
            <option value="60" {{ $selected == 60 ? 'selected' : '' }}>60</option>
        </select>
    </div>
</td>


                            <td>Days/annum</td>
                        
                            
</tr>
     

<tr>
                            <td colspan="2">Number of Proxy</td>
                           
                            <td colspan="3">
                                <input type="number" name="num_proxy" class="form-control" value="{{ old('num_proxy', $mpdraas->num_proxy ?? '') }}" min="0">
                                    
   
</td></tr>
                            
      <tr>
                            <td colspan="2">MP-DRaaS Location</td>
                            <td colspan="3">
                    
        
    <div class="input-group">
    <input name="mpdraas_location" 
           class="form-control bg-light" 
         value="{{ old('mpdraas_location', $solution_type->mpdraas_region ?? $mpdraas->mpdraas_location ?? 'None') }}" 
                   readonly style="border-radius: 0;">
</div>

                     
                               
                            </td>
                           
                        </tr> 


<tr><td colspan="2">DDoS Requirement?</td>
<td colspan="3">

<div class="input-group">
                                       <select name="ddos_requirement" class="form-select">
    <option value="No" @selected(old('ddos_requirement', $mpdraas->ddos_requirement ?? '') == 'No')>No</option>
    <option value="Yes" @selected(old('ddos_requirement', $mpdraas->ddos_requirement ?? '') == 'Yes')>Yes</option>
</select>
                                    </div>


</td><tr>

<tr><td colspan="2">Bandwidth Requirement for Replication</td>
<td colspan="3"><div class="input-group">
    <input name="bandwidth_requirement" id="bandwidth_requirement"
           class="form-control bg-light" 
         value="{{ old('bandwidth_requirement', $mpdraas->bandwidth_requirement ?? '') }}" 
                   readonly style="border-radius: 0;">
</div></td>
<tr>




                                           
                          
                    </tbody>
                </table>




{{-- CSRF untuk fetch --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<table class="table table-bordered w-auto">
  <thead class="table-dark">
    <tr><th colspan="8">Disaster Recovery Components - ONLY ACTIVATED DURING DR</th></tr>
    <tr>
      <th class="bg-dark fw-bold" colspan="3">NETWORK</th>
      <th class="bg-dark fw-bold" colspan="2">QUANTITY</th>
      <th class="bg-dark fw-bold" colspan="3">MONTHLY RECURRING CHARGES</th>
    </tr>
    <tr>
      <th class="bg-dark">Code</th>
      <th class="bg-dark">Component Name</th>
      <th class="bg-dark">Unit</th>
      <th class="bg-dark">KL</th>
      <th class="bg-dark">CJ</th>
      <th class="bg-dark">KL Charges</th>
      <th class="bg-dark">CJ Charges</th>
      <th class="bg-dark">Total</th>
    </tr>
  </thead>
  <tbody>
  @php $saved = $mpdraas->dr_network ?? []; @endphp

  @foreach($drNetworkRows as $row)
    @php
      $code  = $row['code'];
      $price = $row['price'];
      $prev  = $saved[$code] ?? [];
      $klQty = $prev['kl_qty'] ?? 0;
      $cjQty = $prev['cj_qty'] ?? 0;
      $klAmt = number_format(($prev['kl_amount'] ?? 0), 2);
      $cjAmt = number_format(($prev['cj_amount'] ?? 0), 2);
      $tot   = number_format(($prev['total'] ?? 0), 2);
    @endphp
    <tr class="dr-line"
        data-code="{{ $code }}"
        data-price="{{ $price }}">
      <td>{{ $code }}</td>
      <td>{{ $row['name'] }}</td>
      <td>{{ $row['unit'] }}</td>

      <td style="max-width:120px">
        <input type="number" min="0" step="1"
               class="form-control qty-kl"
               value="{{ $klQty }}">
      </td>
      <td style="max-width:120px">
        <input type="number" min="0" step="1"
               class="form-control qty-cj"
               value="{{ $cjQty }}">
      </td>

      <td class="kl-amount">RM{{ $klAmt }}</td>
      <td class="cj-amount">RM{{ $cjAmt }}</td>
      <td class="total-amount">RM{{ $tot }}</td>
    </tr>
  @endforeach
  </tbody>
</table>

<script>
(function(){
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const autosaveUrl = "{{ route('versions.mpdraas.autosave', $version->id) }}";

  function fmt(n){ return 'RM' + (Number(n||0).toFixed(2)); }

  function recalcAndSave(tr){
    const price = Number(tr.dataset.price || 0);
    const code  = tr.dataset.code;

    const klInput = tr.querySelector('.qty-kl');
    const cjInput = tr.querySelector('.qty-cj');

    const klQty = Number(klInput.value || 0);
    const cjQty = Number(cjInput.value || 0);

    const klAmt = klQty * price;
    const cjAmt = cjQty * price;
    const tot   = klAmt + cjAmt;

    tr.querySelector('.kl-amount').textContent = fmt(klAmt);
    tr.querySelector('.cj-amount').textContent = fmt(cjAmt);
    tr.querySelector('.total-amount').textContent = fmt(tot);

    // autosave to server
    fetch(autosaveUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
         'Accept': 'application/json',
        'X-CSRF-TOKEN': token
      },
      body: JSON.stringify({ code: code, kl_qty: klQty, cj_qty: cjQty })
    }).then(r => r.json()).then(j => {
      if(!j.ok){ console.warn('Autosave error', j); }
      
    }).catch(e => console.error(e));
  }

  document.querySelectorAll('tr.dr-line').forEach(tr => {
    tr.querySelectorAll('.qty-kl, .qty-cj').forEach(inp => {
      inp.addEventListener('input', () => recalcAndSave(tr));
      inp.addEventListener('change', () => recalcAndSave(tr));
    });
  });
})();
</script>



         



                            
     



                
            </div>
 

 
  
        <div class="table-responsive mb-4">
            
            <table class="table table-bordered">
               

                        <thead class="table-dark">
                            <tr>
                                <th colspan="5">Storage Summary</th>
                            </tr>
                        </thead>
                          <tbody>

                          <tr>
                            <td class="bg-light"></td>
                             <td class="bg-light">Main</td>
                              <td class="bg-light">Used</td>
                               <td class="bg-light">Delta</td>
                                <td class="bg-light">Total Replication</td>
                
                </tr>


                          <tr>
                            <td>EVS</td>
                             <td id="evs_main">0</td>
                            <td id="evs_used">0</td>
                            <td id="evs_delta">0</td>
                            <td id="evs_total_replication">0</td>
                                                    
                           

                             
                
                </tr>

                <tr>
                    <td>OBS</td>
                             <td id="obs_main">0</td>
                            <td id="obs_used">0</td>
                            <td id="obs_delta">0</td>
                            <td id="obs_total_replication">0</td>
                                                    
                        
                        </tr>

                  

                    
                </tbody>
            </table>
        </div>

    </div> <!-- /d-flex -->


</form>

<form action="" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="version_id" value="{{ $version->id }}">

    
        <input type="file" name="import_file" class="form-control" required>


        <br>
      <button type="submit" class="btn btn-pink"><i class="bi bi-upload"></i> Attach File</button>
   
        
         <a href="{{ asset('assets/MP-DRaaS_Template.xlsx') }}"  class="btn btn-pink" download>
    <i class="bi bi-download"></i> Download Template </a>
  

</form>

       
</div>
             <div class="card-body">
              <div class="table-responsive mb-4" style="overflow-x: auto; white-space: nowrap;">
                <table class="table table-bordered">
                      
                        <thead class="table-dark">
                            <tr>
                                <th colspan="2"><strong>MP-DRaaS VM</strong></th>
                                <th colspan="23"><strong>DR Resources</strong></th>
                            
                           
                            </tr>
                       
                                   <tr>
                            <th colspan="2"></th>
                            <th colspan="4">ECS</th>
                            <th colspan="2">Storage</th>
                            <th colspan="3">Monthly License</th>
                            <th colspan="3">Utilization</th>
                            <th colspan="12"></th>
                       
                            
                        </tr>

                        </thead>

                          <thead class="table-light">
                          <tr>
                            <td>No</td>
                            <td>VM Name</td>
                            <td>Always On</td>
                            <td>Pin</td>
                            <td>vCPU</td>
                            <td>vRAM</td>
                              <td>Flavour Mapping</td>
                            <td>System Disk (GB)</td>
                            <td>Data Disk (GB)</td>
                            <td>Operating System</td>
                            <td>RDS License (count)</td>
                            <td>Microsoft SQL</td>
                            <td>Used System Disk (GB)</td>
                            <td>Used Data Disk (GB)</td>
                            <td>Solution Storage Type</td>
                            <td>RTO Expected (min)</td>
                            <td>Daily Data Change (%)</td>
                            <td>Data Change (GB)</td>
                            <td>Data Change Size(Mbits)</td>
                            <td>Replication Frequency in minutes (max 5 min)</td>
                            <td>Number of Replication Carried out in a Day</td>
                            <td>Amount of Data Change per sync (Mbits)</td>
                            <td>Replication Bandwidth Required (Mbps)</td>
                            <td>RPO that can be achieved (Min)</td>
                            <td>Action</td>
                        </tr>
</thead>
<tbody id="vm-body">
    @forelse($vms as $i => $vm)
        @include('projects.mpdraas._row', ['i' => $i, 'vm' => $vm, 'version' => $version])
    @empty
        {{-- Tiada VM lagi: render 1 row kosong index 0 --}}
        @include('projects.mpdraas._row', ['i' => 0, 'version' => $version])
    @endforelse
</tbody>

{{-- Template untuk Add Row (index placeholder __INDEX__) --}}
<template id="vm-template">
    @include('projects.mpdraas._row', ['i' => '__INDEX__', 'version' => $version])
</template>


                        <!---<tr>
                         <td>1</td>
                          <td><input type="text" name="vm_name" class="form-control w-100" style="min-width: 120px;" value="{{ old('vm_name', $mpdraas->vm_name ?? '') }}"></td>
                          <td>    
                        
                                       <select name="always_on" class="form-select w-100" style="min-width: 120px;">
    <option value="No" @selected(old('always_on', $mpdraas->always_on ?? '') == 'No')>No</option>
    <option value="Yes" @selected(old('always_on', $mpdraas->always_on ?? '') == 'Yes')>Yes</option>
</select>
                                   </td>
                                 
                         
                                

                                       <td>
    <select name="pin" class="form-select w-100" style="min-width: 120px;">
        <option value="No" @selected(old('pin', $mpdraas->pin ?? '') == 'No')>No</option>
        <option value="Yes" @selected(old('pin', $mpdraas->pin ?? '') == 'Yes')>Yes</option>
    </select>
</td>
<td>
    <input type="number" name="vcpu" class="form-control w-100" style="min-width: 100px;" 
           value="{{ old('vcpu', $mpdraas->vcpu ?? '') }}" min="0">
</td>
<td>
    <input type="number" name="vram" class="form-control w-100" style="min-width: 100px;" 
           value="{{ old('vram', $mpdraas->vram ?? '') }}" min="0">
</td>

                                          <td><input  name="flavour_mapping" class="form-control" value="{{ old('flavour_mapping', $mpdraas->flavour_mapping ?? '') }}" readonly style="background-color: black;color: white;"></td>
                                             
                                          
                                          <td><input type="number" name="system_disk" id="system_disk" class="form-control" value="{{ old('system_disk', $mpdraas->system_disk ?? '') }}"  min="0"></td>
                                       <td><input type="number" name="data_disk" id="data_disk" class="form-control" value="{{ old('data_disk', $mpdraas->data_disk ?? '') }}" min="0"></td>

                                        <td>    
                  <select name="operating_system" class="form-select">
                    <option value="Linux" @selected(old('operating_system', $mpdraas->operating_system ?? '') == 'Linux')>Linux</option>
                    <option value="Microsoft Windows Std" @selected(old('operating_system', $mpdraas->operating_system ?? '') == 'Microsoft Windows Std')>Microsoft Windows Std</option>
                    <option value="Microsoft Windows DC" @selected(old('operating_system', $mpdraas->operating_system ?? '') == 'Microsoft Windows DC')>Microsoft Windows DC</option>
                     <option value="Red Hat Enterprise Linux" @selected(old('operating_system', $mpdraas->operating_system ?? '') == 'Red Hat Enterprise Linux')>Red Hat Enterprise Linux</option>
                
</select>

                </td> 

                  <td> 
                    <input type="number" name="rds_count" class="form-control" value="{{ old('rds_count', $mpdraas->rds_count ?? '') }}" min="0"></td>
                    

                 <td>
                  <select name="m_sql" class="form-select">
                     <option value="None" @selected(old('m_sql', $mpdraas->m_sql ?? '') == 'None')>None</option>
                    <option value="Web" @selected(old('m_sql', $mpdraas->m_sql ?? '') == 'Web')>Web</option>
                    <option value="Standard" @selected(old('m_sql', $mpdraas->m_sql ?? '') == 'Standard')>Standard</option>
                     <option value="Enterprise" @selected(old('m_sql', $mpdraas->m_sql ?? '') == 'Enterprise')>Enterprise</option>
              
                
</select>

                </td> 

                <td>   <input  name="used_system_disk" id="used_system_disk" class="form-control" value="{{ old('system_disk', $mpdraas->used_system_disk ?? '') }}" readonly style="background-color: black;color: white;">
                </td>

                 <td>   <input  name="used_data_disk" id="used_data_disk" class="form-control" value="{{ old('data_disk', $mpdraas->used_data_disk ?? '') }}" readonly style="background-color: black;color: white;">
                </td>

                 <td>
                  <select name="solution_type" class="form-select">
                    <option value="None" @selected(old('solution_type', $mpdraas->solution_type ?? '') == 'None')>None</option>
                <option value="EVS" @selected(old('solution_type', $mpdraas->solution_type ?? '') == 'EVS')>EVS</option>
             
              
                
</select>

                </td> 


                 <td>   <input type="number" name="rto_expected" class="form-control" value="{{ old('rto_expected', $mpdraas->rto_expected ?? '') }}" min="0">
                </td>

                 <td>   <input type="number" name="dd_change" id="dd_change" class="form-control" value="{{ old('dd_change', $mpdraas->dd_change ?? '') }}"  min="0">
                </td>



                  <td>   <input name="data_change" id="data_change" class="form-control" value="{{ old('data_change', $mpdraas->data_change ?? '') }}" readonly style="background-color: black;color: white;">
                </td>


                  <td>   <input name="data_change_size" id="data_change_size" class="form-control" value="{{ old('data_change_size', $mpdraas->data_change_size ?? '') }}" readonly style="background-color: black;color: white;">
                </td>

                   <td>   <input type="number" name="replication_frequency" id="replication_frequency" class="form-control" value="{{ old('replication_frequency', $mpdraas->replication_frequency ?? '') }}"  min="0">
                </td>

                  <td>   <input name="num_replication" id="num_replication" class="form-control" value="{{ old('num_replication', $mpdraas->num_replication ?? '') }}" readonly style="background-color: black;color: white;">
                </td>

                  <td>   <input name="amount_data_change" id="amount_data_change" class="form-control" value="{{ old('amount_data_change', $mpdraas->amount_data_change ?? '') }}" readonly style="background-color: black;color: white;">
                </td>

                  <td>   <input name="replication_bandwidth" id="replication_bandwidth" class="form-control" value="{{ old('replication_bandwidth', $mpdraas->replication_bandwidth ?? '') }}" readonly style="background-color: black;color: white;">
                </td>

                  <td>   <input name="rpo_achieved" id="rpo_achieved" class="form-control" value="{{ old('rpo_achieved', $mpdraas->rpo_achieved ?? '') }}" readonly style="background-color: black;color: white;">
                </td>













                        </tr>--->
                        
                                           
                          
                    </tbody>
                </table>
               <div class="d-flex flex-column align-items-start">
  <button type="button" id="btn-add-vm" class="btn btn-pink mb-2">
    Add Row
  </button>

  <button type="submit" 
    class="btn fw-normal shadow-sm mb-2"
    style="
      font-weight:400;
      --bs-btn-color:#fff;
      --bs-btn-bg:#FF82E6;
      --bs-btn-border-color:#FF82E6;
      --bs-btn-hover-color:#fff;
      --bs-btn-hover-bg:#e66fd5;
      --bs-btn-hover-border-color:#e66fd5;
      --bs-btn-focus-shadow-rgb:255,130,230;
      --bs-btn-active-color:#fff;
      --bs-btn-active-bg:#d95fc3;
      --bs-btn-active-border-color:#d95fc3;
      --bs-btn-disabled-color:#fff;
      --bs-btn-disabled-bg:#f2b7eb;
      --bs-btn-disabled-border-color:#f2b7eb;">
    Save MPDRaaS
  </button>

  <!-- alert kecil, rapat bawah Save -->
  <div class="alert alert-danger mt-1 mb-0 py-1 px-2 small d-inline-block"
       role="alert"
       style="font-size:.8rem; max-width:460px;">
    ⚠️ Ensure you click <strong>Save</strong> before continuing to the next step!
  </div>
</div>
 
           <!---<div class="d-flex flex-column align-items-start">
  <button type="button" id="btn-add-vm" class="btn btn-pink">
    Add Row
  </button>


  <button type="submit" 
    class="btn fw-normal shadow-sm"
        style="
          font-weight:400;
          --bs-btn-color:#fff;
          --bs-btn-bg:#FF82E6;
          --bs-btn-border-color:#FF82E6;
          --bs-btn-hover-color:#fff;
          --bs-btn-hover-bg:#e66fd5;
          --bs-btn-hover-border-color:#e66fd5;
          --bs-btn-focus-shadow-rgb:255,130,230;
          --bs-btn-active-color:#fff;
          --bs-btn-active-bg:#d95fc3;
          --bs-btn-active-border-color:#d95fc3;
          --bs-btn-disabled-color:#fff;
          --bs-btn-disabled-bg:#f2b7eb;
          --bs-btn-disabled-border-color:#f2b7eb;">
    Save MPDRaaS
  </button>

 
  <div class="alert alert-danger mt-2 mb-0 py-1 px-2 small d-inline-block"
       role="alert"
       style="font-size:.8rem; max-width:460px;">
    ⚠️ Ensure you click <strong>Save</strong> before continuing to the next step!
  </div>
</div>--->


            </div>
</div>

            <div class="d-flex justify-content-between gap-3"> 




      @if(($solution_type->solution_type ?? '') === 'MP-DRaaS Only')

    <a href="{{ route('versions.region.network.create', $version->id) }}" class="btn btn-secondary" role="button">
        <i class="bi bi-arrow-left"></i> Previous<br>Step
    </a>

    @else


    <a href="{{ route('versions.region.dr.create', $version->id) }}" class="btn btn-secondary" role="button">
        <i class="bi bi-arrow-left"></i> Previous<br>Step
    </a>

    @endif

    

                     
  <div class="d-flex flex-column align-items-centre gap-2">
            <div class="d-flex justify-content-end gap-3"> 
                

                
                <a href="{{ route('versions.security_service.create', $version->id) }}"  
                   class="btn btn-secondary me-2" 
                   role="button">
                   Next: Managed Services & Cloud Security <i class="bi bi-arrow-right"></i>
                </a> 
            </div>

            
            

    </div>
        </form>
    </div>
</div>

@php
$FLAVOURS = [
    ['m3.micro',1,1],['m3.small',1,2],['c3.large',2,4],['m3.large',2,8],['r3.large',2,16],
    ['c3.xlarge',4,8],['m3.xlarge',4,16],['r3.xlarge',4,32],
    ['c3.2xlarge',8,16],['m3.2xlarge',8,32],['r3.2xlarge',8,64],
    ['m3.3xlarge',12,48],['c3.4xlarge',16,32],['m3.4xlarge',16,64],['r3.4xlarge',16,128],
    ['m3.6xlarge',24,96],['c3.8xlarge',32,64],['m3.8xlarge',32,128],['r3.8xlarge',32,256],
    ['r3.12xlarge',48,384],['c3.16xlarge',64,128],['m3.16xlarge',64,256],['r3.16xlarge',64,512],
    ['c3p.xlarge',4,8],['m3p.xlarge',4,16],['r3p.xlarge',4,32],['c3p.2xlarge',8,16],['m3p.2xlarge',8,32],['r3p.2xlarge',8,64],
    ['m3p.3xlarge',12,48],['c3p.4xlarge',16,32],['m3p.4xlarge',16,64],['r3p.4xlarge',16,64],
    ['m3p.6xlarge',24,96],['c3p.8xlarge',32,64],['m3p.8xlarge',32,128],['r3p.8xlarge',32,128],
    ['m3p.12xlarge',48,192],['r3p.12xlarge',48,384],['m3p.16xlarge',64,256],['r3p.16xlarge',64,512],
    ['r3p.46xlarge.metal',64,1408],['m3gnt4.xlarge',4,16],['m3gnt4.2xlarge',8,32],['m3gnt4.4xlarge',16,64],
    ['m3gnt4.8xlarge',32,128],['m3gnt4.16xlarge',64,256],['r3p.46xlarge.ddh',342,1480],
];
@endphp

<script>
(function(){
  const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const UPSERT_URL  = "{{ route('versions.mpdraas.vms.upsert', $version->id) }}";
  const DESTROY_URL_TMPL = @json(url('versions/'.$version->id.'/mpdraas/vms/:id'));

  const vmBody = document.getElementById('vm-body');
  const tmpl   = document.getElementById('vm-template').content.querySelector('tr');
  const addBtn = document.getElementById('btn-add-vm');

  function debounce(fn, ms){ let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), ms); }; }
  function ceil10(x){ return Math.ceil(x*10)/10; }

  function renumberRows(){
    vmBody.querySelectorAll('tr.vm-row').forEach((tr, idx) => {
      const no = tr.querySelector('.row-no');
      if(no){ no.textContent = idx + 1; }
      tr.querySelectorAll('[name^="rows["]').forEach(inp => {
        inp.name = inp.name.replace(/rows\[(\d+|__INDEX__)\]/, `rows[${idx}]`);
      });
      tr.dataset.index = idx;
    });
  }

  // ====== KIRA & UPDATE STORAGE SUMMARY (EVS/OBS) ======
  function updateStorageSummary(){
    const sum = {
      EVS: {main:0, used:0, delta:0},
      OBS: {main:0, used:0, delta:0},
    };

    vmBody.querySelectorAll('tr.vm-row').forEach(tr => {
      const g = sel => tr.querySelector(sel);
      const type = (g('.solution_type')?.value || 'None').toUpperCase();

      if(type !== 'EVS' && type !== 'OBS') return;

      const sys     = parseFloat(g('.system_disk')?.value || 0);
      const dat     = parseFloat(g('.data_disk')?.value || 0);
      const usedSys = parseFloat(g('.used_system_disk')?.value || 0);
      const usedDat = parseFloat(g('.used_data_disk')?.value || 0);
      const delta   = parseFloat(g('.data_change')?.value || 0); // GB

      sum[type].main  += (sys + dat);
      sum[type].used  += (usedSys + usedDat);
      sum[type].delta += delta;
    });

    const evsTotal = sum.EVS.used + sum.EVS.delta;
    const obsTotal = sum.OBS.used + sum.OBS.delta;

    // Tulis ke jadual
    const evsMainEl = document.getElementById('evs_main');
    if (evsMainEl) {
      evsMainEl.textContent = sum.EVS.main.toFixed(0);
      document.getElementById('evs_used').textContent  = sum.EVS.used.toFixed(0);
      document.getElementById('evs_delta').textContent = sum.EVS.delta.toFixed(2);
      document.getElementById('evs_total_replication').textContent = evsTotal.toFixed(2);
    }

    const obsMainEl = document.getElementById('obs_main');
    if (obsMainEl) {
      obsMainEl.textContent = sum.OBS.main.toFixed(0);
      document.getElementById('obs_used').textContent  = sum.OBS.used.toFixed(0);
      document.getElementById('obs_delta').textContent = sum.OBS.delta.toFixed(2);
      document.getElementById('obs_total_replication').textContent = obsTotal.toFixed(2);
    }

    // Simpan dalam hidden input (ikut flow asal: guna jumlah EVS)
    const mainInput  = document.getElementById('main_input');
    const usedInput  = document.getElementById('used_input');
    const deltaInput = document.getElementById('delta_input');
    const totInput   = document.getElementById('total_replication_input');
    if (mainInput && usedInput && deltaInput && totInput){
      mainInput.value  = sum.EVS.main.toFixed(0);
      usedInput.value  = sum.EVS.used.toFixed(0);
      deltaInput.value = sum.EVS.delta.toFixed(2);
      totInput.value   = evsTotal.toFixed(2);
    }
  }
  // ====== TAMAT: STORAGE SUMMARY ======

  function autoFlavour(vcpu, vram){
    const FLAVOURS = @json($FLAVOURS);
    let best = null;
    for(const [name,v,c] of FLAVOURS){
      if(v >= vcpu && c >= vram){
        if(!best || v < best[1] || (v===best[1] && c < best[2])) best = [name,v,c];
      }
    }
    return best ? best[0] : 'No suitable flavour';
  }

  function calcRow(tr){
    const g = sel => tr.querySelector(sel);

    const sys = parseFloat(g('.system_disk')?.value||0);
    const dat = parseFloat(g('.data_disk')?.value||0);
    const ddp = parseFloat(g('.dd_change')?.value||0);
    const frq = parseFloat(g('.replication_frequency')?.value||0);
    const vcpu = parseInt(g('.vcpu')?.value||0);
    const vram = parseInt(g('.vram')?.value||0);

    // sync used_* = input
    if(g('.used_system_disk')) g('.used_system_disk').value = sys;
    if(g('.used_data_disk'))   g('.used_data_disk').value = dat;

    // flavour
    if(g('.flavour_mapping')){
      const now = autoFlavour(vcpu, vram);
      g('.flavour_mapping').value = now;
    }

    // data_change (GB)
    const dchg = (sys + dat) * (ddp/100);
    if(g('.data_change')) g('.data_change').value = dchg.toFixed(2);

    // data_change_size (Mbits)
    const dsize = dchg * 1024 * 8;
    if(g('.data_change_size')) g('.data_change_size').value = Math.round(dsize);

    // num_replication / day
    const nrep = frq > 0 ? Math.ceil(1440 / frq) : 0;
    if(g('.num_replication')) g('.num_replication').value = nrep;

    // amount_data_change per sync (Mbits)
    const perSync = nrep > 0 ? Math.ceil(dsize / nrep) : 0;
    if(g('.amount_data_change')) g('.amount_data_change').value = perSync;

    // replication_bandwidth (Mbps)
    const bw = frq > 0 ? ceil10(perSync / (frq*60)) : 0;
    if(g('.replication_bandwidth')) g('.replication_bandwidth').value = bw;

    // rpo (Min)
    const rpo = bw > 0 ? (perSync / bw) / 60 : 0;
    if(g('.rpo_achieved')) g('.rpo_achieved').value = rpo.toFixed(2);

    return bw;
  }

  const debouncedSave = debounce(saveRow, 500);

  function collectPayload(tr){
    function val(sel){ const el = tr.querySelector(sel); return el ? el.value : null; }
    return {
      id: tr.dataset.id || null,
      row_no: parseInt(tr.dataset.index || 0),

      vm_name: val('.vm_name'),
      always_on: val('.always_on'),
      pin: val('.pin'),
      vcpu: +val('.vcpu') || 0,
      vram: +val('.vram') || 0,
      flavour_mapping: val('.flavour_mapping'),
      system_disk: +val('.system_disk') || 0,
      data_disk: +val('.data_disk') || 0,
      operating_system: val('.operating_system'),
      rds_count: +val('.rds_count') || 0,
      m_sql: val('.m_sql'),
      used_system_disk: +val('.used_system_disk') || 0,
      used_data_disk: +val('.used_data_disk') || 0,
      solution_type: val('.solution_type'),
      rto_expected: +val('.rto_expected') || 0,
      dd_change: +val('.dd_change') || 0,
      data_change: +val('.data_change') || 0,
      data_change_size: +val('.data_change_size') || 0,
      replication_frequency: +val('.replication_frequency') || 0,
      num_replication: +val('.num_replication') || 0,
      amount_data_change: +val('.amount_data_change') || 0,
      replication_bandwidth: +val('.replication_bandwidth') || 0,
      rpo_achieved: +val('.rpo_achieved') || 0,
    };
  }

  function saveRow(tr){
    const payload = collectPayload(tr);
    fetch(UPSERT_URL, {
      method: 'POST',
      headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(j => {
      if(!j.ok){ console.warn('upsert error', j); return; }
      tr.dataset.id = j.vm.id;
      tr.dataset.deleteUrl = DESTROY_URL_TMPL.replace(':id', j.vm.id);
    })
    .catch(err => console.error(err));
  }

  function updateTopBandwidthRequirement(){
    let maxBw = 0;
    vmBody.querySelectorAll('tr.vm-row .replication_bandwidth').forEach(inp => {
      const v = parseFloat(inp.value || 0);
      if(v > maxBw) maxBw = v;
    });
    const out = document.getElementById('bandwidth_requirement');
    if(out){
      out.value = maxBw < 2 ? '2.00' : (Math.round(maxBw*100)/100).toFixed(2);
    }
  }

  function wireRow(tr){
    const inputs = tr.querySelectorAll('input, select');
    inputs.forEach(el => {
      el.addEventListener('input', () => {
        calcRow(tr);
        updateTopBandwidthRequirement();
        updateStorageSummary();   // <= penting
        debouncedSave(tr);
      });
      el.addEventListener('change', () => {
        calcRow(tr);
        updateTopBandwidthRequirement();
        updateStorageSummary();   // <= penting
        debouncedSave(tr);
      });
    });

    const delBtn = tr.querySelector('.btn-delete-row');
    if(delBtn){
      delBtn.addEventListener('click', () => {
        const id = tr.dataset.id;
        if(!id){
          tr.remove();
          renumberRows();
          updateTopBandwidthRequirement();
          updateStorageSummary(); // <= penting
          return;
        }
        const url = (tr.dataset.deleteUrl || DESTROY_URL_TMPL.replace(':id', id));
        fetch(url, {
          method: 'DELETE',
          headers: { 'X-CSRF-TOKEN': CSRF }
        }).then(r => r.json()).then(j => {
          if(j.ok){
            tr.remove();
            renumberRows();
            updateTopBandwidthRequirement();
            updateStorageSummary(); // <= penting
          }else{
            console.warn('delete error', j);
          }
        }).catch(err => console.error(err));
      });
    }

    calcRow(tr);
  }

  // Wire sedia ada
  vmBody.querySelectorAll('tr.vm-row').forEach(wireRow);
  renumberRows();
  updateTopBandwidthRequirement();
  updateStorageSummary(); // <= kira awal

  // Add Row
  addBtn?.addEventListener('click', () => {
    const idx = vmBody.querySelectorAll('tr.vm-row').length;
    const clone = tmpl.cloneNode(true);
    clone.querySelectorAll('[name]').forEach(inp => {
      inp.name = inp.name.replace('__INDEX__', idx);
      if(inp.tagName === 'INPUT'){ inp.value = ''; }
      if(inp.classList.contains('vcpu') || inp.classList.contains('vram') ||
         inp.classList.contains('system_disk') || inp.classList.contains('data_disk') ||
         inp.classList.contains('dd_change') || inp.classList.contains('replication_frequency') ||
         inp.classList.contains('rds_count')){
        inp.value = 0;
      }
    });
    clone.dataset.index = idx;
    clone.dataset.id = '';
    vmBody.appendChild(clone);
    wireRow(clone);
    renumberRows();
    saveRow(clone);
    updateStorageSummary(); // <= selepas tambah baris
  });
})();
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