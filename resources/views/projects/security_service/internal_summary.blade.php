@extends('layouts.app')

@php
    $solution_type = $solution_type ?? $version->solution_type ?? null;
@endphp
@php
    $locked = !empty($summary) && ($summary->is_logged ?? false);
@endphp

@php
    // safe defaults so optional($region) etc. won't error
    $region           = $region           ?? null;
    $security_service = $security_service ?? null;
    $summary          = $summary          ?? null;
    $licenseSummary   = $licenseSummary   ?? [];
    $klManagedServices    = $klManagedServices    ?? [];
    $cyberManagedServices = $cyberManagedServices ?? [];
    $usedFlavours     = $usedFlavours     ?? collect();
    $flavourDetails   = $flavourDetails   ?? collect();
    $drCountsKL       = $drCountsKL       ?? [];
    $drCountsCJ       = $drCountsCJ       ?? [];
    $nonStandardItems = $nonStandardItems ?? collect();
@endphp


@php
    // --- FREEZE WHEN LOGGED ---
    if (!empty($summary) && ($summary->is_logged ?? false)) {
        // Bekukan License Summary daripada snapshot
        $licenseSummary = [
            'windows_std' => [
                'Kuala Lumpur' => (int) ($summary->kl_windows_std ?? 0),
                'Cyberjaya'    => (int) ($summary->cyber_windows_std ?? 0),
            ],
            'windows_dc'  => [
                'Kuala Lumpur' => (int) ($summary->kl_windows_dc ?? 0),
                'Cyberjaya'    => (int) ($summary->cyber_windows_dc ?? 0),
            ],
            'rds'         => [
                'Kuala Lumpur' => (int) ($summary->kl_rds ?? 0),
                'Cyberjaya'    => (int) ($summary->cyber_rds ?? 0),
            ],
            'sql_web'     => [
                'Kuala Lumpur' => (int) ($summary->kl_sql_web ?? 0),
                'Cyberjaya'    => (int) ($summary->cyber_sql_web ?? 0),
            ],
            'sql_std'     => [
                'Kuala Lumpur' => (int) ($summary->kl_sql_std ?? 0),
                'Cyberjaya'    => (int) ($summary->cyber_sql_std ?? 0),
            ],
            'sql_ent'     => [
                'Kuala Lumpur' => (int) ($summary->kl_sql_ent ?? 0),
                'Cyberjaya'    => (int) ($summary->cyber_sql_ent ?? 0),
            ],
            'rhel_1_8'    => [
                'Kuala Lumpur' => (int) ($summary->kl_rhel_1_8 ?? 0),
                'Cyberjaya'    => (int) ($summary->cyber_rhel_1_8 ?? 0),
            ],
            'rhel_9_127'  => [
                'Kuala Lumpur' => (int) ($summary->kl_rhel_9_127 ?? 0),
                'Cyberjaya'    => (int) ($summary->cyber_rhel_9_127 ?? 0),
            ],
        ];

        // Bekukan DR EVS (during activation) jika ada dalam snapshot
        $klEvsDR    = (int) ($summary->kl_evs_dr ?? ($klEvsDR ?? 0));
        $cyberEvsDR = (int) ($summary->cyber_evs_dr ?? ($cyberEvsDR ?? 0));

        // Bekukan ECS summary (table "Computing") jika disimpan sebagai JSON/array
        if (is_array($summary->ecs_flavour_summary ?? null)) {
            $ecsSummary = $summary->ecs_flavour_summary;
        }

        // (Optional) Snapshot DR counts/details
        if (is_array($summary->dr_counts_kl ?? null)) $drCountsKL = $summary->dr_counts_kl;
        if (is_array($summary->dr_counts_cj ?? null)) $drCountsCJ = $summary->dr_counts_cj;
        if (!empty($summary->dr_flavour_details ?? null)) {
            $flavourDetails = collect($summary->dr_flavour_details);
        }
    }

    // Hardening guards (avoid undefined)
    $ecsSummary              = is_array($ecsSummary ?? null) ? $ecsSummary : [];
    $flavourDetails          = collect($flavourDetails ?? []);
    $drCountsKL              = is_array($drCountsKL ?? null) ? $drCountsKL : [];
    $drCountsCJ              = is_array($drCountsCJ ?? null) ? $drCountsCJ : [];
    $usedFlavoursCompute     = collect($usedFlavoursCompute ?? []);
    $computeFlavourDetails   = collect($computeFlavourDetails ?? []);
@endphp



@php
    // value not 0? (numeric > 0 or string/array not zero)
    $nz = function($v) {
        return is_numeric($v) ? ((float)$v) > 0 : !empty($v);
    };
    // at least 1 value
    $any = function(...$vs) use ($nz) {
        foreach ($vs as $x) { if ($nz($x)) return true; }
        return false;
    };
@endphp


@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-between align-items-center">
        <div class="breadcrumb-text">

            {{-- Solution Type (sentiasa link) --}}
            <a href="{{ route('versions.solution_type.create', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.solution_type.create' ? 'active-link' : '' }}">
               Solution Type
            </a>
            <span class="breadcrumb-separator">»</span>

            {{-- Professional Services (skip jika MP-DRaaS Only) --}}
            @if(($solution_type->solution_type ?? '') !== 'MP-DRaaS Only')
                <a href="{{ route('versions.region.create', $version->id) }}"
                   class="breadcrumb-link {{ Route::currentRouteName() === 'versions.region.create' ? 'active-link' : '' }}">
                   Professional Services
                </a>
                <span class="breadcrumb-separator">»</span>
            @endif

            {{-- Network & Global Services --}}
            <a href="{{ route('versions.region.network.create', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.region.network.create' ? 'active-link' : '' }}">
               Network & Global Services
            </a>
            <span class="breadcrumb-separator">»</span>

            {{-- ECS & Backup --}}
            @if(($solution_type->solution_type ?? '') !== 'MP-DRaaS Only')
                <a href="{{ route('versions.backup.create', $version->id) }}"
                   class="breadcrumb-link {{ Route::currentRouteName() === 'versions.backup.create' ? 'active-link' : '' }}">
                   ECS & Backup
                </a>
                <span class="breadcrumb-separator">»</span>
            @endif

            {{-- DR Settings --}}
            @if(($solution_type->solution_type ?? '') !== 'MP-DRaaS Only')
                <a href="{{ route('versions.region.dr.create', $version->id) }}"
                   class="breadcrumb-link {{ Route::currentRouteName() === 'versions.region.dr.create' ? 'active-link' : '' }}">
                   DR Settings
                </a>
                <span class="breadcrumb-separator">»</span>
            @endif

            {{-- MP-DRaaS (skip jika TCS Only) --}}
            @if(($solution_type->solution_type ?? '') !== 'TCS Only')
                <a href="{{ route('versions.mpdraas.create', $version->id) }}"
                   class="breadcrumb-link {{ Route::currentRouteName() === 'versions.mpdraas.create' ? 'active-link' : '' }}">
                   MP-DRaaS
                </a>
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

          
            <a href="{{ route('versions.non_standard_items.create', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.non_standard_items.create' ? 'active-link' : '' }}">
               3rd Party (Non-Standard)
            </a>
            <span class="breadcrumb-separator">»</span>

            {{-- Internal Summary (current) --}}
            <a href="{{ route('versions.internal_summary.show', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.internal_summary.show' ? 'active-link' : '' }}">
               Internal Summary
            </a>
            <span class="breadcrumb-separator">»</span>

            {{-- Steps selepas summary kekal boleh akses --}}
            <a href="{{ route('versions.quotation.ratecard', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.quotation.ratecard' ? 'active-link' : '' }}">
               Breakdown Price
            </a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.quotation.preview', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.quotation.preview' ? 'active-link' : '' }}">
               Quotation (Monthly)
            </a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.quotation.annual', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.quotation.annual' ? 'active-link' : '' }}">
               Quotation (Annual)
            </a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.download_zip', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.download_zip' ? 'active-link' : '' }}">
               Download Zip File
            </a>
        </div>

        <button type="button" class="btn-close" style="margin-left: auto;" onclick="window.location.href='{{ route('projects.index') }}'"></button>
    </div>

    @if(session('status'))
        <div class="alert alert-success m-3">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger m-3">{{ session('error') }}</div>
    @endif

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
                    
