@extends('layouts.app')

@php
    $solution_type = $solution_type ?? $version->solution_type ?? null;
    
@endphp


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
    <!---<div class="card-body">
      
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 border-end">
                        <div class="text-muted small">PROJECT</div>
                        <div class="fw-bold">{{ $project->name }}</div>
                        <div class="text-muted small mt-1">ID: {{ $project->id }}</div>
                    </div>
                    
                    <div class="col-md-3 border-end">
                        <div class="text-muted small">CUSTOMER</div>
                        <div class="fw-bold">{{ $project->customer->name ?? 'N/A' }}</div>
                        <div class="text-muted small mt-1">ID: {{ $project->customer_id }}</div>
                    </div>
                    
                    <div class="col-md-3 border-end">
                        <div class="text-muted small">VERSION</div>
                        <div class="fw-bold">{{ $version->version_name }}</div>
                        <div class="text-muted small mt-1">v{{ $version->version_number }}</div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="text-muted small">PRESALE</div>
                        <div class="fw-bold">{{ $project->presale->name ?? $project->presale->email ?? 'Unassigned' }}</div>
                        <div class="text-muted small mt-1">{{ $project->created_at->format('d M Y') }}</div>
                    </div>
                </div>
            </div>
        </div>---->

<div class="card-body">
    @if(empty($missing ?? []))
        <div class="table-responsive">

        <div class="card-body">
                <div class="row">
                    <div class="col-md-3 border-end">
                        <div class="text-muted small">PROJECT</div>
                        <div class="fw-bold">{{ $project->name }}</div>
                        <div class="text-muted small mt-1">ID: {{ $project->id }}</div>
                    </div>
                    
                    <div class="col-md-3 border-end">
                        <div class="text-muted small">CUSTOMER</div>
                        <div class="fw-bold">{{ $project->customer->name ?? 'N/A' }}</div>
                        <div class="text-muted small mt-1">ID: {{ $project->customer_id }}</div>
                    </div>
                    
                    <div class="col-md-3 border-end">
                        <div class="text-muted small">VERSION</div>
                        <div class="fw-bold">{{ $version->version_name }}</div>
                        <div class="text-muted small mt-1">v{{ $version->version_number }}</div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="text-muted small">PRESALE</div>
                        <div class="fw-bold">{{ $project->presale->name ?? $project->presale->email ?? 'Unassigned' }}</div>
                        <div class="text-muted small mt-1">{{ $project->created_at->format('d M Y') }}</div>
                    </div>
                </div>
            </div>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th></th>
                        <th></th>
                        <th>Kuala Lumpur</th>
                        <th>Cyberjaya</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Professional Services -->
                    <tr>
                        <td style="background-color: #e76ccf;font-weight: bold;">Professional Services</td>
                        <td style="background-color: #e76ccf;font-weight: bold;">Unit</td>
                        <td style="background-color: #e76ccf;font-weight: bold;">Qty</td>
                        <td style="background-color: #e76ccf;font-weight: bold;">Qty</td>
                    </tr>
                    <tr>
                        <td>Professional Services (ONE TIME Provisioning)</td>
                        <td>Days</td>
                       <td colspan="2">{{ $summary->mandays ?? 0 }}</td>

                    </tr>
                  <tr>
    <td>Migration Tools One Time Charge</td>
    <td>Unit Per Month*</td>

    {{-- License Count (Unit) --}}
    <td>
        {{ 
            $summary->kl_license_count 
            ?? $summary->cyber_license_count 
            ?? 0 
        }} Unit
    </td>

    {{-- Duration (Months) --}}
    <td>
        {{ 
            $summary->kl_duration 
            ?? $summary->cyber_duration 
            ?? 0 
        }} Months
    </td>
</tr>

                    
                    <!-- Managed Services -->
                    <tr>
                        <td style="background-color: #e76ccf;font-weight: bold;">Managed Services</td>
                        <td style="background-color: #e76ccf;font-weight: bold;">Unit</td>
                        <td style="background-color: #e76ccf;font-weight: bold;">KL.Qty</td>
                        <td style="background-color: #e76ccf;font-weight: bold;">CJ.Qty</td>
                    </tr>


@php
    $services = [
        'Managed Operating System',
        'Managed Backup and Restore',
        'Managed Patching',
        'Managed DR',
    ];
