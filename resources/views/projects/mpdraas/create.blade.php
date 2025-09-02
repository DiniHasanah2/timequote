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
         
            <a href="{{ route('versions.security_service.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.security_service.create' ? 'active-link' : '' }}">Security Services</a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.non_standard_items.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.non_standard_items.create' ? 'active-link' : '' }}">Other Services</a>
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
        <!---<tr>
                            <td colspan="2">Starter Promotion (12 Months Validity)</td>
                           
                            <td colspan="3">
                                    <div class="input-group">
                                       <select name="starter_promotion" class="form-select">
    <option value="No" @selected(old('starter_promotion', $mpdraas->starter_promotion ?? '') == 'No')>No</option>
    <option value="Yes" @selected(old('starter_promotion', $mpdraas->starter_promotion ?? '') == 'Yes')>Yes</option>
</select>
                                    </div>
   
</td></tr>--->

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

    // autosave ke server
    fetch(autosaveUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token
      },
      body: JSON.stringify({ code: code, kl_qty: klQty, cj_qty: cjQty })
    }).then(r => r.json()).then(j => {
      if(!j.ok){ console.warn('Autosave error', j); }
      // kalau nak update summary total global, boleh baca j.summary
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



                 <!-- <table class="table table-bordered w-auto">
                      <body>
                        <thead class="table-dark">
                            <tr>
                                <th colspan="15">Disaster Recovery Components - ONLY ACTIVATED DURING DR</th>
                            </tr>
                        </thead>



                          <tr>
                            <td class="bg-light fw-bold" colspan="3">NETWORK</td>
                            <td class="bg-light  fw-bold" colspan="2">QUANTITY</td>
                            <td class="bg-light  fw-bold" colspan="3">MONTHLY RECURRING CHARGES</td>
                        
  




                       
                        
                            
</tr>
 <tr>
                            <td class="bg-light">Code</td>
                             <td class="bg-light">Component Name</td>
                              <td class="bg-light">Unit</td>
                               <td class="bg-light">KL</td>
                                <td class="bg-light">CJ</td>
                                 <td class="bg-light">KL Charges</td>
                                  <td class="bg-light">CJ Charges</td>
                                   <td class="bg-light">Total</td>
                                    
                            
</tr>
        

<tr><td>CNET-BWS-SHR-DAY</td> <td>Network
    <br>(Per Day) DR Bandwidth
</td><td>Mbps</td>
<td> <input type="number" name="" class="form-control" value="" min="0">
                </td>
<td><input type="number" name="" class="form-control" value="" min="0"></td>
<td>RM-</td>
<td>RM-</td>
<td>RM-</td></tr>

<tr><td>CNET-BWD-SHR-DAY</td><td>Network
     <br>(Per Day) DR Bandwidth + AntiDDoS
</td><td>Mbps</td>
<td> <input type="number" name="" class="form-control" value="" min="0">
                </td>
<td><input type="number" name="" class="form-control" value="" min="0"></td>
<td>RM-</td>
<td>RM-</td>
<td>RM-</td></tr>

<tr><td>CNET-EIP-SHR-DAY</td><td>Network
     <br>(Per Day) DR Elastic IP
</td>
<td>Unit</td>
<td> <input type="number" name="" class="form-control" value="" min="0">
                </td>
<td><input type="number" name="" class="form-control" value="" min="0"></td>
<td>RM-</td>
<td>RM-</td>
<td>RM-</td></tr>

<tr><td>CMDR-ELB-DRD-STD</td><td>Elastic Load Balancer</td><td>Unit</td>
<td> <input type="number" name="" class="form-control" value="" min="0">
                </td>
<td><input type="number" name="" class="form-control" value="" min="0"></td>
<td>RM-</td>
<td>RM-</td>
<td>RM-</td></tr>


<tr><td>CMDR-NAT-DRD-S</td><td>NAT GATEWAY (Small)</td><td>Unit</td>
<td> <input type="number" name="" class="form-control" value="" min="0">
                </td>
<td><input type="number" name="" class="form-control" value="" min="0"></td>
<td>RM-</td>
<td>RM-</td>
<td>RM-</td></tr>
<tr><td>CMDR-NAT-DRD-M</td>
<td>NAT GATEWAY (Medium)</td><td>Unit</td>
<td> <input type="number" name="" class="form-control" value="" min="0">
                </td>
<td><input type="number" name="" class="form-control" value="" min="0"></td>
<td>RM-</td>
<td>RM-</td>
<td>RM-</td></tr>
<tr><td>CMDR-NAT-DRD-L</td><td>NAT GATEWAY (Large)</td><td>Unit</td><td> <input type="number" name="" class="form-control" value="" min="0">
                </td>
<td><input type="number" name="" class="form-control" value="" min="0"></td>
<td>RM-</td>
<td>RM-</td>
<td>RM-</td></tr>
<tr><td>CMDR-NAT-DRD-XL</td><td>NAT GATEWAY (Extra-Large)</td><td>Unit</td><td> <input type="number" name="" class="form-control" value="" min="0">
                </td>
<td><input type="number" name="" class="form-control" value="" min="0"></td>
<td>RM-</td>
<td>RM-</td>
<td>RM-</td></tr>
<tr><td>CSEC-VFW-DDT-FGDAY</td><td>Additional Services - Security
     <br>(Per Day) DR Cloud Firewall (Fortigate)
</td><td>Unit</td><td> <input type="number" name="" class="form-control" value="" min="0">
                </td>
<td><input type="number" name="" class="form-control" value="" min="0"></td>
<td>RM-</td>
<td>RM-</td>
<td>RM-</td></tr>
<tr><td>CSEC-VFW-DDT-OSDAY</td> <td>Additional Services - Security
     <br>(Per Day) DR Cloud Firewall (OPNSense)
</td><td>Unit</td><td> <input type="number" name="" class="form-control" value="" min="0">
                </td>
<td><input type="number" name="" class="form-control" value="" min="0"></td>
<td>RM-</td>
<td>RM-</td>
<td>RM-</td></tr>



                            
     


                                           
                          
                    </tbody>
                </table>--->

                
            </div>
 <!-- Table Kanan -->

 
  
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
                                <th colspan="22"><strong>DR Resources</strong></th>
                                <!---<th class="bg-light text-dark border border-secondary-subtle" style="border: 1px solid #dee2e6;">DDoS Requirement?</th>
                            <th class="bg-light text-dark border border-secondary-subtle" style="border: 1px solid #dee2e6;">
                             
                            <div class="input-group">
                                       <select name="ddos_requirement" class="form-select">
    <option value="No" @selected(old('ddos_requirement', $mpdraas->ddos_requirement ?? '') == 'No')>No</option>
    <option value="Yes" @selected(old('ddos_requirement', $mpdraas->ddos_requirement ?? '') == 'Yes')>Yes</option>
</select>
                                    </div>
                            </th>--->
                           
                            </tr>
                       
                                   <tr>
                            <th colspan="2"></th>
                            <th colspan="4">ECS</th>
                            <th colspan="2">Storage</th>
                            <th colspan="3">Monthly License</th>
                            <th colspan="3">Utilization</th>
                            <th colspan="10"></th>
                            <!---<th class="bg-light text-dark border border-secondary-subtle" style="border: 1px solid #dee2e6;">Bandwidth Requirement for Replication</th>
                            <th class="bg-light text-dark border border-secondary-subtle" style="border: 1px solid #dee2e6;">
                                  <div class="input-group">
    <input name="bandwidth_requirement" id="bandwidth_requirement"
           class="form-control bg-light" 
         value="{{ old('bandwidth_requirement', $mpdraas->bandwidth_requirement ?? '') }}" 
                   readonly style="border-radius: 0;">
</div>
                            </th>--->
                            
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

                        </tr>
</thead>


                        <tr>
                         <td>1</td>
                          <td><input type="text" name="vm_name" class="form-control w-100" style="min-width: 120px;" value="{{ old('vm_name', $mpdraas->vm_name ?? '') }}"></td>
                          <td>    
                        
                                       <select name="always_on" class="form-select w-100" style="min-width: 120px;">
    <option value="No" @selected(old('always_on', $mpdraas->always_on ?? '') == 'No')>No</option>
    <option value="Yes" @selected(old('always_on', $mpdraas->always_on ?? '') == 'Yes')>Yes</option>
</select>
                                   </td>
                                 
                         
                                       <!--- <td><select name="pin" class="form-select">
    <option value="No" @selected(old('pin', $mpdraas->pin ?? '') == 'No')>No</option>
    <option value="Yes" @selected(old('pin', $mpdraas->pin ?? '') == 'Yes')>Yes</option>
</select>
                                    </td>
                                      <td><input type="number" name="vcpu" class="form-control" value="{{ old('vcpu', $mpdraas->vcpu ?? '') }}" min="0"></td>
                                       <td><input type="number" name="vram" class="form-control" value="{{ old('vram', $mpdraas->vram ?? '') }}" min="0"></td>--->

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
                <!---<option value="OBS" @selected(old('solution_type', $mpdraas->solution_type ?? '') == 'OBS')>OBS</option>--->
              
                
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













                        </tr>
                        
                                           
                          
                    </tbody>
                </table>
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
            <div class="d-flex justify-content-end gap-3"> <!-- Added gap-3 for spacing -->
                <button type="submit" class="btn btn-pink">Save MPDRaaS</button>

                
                <a href="{{ route('versions.security_service.create', $version->id) }}"  
                   class="btn btn-secondary me-2" 
                   role="button">
                   Next: Security Services <i class="bi bi-arrow-right"></i>
                </a> 
            </div>

            
              <div class="alert alert-danger py-1 px-2 small mb-0" role="alert" style="font-size: 0.8rem;">
            ⚠️ Ensure you click <strong>Save</strong> before continuing to the next step!
    </div>

    </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const systemDiskInput = document.getElementById('system_disk');
        const dataDiskInput = document.getElementById('data_disk');
        const usedSystemDisk = document.getElementById('used_system_disk');
        const usedDataDisk = document.getElementById('used_data_disk');

        function syncUsedSystemDisk() {
            usedSystemDisk.value = systemDiskInput.value;
        }

        function syncUsedDataDisk() {
            usedDataDisk.value = dataDiskInput.value;
        }

        // Initial sync on page load
        syncUsedSystemDisk();
        syncUsedDataDisk();

        // Sync on input
        systemDiskInput.addEventListener('input', syncUsedSystemDisk);
        dataDiskInput.addEventListener('input', syncUsedDataDisk);
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const systemDiskInput = document.getElementById('system_disk');
    const dataDiskInput = document.getElementById('data_disk');
    const ddChangeInput = document.getElementById('dd_change');
    const dataChangeOutput = document.getElementById('data_change');

    function calculateDataChange() {
        const system = parseFloat(systemDiskInput.value) || 0;
        const data = parseFloat(dataDiskInput.value) || 0;
        const ddPercent = parseFloat(ddChangeInput.value) || 0;

        const totalDisk = system + data;
        const result = totalDisk * (ddPercent / 100);

        dataChangeOutput.value = result.toFixed(2);
    }

    // Recalculate whenever user changes any field
    systemDiskInput.addEventListener('input', calculateDataChange);
    dataDiskInput.addEventListener('input', calculateDataChange);
    ddChangeInput.addEventListener('input', calculateDataChange);

    // Initial calculation on page load
    calculateDataChange();
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const systemDiskInput = document.getElementById('system_disk');
    const dataDiskInput = document.getElementById('data_disk');
    const ddChangeInput = document.getElementById('dd_change');
    const dataChangeSizeOutput = document.getElementById('data_change_size');

    function calculateDataChangeSize() {
        const system = parseFloat(systemDiskInput.value) || 0;
        const data = parseFloat(dataDiskInput.value) || 0;
        const ddPercent = parseFloat(ddChangeInput.value) || 0;

        const totalDisk = system + data;
        const dataChangeSize = totalDisk * (ddPercent / 100) * 1024 * 8;

        dataChangeSizeOutput.value = dataChangeSize.toFixed(2); // in Megabits
    }

    // Run once on load
    calculateDataChangeSize();

    // Update whenever inputs change
    systemDiskInput.addEventListener('input', calculateDataChangeSize);
    dataDiskInput.addEventListener('input', calculateDataChangeSize);
    ddChangeInput.addEventListener('input', calculateDataChangeSize);
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const replicationFrequencyInput = document.getElementById('replication_frequency');
    const numReplicationOutput = document.getElementById('num_replication');

    function calculateNumReplication() {
        const freq = parseFloat(replicationFrequencyInput.value) || 0;

        if (freq > 0) {
            const result = Math.ceil(1440 / freq); // 1440 mins/day
            numReplicationOutput.value = result;
        } else {
            numReplicationOutput.value = 0;
        }
    }

    // Run on load
    calculateNumReplication();

    // Recalculate on input
    replicationFrequencyInput.addEventListener('input', calculateNumReplication);
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const systemDiskInput = document.getElementById('system_disk');
    const dataDiskInput = document.getElementById('data_disk');
    const usedSystemDiskInput = document.getElementById('used_system_disk');
    const usedDataDiskInput = document.getElementById('used_data_disk');
    const ddChangeInput = document.getElementById('dd_change');
    const dataChangeInput = document.getElementById('data_change');
    const dataChangeSizeInput = document.getElementById('data_change_size');
    const replicationFrequencyInput = document.getElementById('replication_frequency');
    const numReplicationInput = document.getElementById('num_replication');
    const amountDataChangeInput = document.getElementById('amount_data_change');
    const replicationBandwidthInput = document.getElementById('replication_bandwidth');
    const rpoAchievedInput = document.getElementById('rpo_achieved');
    const bandwidthRequirementInput = document.getElementById('bandwidth_requirement');

    function calculateAll() {
        const system = parseFloat(systemDiskInput.value) || 0;
        const data = parseFloat(dataDiskInput.value) || 0;
        const ddPercent = parseFloat(ddChangeInput.value) || 0;
        const freq = parseFloat(replicationFrequencyInput.value) || 0;

        // Sync used_system_disk & used_data_disk
        usedSystemDiskInput.value = system;
        usedDataDiskInput.value = data;

        // 1. data_change
        const dataChange = (system + data) * (ddPercent / 100);
        dataChangeInput.value = dataChange.toFixed(2);

        // 2. data_change_size
        const dataChangeSize = dataChange * 1024 * 8;
        dataChangeSizeInput.value = dataChangeSize.toFixed(0);

        // 3. num_replication
        const numReplication = freq > 0 ? Math.ceil(1440 / freq) : 0;
        numReplicationInput.value = numReplication;

        // 4. amount_data_change
        const amountDataChange = (numReplication > 0)
            ? Math.ceil(dataChangeSize / numReplication)
            : 0;
        amountDataChangeInput.value = amountDataChange;

        // 5. replication_bandwidth
        const bandwidth = (freq > 0)
            ? Math.ceil((amountDataChange / (freq * 60)) * 10) / 10
            : 0;
        replicationBandwidthInput.value = bandwidth;

        // 6. rpo_achieved
        const rpo = (bandwidth > 0)
            ? (amountDataChange / bandwidth) / 60
            : 0;
        rpoAchievedInput.value = rpo.toFixed(2);

        // 7. bandwidth_requirement
        bandwidthRequirementInput.value = bandwidth < 2 ? 2 : Math.round(bandwidth);
    }

    // Trigger on change of any dependent input
    [
        systemDiskInput,
        dataDiskInput,
        ddChangeInput,
        replicationFrequencyInput
    ].forEach(input => input.addEventListener('input', calculateAll));

    // Initial run
    calculateAll();
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const solutionTypeInput = document.querySelector('select[name="solution_type"]');
    const systemDiskInput = document.getElementById('system_disk');
    const dataDiskInput = document.getElementById('data_disk');
    const usedSystemDiskInput = document.getElementById('used_system_disk');
    const usedDataDiskInput = document.getElementById('used_data_disk');
    const dataChangeInput = document.getElementById('data_change');

    const evsMain = document.getElementById('evs_main');
    const evsUsed = document.getElementById('evs_used');
    const evsDelta = document.getElementById('evs_delta');
    const evsTotalReplication = document.getElementById('evs_total_replication');

    function updateEVSStorageSummary() {
        const selected = solutionTypeInput.value;
        if (selected !== 'EVS') {
            evsMain.textContent = '0';
            evsUsed.textContent = '0';
            evsDelta.textContent = '0';
            evsTotalReplication.textContent = '0';
            return;
        }

        const systemDisk = parseFloat(systemDiskInput.value) || 0;
        const dataDisk = parseFloat(dataDiskInput.value) || 0;
        const usedSystemDisk = parseFloat(usedSystemDiskInput.value) || 0;
        const usedDataDisk = parseFloat(usedDataDiskInput.value) || 0;
        const dataChange = parseFloat(dataChangeInput.value) || 0;

        const main = systemDisk + dataDisk;
        const used = usedSystemDisk + usedDataDisk;
        const delta = dataChange;
        const totalReplication = used + delta;

        evsMain.textContent = main.toFixed(0);
        evsUsed.textContent = used.toFixed(0);
        evsDelta.textContent = delta.toFixed(2);
        evsTotalReplication.textContent = totalReplication.toFixed(2);

        document.getElementById('main_input').value = main.toFixed(0);
document.getElementById('used_input').value = used.toFixed(0);
document.getElementById('delta_input').value = delta.toFixed(2);
document.getElementById('total_replication_input').value = totalReplication.toFixed(2);

    }




    
    // Trigger when relevant inputs change
    [
        solutionTypeInput,
        systemDiskInput,
        dataDiskInput,
        usedSystemDiskInput,
        usedDataDiskInput,
        dataChangeInput
    ].forEach(input => {
        input.addEventListener('input', updateEVSStorageSummary);
        input.addEventListener('change', updateEVSStorageSummary);
    });

    // Run on page load
    updateEVSStorageSummary();
});
</script>