@php
    $ps_show = $any($summary->mandays ?? 0,
                    $summary->kl_license_count ?? 0, $summary->cyber_license_count ?? 0,
                    $summary->kl_duration ?? 0,       $summary->cyber_duration ?? 0);
@endphp
@if($ps_show)
<tr>
    <td style="background-color:#e76ccf;font-weight:bold;">Professional Services</td>
    <td style="background-color:#e76ccf;font-weight:bold;">Unit</td>
    <td style="background-color:#e76ccf;font-weight:bold;">Qty</td>
    <td style="background-color:#e76ccf;font-weight:bold;">Qty</td>
</tr>

@if($nz($summary->mandays ?? 0))
<tr>
    <td>Professional Services (ONE TIME Provisioning)</td>
    <td>Days</td>
    <td colspan="2">{{ (int)($summary->mandays ?? 0) }}</td>
</tr>
@endif

@php
    // guna nilai mentah; JANGAN auto “1” — bila kosong, baris disembunyi
    $migUnit   = max((int)($summary->kl_license_count ?? 0), (int)($summary->cyber_license_count ?? 0));
    $migMonths = max((int)($summary->kl_duration ?? 0),      (int)($summary->cyber_duration ?? 0));
@endphp
@if($any($migUnit, $migMonths))
<tr>
    <td>Migration Tools One Time Charge</td>
    <td>Unit Per Month*</td>
    <td>{{ $migUnit }} Unit</td>
    <td>{{ $migMonths }} Months</td>
</tr>
@endif
@endif


{{-- Managed Services (tunjuk hanya servis yang >0) --}}
@php
    $managedRows = collect([
        ['name' => 'Managed Operating System',
         'kl' => $klManagedServices['Managed Operating System'] ?? 0,
         'cj' => $cyberManagedServices['Managed Operating System'] ?? 0],
        ['name' => 'Managed Backup and Restore',
         'kl' => $klManagedServices['Managed Backup and Restore'] ?? 0,
         'cj' => $cyberManagedServices['Managed Backup and Restore'] ?? 0],
        ['name' => 'Managed Patching',
         'kl' => $klManagedServices['Managed Patching'] ?? 0,
         'cj' => $cyberManagedServices['Managed Patching'] ?? 0],
        ['name' => 'Managed DR',
         'kl' => $klManagedServices['Managed DR'] ?? 0,
         'cj' => $cyberManagedServices['Managed DR'] ?? 0],
    ])->filter(fn($r) => ($r['kl'] ?? 0) > 0 || ($r['cj'] ?? 0) > 0);
@endphp

@if($managedRows->count())
<tr>
    <td style="background-color:#e76ccf;font-weight:bold;">Managed Services</td>
    <td style="background-color:#e76ccf;font-weight:bold;">Unit</td>
    <td style="background-color:#e76ccf;font-weight:bold;">KL.Qty</td>
    <td style="background-color:#e76ccf;font-weight:bold;">CJ.Qty</td>
</tr>
@foreach($managedRows as $r)
<tr>
    <td>{{ $r['name'] }}</td>
    <td>VM</td>
    <td>{{ $r['kl'] }}</td>
    <td>{{ $r['cj'] }}</td>
</tr>
@endforeach
@endif


                        <!--<tr>
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
                        @endforeach--->

                        <!---<tr>
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
                            <td>{{ $summary->kl_gslb ?? 0 }}</td>
                            <td>{{ $summary->cyber_gslb ?? 0 }}</td>
                               
                        </tr>--->
                        {{-- Network (auto-filter baris nilai kosong) --}}