@endphp

@foreach($services as $service)
<tr>
    <td>{{ $service }}</td>
    <td>VM</td>
    <td>{{ $klManagedServices[$service] ?? 0 }}</td>
    <td>{{ $cyberManagedServices[$service] ?? 0 }}</td>
</tr>
@endforeach



                    <!-- Network -->
                    <tr>
                        <td style="background-color: #e76ccf;font-weight: bold;">Network</td>
                        <td style="background-color: #e76ccf;font-weight: bold;">Unit</td>
                        <td style="background-color: #e76ccf;font-weight: bold;">KL.Qty</td>
                        <td style="background-color: #e76ccf;font-weight: bold;">CJ.Qty</td>
                    </tr>
                    <tr>
    <td>Bandwidth</td>
    <td>Mbps</td>
    <td>{{ $summary->kl_bandwidth ?? 0 }}</td>
    <td>{{ $summary->cyber_bandwidth ?? 0 }}</td>
</tr>
<tr>
    <td>Bandwidth with Anti-DDoS</td>
    <td>Mbps</td>
    <td>{{ $summary->kl_bandwidth_with_antiddos ?? 0 }}</td>
    <td>{{ $summary->cyber_bandwidth_with_antiddos ?? 0 }}</td>
</tr>
<tr>
    <td>Included Elastic IP (FOC)</td>
    <td>Unit</td>
    <td>{{ $summary->kl_included_elastic_ip ?? 0 }}</td>
    <td>{{ $summary->cyber_included_elastic_ip ?? 0 }}</td>
</tr>
<tr>
    <td>Elastic IP</td>
    <td>Unit</td>
    <td>{{ $summary->kl_elastic_ip ?? 0 }}</td>
    <td>{{ $summary->cyber_elastic_ip ?? 0 }}</td>
</tr>
<tr>
    <td>Elastic Load Balancer (External)</td>
    <td>Unit</td>
    <td>{{ $summary->kl_elastic_load_balancer ?? 0 }}</td>
    <td>{{ $summary->cyber_elastic_load_balancer ?? 0 }}</td>
</tr>
<tr>
    <td>Direct Connect Virtual Gateway</td>
    <td>Unit</td>
    <td>{{ $summary->kl_direct_connect_virtual ?? 0 }}</td>
    <td>{{ $summary->cyber_direct_connect_virtual ?? 0 }}</td>
</tr>
<tr>
    <td>L2BR instance</td>
    <td>Unit</td>
    <td>{{ $summary->kl_l2br_instance ?? 0 }}</td>
    <td>{{ $summary->cyber_l2br_instance ?? 0 }}</td>
</tr>
<tr>
    <td>Virtual Private Leased Line (vPLL)</td>
    <td>Mbps</td>
    <td>{{ $summary->kl_virtual_private_leased_line ?? 0 }}</td>
    <td><input class="form-control bg-light text-muted" disabled></td>
</tr>
<tr>
    <td>vPLL L2BR</td>
    <td>Pair</td>
    <td>{{ $summary->kl_vpll_l2br ?? 0 }}</td>
    <td><input class="form-control bg-light text-muted" disabled></td>
</tr>
<tr>
    <td>NAT Gateway (Small)</td>
    <td>Unit</td>
    <td>{{ $summary->kl_nat_gateway_small ?? 0 }}</td>
    <td>{{ $summary->cyber_nat_gateway_small ?? 0 }}</td>
</tr>
<tr>
    <td>NAT Gateway (Medium)</td>
    <td>Unit</td>
    <td>{{ $summary->kl_nat_gateway_medium ?? 0 }}</td>
    <td>{{ $summary->cyber_nat_gateway_medium ?? 0 }}</td>
</tr>
<tr>
    <td>NAT Gateway (Large)</td>
    <td>Unit</td>
    <td>{{ $summary->kl_nat_gateway_large ?? 0 }}</td>
    <td>{{ $summary->cyber_nat_gateway_large ?? 0 }}</td>
</tr>
<tr>
    <td>NAT Gateway (Extra-Large)</td>
    <td>Unit</td>
    <td>{{ $summary->kl_nat_gateway_xlarge ?? 0 }}</td>
    <td>{{ $summary->cyber_nat_gateway_xlarge ?? 0 }}</td>
