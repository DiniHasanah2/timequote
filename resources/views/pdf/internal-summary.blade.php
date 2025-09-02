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
            <tr>
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
            </tr>

            {{-- Managed Services --}}
            <tr>
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
            @endforeach

            {{-- Network --}}
            <tr>
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
            </tr>

            {{-- Computing --}}
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
            @endforeach

            {{-- License: Microsoft --}}
            <tr>
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
            </tr>

            {{-- Storage --}}
            <tr>
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
            </tr>

            {{-- Backup & DR --}}
            <tr>
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
            </tr>

            {{-- DR in VPC --}}
            <tr>
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
            </tr>

            {{-- DR Network & Security --}}
            <tr>
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
            </tr>

            {{-- DR During Activation: EVS + ECS (.dr) --}}
            <tr>
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
            @endforeach

            {{-- DR Licenses --}}
            <tr>
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
            </tr>

            {{-- Additional Services: Cloud Security --}}
            <tr>
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
            </tr>

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