@php
    $networkRows = [
        ['label'=>'Bandwidth','unit'=>'Mbps','kl'=>$summary->kl_bandwidth ?? 0,'cj'=>$summary->cyber_bandwidth ?? 0],
        ['label'=>'Bandwidth with Anti-DDoS','unit'=>'Mbps','kl'=>$summary->kl_bandwidth_with_antiddos ?? 0,'cj'=>$summary->cyber_bandwidth_with_antiddos ?? 0],
        ['label'=>'Included Elastic IP (FOC)','unit'=>'Unit','kl'=>$summary->kl_included_elastic_ip ?? 0,'cj'=>$summary->cyber_included_elastic_ip ?? 0],
        ['label'=>'Elastic IP','unit'=>'Unit','kl'=>$summary->kl_elastic_ip ?? 0,'cj'=>$summary->cyber_elastic_ip ?? 0],
        ['label'=>'Elastic Load Balancer (External)','unit'=>'Unit','kl'=>$summary->kl_elastic_load_balancer ?? 0,'cj'=>$summary->cyber_elastic_load_balancer ?? 0],
        ['label'=>'Direct Connect Virtual Gateway','unit'=>'Unit','kl'=>$summary->kl_direct_connect_virtual ?? 0,'cj'=>$summary->cyber_direct_connect_virtual ?? 0],
        ['label'=>'L2BR instance','unit'=>'Unit','kl'=>$summary->kl_l2br_instance ?? 0,'cj'=>$summary->cyber_l2br_instance ?? 0],
        ['label'=>'Virtual Private Leased Line (vPLL)','unit'=>'Mbps','kl'=>$summary->kl_virtual_private_leased_line ?? 0,'cj'=>null], // CJ tiada
        ['label'=>'vPLL L2BR','unit'=>'Pair','kl'=>$summary->kl_vpll_l2br ?? 0,'cj'=>null],
        ['label'=>'NAT Gateway (Small)','unit'=>'Unit','kl'=>$summary->kl_nat_gateway_small ?? 0,'cj'=>$summary->cyber_nat_gateway_small ?? 0],
        ['label'=>'NAT Gateway (Medium)','unit'=>'Unit','kl'=>$summary->kl_nat_gateway_medium ?? 0,'cj'=>$summary->cyber_nat_gateway_medium ?? 0],
        ['label'=>'NAT Gateway (Large)','unit'=>'Unit','kl'=>$summary->kl_nat_gateway_large ?? 0,'cj'=>$summary->cyber_nat_gateway_large ?? 0],
        ['label'=>'NAT Gateway (Extra-Large)','unit'=>'Unit','kl'=>$summary->kl_nat_gateway_xlarge ?? 0,'cj'=>$summary->cyber_nat_gateway_xlarge ?? 0],
        ['label'=>'Managed Global Server Load Balancer (GSLB)','unit'=>'Domain','kl'=>$summary->kl_gslb ?? 0,'cj'=>$summary->cyber_gslb ?? 0],
    ];
    $networkRows = array_values(array_filter($networkRows, fn($r) =>
        ($r['kl'] ?? 0) > 0 || ($r['cj'] ?? 0) > 0
    ));
@endphp

@if(count($networkRows))
<tr>
    <td style="background-color:#e76ccf;font-weight:bold;">Network</td>
    <td style="background-color:#e76ccf;font-weight:bold;">Unit</td>
    <td style="background-color:#e76ccf;font-weight:bold;">KL.Qty</td>
    <td style="background-color:#e76ccf;font-weight:bold;">CJ.Qty</td>
</tr>
@foreach($networkRows as $r)
<tr>
    <td>{{ $r['label'] }}</td>
    <td>{{ $r['unit'] }}</td>
    <td>{{ $r['kl'] ?? 0 }}</td>
    <td>
        @if(is_null($r['cj']))
            <input class="form-control bg-light text-muted" disabled>
        @else
            {{ $r['cj'] }}
        @endif
    </td>
</tr>
@endforeach
@endif

@php $usedFlavours = $usedFlavoursCompute->sort()->values(); @endphp
@if($usedFlavours->count())
<tr>
    <td colspan="4" style="background-color:#e76ccf;font-weight:bold;">Computing</td>
</tr>
<thead class="table-light">
<tr>
    <th>Compute - Elastic Cloud Server (ECS)</th>
    <th>Sizing</th>
    <th>KL.Qty</th>
    <th>CJ.Qty</th>
</tr>
</thead>

@foreach($usedFlavours as $flavour)
@php
    $details = $computeFlavourDetails->get($flavour);
    $sizing  = $details ? ($details['vcpu'].' vCPU , '.$details['vram'].' vRAM') : '-';
    $klQty   = (int)($ecsSummary['Kuala Lumpur'][$flavour] ?? 0);
    $cjQty   = (int)($ecsSummary['Cyberjaya'][$flavour] ?? 0);
@endphp
<tr style="background-color:rgb(251,194,224);">
    <td><a href="{{ route('flavour.index',['highlight'=>$flavour]) }}">{{ $flavour }}</a></td>
    <td>{{ $sizing }}</td>
    <td>{{ $klQty }}</td>
    <td>{{ $cjQty }}</td>
</tr>
@endforeach
@endif



                        <!---<tr>
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
                            // Flavour asas (bukan .dr) – gunakan data dari controller
                            $usedFlavours = $usedFlavoursCompute->sort()->values();
                        @endphp

                        @foreach($usedFlavours as $flavour)
                            @php
                                $details = $computeFlavourDetails->get($flavour);
                                $sizing  = $details ? ($details['vcpu'].' vCPU , '.$details['vram'].' vRAM') : '-';
                                $klQty   = $ecsSummary['Kuala Lumpur'][$flavour] ?? 0;
                                $cjQty   = $ecsSummary['Cyberjaya'][$flavour] ?? 0;
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
                        @endforeach--->
{{-- License (tunjuk baris yang ada qty) --}}
@php
    $msRows = [
        ['label'=>'Microsoft Windows Server (Core Pack) - Standard','kl'=>$licenseSummary['windows_std']['Kuala Lumpur'] ?? 0,'cj'=>$licenseSummary['windows_std']['Cyberjaya'] ?? 0],
        ['label'=>'Microsoft Windows Server (Core Pack) - Data Center','kl'=>$licenseSummary['windows_dc']['Kuala Lumpur'] ?? 0,'cj'=>$licenseSummary['windows_dc']['Cyberjaya'] ?? 0],
        ['label'=>'Microsoft Remote Desktop Services (SAL)','kl'=>$licenseSummary['rds']['Kuala Lumpur'] ?? 0,'cj'=>$licenseSummary['rds']['Cyberjaya'] ?? 0],
        ['label'=>'Microsoft SQL (Web) (Core Pack)','kl'=>$licenseSummary['sql_web']['Kuala Lumpur'] ?? 0,'cj'=>$licenseSummary['sql_web']['Cyberjaya'] ?? 0],
        ['label'=>'Microsoft SQL (Standard) (Core Pack)','kl'=>$licenseSummary['sql_std']['Kuala Lumpur'] ?? 0,'cj'=>$licenseSummary['sql_std']['Cyberjaya'] ?? 0],
        ['label'=>'Microsoft SQL (Enterprise) (Core Pack)','kl'=>$licenseSummary['sql_ent']['Kuala Lumpur'] ?? 0,'cj'=>$licenseSummary['sql_ent']['Cyberjaya'] ?? 0],
    ];
    $msRows = array_values(array_filter($msRows, fn($r)=>($r['kl'] ?? 0)>0 || ($r['cj'] ?? 0)>0));

    $rhelRows = [
        ['label'=>'RHEL (1-8vCPU)','kl'=>$licenseSummary['rhel_1_8']['Kuala Lumpur'] ?? 0,'cj'=>$licenseSummary['rhel_1_8']['Cyberjaya'] ?? 0],
        ['label'=>'RHEL (9-127vCPU)','kl'=>$licenseSummary['rhel_9_127']['Kuala Lumpur'] ?? 0,'cj'=>$licenseSummary['rhel_9_127']['Cyberjaya'] ?? 0],
    ];
    $rhelRows = array_values(array_filter($rhelRows, fn($r)=>($r['kl'] ?? 0)>0 || ($r['cj'] ?? 0)>0));
