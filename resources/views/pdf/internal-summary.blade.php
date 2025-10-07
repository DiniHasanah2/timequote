{{-- resources/views/pdf/internal-summary.blade.php --}}
@php
    /** @var \App\Models\Version $version */
    /** @var \App\Models\InternalSummary $internal */
    $summary  = $internal ?? null;
    $project  = $project  ?? ($version->project ?? null);
    $customer = optional($project)->customer;
    $presale  = optional($project)->presale;

    // Logo
    $logoBase64 = $logoBase64 ?? null;
    $logoPath   = $logoPath   ?? null;

    // ===== Managed Services count =====
    $securityService = $version->security_service ?? null;
    $managedTypes = [
        'Managed Operating System',
        'Managed Backup and Restore',
        'Managed Patching',
        'Managed DR',
    ];
    $klManagedServices = array_fill_keys($managedTypes, 0);
    $cyberManagedServices = array_fill_keys($managedTypes, 0);
    if ($securityService) {
        foreach (range(1,4) as $i) {
            $s = $securityService->{'kl_managed_services_'.$i} ?? null;
            if (in_array($s, $managedTypes, true)) $klManagedServices[$s]++;
        }
        foreach (range(1,4) as $i) {
            $s = $securityService->{'cyber_managed_services_'.$i} ?? null;
            if (in_array($s, $managedTypes, true)) $cyberManagedServices[$s]++;
        }
    }

    // ===== License Summary =====
    $licenseSummary = [
        'windows_std' => [
            'Kuala Lumpur' => (int) ($summary->kl_windows_std   ?? 0),
            'Cyberjaya'    => (int) ($summary->cyber_windows_std?? 0),
        ],
        'windows_dc'  => [
            'Kuala Lumpur' => (int) ($summary->kl_windows_dc    ?? 0),
            'Cyberjaya'    => (int) ($summary->cyber_windows_dc ?? 0),
        ],
        'rds'         => [
            'Kuala Lumpur' => (int) ($summary->kl_rds           ?? 0),
            'Cyberjaya'    => (int) ($summary->cyber_rds        ?? 0),
        ],
        'sql_web'     => [
            'Kuala Lumpur' => (int) ($summary->kl_sql_web       ?? 0),
            'Cyberjaya'    => (int) ($summary->cyber_sql_web    ?? 0),
        ],
        'sql_std'     => [
            'Kuala Lumpur' => (int) ($summary->kl_sql_std       ?? 0),
            'Cyberjaya'    => (int) ($summary->cyber_sql_std    ?? 0),
        ],
        'sql_ent'     => [
            'Kuala Lumpur' => (int) ($summary->kl_sql_ent       ?? 0),
            'Cyberjaya'    => (int) ($summary->cyber_sql_ent    ?? 0),
        ],
        'rhel_1_8'    => [
            'Kuala Lumpur' => (int) ($summary->kl_rhel_1_8      ?? 0),
            'Cyberjaya'    => (int) ($summary->cyber_rhel_1_8   ?? 0),
        ],
        'rhel_9_127'  => [
            'Kuala Lumpur' => (int) ($summary->kl_rhel_9_127    ?? 0),
            'Cyberjaya'    => (int) ($summary->cyber_rhel_9_127 ?? 0),
        ],
    ];

    // ===== ECS Summary =====
    $ecsSummary = is_array($summary->ecs_flavour_summary ?? null) ? $summary->ecs_flavour_summary : [];
    $ecsFlavoursCfg = collect(config('flavours', []))->keyBy('name');
    $usedFlavours = collect($ecsSummary['Kuala Lumpur'] ?? [])
        ->keys()
        ->merge(collect($ecsSummary['Cyberjaya'] ?? [])->keys())
        ->unique()->sort()->values();

    // ===== IMS + Storage totals =====
    $klEvs    = (float) ($summary->kl_evs    ?? 0);
    $cyberEvs = (float) ($summary->cyber_evs ?? 0);

    // ===== DR During Activation =====
    $klEvsDR    = (float) ($summary->kl_evs_dr    ?? 0);
    $cyberEvsDR = (float) ($summary->cyber_evs_dr ?? 0);

    $drCountsKL = is_array($summary->dr_counts_kl ?? null) ? $summary->dr_counts_kl : [];
    $drCountsCJ = is_array($summary->dr_counts_cj ?? null) ? $summary->dr_counts_cj : [];
    $drUsedFlavours = collect(array_keys((array)$drCountsKL))
        ->merge(array_keys((array)$drCountsCJ))
        ->unique()->sort()->values();

    $nonStandardItems = $version->non_standard_items ?? collect();
    $nf0 = fn($v) => number_format((float)$v, 0);
@endphp