<script>
document.addEventListener('DOMContentLoaded', function () {
    const solutionTypeInput = document.querySelector('select[name="solution_type"]');
    const systemDiskInput = document.getElementById('system_disk');
    const dataDiskInput = document.getElementById('data_disk');
    const usedSystemDiskInput = document.getElementById('used_system_disk');
    const usedDataDiskInput = document.getElementById('used_data_disk');
    const dataChangeInput = document.getElementById('data_change');

    const obsMain = document.getElementById('obs_main');
    const obsUsed = document.getElementById('obs_used');
    const obsDelta = document.getElementById('obs_delta');
    const obsTotalReplication = document.getElementById('obs_total_replication');

    function updateOBSStorageSummary() {
        const selected = solutionTypeInput.value;
        if (selected !== 'OBS') {
            obsMain.textContent = '0';
            obsUsed.textContent = '0';
            obsDelta.textContent = '0';
            obsTotalReplication.textContent = '0';
            return;
        }

        const systemDisk = parseFloat(systemDiskInput.value) || 0;
        const dataDisk = parseFloat(dataDiskInput.value) || 0;
        const usedSystemDisk = parseFloat(usedSystemDiskInput.value) || 0;
        const usedDataDisk = parseFloat(usedDataDiskInput.value) || 0;
        const dataChange = parseFloat(dataChangeInput.value) || 0;

        const main = systemDisk + dataDisk;
        const used = usedSystemDisk + usedDataDisk;
        const delta = dataChange;
        const totalReplication = used + delta;

        obsMain.textContent = main.toFixed(0);
        obsUsed.textContent = used.toFixed(0);
        obsDelta.textContent = delta.toFixed(2);
        obsTotalReplication.textContent = totalReplication.toFixed(2);

        document.getElementById('main_input').value = main.toFixed(0);
document.getElementById('used_input').value = used.toFixed(0);
document.getElementById('delta_input').value = delta.toFixed(2);
document.getElementById('total_replication_input').value = totalReplication.toFixed(2);

    }

    // Trigger when relevant inputs change
    [
        solutionTypeInput,
        systemDiskInput,
        dataDiskInput,
        usedSystemDiskInput,
        usedDataDiskInput,
        dataChangeInput
    ].forEach(input => {
        input.addEventListener('input', updateOBSStorageSummary);
        input.addEventListener('change', updateOBSStorageSummary);
    });

    // Run on page load
    updateOBSStorageSummary();
});
</script>