@endphp

@if(count($msRows) || count($rhelRows))
<tr><td colspan="4" style="background-color:#e76ccf;font-weight:bold;">License</td></tr>
@endif

@if(count($msRows))
<thead class="table-light">
<tr><th>Microsoft</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
</thead>
@foreach($msRows as $r)
<tr><td>{{ $r['label'] }}</td><td>Unit</td><td>{{ $r['kl'] }}</td><td>{{ $r['cj'] }}</td></tr>
@endforeach
@endif

@if(count($rhelRows))
<thead class="table-light">
<tr><th>Red Hat Enterprise License</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
</thead>
@foreach($rhelRows as $r)
<tr><td>{{ $r['label'] }}</td><td>Unit</td><td>{{ $r['kl'] }}</td><td>{{ $r['cj'] }}</td></tr>
@endforeach
@endif

                        <!---<tr>
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
                            <td>{{ $licenseSummary['windows_std']['Kuala Lumpur'] ?? 0 }}</td>
                            <td>{{ $licenseSummary['windows_std']['Cyberjaya'] ?? 0 }}</td>
                          
                        </tr>
                        <tr>
                            <td>Microsoft Windows Server (Core Pack) - Data Center</td>
                            <td>Unit</td>
                            <td>{{ $licenseSummary['windows_dc']['Kuala Lumpur'] ?? 0 }}</td>
                            <td>{{ $licenseSummary['windows_dc']['Cyberjaya'] ?? 0 }}</td>
                            
                        </tr>
                        <tr>
                            <td>Microsoft Remote Desktop Services (SAL)</td>
                            <td>Unit</td>
                            <td>{{ $licenseSummary['rds']['Kuala Lumpur'] ?? 0 }}</td>
                            <td>{{ $licenseSummary['rds']['Cyberjaya'] ?? 0 }}</td>
                            
                        </tr>
                        <tr>
                            <td>Microsoft SQL (Web) (Core Pack)</td>
                            <td>Unit</td>
                            <td>{{ $licenseSummary['sql_web']['Kuala Lumpur'] ?? 0 }}</td>
                            <td>{{ $licenseSummary['sql_web']['Cyberjaya'] ?? 0 }}</td>
                             
                        </tr>
                        <tr>
                            <td>Microsoft SQL (Standard) (Core Pack)</td>
                            <td>Unit</td>
                            <td>{{ $licenseSummary['sql_std']['Kuala Lumpur'] ?? 0 }}</td>
                            <td>{{ $licenseSummary['sql_std']['Cyberjaya'] ?? 0 }}</td>
                          
                        </tr>
                        <tr>
                            <td>Microsoft SQL (Enterprise) (Core Pack)</td>
                            <td>Unit</td>
                            <td>{{ $licenseSummary['sql_ent']['Kuala Lumpur'] ?? 0 }}</td>
                            <td>{{ $licenseSummary['sql_ent']['Cyberjaya'] ?? 0 }}</td>
                          
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
                            <td>{{ $licenseSummary['rhel_1_8']['Kuala Lumpur'] ?? 0 }}</td>
                            <td>{{ $licenseSummary['rhel_1_8']['Cyberjaya'] ?? 0 }}</td>
                           
                        </tr>
                        <tr>
                            <td>RHEL (9-127vCPU)</td>
                            <td>Unit</td>
                            <td>{{ $licenseSummary['rhel_9_127']['Kuala Lumpur'] ?? 0 }}</td>
                            <td>{{ $licenseSummary['rhel_9_127']['Cyberjaya'] ?? 0 }}</td>
                           
                        </tr>--->
{{-- Storage --}}
@php
    $storageRows = [
        ['label'=>'Elastic Volume Service (EVS)','unit'=>'GB','kl'=>$summary->kl_evs ?? 0,'cj'=>$summary->cyber_evs ?? 0],
        ['label'=>'Scalable File Service (SFS)','unit'=>'GB','kl'=>$summary->kl_scalable_file_service ?? 0,'cj'=>$summary->cyber_scalable_file_service ?? 0],
        ['label'=>'Object Storage Service (OBS)','unit'=>'GB','kl'=>$summary->kl_object_storage_service ?? 0,'cj'=>$summary->cyber_object_storage_service ?? 0],
    ];
    $storageRows = array_values(array_filter($storageRows, fn($r)=>($r['kl'] ?? 0)>0 || ($r['cj'] ?? 0)>0));
@endphp
@if(count($storageRows))
<tr><td colspan="4" style="background-color:#e76ccf;font-weight:bold;">Storage</td></tr>
<thead class="table-light">
<tr><th>Storage Type</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
</thead>
@foreach($storageRows as $r)
<tr><td>{{ $r['label'] }}</td><td>{{ $r['unit'] }}</td><td>{{ number_format($r['kl']) }}</td><td>{{ number_format($r['cj']) }}</td></tr>
@endforeach
@endif

{{-- IMS --}}
@php
    $imsRows = [
        ['label'=>'Snapshot Storage','unit'=>'GB','kl'=>$summary->kl_snapshot_storage ?? 0,'cj'=>$summary->cyber_snapshot_storage ?? 0],
        ['label'=>'Image Storage','unit'=>'GB','kl'=>$summary->kl_image_storage ?? 0,'cj'=>$summary->cyber_image_storage ?? 0],
    ];
    $imsRows = array_values(array_filter($imsRows, fn($r)=>($r['kl'] ?? 0)>0 || ($r['cj'] ?? 0)>0));
@endphp
@if(count($imsRows))
<thead class="table-light">
<tr><th>Image Management Service (IMS)</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
</thead>
@foreach($imsRows as $r)
<tr><td>{{ $r['label'] }}</td><td>{{ $r['unit'] }}</td><td>{{ number_format($r['kl']) }}</td><td>{{ number_format($r['cj']) }}</td></tr>
@endforeach
@endif

                        <!---<tr>
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
                             
                        </tr>--->
{{-- Backup in VPC --}}
@php
    $bkRows = [
        ['label'=>'Cloud Server Backup Service - Full Backup Capacity','unit'=>'GB','kl'=>$summary->kl_full_backup_capacity ?? 0,'cj'=>$summary->cyber_full_backup_capacity ?? 0],
        ['label'=>'Cloud Server Backup Service - Incremental Backup Capacity','unit'=>'GB','kl'=>$summary->kl_incremental_backup_capacity ?? 0,'cj'=>$summary->cyber_incremental_backup_capacity ?? 0],
        ['label'=>'Cloud Server Replication Service - Retention Capacity','unit'=>'GB','kl'=>$summary->kl_replication_retention_capacity ?? 0,'cj'=>$summary->cyber_replication_retention_capacity ?? 0],
    ];
    $bkRows = array_values(array_filter($bkRows, fn($r)=>($r['kl'] ?? 0)>0 || ($r['cj'] ?? 0)>0));