</tr>
<tr>
    <td>Managed Global Server Load Balancer (GSLB)</td>
    <td>Domain</td>
    <td>{{ $summary->kl_gslb ?? 0 }}</</td>
    <td>{{ $summary->cyber_gslb ?? 0 }}</</td>
</tr>










<tr>
    <td colspan="4" style="background-color: #e76ccf; font-weight: bold;">Computing</td>
</tr>

<thead class="table-light">
    <tr>
        <th>Compute - Elastic Cloud Server (ECS)</th>
        <th>Sizing</th>
        <th>KL.Qty</th>
        <th>CJ.Qty</th>
    </tr>
</thead>

@php
    $ecsFlavours = collect(config('flavours'))->keyBy('name');

    // List semua flavour yang digunakan sama ada di KL atau CJ
    $usedFlavours = collect($ecsSummary['Kuala Lumpur'] ?? [])
        ->keys()
        ->merge(collect($ecsSummary['Cyberjaya'] ?? [])->keys())
        ->unique()
        ->sort();
@endphp

@foreach($usedFlavours as $flavour)
    @php
        $details = $ecsFlavours->get($flavour);
        $sizing = $details ? "{$details['vcpu']} vCPU , {$details['vram']} vRAM" : '-';
        $klQty = $ecsSummary['Kuala Lumpur'][$flavour] ?? 0;
        $cjQty = $ecsSummary['Cyberjaya'][$flavour] ?? 0;
    @endphp
    <tr style="background-color: rgb(251, 194, 224);">
        <td>
    <a href="{{ route('flavour.index', ['highlight' => $flavour]) }}">
        {{ $flavour }}
    </a>
</td>

        <td>{{ $sizing }}</td>
        <td>{{ $klQty }}</td>
        <td>{{ $cjQty }}</td>
    </tr>
@endforeach


        
    






<tr>
     <td colspan="4" style="background-color: #e76ccf; font-weight: bold;">License</td>
</tr>


                     <thead class="table-light">
                    <tr>
                        <th>Microsoft</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
                </thead>

    <tr>
    <td>Microsoft Windows Server (Core Pack) - Standard</td>
    <td>Unit</td>
    <td>{{ $licenseSummary['windows_std']['Kuala Lumpur'] }}</td>
    <td>{{ $licenseSummary['windows_std']['Cyberjaya'] }}</td>
</tr>
<tr>
    <td>Microsoft Windows Server (Core Pack) - Data Center</td>
    <td>Unit</td>
    <td>{{ $licenseSummary['windows_dc']['Kuala Lumpur'] }}</td>
    <td>{{ $licenseSummary['windows_dc']['Cyberjaya'] }}</td>
</tr>
<tr>
    <td>Microsoft Remote Desktop Services (SAL)</td>
    <td>Unit</td>
    <td>{{ $licenseSummary['rds']['Kuala Lumpur'] }}</td>
    <td>{{ $licenseSummary['rds']['Cyberjaya'] }}</td>
</tr>
<tr>
    <td>Microsoft SQL (Web) (Core Pack)</td>
    <td>Unit</td>
    <td>{{ $licenseSummary['sql_web']['Kuala Lumpur'] }}</td>
    <td>{{ $licenseSummary['sql_web']['Cyberjaya'] }}</td>
</tr>
<tr>
    <td>Microsoft SQL (Standard) (Core Pack)</td>
    <td>Unit</td>
    <td>{{ $licenseSummary['sql_std']['Kuala Lumpur'] }}</td>
    <td>{{ $licenseSummary['sql_std']['Cyberjaya'] }}</td>
</tr>
<tr>
    <td>Microsoft SQL (Enterprise) (Core Pack)</td>
    <td>Unit</td>
    <td>{{ $licenseSummary['sql_ent']['Kuala Lumpur'] }}</td>
    <td>{{ $licenseSummary['sql_ent']['Cyberjaya'] }}</td>
</tr>



    <thead class="table-light">
                    <tr>
                        <th>Red Hat Enterprise License</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
                </thead>

<tr>
    <td>RHEL (1-8vCPU)</td>
    <td>Unit</td>
    <td>{{ $licenseSummary['rhel_1_8']['Kuala Lumpur'] }}</td>
    <td>{{ $licenseSummary['rhel_1_8']['Cyberjaya'] }}</td>