<!---<script>
document.addEventListener('DOMContentLoaded', function () {
    const dataChangeSizeInput = document.getElementById('data_change_size');
    const numReplicationInput = document.getElementById('num_replication');
    const amountDataChangeOutput = document.getElementById('amount_data_change');

    function calculateAmountDataChange() {
        const dataChangeSize = parseFloat(dataChangeSizeInput.value) || 0;
        const numReplication = parseFloat(numReplicationInput.value) || 0;

        if (numReplication > 0) {
            const result = Math.ceil(dataChangeSize / numReplication);
            amountDataChangeOutput.value = result;
        } else {
            amountDataChangeOutput.value = 0;
        }
    }

    // Calculate when related values change
    dataChangeSizeInput.addEventListener('input', calculateAmountDataChange);
    numReplicationInput.addEventListener('input', calculateAmountDataChange);

    // Run on page load
    calculateAmountDataChange();
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const amountDataChangeInput = document.getElementById('amount_data_change');
    const replicationFrequencyInput = document.getElementById('replication_frequency');
    const replicationBandwidthOutput = document.getElementById('replication_bandwidth');

    function calculateReplicationBandwidth() {
        const amountData = parseFloat(amountDataChangeInput.value) || 0;
        const frequency = parseFloat(replicationFrequencyInput.value) || 0;

        if (frequency > 0) {
            const seconds = frequency * 60;
            const raw = amountData / seconds;
            const rounded = Math.ceil(raw * 10) / 10; // round up to 1 decimal
            replicationBandwidthOutput.value = rounded;
        } else {
            replicationBandwidthOutput.value = '';
        }
    }

    // Run on load
    calculateReplicationBandwidth();

    // Recalculate on input change
    amountDataChangeInput.addEventListener('input', calculateReplicationBandwidth);
    replicationFrequencyInput.addEventListener('input', calculateReplicationBandwidth);
});
</script>