@endphp
@if(count($bkRows))
<tr><td colspan="4" style="background-color:#e76ccf;font-weight:bold;">Backup and DR</td></tr>
<thead class="table-light">
<tr><th>Backup Service in VPC</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
</thead>
@foreach($bkRows as $r)
<tr><td>{{ $r['label'] }}</td><td>{{ $r['unit'] }}</td><td>{{ number_format($r['kl']) }}</td><td>{{ number_format($r['cj']) }}</td></tr>
@endforeach
@endif

                        <!---<tr>
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
                            <td>{{ number_format($summary->kl_full_backup_capacity ?? 0) }}</td>
                            <td>{{ number_format($summary->cyber_full_backup_capacity ?? 0) }}</td>
                           
                        </tr>
                        <tr>
                            <td>Cloud Server Backup Service - Incremental Backup Capacity</td>
                            <td>GB</td>
                            <td>{{ number_format($summary->kl_incremental_backup_capacity ?? 0) }}</td>
                            <td>{{ number_format($summary->cyber_incremental_backup_capacity ?? 0) }}</td>
                            
                        </tr>
                        <tr>
                            <td>Cloud Server Replication Service - Retention Capacity</td>
                            <td>GB</td>
                            <td>{{ number_format($summary->kl_replication_retention_capacity ?? 0) }}</td>
                            <td>{{ number_format($summary->cyber_replication_retention_capacity ?? 0) }}</td>
                             
                        </tr>--->
{{-- DR in VPC --}}
@php
    $drvpcRows = [
        ['label'=>'Cold DR Days','unit'=>'Days','kl'=>$summary->kl_cold_dr_days ?? 0,'cj'=>$summary->cyber_cold_dr_days ?? 0],
        ['label'=>'Cold DR - Seeding VM','unit'=>'Unit','kl'=>$summary->kl_cold_dr_seeding_vm ?? 0,'cj'=>$summary->cyber_cold_dr_seeding_vm ?? 0],
        ['label'=>'Cloud Server Disaster Recovery Storage','unit'=>'GB','kl'=>$summary->kl_dr_storage ?? 0,'cj'=>$summary->cyber_dr_storage ?? 0],
        ['label'=>'Cloud Server Disaster Recovery Replication','unit'=>'Unit','kl'=>$summary->kl_dr_replication ?? 0,'cj'=>$summary->cyber_dr_replication ?? 0],
        ['label'=>'Cloud Server Disaster Recovery Days (DR Declaration)','unit'=>'Days','kl'=>$summary->kl_dr_declaration ?? 0,'cj'=>$summary->cyber_dr_declaration ?? 0],
        ['label'=>'Cloud Server Disaster Recovery Managed Service - Per Day','unit'=>'Unit','kl'=>$summary->kl_dr_managed_service ?? 0,'cj'=>$summary->cyber_dr_managed_service ?? 0],
    ];
    $drvpcRows = array_values(array_filter($drvpcRows, fn($r)=>($r['kl'] ?? 0)>0 || ($r['cj'] ?? 0)>0));
@endphp
@if(count($drvpcRows))
<thead class="table-light">
<tr><th>Disaster Recovery in VPC</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
</thead>
@foreach($drvpcRows as $r)
<tr><td>{{ $r['label'] }}</td><td>{{ $r['unit'] }}</td><td>{{ number_format($r['kl']) }}</td><td>{{ number_format($r['cj']) }}</td></tr>
@endforeach
@endif

                        <!---<thead class="table-light">
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
                            <td>{{ number_format($summary->kl_cold_dr_days ?? 0, 0) }}</td>
                            <td>{{ number_format($summary->cyber_cold_dr_days ?? 0, 0) }}</td>
                            
                        </tr>
                        <tr>
                            <td>Cold DR - Seeding VM</td>
                            <td>Unit</td>
                            <td>{{ $summary->kl_cold_dr_seeding_vm ?? 0 }}</td>
                            <td>{{ $summary->cyber_cold_dr_seeding_vm ?? 0 }}</td>
                           
                        </tr>
                        <tr>
                            <td>Cloud Server Disaster Recovery Storage</td>
                            <td>GB</td>
                            <td>{{ number_format($summary->kl_dr_storage ?? 0, 0) }}</td>
                            <td>{{ number_format($summary->cyber_dr_storage ?? 0, 0) }}</td>
                             
                        </tr>
                        <tr>
                            <td>Cloud Server Disaster Recovery Replication</td>
                            <td>Unit</td>
                            <td>{{ $summary->kl_dr_replication ?? 0 }}</td>
                            <td>{{ $summary->cyber_dr_replication ?? 0 }}</td>
                             
                        </tr>
                        <tr>
                            <td>Cloud Server Disaster Recovery Days (DR Declaration)</td>
                            <td>Days</td>
                            <td>{{ number_format($summary->kl_dr_declaration ?? 0, 0) }}</td>
                            <td>{{ number_format($summary->cyber_dr_declaration ?? 0, 0) }}</td>
                             
                        </tr>
                        <tr>
                            <td>Cloud Server Disaster Recovery Managed Service - Per Day</td>
                            <td>Unit</td>
                            <td>{{ $summary->kl_dr_managed_service ?? 0 }}</td>
                            <td>{{ $summary->cyber_dr_managed_service ?? 0 }}</td>
                          
                        </tr>--->
{{-- DR Network & Security --}}
@php
    $drnsRows = [
        ['label'=>'Cloud Server Disaster Recovery (vPLL)','unit'=>'Mbps','kl'=>$summary->kl_dr_vpll ?? 0,'cj'=>$summary->cyber_dr_vpll ?? 0],
        ['label'=>'DR Elastic IP','unit'=>'Unit Per Day','kl'=>$summary->kl_dr_elastic_ip ?? 0,'cj'=>$summary->cyber_dr_elastic_ip ?? 0],
        ['label'=>'DR Bandwidth','unit'=>'Mbps Per Day','kl'=>$summary->kl_dr_bandwidth ?? 0,'cj'=>$summary->cyber_dr_bandwidth ?? 0],
        ['label'=>'DR Bandwidth + Anti-DDoS','unit'=>'Mbps Per Day','kl'=>$summary->kl_dr_bandwidth_antiddos ?? 0,'cj'=>$summary->cyber_dr_bandwidth_antiddos ?? 0],
        ['label'=>'DR Cloud Firewall (Fortigate)','unit'=>'Unit Per Day','kl'=>$summary->kl_dr_firewall_fortigate ?? 0,'cj'=>$summary->cyber_dr_firewall_fortigate ?? 0],
        ['label'=>'DR Cloud Firewall (OPNSense)','unit'=>'Unit Per Day','kl'=>$summary->kl_dr_firewall_opnsense ?? 0,'cj'=>$summary->cyber_dr_firewall_opnsense ?? 0],
    ];
    $drnsRows = array_values(array_filter($drnsRows, fn($r)=>($r['kl'] ?? 0)>0 || ($r['cj'] ?? 0)>0));