</tr>
<tr>
    <td>RHEL (9-127vCPU)</td>
    <td>Unit</td>
    <td>{{ $licenseSummary['rhel_9_127']['Kuala Lumpur'] }}</td>
    <td>{{ $licenseSummary['rhel_9_127']['Cyberjaya'] }}</td>
</tr>

    


                  
                  
                  
                 
                 








<tr>
     <td colspan="4" style="background-color: #e76ccf; font-weight: bold;">Storage</td>
  
</tr>


                     <thead class="table-light">
                    <tr>
                        <th>Storage Type</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
                </thead>


                    <tr>
                        <td>Elastic Volume Service (EVS)</td>
                        <td>GB</td>
                    <td>{{ $summary->kl_evs ?? 0 }}</td>
    <td>{{ $summary->cyber_evs ?? 0 }}</td>
                     </tr>



                    <tr>
                        <td>Scalable File Service (SFS)</td>
                        <td>GB</td>
                        <td>{{ $summary->kl_scalable_file_service ?? 0 }}</td>
    <td>{{ $summary->cyber_scalable_file_service ?? 0 }}</td>
                     </tr>

                     <tr>
                        <td>Object Storage Service (OBS)</td>
                        <td>GB</td>
                       <td>{{ $summary->kl_object_storage_service ?? 0 }}</td>
    <td>{{ $summary->cyber_object_storage_service ?? 0 }}</td>
                     </tr>


                        <thead class="table-light">
                    <tr>
                        <th>Image Management Service (IMS)</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
                </thead>

<tr>
    <td>Snapshot Storage</td>
    <td>GB</td>
    <td>{{ $summary->kl_snapshot_storage ?? 0 }}</td>
    <td>{{ $summary->cyber_snapshot_storage ?? 0 }}</td>
</tr>
<tr>
    <td>Image Storage</td>
    <td>GB</td>
    <td>{{ $summary->kl_image_storage ?? 0 }}</td>
    <td>{{ $summary->cyber_image_storage ?? 0 }}</td>
</tr>






<tr>
     <td colspan="4" style="background-color: #e76ccf; font-weight: bold;">Backup and DR</td>
  
</tr>


                     <thead class="table-light">
                    <tr>
                        <th>Backup Service in VPC</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
                </thead>


                   <tr>
    <td>Cloud Server Backup Service - Full Backup Capacity</td>
    <td>GB</td>
    <td>{{ number_format($summary['kl_full_backup_capacity'], ) }}</td>
    <td>{{ number_format($summary['cyber_full_backup_capacity'], ) }}</td>
</tr>

<tr>
    <td>Cloud Server Backup Service - Incremental Backup Capacity</td>
    <td>GB</td>
    <td>{{ number_format($summary['kl_incremental_backup_capacity'], ) }}</td>
    <td>{{ number_format($summary['cyber_incremental_backup_capacity'], ) }}</td>
</tr>

<tr>
    <td>Cloud Server Replication Service - Retention Capacity</td>
    <td>GB</td>
    <td>{{ number_format($summary['kl_replication_retention_capacity'], ) }}</td>
    <td>{{ number_format($summary['cyber_replication_retention_capacity'], ) }}</td>
</tr>




               


                        <thead class="table-light">
                    <tr>
                        <th>Disaster Recovery in VPC</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
                </thead>


                <tr>
    <td>Cold DR Days</td>
    <td>Days</td>
      <td>{{ number_format($summary['kl_cold_dr_days'], ) }}</td>
     <td>{{ number_format($summary['cyber_cold_dr_days'], ) }}</td>
  
   
</tr>

<tr>
    <td>Cold DR - Seeding VM</td>
    <td>Unit</td>
    <td>{{ $summary['kl_cold_dr_seeding_vm'] }}</td>
     <td>{{ $summary['cyber_cold_dr_seeding_vm'] }}</td>
</tr>

<tr>
    <td>Cloud Server Disaster Recovery Storage</td>
    <td>GB</td>
    <td>{{ number_format($summary['kl_dr_storage'], ) }}</td>
    <td>{{ number_format($summary['cyber_dr_storage'], ) }}</td>
</tr>