@php
   
    $nz = function($v) {
        return is_numeric($v) ? ((float)$v) > 0 : !empty($v);
    };
    // at least satu nilai ada
    $any = function(...$vs) use ($nz) {
        foreach ($vs as $x) { if ($nz($x)) return true; }
        return false;
    };
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Internal Summary PDF</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #000; padding: 4px; vertical-align: top; }
        .no-border td, .no-border th { border: none !important; }
        .center { text-align: center; }
        .right { text-align: right; }
        .grey { background: #f0f0f0; }
        .dark { background: rgb(147,145,145); color: #fff; }
        .pink { background: #e76ccf; color: #000; }
        .rose { background: rgb(251, 194, 224); }
        .wrap { border: 1px solid #ddd; border-radius: 6px; padding: 10px; background: #fff; }
        .section-title { background: #e76ccf; font-weight: bold; }
        .small { font-size: 11px; }
        .muted { color: #666; }
    </style>
</head>
<body>

{{-- Header: Logo + Title --}}
<div class="rose" style="padding:14px 18px; text-align:center;">
    <table class="no-border" style="display:inline-table; width:auto;">
        <tr>
            <td style="padding:0 8px; vertical-align:middle;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo" style="height:22px; vertical-align:middle;">
                @elseif(!empty($logoPath) && is_file($logoPath))
                    <img src="file://{{ $logoPath }}" alt="Logo" style="height:22px; vertical-align:middle;">
                @endif
            </td>
            <td style="padding:0 8px; vertical-align:middle; white-space:nowrap;">
                <span style="font-size:18px; font-weight:700;">INTERNAL SUMMARY</span>
            </td>
        </tr>
    </table>
</div>

<div class="wrap">
    {{-- Top info blocks --}}
    <table style="margin-bottom:8px;">
        <tr>
            <td style="width:25%;">
                <div class="muted small">PROJECT</div>
                <div style="font-weight:700;">{{ $project->name ?? '-' }}</div>
                <div class="small muted">ID: {{ $project->id ?? '-' }}</div>
            </td>
            <td style="width:25%;">
                <div class="muted small">CUSTOMER</div>
                <div style="font-weight:700;">{{ $customer->name ?? 'N/A' }}</div>
                <div class="small muted">ID: {{ $project->customer_id ?? '-' }}</div>
            </td>
            <td style="width:25%;">
                <div class="muted small">VERSION</div>
                <div style="font-weight:700;">{{ $version->version_name ?? '-' }}</div>
                <div class="small muted">v{{ $version->version_number ?? '-' }}</div>
            </td>
            <td style="width:25%;">
                <div class="muted small">PRESALE</div>
                <div style="font-weight:700;">{{ $presale->name ?? $presale->email ?? 'Unassigned' }}</div>
                <div class="small muted">{{ optional($project->created_at)->format('d M Y') }}</div>
            </td>
        </tr>
    </table>

    {{-- Main summary table --}}
    <table>
        <thead>
            <tr>
                <th style="width:45%"></th>
                <th style="width:15%">Unit</th>
                <th style="width:20%">Kuala Lumpur</th>
                <th style="width:20%">Cyberjaya</th>
            </tr>
        </thead>
        <tbody>
            {{-- Professional Services --}}
@php
    $ps_show = $any($summary->mandays ?? 0,
                    $summary->kl_license_count ?? 0, $summary->cyber_license_count ?? 0,
                    $summary->kl_duration ?? 0,       $summary->cyber_duration ?? 0);
@endphp
@if($ps_show)
<tr>
    <td class="section-title">Professional Services</td>
    <td class="section-title">Unit</td>
    <td class="section-title">Qty</td>
    <td class="section-title">Qty</td>
</tr>

@if($nz($summary->mandays ?? 0))
<tr>
    <td>Professional Services (ONE TIME Provisioning)</td>
    <td>Days</td>
    <td colspan="2">{{ (int)($summary->mandays ?? 0) }}</td>
</tr>
@endif

@php
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

            <!---<tr>
                <td class="section-title">Professional Services</td>
                <td class="section-title">Unit</td>
                <td class="section-title">Qty</td>
                <td class="section-title">Qty</td>
            </tr>
            <tr>
                <td>Professional Services (ONE TIME Provisioning)</td>
                <td>Days</td>
                <td colspan="2">{{ (int)($summary->mandays ?? 0) }}</td>
            </tr>
            <tr>
                <td>Migration Tools One Time Charge</td>
                <td>Unit Per Month*</td>
                <td>{{ (int)($summary->kl_license_count ?? $summary->cyber_license_count ?? 0) }} Unit</td>
                <td>{{ (int)($summary->kl_duration ?? $summary->cyber_duration ?? 0) }} Months</td>
            </tr>--->

           {{-- Managed Services --}}
@php
    $managedRows = collect($managedTypes ?? [])->map(function($svc) use ($klManagedServices, $cyberManagedServices){
        return ['name'=>$svc,'kl'=>(int)($klManagedServices[$svc] ?? 0),'cj'=>(int)($cyberManagedServices[$svc] ?? 0)];
    })->filter(fn($r)=>($r['kl']>0 || $r['cj']>0))->values();
@endphp

@if($managedRows->count())
<tr>
    <td class="section-title">Managed Services</td>
    <td class="section-title">Unit</td>
    <td class="section-title">KL.Qty</td>
    <td class="section-title">CJ.Qty</td>
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

            <!---<tr>
                <td class="section-title">Managed Services</td>
                <td class="section-title">Unit</td>
                <td class="section-title">KL.Qty</td>
                <td class="section-title">CJ.Qty</td>
            </tr>
            @foreach($managedTypes as $svc)
                <tr>
                    <td>{{ $svc }}</td>
                    <td>VM</td>
                    <td>{{ (int)($klManagedServices[$svc] ?? 0) }}</td>
                    <td>{{ (int)($cyberManagedServices[$svc] ?? 0) }}</td>
                </tr>
            @endforeach--->

            {{-- Network --}}
@php
    $networkRows = [
        ['label'=>'Bandwidth','unit'=>'Mbps','kl'=>$summary->kl_bandwidth ?? 0,'cj'=>$summary->cyber_bandwidth ?? 0],
        ['label'=>'Bandwidth with Anti-DDoS','unit'=>'Mbps','kl'=>$summary->kl_bandwidth_with_antiddos ?? 0,'cj'=>$summary->cyber_bandwidth_with_antiddos ?? 0],
        ['label'=>'Included Elastic IP (FOC)','unit'=>'Unit','kl'=>$summary->kl_included_elastic_ip ?? 0,'cj'=>$summary->cyber_included_elastic_ip ?? 0],
        ['label'=>'Elastic IP','unit'=>'Unit','kl'=>$summary->kl_elastic_ip ?? 0,'cj'=>$summary->cyber_elastic_ip ?? 0],
        ['label'=>'Elastic Load Balancer (External)','unit'=>'Unit','kl'=>$summary->kl_elastic_load_balancer ?? 0,'cj'=>$summary->cyber_elastic_load_balancer ?? 0],
        ['label'=>'Direct Connect Virtual Gateway','unit'=>'Unit','kl'=>$summary->kl_direct_connect_virtual ?? 0,'cj'=>$summary->cyber_direct_connect_virtual ?? 0],
        ['label'=>'L2BR instance','unit'=>'Unit','kl'=>$summary->kl_l2br_instance ?? 0,'cj'=>$summary->cyber_l2br_instance ?? 0],
        // CJ memang N/A untuk dua item bawah ni
        ['label'=>'Virtual Private Leased Line (vPLL)','unit'=>'Mbps','kl'=>$summary->kl_virtual_private_leased_line ?? 0,'cj'=>null],
        ['label'=>'vPLL L2BR','unit'=>'Pair','kl'=>$summary->kl_vpll_l2br ?? 0,'cj'=>null],
        ['label'=>'NAT Gateway (Small)','unit'=>'Unit','kl'=>$summary->kl_nat_gateway_small ?? 0,'cj'=>$summary->cyber_nat_gateway_small ?? 0],
        ['label'=>'NAT Gateway (Medium)','unit'=>'Unit','kl'=>$summary->kl_nat_gateway_medium ?? 0,'cj'=>$summary->cyber_nat_gateway_medium ?? 0],
        ['label'=>'NAT Gateway (Large)','unit'=>'Unit','kl'=>$summary->kl_nat_gateway_large ?? 0,'cj'=>$summary->cyber_nat_gateway_large ?? 0],
        ['label'=>'NAT Gateway (Extra-Large)','unit'=>'Unit','kl'=>$summary->kl_nat_gateway_xlarge ?? 0,'cj'=>$summary->cyber_nat_gateway_xlarge ?? 0],
        ['label'=>'Managed Global Server Load Balancer (GSLB)','unit'=>'Domain','kl'=>$summary->kl_gslb ?? 0,'cj'=>$summary->cyber_gslb ?? 0],
    ];
    $networkRows = array_values(array_filter($networkRows, function($r){
        $kl = (float)($r['kl'] ?? 0);
        $cj = is_null($r['cj']) ? 0 : (float)$r['cj'];
        return ($kl > 0) || ($cj > 0);
    }));
@endphp

@if(count($networkRows))
<tr>
    <td class="section-title">Network</td>
    <td class="section-title">Unit</td>
    <td class="section-title">KL.Qty</td>
    <td class="section-title">CJ.Qty</td>
</tr>
@foreach($networkRows as $r)
<tr>
    <td>{{ $r['label'] }}</td>
    <td>{{ $r['unit'] }}</td>
    <td>{{ (int)($r['kl'] ?? 0) }}</td>
    <td>
        @if(is_null($r['cj']))
            <span class="grey" style="display:inline-block;padding:2px 6px;">N/A</span>
        @else
            {{ (int)$r['cj'] }}
        @endif
    </td>
</tr>
@endforeach
@endif

            <!---<tr>
                <td class="section-title">Network</td>
                <td class="section-title">Unit</td>
                <td class="section-title">KL.Qty</td>
                <td class="section-title">CJ.Qty</td>
            </tr>
            <tr>
                <td>Bandwidth</td>
                <td>Mbps</td>
                <td>{{ (int)($summary->kl_bandwidth ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_bandwidth ?? 0) }}</td>
            </tr>
            <tr>
                <td>Bandwidth with Anti-DDoS</td>
                <td>Mbps</td>
                <td>{{ (int)($summary->kl_bandwidth_with_antiddos ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_bandwidth_with_antiddos ?? 0) }}</td>
            </tr>
            <tr>
                <td>Included Elastic IP (FOC)</td>
                <td>Unit</td>
                <td>{{ (int)($summary->kl_included_elastic_ip ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_included_elastic_ip ?? 0) }}</td>
            </tr>
            <tr>
                <td>Elastic IP</td>
                <td>Unit</td>
                <td>{{ (int)($summary->kl_elastic_ip ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_elastic_ip ?? 0) }}</td>
            </tr>
            <tr>
                <td>Elastic Load Balancer (External)</td>
                <td>Unit</td>
                <td>{{ (int)($summary->kl_elastic_load_balancer ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_elastic_load_balancer ?? 0) }}</td>
            </tr>
            <tr>
                <td>Direct Connect Virtual Gateway</td>
                <td>Unit</td>
                <td>{{ (int)($summary->kl_direct_connect_virtual ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_direct_connect_virtual ?? 0) }}</td>
            </tr>
            <tr>
                <td>L2BR instance</td>
                <td>Unit</td>
                <td>{{ (int)($summary->kl_l2br_instance ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_l2br_instance ?? 0) }}</td>
            </tr>
            <tr>
                <td>Virtual Private Leased Line (vPLL)</td>
                <td>Mbps</td>
                <td>{{ (int)($summary->kl_virtual_private_leased_line ?? 0) }}</td>
                <td class="grey">N/A</td>
            </tr>
            <tr>
                <td>vPLL L2BR</td>
                <td>Pair</td>
                <td>{{ (int)($summary->kl_vpll_l2br ?? 0) }}</td>
                <td class="grey">N/A</td>
            </tr>
            <tr>
                <td>NAT Gateway (Small)</td>
                <td>Unit</td>
                <td>{{ (int)($summary->kl_nat_gateway_small ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_nat_gateway_small ?? 0) }}</td>
            </tr>
            <tr>
                <td>NAT Gateway (Medium)</td>
                <td>Unit</td>
                <td>{{ (int)($summary->kl_nat_gateway_medium ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_nat_gateway_medium ?? 0) }}</td>
            </tr>
            <tr>
                <td>NAT Gateway (Large)</td>
                <td>Unit</td>
                <td>{{ (int)($summary->kl_nat_gateway_large ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_nat_gateway_large ?? 0) }}</td>
            </tr>
            <tr>
                <td>NAT Gateway (Extra-Large)</td>
                <td>Unit</td>
                <td>{{ (int)($summary->kl_nat_gateway_xlarge ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_nat_gateway_xlarge ?? 0) }}</td>
            </tr>
            <tr>
                <td>Managed Global Server Load Balancer (GSLB)</td>
                <td>Domain</td>
                <td>{{ (int)($summary->kl_gslb ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_gslb ?? 0) }}</td>
            </tr>--->

          {{-- Computing --}}
@if($usedFlavours->count())
<tr>
    <td colspan="4" class="section-title">Computing</td>
</tr>
<tr>
    <th>Compute - Elastic Cloud Server (ECS)</th>
    <th>Sizing</th>
    <th>KL.Qty</th>
    <th>CJ.Qty</th>
</tr>
@foreach($usedFlavours as $flavour)
    @php
        $meta   = $ecsFlavoursCfg->get($flavour);
        $sizing = $meta ? (($meta['vcpu'] ?? '-') . ' vCPU , ' . ($meta['vram'] ?? '-') . ' vRAM') : '-';
        $klQty  = (int)data_get($ecsSummary, "Kuala Lumpur.$flavour", 0);
        $cjQty  = (int)data_get($ecsSummary, "Cyberjaya.$flavour", 0);
        if ($klQty===0 && $cjQty===0) { continue; }
    @endphp
    <tr class="rose">
        <td>{{ $flavour }}</td>
        <td>{{ $sizing }}</td>
        <td>{{ $klQty }}</td>
        <td>{{ $cjQty }}</td>
    </tr>
@endforeach
@endif

            <!---<tr>
                <td colspan="4" class="section-title">Computing</td>
            </tr>
            <tr>
                <th>Compute - Elastic Cloud Server (ECS)</th>
                <th>Sizing</th>
                <th>KL.Qty</th>
                <th>CJ.Qty</th>
            </tr>
            @foreach($usedFlavours as $flavour)
                @php
                    $meta = $ecsFlavoursCfg->get($flavour);
                    $sizing = $meta ? (($meta['vcpu'] ?? '-') . ' vCPU , ' . ($meta['vram'] ?? '-') . ' vRAM') : '-';
                    $klQty = (int) (data_get($ecsSummary, "Kuala Lumpur.$flavour", 0));
                    $cjQty = (int) (data_get($ecsSummary, "Cyberjaya.$flavour", 0));
                @endphp
                <tr class="rose">
                    <td>{{ $flavour }}</td>
                    <td>{{ $sizing }}</td>
                    <td>{{ $klQty }}</td>
                    <td>{{ $cjQty }}</td>
                </tr>
            @endforeach--->

           {{-- License --}}
@php
    $msRows = [
        ['label'=>'Microsoft Windows Server (Core Pack) - Standard','kl'=>$licenseSummary['windows_std']['Kuala Lumpur'] ?? 0,'cj'=>$licenseSummary['windows_std']['Cyberjaya'] ?? 0],
        ['label'=>'Microsoft Windows Server (Core Pack) - Data Center','kl'=>$licenseSummary['windows_dc']['Kuala Lumpur'] ?? 0,'cj'=>$licenseSummary['windows_dc']['Cyberjaya'] ?? 0],
        ['label'=>'Microsoft Remote Desktop Services (SAL)','kl'=>$licenseSummary['rds']['Kuala Lumpur'] ?? 0,'cj'=>$licenseSummary['rds']['Cyberjaya'] ?? 0],
        ['label'=>'Microsoft SQL (Web) (Core Pack)','kl'=>$licenseSummary['sql_web']['Kuala Lumpur'] ?? 0,'cj'=>$licenseSummary['sql_web']['Cyberjaya'] ?? 0],
        ['label'=>'Microsoft SQL (Standard) (Core Pack)','kl'=>$licenseSummary['sql_std']['Kuala Lumpur'] ?? 0,'cj'=>$licenseSummary['sql_std']['Cyberjaya'] ?? 0],
        ['label'=>'Microsoft SQL (Enterprise) (Core Pack)','kl'=>$licenseSummary['sql_ent']['Kuala Lumpur'] ?? 0,'cj'=>$licenseSummary['sql_ent']['Cyberjaya'] ?? 0],
    ];
    $msRows = array_values(array_filter($msRows, fn($r)=>((int)$r['kl']>0)||((int)$r['cj']>0)));

    $rhelRows = [
        ['label'=>'RHEL (1–8vCPU)','kl'=>$licenseSummary['rhel_1_8']['Kuala Lumpur'] ?? 0,'cj'=>$licenseSummary['rhel_1_8']['Cyberjaya'] ?? 0],
        ['label'=>'RHEL (9–127vCPU)','kl'=>$licenseSummary['rhel_9_127']['Kuala Lumpur'] ?? 0,'cj'=>$licenseSummary['rhel_9_127']['Cyberjaya'] ?? 0],
    ];
    $rhelRows = array_values(array_filter($rhelRows, fn($r)=>((int)$r['kl']>0)||((int)$r['cj']>0)));
@endphp

@if(count($msRows) || count($rhelRows))
<tr><td colspan="4" class="section-title">License</td></tr>
@endif

@if(count($msRows))
<tr><th>Microsoft</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
@foreach($msRows as $r)
<tr><td>{{ $r['label'] }}</td><td>Unit</td><td>{{ (int)$r['kl'] }}</td><td>{{ (int)$r['cj'] }}</td></tr>
@endforeach
@endif

@if(count($rhelRows))
<tr><th>Red Hat Enterprise License</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
@foreach($rhelRows as $r)
<tr><td>{{ $r['label'] }}</td><td>Unit</td><td>{{ (int)$r['kl'] }}</td><td>{{ (int)$r['cj'] }}</td></tr>
@endforeach
@endif

            <!---<tr>
                <td colspan="4" class="section-title">License</td>
            </tr>
            <tr>
                <th>Microsoft</th>
                <th>Unit</th>
                <th>KL.Qty</th>
                <th>CJ.Qty</th>
            </tr>
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

            {{-- RHEL --}}
            <tr>
                <th>Red Hat Enterprise License</th>
                <th>Unit</th>
                <th>KL.Qty</th>
                <th>CJ.Qty</th>
            </tr>
            <tr>
                <td>RHEL (1–8vCPU)</td>
                <td>Unit</td>
                <td>{{ $licenseSummary['rhel_1_8']['Kuala Lumpur'] ?? 0 }}</td>
                <td>{{ $licenseSummary['rhel_1_8']['Cyberjaya'] ?? 0 }}</td>
            </tr>
            <tr>
                <td>RHEL (9–127vCPU)</td>
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
    $storageRows = array_values(array_filter($storageRows, fn($r)=>((float)$r['kl']>0)||((float)$r['cj']>0)));
@endphp
@if(count($storageRows))
<tr><td colspan="4" class="section-title">Storage</td></tr>
<tr><th>Storage Type</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
@foreach($storageRows as $r)
<tr><td>{{ $r['label'] }}</td><td>{{ $r['unit'] }}</td><td>{{ $nf0($r['kl']) }}</td><td>{{ $nf0($r['cj']) }}</td></tr>
@endforeach
@endif

{{-- IMS --}}
@php
    $imsRows = [
        ['label'=>'Snapshot Storage','unit'=>'GB','kl'=>$summary->kl_snapshot_storage ?? 0,'cj'=>$summary->cyber_snapshot_storage ?? 0],
        ['label'=>'Image Storage','unit'=>'GB','kl'=>$summary->kl_image_storage ?? 0,'cj'=>$summary->cyber_image_storage ?? 0],
    ];
    $imsRows = array_values(array_filter($imsRows, fn($r)=>((float)$r['kl']>0)||((float)$r['cj']>0)));
@endphp
@if(count($imsRows))
<tr><th>Image Management Service (IMS)</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
@foreach($imsRows as $r)
<tr><td>{{ $r['label'] }}</td><td>{{ $r['unit'] }}</td><td>{{ $nf0($r['kl']) }}</td><td>{{ $nf0($r['cj']) }}</td></tr>
@endforeach
@endif

            <!---<tr>
                <td colspan="4" class="section-title">Storage</td>
            </tr>
            <tr>
                <th>Storage Type</th>
                <th>Unit</th>
                <th>KL.Qty</th>
                <th>CJ.Qty</th>
            </tr>
            <tr>
                <td>Elastic Volume Service (EVS)</td>
                <td>GB</td>
                <td>{{ $nf0($summary->kl_evs ?? 0) }}</td>
                <td>{{ $nf0($summary->cyber_evs ?? 0) }}</td>
            </tr>
            <tr>
                <td>Scalable File Service (SFS)</td>
                <td>GB</td>
                <td>{{ $nf0($summary->kl_scalable_file_service ?? 0) }}</td>
                <td>{{ $nf0($summary->cyber_scalable_file_service ?? 0) }}</td>
            </tr>
            <tr>
                <td>Object Storage Service (OBS)</td>
                <td>GB</td>
                <td>{{ $nf0($summary->kl_object_storage_service ?? 0) }}</td>
                <td>{{ $nf0($summary->cyber_object_storage_service ?? 0) }}</td>
            </tr>

            {{-- IMS --}}
            <tr>
                <th>Image Management Service (IMS)</th>
                <th>Unit</th>
                <th>KL.Qty</th>
                <th>CJ.Qty</th>
            </tr>
            <tr>
                <td>Snapshot Storage</td>
                <td>GB</td>
                <td>{{ $nf0($summary->kl_snapshot_storage ?? 0) }}</td>
                <td>{{ $nf0($summary->cyber_snapshot_storage ?? 0) }}</td>
            </tr>
            <tr>
                <td>Image Storage</td>
                <td>GB</td>
                <td>{{ $nf0($summary->kl_image_storage ?? 0) }}</td>
                <td>{{ $nf0($summary->cyber_image_storage ?? 0) }}</td>
            </tr>--->

            {{-- Backup and DR --}}
@php
    $bkRows = [
        ['label'=>'Cloud Server Backup Service - Full Backup Capacity','unit'=>'GB','kl'=>$summary->kl_full_backup_capacity ?? 0,'cj'=>$summary->cyber_full_backup_capacity ?? 0],
        ['label'=>'Cloud Server Backup Service - Incremental Backup Capacity','unit'=>'GB','kl'=>$summary->kl_incremental_backup_capacity ?? 0,'cj'=>$summary->cyber_incremental_backup_capacity ?? 0],
        ['label'=>'Cloud Server Replication Service - Retention Capacity','unit'=>'GB','kl'=>$summary->kl_replication_retention_capacity ?? 0,'cj'=>$summary->cyber_replication_retention_capacity ?? 0],
    ];
    $bkRows = array_values(array_filter($bkRows, fn($r)=>((float)$r['kl']>0)||((float)$r['cj']>0)));
@endphp
@if(count($bkRows))
<tr><td colspan="4" class="section-title">Backup and DR</td></tr>
<tr><th>Backup Service in VPC</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
@foreach($bkRows as $r)
<tr><td>{{ $r['label'] }}</td><td>{{ $r['unit'] }}</td><td>{{ $nf0($r['kl']) }}</td><td>{{ $nf0($r['cj']) }}</td></tr>
@endforeach
@endif

            <!---<tr>
                <td colspan="4" class="section-title">Backup and DR</td>
            </tr>
            <tr>
                <th>Backup Service in VPC</th>
                <th>Unit</th>
                <th>KL.Qty</th>
                <th>CJ.Qty</th>
            </tr>
            <tr>
                <td>Cloud Server Backup Service - Full Backup Capacity</td>
                <td>GB</td>
                <td>{{ $nf0($summary->kl_full_backup_capacity ?? 0) }}</td>
                <td>{{ $nf0($summary->cyber_full_backup_capacity ?? 0) }}</td>
            </tr>
            <tr>
                <td>Cloud Server Backup Service - Incremental Backup Capacity</td>
                <td>GB</td>
                <td>{{ $nf0($summary->kl_incremental_backup_capacity ?? 0) }}</td>
                <td>{{ $nf0($summary->cyber_incremental_backup_capacity ?? 0) }}</td>
            </tr>
            <tr>
                <td>Cloud Server Replication Service - Retention Capacity</td>
                <td>GB</td>
                <td>{{ $nf0($summary->kl_replication_retention_capacity ?? 0) }}</td>
                <td>{{ $nf0($summary->cyber_replication_retention_capacity ?? 0) }}</td>
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
    $drvpcRows = array_values(array_filter($drvpcRows, fn($r)=>((float)$r['kl']>0)||((float)$r['cj']>0)));
@endphp
@if(count($drvpcRows))
<tr><th>Disaster Recovery in VPC</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
@foreach($drvpcRows as $r)
<tr><td>{{ $r['label'] }}</td><td>{{ $r['unit'] }}</td><td>{{ $nf0($r['kl']) }}</td><td>{{ $nf0($r['cj']) }}</td></tr>
@endforeach
@endif

            <!---<tr>
                <th>Disaster Recovery in VPC</th>
                <th>Unit</th>
                <th>KL.Qty</th>
                <th>CJ.Qty</th>
            </tr>
            <tr>
                <td>Cold DR Days</td>
                <td>Days</td>
                <td>{{ $nf0($summary->kl_cold_dr_days ?? 0) }}</td>
                <td>{{ $nf0($summary->cyber_cold_dr_days ?? 0) }}</td>
            </tr>
            <tr>
                <td>Cold DR - Seeding VM</td>
                <td>Unit</td>
                <td>{{ (int)($summary->kl_cold_dr_seeding_vm ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_cold_dr_seeding_vm ?? 0) }}</td>
            </tr>
            <tr>
                <td>Cloud Server Disaster Recovery Storage</td>
                <td>GB</td>
                <td>{{ $nf0($summary->kl_dr_storage ?? 0) }}</td>
                <td>{{ $nf0($summary->cyber_dr_storage ?? 0) }}</td>
            </tr>
            <tr>
                <td>Cloud Server Disaster Recovery Replication</td>
                <td>Unit</td>
                <td>{{ (int)($summary->kl_dr_replication ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_dr_replication ?? 0) }}</td>
            </tr>
            <tr>
                <td>Cloud Server Disaster Recovery Days (DR Declaration)</td>
                <td>Days</td>
                <td>{{ $nf0($summary->kl_dr_declaration ?? 0) }}</td>
                <td>{{ $nf0($summary->cyber_dr_declaration ?? 0) }}</td>
            </tr>
            <tr>
                <td>Cloud Server Disaster Recovery Managed Service - Per Day</td>
                <td>Unit</td>
                <td>{{ (int)($summary->kl_dr_managed_service ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_dr_managed_service ?? 0) }}</td>
            </tr>--->
{{-- DR Network & Security --}}
@php
    $drnetRows = [
        ['label'=>'Cloud Server Disaster Recovery (vPLL)','unit'=>'Mbps','kl'=>$summary->kl_dr_vpll ?? 0,'cj'=>$summary->cyber_dr_vpll ?? 0],
        ['label'=>'DR Elastic IP','unit'=>'Unit Per Day','kl'=>$summary->kl_dr_elastic_ip ?? 0,'cj'=>$summary->cyber_dr_elastic_ip ?? 0],
        ['label'=>'DR Bandwidth','unit'=>'Mbps Per Day','kl'=>$summary->kl_dr_bandwidth ?? 0,'cj'=>$summary->cyber_dr_bandwidth ?? 0],
        ['label'=>'DR Bandwidth + Anti-DDoS','unit'=>'Mbps Per Day','kl'=>$summary->kl_dr_bandwidth_antiddos ?? 0,'cj'=>$summary->cyber_dr_bandwidth_antiddos ?? 0],
        ['label'=>'DR Cloud Firewall (Fortigate)','unit'=>'Unit Per Day','kl'=>$summary->kl_dr_firewall_fortigate ?? 0,'cj'=>$summary->cyber_dr_firewall_fortigate ?? 0],
        ['label'=>'DR Cloud Firewall (OPNSense)','unit'=>'Unit Per Day','kl'=>$summary->kl_dr_firewall_opnsense ?? 0,'cj'=>$summary->cyber_dr_firewall_opnsense ?? 0],
    ];
    $drnetRows = array_values(array_filter($drnetRows, fn($r)=>((float)$r['kl']>0)||((float)$r['cj']>0)));
@endphp
@if(count($drnetRows))
<tr><th>Disaster Recovery Network and Security</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
@foreach($drnetRows as $r)
<tr><td>{{ $r['label'] }}</td><td>{{ $r['unit'] }}</td><td>{{ (int)$r['kl'] }}</td><td>{{ (int)$r['cj'] }}</td></tr>
@endforeach
@endif

            <!---<tr>
                <th>Disaster Recovery Network and Security</th>
                <th>Unit</th>
                <th>KL.Qty</th>
                <th>CJ.Qty</th>
            </tr>
            <tr>
                <td>Cloud Server Disaster Recovery (vPLL)</td>
                <td>Mbps</td>
                <td>{{ (int)($summary->kl_dr_vpll ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_dr_vpll ?? 0) }}</td>
            </tr>
            <tr>
                <td>DR Elastic IP</td>
                <td>Unit Per Day</td>
                <td>{{ (int)($summary->kl_dr_elastic_ip ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_dr_elastic_ip ?? 0) }}</td>
            </tr>
            <tr>
                <td>DR Bandwidth</td>
                <td>Mbps Per Day</td>
                <td>{{ (int)($summary->kl_dr_bandwidth ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_dr_bandwidth ?? 0) }}</td>
            </tr>
            <tr>
                <td>DR Bandwidth + Anti-DDoS</td>
                <td>Mbps Per Day</td>
                <td>{{ (int)($summary->kl_dr_bandwidth_antiddos ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_dr_bandwidth_antiddos ?? 0) }}</td>
            </tr>
            <tr>
                <td>DR Cloud Firewall (Fortigate)</td>
                <td>Unit Per Day</td>
                <td>{{ (int)($summary->kl_dr_firewall_fortigate ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_dr_firewall_fortigate ?? 0) }}</td>
            </tr>
            <tr>
                <td>DR Cloud Firewall (OPNSense)</td>
                <td>Unit Per Day</td>
                <td>{{ (int)($summary->kl_dr_firewall_opnsense ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_dr_firewall_opnsense ?? 0) }}</td>
            </tr>--->
@php
    $hasDrAct = ($klEvsDR > 0) || ($cyberEvsDR > 0) || ($drUsedFlavours->count() > 0);
@endphp
@if($hasDrAct)
<tr><th>Disaster Recovery Resources (During DR Activation)</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>

@if($klEvsDR>0 || $cyberEvsDR>0)
<tr>
    <td>DR Elastic Volume Service (EVS)</td>
    <td>GB</td>
    <td>{{ $nf0($klEvsDR) }}</td>
    <td>{{ $nf0($cyberEvsDR) }}</td>
</tr>
@endif

@foreach($drUsedFlavours as $flavour)
    @php
        $klQtyDr = (int)($drCountsKL[$flavour] ?? 0);
        $cjQtyDr = (int)($drCountsCJ[$flavour] ?? 0);
        if ($klQtyDr===0 && $cjQtyDr===0) { continue; }
        $flavourWithDR = $flavour . '.dr';
        $metaDr  = $ecsFlavoursCfg->get($flavourWithDR) ?: $ecsFlavoursCfg->get($flavour);
        $sizingDr = $metaDr ? (($metaDr['vcpu'] ?? '-') . ' vCPU , ' . ($metaDr['vram'] ?? '-') . ' vRAM') : '-';
    @endphp
    <tr class="rose">
        <td>{{ $flavourWithDR }}</td>
        <td>{{ $sizingDr }}</td>
        <td>{{ $klQtyDr }}</td>
        <td>{{ $cjQtyDr }}</td>
    </tr>
@endforeach
@endif

           
            <!---<tr>
                <th>Disaster Recovery Resources (During DR Activation)</th>
                <th>Unit</th>
                <th>KL.Qty</th>
                <th>CJ.Qty</th>
            </tr>
            <tr>
                <td>DR Elastic Volume Service (EVS)</td>
                <td>GB</td>
                <td>{{ $nf0($klEvsDR) }}</td>
                <td>{{ $nf0($cyberEvsDR) }}</td>
            </tr>

            @foreach($drUsedFlavours as $flavour)
                @php
                    $flavourWithDR = $flavour . '.dr';
                    $metaDr = $ecsFlavoursCfg->get($flavourWithDR) ?: $ecsFlavoursCfg->get($flavour);
                    $sizingDr = $metaDr ? (($metaDr['vcpu'] ?? '-') . ' vCPU , ' . ($metaDr['vram'] ?? '-') . ' vRAM') : '-';
                    $klQtyDr = (int)($drCountsKL[$flavour] ?? 0); // asal CJ → DR ke KL
                    $cjQtyDr = (int)($drCountsCJ[$flavour] ?? 0); // asal KL → DR ke CJ
                @endphp
                <tr class="rose">
                    <td>{{ $flavourWithDR }}</td>
                    <td>{{ $sizingDr }}</td>
                    <td>{{ $klQtyDr }}</td>
                    <td>{{ $cjQtyDr }}</td>
                </tr>
            @endforeach--->

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
    $drlRows = array_values(array_filter($drlRows, fn($r)=>((int)$r['kl']>0)||((int)$r['cj']>0)));
@endphp
@if(count($drlRows))
<tr><th>Disaster Recovery Licenses</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
@foreach($drlRows as $r)
<tr><td>{{ $r['label'] }}</td><td>{{ $r['unit'] }}</td><td>{{ (int)$r['kl'] }}</td><td>{{ (int)$r['cj'] }}</td></tr>
@endforeach
@endif

            <!---<tr>
                <th>Disaster Recovery Licenses</th>
                <th>Unit</th>
                <th>KL.Qty</th>
                <th>CJ.Qty</th>
            </tr>
            <tr>
                <td>License Month</td>
                <td>Month(s)</td>
                <td>{{ (int)($summary->kl_dr_license_months ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_dr_license_months ?? 0) }}</td>
            </tr>
            <tr>
                <td>DR Per Month - Microsoft Windows Server (Core Pack) - Standard</td>
                <td>Unit Per Month</td>
                <td>{{ (int)($summary->kl_dr_windows_std ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_dr_windows_std ?? 0) }}</td>
            </tr>
            <tr>
                <td>DR Per Month - Microsoft Windows Server (Core Pack) - Data Center</td>
                <td>Unit Per Month</td>
                <td>{{ (int)($summary->kl_dr_windows_dc ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_dr_windows_dc ?? 0) }}</td>
            </tr>
            <tr>
                <td>DR Per Month - Microsoft Remote Desktop Services (SAL)</td>
                <td>Unit Per Month</td>
                <td>{{ (int)($summary->kl_dr_rds ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_dr_rds ?? 0) }}</td>
            </tr>
            <tr>
                <td>DR Per Month - Microsoft SQL (Web) (Core Pack)</td>
                <td>Unit Per Month</td>
                <td>{{ (int)($summary->kl_dr_sql_web ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_dr_sql_web ?? 0) }}</td>
            </tr>
            <tr>
                <td>DR Per Month - Microsoft SQL (Standard) (Core Pack)</td>
                <td>Unit Per Month</td>
                <td>{{ (int)($summary->kl_dr_sql_std ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_dr_sql_std ?? 0) }}</td>
            </tr>
            <tr>
                <td>DR Per Month - Microsoft SQL (Enterprise) (Core Pack)</td>
                <td>Unit Per Month</td>
                <td>{{ (int)($summary->kl_dr_sql_ent ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_dr_sql_ent ?? 0) }}</td>
            </tr>
            <tr>
                <td>DR Per Month - RHEL (1–8vCPU)</td>
                <td>Unit Per Month</td>
                <td>{{ (int)($summary->kl_dr_rhel_1_8 ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_dr_rhel_1_8 ?? 0) }}</td>
            </tr>
            <tr>
                <td>DR Per Month - RHEL (9–127vCPU)</td>
                <td>Unit Per Month</td>
                <td>{{ (int)($summary->kl_dr_rhel_9_127 ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_dr_rhel_9_127 ?? 0) }}</td>
            </tr>--->

            {{-- Additional Services --}}
@php
    $cloudSecRows = [
        ['label'=>'Cloud Firewall (Fortigate)','unit'=>'Unit','kl'=>$summary->kl_firewall_fortigate ?? 0,'cj'=>$summary->cyber_firewall_fortigate ?? 0],
        ['label'=>'Cloud Firewall (OPNSense)','unit'=>'Unit','kl'=>$summary->kl_firewall_opnsense ?? 0,'cj'=>$summary->cyber_firewall_opnsense ?? 0],
        ['label'=>'Cloud Shared WAF (Mbps)','unit'=>'Mbps','kl'=>$summary->kl_shared_waf ?? 0,'cj'=>$summary->cyber_shared_waf ?? 0],
        ['label'=>'Anti-Virus (Panda)','unit'=>'Unit','kl'=>$summary->kl_antivirus ?? 0,'cj'=>$summary->cyber_antivirus ?? 0],
    ];
    $cloudSecRows = array_values(array_filter($cloudSecRows, fn($r)=>((int)$r['kl']>0)||((int)$r['cj']>0)));
@endphp
@if(count($cloudSecRows))
<tr><td colspan="4" class="section-title">Additional Services</td></tr>
<tr><th>Cloud Security</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
@foreach($cloudSecRows as $r)
<tr><td>{{ $r['label'] }}</td><td>{{ $r['unit'] }}</td><td>{{ (int)$r['kl'] }}</td><td>{{ (int)$r['cj'] }}</td></tr>
@endforeach
@endif

{{-- Security Services --}}
@php
    $secSvcRows = [
        ['label'=>'Cloud Vulnerability Assessment (Per IP)','unit'=>'Mbps','kl'=>$summary->kl_cloud_vulnerability ?? 0,'cj'=>$summary->cyber_cloud_vulnerability ?? 0],
    ];
    $secSvcRows = array_values(array_filter($secSvcRows, fn($r)=>((int)$r['kl']>0)||((int)$r['cj']>0)));
@endphp
@if(count($secSvcRows))
<tr><th>Security Services</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
@foreach($secSvcRows as $r)
<tr><td>{{ $r['label'] }}</td><td>{{ $r['unit'] }}</td><td>{{ (int)$r['kl'] }}</td><td>{{ (int)$r['cj'] }}</td></tr>
@endforeach
@endif

{{-- Monitoring Service --}}
@php
    $monKL = ((int)($summary->kl_insight_vmonitoring ?? 0)) === 1 ? 1 : 0;
    $monCJ = ((int)($summary->cyber_insight_vmonitoring ?? 0)) === 1 ? 1 : 0;
@endphp
@if($monKL || $monCJ)
<tr><th>Monitoring Service</th><th>Unit</th><th>KL.Qty</th><th>CJ.Qty</th></tr>
<tr><td>TCS inSight vMonitoring</td><td>Unit</td><td>{{ $monKL }}</td><td>{{ $monCJ }}</td></tr>
@endif

            <!---<tr>
                <td colspan="4" class="section-title">Additional Services</td>
            </tr>
            <tr>
                <th>Cloud Security</th>
                <th>Unit</th>
                <th>KL.Qty</th>
                <th>CJ.Qty</th>
            </tr>
            <tr>
                <td>Cloud Firewall (Fortigate)</td>
                <td>Unit</td>
                <td>{{ (int)($summary->kl_firewall_fortigate ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_firewall_fortigate ?? 0) }}</td>
            </tr>
            <tr>
                <td>Cloud Firewall (OPNSense)</td>
                <td>Unit</td>
                <td>{{ (int)($summary->kl_firewall_opnsense ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_firewall_opnsense ?? 0) }}</td>
            </tr>
            <tr>
                <td>Cloud Shared WAF (Mbps)</td>
                <td>Mbps</td>
                <td>{{ (int)($summary->kl_shared_waf ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_shared_waf ?? 0) }}</td>
            </tr>
            <tr>
                <td>Anti-Virus (Panda)</td>
                <td>Unit</td>
                <td>{{ (int)($summary->kl_antivirus ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_antivirus ?? 0) }}</td>
            </tr>

            {{-- Security Services --}}
            <tr>
                <th>Security Services</th>
                <th>Unit</th>
                <th>KL.Qty</th>
                <th>CJ.Qty</th>
            </tr>
            <tr>
                <td>Cloud Vulnerability Assessment (Per IP)</td>
                <td>Mbps</td>
                <td>{{ (int)($summary->kl_cloud_vulnerability ?? 0) }}</td>
                <td>{{ (int)($summary->cyber_cloud_vulnerability ?? 0) }}</td>
            </tr>

            {{-- Monitoring --}}
            <tr>
                <th>Monitoring Service</th>
                <th>Unit</th>
                <th>KL.Qty</th>
                <th>CJ.Qty</th>
            </tr>
            <tr>
                <td>TCS inSight vMonitoring</td>
                <td>Unit</td>
                <td>{{ ((int)($summary->kl_insight_vmonitoring ?? 0)) === 1 ? 1 : 0 }}</td>
                <td>{{ ((int)($summary->cyber_insight_vmonitoring ?? 0)) === 1 ? 1 : 0 }}</td>
            </tr>--->

            {{-- Non-Standard Items --}}
            @if($nonStandardItems && $nonStandardItems->count())
                <tr>
                    <td colspan="4" class="section-title">Non-Standard Item Services</td>
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
                        <td class="right">{{ number_format((float)$item->selling_price, 2) }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

</body>
</html>