@endphp
@if(count($drnsRows))
<thead class="table-light">
<tr><th>Disaster Recovery Network and Security</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
</thead>
@foreach($drnsRows as $r)
<tr><td>{{ $r['label'] }}</td><td>{{ $r['unit'] }}</td><td>{{ $r['kl'] }}</td><td>{{ $r['cj'] }}</td></tr>
@endforeach
@endif

                        <!---<thead class="table-light">
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
                            <td>{{ $summary->kl_dr_vpll ?? 0 }}</td>
                            <td>{{ $summary->cyber_dr_vpll ?? 0 }}</td>
                             
                        </tr>
                        <tr>
                            <td>DR Elastic IP</td>
                            <td>Unit Per Day</td>
                            <td>{{ $summary->kl_dr_elastic_ip ?? 0 }}</td>
                            <td>{{ $summary->cyber_dr_elastic_ip ?? 0 }}</td>
                           
                        </tr>
                        <tr>
                            <td>DR Bandwidth</td>
                            <td>Mbps Per Day</td>
                            <td>{{ $summary->kl_dr_bandwidth ?? 0 }}</td>
                            <td>{{ $summary->cyber_dr_bandwidth ?? 0 }}</td>
                            
                        </tr>
                        <tr>
                            <td>DR Bandwidth + Anti-DDoS</td>
                            <td>Mbps Per Day</td>
                            <td>{{ $summary->kl_dr_bandwidth_antiddos ?? 0 }}</td>
                            <td>{{ $summary->cyber_dr_bandwidth_antiddos ?? 0 }}</td>
                           
                        </tr>
                        <tr>
                            <td>DR Cloud Firewall (Fortigate)</td>
                            <td>Unit Per Day</td>
                            <td>{{ $summary->kl_dr_firewall_fortigate ?? 0 }}</td>
                            <td>{{ $summary->cyber_dr_firewall_fortigate ?? 0 }}</td>
                            
                        </tr>
                        <tr>
                            <td>DR Cloud Firewall (OPNSense)</td>
                            <td>Unit Per Day</td>
                            <td>{{ $summary->kl_dr_firewall_opnsense ?? 0 }}</td>
                            <td>{{ $summary->cyber_dr_firewall_opnsense ?? 0 }}</td>
                             
                        </tr>--->
@php
    $hasDrFlavourRows = (collect($drCountsKL)->sum() + collect($drCountsCJ)->sum()) > 0;
@endphp
@if($any($klEvsDR ?? 0, $cyberEvsDR ?? 0) || $hasDrFlavourRows)
<thead class="table-light">
<tr><th>Disaster Recovery Resources (During DR Activation)</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
</thead>

@if($any($klEvsDR ?? 0, $cyberEvsDR ?? 0))
<tr>
    <td>DR Elastic Volume Service (EVS)</td>
    <td>GB</td>
    <td>{{ $klEvsDR ?? 0 }}</td>
    <td>{{ $cyberEvsDR ?? 0 }}</td>
</tr>
@endif

@foreach(($usedFlavours ?? collect()) as $flavour)
    @php
        $flavourWithDR = $flavour . '.dr';
        $details = $flavourDetails->get($flavourWithDR);
        $sizing  = $details ? "{$details['vcpu']} vCPU , {$details['vram']} vRAM" : '-';
        $klQty   = (int)($drCountsKL[$flavour] ?? 0);
        $cjQty   = (int)($drCountsCJ[$flavour] ?? 0);
    @endphp
    @continue(!$any($klQty, $cjQty)) 
    <tr style="background-color:rgb(251,194,224);">
        <td><a href="{{ route('flavour.index',['highlight'=>$flavourWithDR]) }}">{{ $flavourWithDR }}</a></td>
        <td>{{ $sizing }}</td>
        <td>{{ $klQty }}</td>
        <td>{{ $cjQty }}</td>
    </tr>
@endforeach
@endif


{{-- DR Licenses --}}
@php
    $drlRows = [
        ['label'=>'License Month','unit'=>'Month(s)','kl'=>$summary->kl_dr_license_months ?? 0,'cj'=>$summary->cyber_dr_license_months ?? 0],
        ['label'=>'DR Per Month - Microsoft Windows Server (Core Pack) - Standard','unit'=>'Unit Per Month','kl'=>$summary->kl_dr_windows_std ?? 0,'cj'=>$summary->cyber_dr_windows_std ?? 0],
        ['label'=>'DR Per Month - Microsoft Windows Server (Core Pack) - Data Center','unit'=>'Unit Per Month','kl'=>$summary->kl_dr_windows_dc ?? 0,'cj'=>$summary->cyber_dr_windows_dc ?? 0],
        ['label'=>'DR Per Month - Microsoft Remote Desktop Services (SAL)','unit'=>'Unit Per Month','kl'=>$summary->kl_dr_rds ?? 0,'cj'=>$summary->cyber_dr_rds ?? 0],
        ['label'=>'DR Per Month - Microsoft SQL (Web) (Core Pack)','unit'=>'Unit Per Month','kl'=>$summary->kl_dr_sql_web ?? 0,'cj'=>$summary->cyber_dr_sql_web ?? 0],
        ['label'=>'DR Per Month - Microsoft SQL (Standard) (Core Pack)','unit'=>'Unit Per Month','kl'=>$summary->kl_dr_sql_std ?? 0,'cj'=>$summary->cyber_dr_sql_std ?? 0],
        ['label'=>'DR Per Month - Microsoft SQL (Enterprise) (Core Pack)','unit'=>'Unit Per Month','kl'=>$summary->kl_dr_sql_ent ?? 0,'cj'=>$summary->cyber_dr_sql_ent ?? 0],
        ['label'=>'DR Per Month - RHEL (1–8vCPU)','unit'=>'Unit Per Month','kl'=>$summary->kl_dr_rhel_1_8 ?? 0,'cj'=>$summary->cyber_dr_rhel_1_8 ?? 0],
        ['label'=>'DR Per Month - RHEL (9–127vCPU)','unit'=>'Unit Per Month','kl'=>$summary->kl_dr_rhel_9_127 ?? 0,'cj'=>$summary->cyber_dr_rhel_9_127 ?? 0],
    ];
    $drlRows = array_values(array_filter($drlRows, fn($r)=>($r['kl'] ?? 0)>0 || ($r['cj'] ?? 0)>0));