<tr>
    <td>Cloud Server Disaster Recovery Replication</td>
    <td>Unit</td>
    <td>{{ $summary['kl_dr_replication'] }}</td>
     <td>{{ $summary['cyber_dr_replication'] }}</td>
</tr>

<tr>
    <td>Cloud Server Disaster Recovery Days (DR Declaration)</td>
    <td>Days</td>
 <td>{{ number_format($summary['kl_dr_declaration'] ?? 0, 0) }}</td>
<td>{{ number_format($summary['cyber_dr_declaration'] ?? 0, 0) }}</td>

</tr>



<tr>
    <td>Cloud Server Disaster Recovery Managed Service - Per Day</td>
    <td>Unit</td>
    <td>{{ $summary['kl_dr_managed_service'] }}</td>
    <td>{{ $summary['cyber_dr_managed_service'] }}</td>
</tr>

                


                        <thead class="table-light">
                    <tr>
                        <th>Disaster Recovery Network and Security</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
                </thead>

           <tr>
  <td>Cloud Server Disaster Recovery (vPLL)</td>
  <td>Mbps</td>
  <td>{{ $summary['kl_dr_vpll'] ?? 0 }}</td>
  <td>{{ $summary['cyber_dr_vpll'] ?? 0 }}</td>
</tr>
<tr>
  <td>DR Elastic IP</td>
  <td>Unit Per Day</td>
  <td>{{ $summary['kl_dr_elastic_ip'] ?? 0 }}</td>
  <td>{{ $summary['cyber_dr_elastic_ip'] ?? 0 }}</td>
</tr>
<tr>
  <td>DR Bandwidth</td>
  <td>Mbps Per Day</td>
   <td>{{ $summary['kl_dr_bandwidth'] ?? 0 }}</td>
  <td>{{ $summary['cyber_dr_bandwidth'] ?? 0 }}</td>
</tr>
<tr>
  <td>DR Bandwidth + Anti-DDoS</td>
  <td>Mbps Per Day</td>
  <td>{{ $summary['kl_dr_bandwidth_antiddos'] ?? 0 }}</td>
  <td>{{ $summary['cyber_dr_bandwidth_antiddos'] ?? 0 }}</td>
</tr>
<tr>
  <td>DR Cloud Firewall (Fortigate)</td>
  <td>Unit Per Day</td>
 <td>{{ $summary['kl_dr_firewall_fortigate'] ?? 0 }}</td>
  <td>{{ $summary['cyber_dr_firewall_fortigate'] ?? 0 }}</td>
</tr>
<tr>
  <td>DR Cloud Firewall (OPNSense)</td>
  <td>Unit Per Day</td>
 <td>{{ $summary['kl_dr_firewall_opnsense'] ?? 0 }}</td>
  <td>{{ $summary['cyber_dr_firewall_opnsense'] ?? 0 }}</td>
</tr>





                   <thead class="table-light">
                    <tr>
                        <th>Disaster Recovery Resources (During DR Activation)</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
                </thead>

<tr>
  <td>DR Elastic Volume Service (EVS)</td>
  <td>GB</td>
  <td>{{ $klEvsDR }}</td>   
  <td>{{ $cyberEvsDR }}</td>
</tr>


@foreach($usedFlavours as $flavour)
    @php
        $flavourWithDR = $flavour . '.dr';  // sentiasa papar DR variant
        $details = $flavourDetails->get($flavourWithDR);
        $sizing = $details ? "{$details['vcpu']} vCPU , {$details['vram']} vRAM" : '-';

        // ambil kiraan sebenar (BUKAN 1/0)
        $klQty = $drCountsKL[$flavour] ?? 0; // asal CJ → masuk KL
        $cjQty = $drCountsCJ[$flavour] ?? 0; // asal KL → masuk CJ
    @endphp
    <tr style="background-color: rgb(251, 194, 224);">
        <td>
            <a href="{{ route('flavour.index', ['highlight' => $flavourWithDR]) }}">
                {{ $flavourWithDR }}
            </a>
        </td>
        <td>{{ $sizing }}</td>
        <td>{{ $klQty }}</td>
        <td>{{ $cjQty }}</td>
    </tr>
@endforeach



                   <thead class="table-light">
                    <tr>
                        <th>Disaster Recovery Licenses</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
                </thead>