<script>
document.addEventListener('DOMContentLoaded', function () {
    const amountDataChangeInput = document.getElementById('amount_data_change');
    const replicationBandwidthInput = document.getElementById('replication_bandwidth');
    const rpoAchievedOutput = document.getElementById('rpo_achieved');

    function calculateRPOAchieved() {
        const amountData = parseFloat(amountDataChangeInput.value) || 0;
        const bandwidth = parseFloat(replicationBandwidthInput.value) || 0;

        if (bandwidth > 0) {
            const result = (amountData / bandwidth) / 60;
            rpoAchievedOutput.value = result.toFixed(2); // or toFixed(0) if you prefer minutes rounded
        } else {
            rpoAchievedOutput.value = 0;
        }
    }

    // Trigger on page load
    calculateRPOAchieved();

    // Recalculate when input changes
    amountDataChangeInput.addEventListener('input', calculateRPOAchieved);
    replicationBandwidthInput.addEventListener('input', calculateRPOAchieved);
});
</script>--->

<script>
document.addEventListener('DOMContentLoaded', function () {
    const replicationBandwidthInput = document.getElementById('replication_bandwidth');
    const bandwidthRequirementOutput = document.getElementById('bandwidth_requirement');

    function calculateBandwidthRequirement() {
        const replicationBandwidth = parseFloat(replicationBandwidthInput.value) || 0;
        const result = replicationBandwidth < 2 ? 2 : replicationBandwidth;
        bandwidthRequirementOutput.value = result.toFixed(2); // or toFixed(0) if you want it rounded
    }

    // Run on load
    calculateBandwidthRequirement();

    // Recalculate when replication_bandwidth changes
    replicationBandwidthInput.addEventListener('input', calculateBandwidthRequirement);
});
</script>