@endphp
@if(count($drlRows))
<thead class="table-light">
<tr><th>Disaster Recovery Licenses</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
</thead>
@foreach($drlRows as $r)
<tr><td>{{ $r['label'] }}</td><td>{{ $r['unit'] }}</td><td>{{ $r['kl'] }}</td><td>{{ $r['cj'] }}</td></tr>
@endforeach
@endif






                        <!---<thead class="table-light">
                            <tr>
                                <th>Disaster Recovery Licenses</th>
                                <th>Unit</th>
                                <th>KL.Qty</th>
                                <th>CJ.Qty</th>
                              
                            </tr>
                        </thead>
                        <tr>
                            <td>License Month</td>
                            <td>Month(s)</td>
                            <td>{{ $summary->kl_dr_license_months ?? 0 }}</td>
                            <td>{{ $summary->cyber_dr_license_months ?? 0 }}</td>
                           
                        </tr>
                        <tr>
                            <td>DR Per Month - Microsoft Windows Server (Core Pack) - Standard</td>
                            <td>Unit Per Month</td>
                            <td>{{ $summary->kl_dr_windows_std ?? 0 }}</td>
                            <td>{{ $summary->cyber_dr_windows_std ?? 0 }}</td>
                            
                        </tr>
                        <tr>
                            <td>DR Per Month - Microsoft Windows Server (Core Pack) - Data Center</td>
                            <td>Unit Per Month</td>
                            <td>{{ $summary->kl_dr_windows_dc ?? 0 }}</td>
                            <td>{{ $summary->cyber_dr_windows_dc ?? 0 }}</td>
                             
                        </tr>
                        <tr>
                            <td>DR Per Month - Microsoft Remote Desktop Services (SAL)</td>
                            <td>Unit Per Month</td>
                            <td>{{ $summary->kl_dr_rds ?? 0 }}</td>
                            <td>{{ $summary->cyber_dr_rds ?? 0 }}</td>
                            
                        </tr>
                        <tr>
                            <td>DR Per Month - Microsoft SQL (Web) (Core Pack)</td>
                            <td>Unit Per Month</td>
                            <td>{{ $summary->kl_dr_sql_web ?? 0 }}</td>
                            <td>{{ $summary->cyber_dr_sql_web ?? 0 }}</td>
                             
                        </tr>
                        <tr>
                            <td>DR Per Month - Microsoft SQL (Standard) (Core Pack)</td>
                            <td>Unit Per Month</td>
                            <td>{{ $summary->kl_dr_sql_std ?? 0 }}</td>
                            <td>{{ $summary->cyber_dr_sql_std ?? 0 }}</td>
                           
                        </tr>
                        <tr>
                            <td>DR Per Month - Microsoft SQL (Enterprise) (Core Pack)</td>
                            <td>Unit Per Month</td>
                            <td>{{ $summary->kl_dr_sql_ent ?? 0 }}</td>
                            <td>{{ $summary->cyber_dr_sql_ent ?? 0 }}</td>
                           
                        </tr>
                        <tr>
                            <td>DR Per Month - RHEL (1–8vCPU)</td>
                            <td>Unit Per Month</td>
                            <td>{{ $summary->kl_dr_rhel_1_8 ?? 0 }}</td>
                            <td>{{ $summary->cyber_dr_rhel_1_8 ?? 0 }}</td>
                             
                        </tr>
                        <tr>
                            <td>DR Per Month - RHEL (9–127vCPU)</td>
                            <td>Unit Per Month</td>
                            <td>{{ $summary->kl_dr_rhel_9_127 ?? 0 }}</td>
                            <td>{{ $summary->cyber_dr_rhel_9_127 ?? 0 }}</td>
                       
                        </tr>--->
{{-- Additional Services (Cloud Security) --}}
@php
    $addRows = [
        ['label'=>'Cloud Firewall (Fortigate)','unit'=>'Unit','kl'=>$summary->kl_firewall_fortigate ?? 0,'cj'=>$summary->cyber_firewall_fortigate ?? 0],
        ['label'=>'Cloud Firewall (OPNSense)','unit'=>'Unit','kl'=>$summary->kl_firewall_opnsense ?? 0,'cj'=>$summary->cyber_firewall_opnsense ?? 0],
        ['label'=>'Cloud Shared WAF (Mbps)','unit'=>'Mbps','kl'=>$summary->kl_shared_waf ?? 0,'cj'=>$summary->cyber_shared_waf ?? 0],
        ['label'=>'Anti-Virus (Panda)','unit'=>'Unit','kl'=>$summary->kl_antivirus ?? 0,'cj'=>$summary->cyber_antivirus ?? 0],
    ];
    $addRows = array_values(array_filter($addRows, fn($r)=>($r['kl'] ?? 0)>0 || ($r['cj'] ?? 0)>0));
@endphp
@if(count($addRows))
<tr><td colspan="4" style="background-color:#e76ccf;font-weight:bold;">Additional Services</td></tr>
<thead class="table-light">
<tr><th>Cloud Security</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
</thead>
@foreach($addRows as $r)
<tr><td>{{ $r['label'] }}</td><td>{{ $r['unit'] }}</td><td>{{ $r['kl'] }}</td><td>{{ $r['cj'] }}</td></tr>
@endforeach
@endif

{{-- Security Services --}}
@php
    $secRows = [
        ['label'=>'Cloud Vulnerability Assessment (Per IP)','unit'=>'Mbps','kl'=>$summary->kl_cloud_vulnerability ?? 0,'cj'=>$summary->cyber_cloud_vulnerability ?? 0],
    ];
    $secRows = array_values(array_filter($secRows, fn($r)=>($r['kl'] ?? 0)>0 || ($r['cj'] ?? 0)>0));