<tr><td>License Month</td><td>Month(s)</td>
  <td>{{ $summary->kl_dr_license_months }}</td>
<td>{{ $summary->cyber_dr_license_months }}</td>

</tr>
<tr><td>DR Per Month - Microsoft Windows Server (Core Pack) - Standard</td><td>Unit Per Month</td>
  <td>{{ $summary->kl_dr_windows_std }}</td>
<td>{{ $summary->cyber_dr_windows_std }}</td>
</tr>
<tr><td>DR Per Month - Microsoft Windows Server (Core Pack) - Data Center</td><td>Unit Per Month</td>
  <td>{{ $summary->kl_dr_windows_dc }}</td>
<td>{{ $summary->cyber_dr_windows_dc }}</td>
</tr>
<tr><td>DR Per Month - Microsoft Remote Desktop Services (SAL)</td><td>Unit Per Month</td>
<td>{{ $summary->kl_dr_rds }}</td>
<td>{{ $summary->cyber_dr_rds }}</td>
</tr>
<tr><td>DR Per Month - Microsoft SQL (Web) (Core Pack)</td><td>Unit Per Month</td>
 <td>{{ $summary->kl_dr_sql_web }}</td>
<td>{{ $summary->cyber_dr_sql_web }}</td>
</tr>
<tr><td>DR Per Month - Microsoft SQL (Standard) (Core Pack)</td><td>Unit Per Month</td>
 <td>{{ $summary->kl_dr_sql_std }}</td>
<td>{{ $summary->cyber_dr_sql_std }}</td>
</tr>
<tr><td>DR Per Month - Microsoft SQL (Enterprise) (Core Pack)</td><td>Unit Per Month</td>
 <td>{{ $summary->kl_dr_sql_ent }}</td>
<td>{{ $summary->cyber_dr_sql_ent }}</td>
</tr>
<tr><td>DR Per Month - RHEL (1–8vCPU)</td><td>Unit Per Month</td>
<td>{{ $summary->kl_dr_rhel_1_8 }}</td>
<td>{{ $summary->cyber_dr_rhel_1_8 }}</td>
</tr>
<tr><td>DR Per Month - RHEL (9–127vCPU)</td><td>Unit Per Month</td>
 <td>{{ $summary->kl_dr_rhel_9_127 }}</td>
<td>{{ $summary->cyber_dr_rhel_9_127 }}</td>
</tr>


 <!---<tr>
    <td>DR Per Month - Microsoft Windows Server (Core Pack) - Standard</td>
   <td>Unit Per Month</td>
    <td>{{ $licenseSummary['windows_std']['Cyberjaya'] }}</td>
    <td>{{ $licenseSummary['windows_std']['Kuala Lumpur'] }}</td>
</tr>
<tr>
    <td>DR Per Month - Microsoft Windows Server (Core Pack) - Data Center</td>
    <td>Unit Per Month</td>
    <td>{{ $licenseSummary['windows_dc']['Cyberjaya'] }}</td>
      <td>{{ $licenseSummary['windows_dc']['Kuala Lumpur'] }}</td>
</tr>
<tr>
    <td>DR Per Month - Microsoft Remote Desktop Services (SAL)</td>
    <td>Unit Per Month</td>
    <td>{{ $licenseSummary['rds']['Cyberjaya'] }}</td>
    <td>{{ $licenseSummary['rds']['Kuala Lumpur'] }}</td>
</tr>
<tr>
    <td>DR Per Month - Microsoft SQL (Web) (Core Pack)</td>
    <td>Unit Per Month</td>
    <td>{{ $licenseSummary['sql_web']['Cyberjaya'] }}</td>
     <td>{{ $licenseSummary['sql_web']['Kuala Lumpur'] }}</td>
</tr>
<tr>
    <td>DR Per Month - Microsoft SQL (Standard) (Core Pack)</td>
    <td>Unit Per Month</td>
    <td>{{ $licenseSummary['sql_std']['Cyberjaya'] }}</td>
     <td>{{ $licenseSummary['sql_std']['Kuala Lumpur'] }}</td>
</tr>
<tr>
    <td>DR Per Month - Microsoft SQL (Enterprise) (Core Pack)</td>
    <td>Unit Per Month</td>
    <td>{{ $licenseSummary['sql_ent']['Cyberjaya'] }}</td>
    <td>{{ $licenseSummary['sql_ent']['Kuala Lumpur'] }}</td>