<script>
    // Fungsi untuk menghitung flavour mapping
    function calculateFlavourMapping(vcpuInput, vramInput) {
        // Daftar flavour yang tersedia (dapat disesuaikan)
        const flavours = [
            { name: 'm3.micro', vcpu: 1, vram: 1 },
            { name: 'm3.small', vcpu: 1, vram: 2 },
            { name: 'c3.large', vcpu: 2, vram: 4 },
            { name: 'm3.large', vcpu: 2, vram: 8 },
            { name: 'r3.large', vcpu: 2, vram: 16 },
            { name: 'c3.xlarge', vcpu: 4, vram: 8 },
            { name: 'm3.xlarge', vcpu: 4, vram: 16 },
            { name: 'r3.xlarge', vcpu: 4, vram: 32 },

            { name: 'c3.2xlarge', vcpu: 8, vram: 16 },
            { name: 'm3.2xlarge', vcpu: 8, vram: 32 },
            { name: 'r3.2xlarge', vcpu: 8, vram: 64 },
            { name: 'm3.3xlarge', vcpu: 12, vram: 48 },
            { name: 'c3.4xlarge', vcpu: 16, vram: 32 },
            { name: 'm3.4xlarge', vcpu: 16, vram: 64 },
            { name: 'r3.4xlarge', vcpu: 16, vram: 128 },
            { name: 'm3.6xlarge', vcpu: 24, vram: 96 },
            { name: 'c3.8xlarge', vcpu: 32, vram: 64 },
            { name: 'm3.8xlarge', vcpu: 32, vram: 128 },
            { name: 'r3.8xlarge', vcpu: 32, vram: 256 },
            { name: 'r3.12xlarge', vcpu: 48, vram: 384 },
            { name: 'c3.16xlarge', vcpu: 64, vram: 128 },
            { name: 'm3.16xlarge', vcpu: 64, vram: 256 },
            { name: 'r3.16xlarge', vcpu: 64, vram: 512 },

            { name: 'c3p.xlarge', vcpu: 4, vram: 8 },
            { name: 'm3p.xlarge', vcpu: 4, vram: 16 },
            { name: 'r3p.xlarge', vcpu: 4, vram: 32 },
            { name: 'c3p.2xlarge', vcpu: 8, vram: 16 },
            { name: 'm3p.2xlarge', vcpu: 8, vram: 32 },
            { name: 'r3p.2xlarge', vcpu: 8, vram: 64 },
            { name: 'm3p.3xlarge', vcpu: 12, vram: 48 },
            { name: 'c3p.4xlarge', vcpu: 16, vram: 32 },
             { name: 'm3p.4xlarge', vcpu: 16, vram: 64 },
    { name: 'r3p.4xlarge', vcpu: 16, vram: 64 },
    { name: 'm3p.6xlarge', vcpu: 24, vram: 96 },
    { name: 'c3p.8xlarge', vcpu: 32, vram: 64 },
    { name: 'm3p.8xlarge', vcpu: 32, vram: 128 },
    { name: 'r3p.8xlarge', vcpu: 32, vram: 128 },
    { name: 'm3p.12xlarge', vcpu: 48, vram: 192 },
    { name: 'r3p.12xlarge', vcpu: 48, vram: 384 },
    { name: 'm3p.16xlarge', vcpu: 64, vram: 256 },
    { name: 'r3p.16xlarge', vcpu: 64, vram: 512 },
    { name: 'r3p.46xlarge.metal', vcpu: 64, vram: 1408 },
    { name: 'm3gnt4.xlarge', vcpu: 4, vram: 16 },
    { name: 'm3gnt4.2xlarge', vcpu: 8, vram: 32 },
    { name: 'm3gnt4.4xlarge', vcpu: 16, vram: 64 },
    { name: 'm3gnt4.8xlarge', vcpu: 32, vram: 128 },
    { name: 'm3gnt4.16xlarge', vcpu: 64, vram: 256 },
    { name: 'r3p.46xlarge.ddh', vcpu: 342, vram: 1480 }




          
          
        ];

        // Filter flavour yang memenuhi kebutuhan
        const suitableFlavours = flavours.filter(flavour => 
            flavour.vcpu >= vcpuInput && flavour.vram >= vramInput
        );

        // Urutkan berdasarkan vCPU dan vRAM terkecil
        suitableFlavours.sort((a, b) => {
            if (a.vcpu !== b.vcpu) return a.vcpu - b.vcpu;
            return a.vram - b.vram;
        });

        // Ambil flavour pertama yang memenuhi atau kembalikan null
        return suitableFlavours.length > 0 ? suitableFlavours[0].name : null;
    }





    // Event listener untuk input vCPU dan vRAM
    document.addEventListener('DOMContentLoaded', function() {
        const vcpuInput = document.querySelector('input[name="vcpu"]');
        const vramInput = document.querySelector('input[name="vram"]');
        const flavourMappingInput = document.querySelector('input[name="flavour_mapping"]');

        function updateFlavourMapping() {
            const vcpu = parseInt(vcpuInput.value) || 0;
            const vram = parseInt(vramInput.value) || 0;
            
            if (vcpu > 0 && vram > 0) {
                const flavour = calculateFlavourMapping(vcpu, vram);
                flavourMappingInput.value = flavour || 'No suitable flavour found';
            } else {
                flavourMappingInput.value = '';
            }
        }

        vcpuInput.addEventListener('input', updateFlavourMapping);
        vramInput.addEventListener('input', updateFlavourMapping);
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