@endphp
@if(count($secRows))
<thead class="table-light">
<tr><th>Security Services</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
</thead>
@foreach($secRows as $r)
<tr><td>{{ $r['label'] }}</td><td>{{ $r['unit'] }}</td><td>{{ $r['kl'] }}</td><td>{{ $r['cj'] }}</td></tr>
@endforeach
@endif

{{-- Monitoring Service --}}
@php
    $monRows = [
        ['label'=>'TCS inSight vMonitoring','unit'=>'Unit',
         'kl'=> (int)(($summary->kl_insight_vmonitoring ?? 0) == 1),
         'cj'=> (int)(($summary->cyber_insight_vmonitoring ?? 0) == 1)],
    ];
    $monRows = array_values(array_filter($monRows, fn($r)=>($r['kl'] ?? 0)>0 || ($r['cj'] ?? 0)>0));
@endphp
@if(count($monRows))
<thead class="table-light">
<tr><th>Monitoring Service</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
</thead>
@foreach($monRows as $r)
<tr><td>{{ $r['label'] }}</td><td>{{ $r['unit'] }}</td><td>{{ $r['kl'] }}</td><td>{{ $r['cj'] }}</td></tr>
@endforeach
@endif

                        <!---<tr>
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
                            <td>{{ $summary->kl_cloud_vulnerability ?? 0 }}</td>
                            <td>{{ $summary->cyber_cloud_vulnerability ?? 0 }}</td>
                          
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
                            <td>{{ ($summary->kl_insight_vmonitoring ?? 0) == 1 ? 1 : 0 }}</td>
                            <td>{{ ($summary->cyber_insight_vmonitoring ?? 0) == 1 ? 1 : 0 }}</td>
                             
                        </tr>--->



                    
@php
   
    $mpUsedFlavours   = $mpUsedFlavours   ?? collect();
    $mpFlavourDetails = $mpFlavourDetails ?? collect();
    $mpDrCountsKL     = $mpDrCountsKL     ?? [];
    $mpDrCountsCJ     = $mpDrCountsCJ     ?? [];

    
    $hasMp = !empty($mpdraas) && (
        (int)($mpdraas['activation_days'] ?? 0) > 0 ||
        (float)($mpdraas['bandwidth'] ?? 0) > 0 ||
        (count($mpUsedFlavours ?? []) > 0) ||
        (collect($mpdraas['dr_network'] ?? [])->sum(
            fn($r) => (float)($r['kl_qty'] ?? 0) + (float)($r['cj_qty'] ?? 0)
        ) > 0)
    );
@endphp
@if($hasMp)
<tr>
    <td colspan="4" style="background-color:#e76ccf; font-weight:bold;">
        Multi-Platform DR (MP-DRaaS)
    </td>
</tr>

<tr>
    <td class="table-light" style="width:35%">Activation Days (per annum)</td>
    <td style="width:15%">{{ $mpdraas['activation_days'] ?? 0 }}</td>
    <td class="table-light" style="width:35%">DDoS Requirement</td>
    <td style="width:15%">{{ $mpdraas['ddos'] ?? 'No' }}</td>
</tr>
<tr>
    <td class="table-light">DR Location</td>
    <td>{{ $mpdraas['location'] ?? 'None' }}</td>
    <td class="table-light">Bandwidth Requirement for Replication (Mbps)</td>
    <td>{{ number_format((float)($mpdraas['bandwidth'] ?? 0), 2) }}</td>
</tr>

<tr class="table-light">
    <th>MP-DRaaS Flavour Mapping</th>
    <th>Sizing</th>
    <th>KL.Qty</th>
    <th>CJ.Qty</th>
</tr>

<!---@php
  $isKL = ($mpdraas['location'] ?? '') === 'Kuala Lumpur';
  $isCJ = ($mpdraas['location'] ?? '') === 'Cyberjaya';
@endphp--->

@php
  $loc  = (string)($mpdraas['location'] ?? '');
  $isKL = strcasecmp($loc, 'Kuala Lumpur') === 0;
  $isCJ = strcasecmp($loc, 'Cyberjaya') === 0;
@endphp


@foreach(($mpUsedFlavours ?? collect()) as $flavour)
  @php
    $flavourWithDR = $flavour . '.dr';
    $details = $mpFlavourDetails->get(strtolower($flavourWithDR));
    $sizing  = $details ? ($details['vcpu'].' vCPU , '.$details['vram'].' vRAM') : '-';
    $klQty   = $isKL ? ($mpDrCountsKL[$flavour] ?? 0) : 0;
    $cjQty   = $isCJ ? ($mpDrCountsCJ[$flavour] ?? 0) : 0;
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
@endif


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

                        <!---<tr>
  <td colspan="4">
    <a href="{{ route('versions.customization.show', $version->id) }}"
       class="btn btn-outline-secondary btn-sm"
       aria-label="Open customization for entire subscription period">
      <i class="bi bi-sliders me-1"></i>
      Customization for entire subscription period
    </a>
  </td>
</tr>--->



                
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

        <div>
            <form method="POST" action="{{ route('versions.internal_summary.commit', $version->id) }}"
                  onsubmit="return confirm('Are you sure to lock and commit this Internal Summary? You can not edit previous steps after commit.');">
                @csrf
                <div class="d-flex justify-content-between mt-3">
                    <button type="submit" class="btn btn-pink" {{ $locked ? 'disabled' : '' }}>
                        Lock & Commit Summary
                    </button>
                    <a href="{{ route('versions.internal_summary.pdf', $version->id) }}" class="btn btn-pink">
                        <i class="bi bi-download"></i> Download PDF
                    </a>
                    
                </div>
            </form>
        </div>

       @php
  $alertCls = 'alert alert-warning my-2 ms-0 px-2 py-1 fs-6 d-inline-block text-start';
@endphp

@if($locked)
  <div class="{{ $alertCls }}">
    <strong>Committed for Commercial</strong>
    on {{ \Carbon\Carbon::parse($summary->logged_at)->timezone('Asia/Kuala_Lumpur')->format('d M Y, H:i') }}.
    All pricing will use this snapshot.
  </div>
@else
  <div class="{{ $alertCls }}">
    ⚠️ Not committed yet. Pricing will still recalculate if you change inputs.
  </div>
@endif



        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('versions.non_standard_items.create', $version->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Previous Step
            </a>
            <span></span>
            <a href="{{ route('versions.quotation.ratecard', $version->id) }}" class="btn btn-secondary">
                Preview Rate Card <i class="bi bi-arrow-right"></i>
            </a>
        </div>
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
    	color: #FF82E6 !important;
    	text-decoration: underline;
	}
	.breadcrumb-separator {
    	color: #999;
	}
</style>
@endpush