</tr>




<tr>
    <td>DR Per Month - RHEL (1-8vCPU)</td>
    <td>Unit Per Month</td>
    <td>{{ $licenseSummary['rhel_1_8']['Cyberjaya'] }}</td>
      <td>{{ $licenseSummary['rhel_1_8']['Kuala Lumpur'] }}</td>
</tr>
<tr>
    <td>DR Per Month - RHEL (9-127vCPU)</td>
    <td>Unit Per Month</td>
    <td>{{ $licenseSummary['rhel_9_127']['Cyberjaya'] }}</td>
    <td>{{ $licenseSummary['rhel_9_127']['Kuala Lumpur'] }}</td>
</tr>--->

    


    


















                    <tr>
                          <td colspan="4" style="background-color: #e76ccf; font-weight: bold;">Additional Services</td>
                      
                    </tr> 







                      <thead class="table-light">
                    <tr>
                        <th>Cloud Security</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
                </thead>
                    <tr>
                        <td>Cloud Firewall (Fortigate)</td>
                        <td>Unit</td>
                     <td>{{ $summary->kl_firewall_fortigate ?? 0 }}</td>
    <td>{{ $summary->cyber_firewall_fortigate ?? 0 }}</td>
                    </tr>

                      <tr>
                        <td>Cloud Firewall (OPNSense)</td>
                        <td>Unit</td>
                      <td>{{ $summary->kl_firewall_opnsense ?? 0 }}</td>
    <td>{{ $summary->cyber_firewall_opnsense ?? 0 }}</td>
                    </tr>


                     <tr>
                        <td>Cloud Shared WAF (Mbps)</td>
                        <td>Mbps</td>
                      <td>{{ $summary->kl_shared_waf ?? 0 }}</td>
    <td>{{ $summary->cyber_shared_waf ?? 0 }}</td>
                    </tr>


                     <tr>
                        <td>Anti-Virus (Panda)</td>
                        <td>Unit</td>
                     <td>{{ $summary->kl_antivirus ?? 0 }}</td>
    <td>{{ $summary->cyber_antivirus ?? 0 }}</td>
                    </tr>


                    
                  <thead class="table-light">
                    <tr>
                        <th>Security Services</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
                </thead>
                    <tr>
                        <td>Cloud Vulnerability Assessment (Per IP)</td>
                        <td>Mbps</td>
                    <td>{{ $summary->kl_cloud_vulnerability ?? 0 }}</</td>

                    
                        <td>{{ $summary->cyber_cloud_vulnerability ?? 0 }}</</td>
                    </tr>




                      <thead class="table-light">
                    <tr>
                        <th>Monitoring Service</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
                </thead>
                    



<tr>
    <td>TCS inSight vMonitoring</td>
    <td>Unit</td>
    <td>{{ $summary->kl_insight_vmonitoring == 1 ? 1 : 0 }}</td>
    <td>{{ $summary->cyber_insight_vmonitoring == 1 ? 1 : 0 }}</td>
</tr>





@if($nonStandardItems && $nonStandardItems->count())
    <tr>
        <td colspan="4" style="background-color: #e76ccf; font-weight: bold;">
            Non-Standard Item Services
        </td>
    </tr>
    <tr>
        <th>Item</th>
        <th>Unit</th>
        <th>Quantity</th>
        <th>Selling Price (RM)</th>
    </tr>

    @foreach($nonStandardItems as $item)
        <tr>
            <td>{{ $item->item_name }}</td>
            <td>{{ $item->unit }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($item->selling_price, 2) }}</td>
        </tr>
    @endforeach
@endif







                </tbody>
            </table>
        </div>
@else
    <div class="alert alert-warning d-flex justify-content-between align-items-center" role="alert">
        <div>
            ⚠️ <strong>Please fill all required sections before viewing Internal Summary.</strong>
        </div>
</div>
@endif
























      
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('versions.non_standard_items.create', $version->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Previous Step
            </a>

            <a href="{{ route('versions.quotation.ratecard', $version->id) }}" class="btn btn-secondary">
   Preview Rate Card <i class="bi bi-arrow-right"></i></a>

           

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
@endpush