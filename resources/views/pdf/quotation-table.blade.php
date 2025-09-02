@php
    // rate card (untuk paparan jadual)
    $licenseKL = collect($licenseRateCard)->sum('kl_price');
    $licenseCJ = collect($licenseRateCard)->sum('cj_price');

    // ECS (guard kalau controller tak pass)
    $klEcsTotal = $klEcsTotal ?? collect($ecsSummary)->sum('kl_price');
    $cjEcsTotal = $cjEcsTotal ?? collect($ecsSummary)->sum('cj_price');

    // >>> Tambah ini (untuk baris jadual Storage & Backup)
    $klStorageTotal = collect($storageSummary ?? [])->sum('kl_price');
    $cjStorageTotal = collect($storageSummary ?? [])->sum('cj_price');

    $klBackupTotal  = collect($backupSummary ?? [])->sum('kl_price');
    $cjBackupTotal  = collect($backupSummary ?? [])->sum('cj_price');

    // ikut skrin: license ambil total kalau ada, jika tidak fallback KL+CJ
    $licenseTotal = $totalLicenseCharges ?? ($licenseKL + $licenseCJ);

    // === TIADA double count ===
    $monthlyTotal =
        ($totalManagedCharges ?? 0) +
        (($klTotal ?? 0) + ($cjTotal ?? 0)) +
        (($klEcsTotal ?? 0) + ($cjEcsTotal ?? 0)) +
        $licenseTotal +
        ($totalStorageCharges ?? 0) +
        ($totalBackupCharges ?? 0) +
        ($totalcloudSecurityCharges ?? 0) +
        ($totalMonitoringCharges ?? 0) +
        ($totalSecurityCharges ?? 0);

    $duration      = ($mode ?? 'monthly') === 'annual' ? 12 : ($contractDuration ?? 12);
    $contractTotal = ($monthlyTotal * $duration) + ($totalProfessionalCharges ?? 0);
    $serviceTax    = $contractTotal * 0.08;
    $finalTotal    = $contractTotal + $serviceTax;
@endphp





@php
    $backupSummary       = $backupSummary       ?? [];
    $totalBackupCharges  = $totalBackupCharges  ?? 0;
    $storageSummary      = $storageSummary      ?? [];
    $totalStorageCharges = $totalStorageCharges ?? 0;
    $cloudSecuritySummary= $cloudSecuritySummary?? [];
    $monitoringSummary   = $monitoringSummary   ?? [];
    $securitySummary     = $securitySummary     ?? [];
@endphp

@php
    // Mode & multiplier untuk paparan PDF
    $mode = $mode ?? 'monthly';
    $mult = $mode === 'annual' ? 12 : 1;
    $chargesLabel = $mult === 12 ? 'Annual Charges' : 'Monthly Charges';
@endphp


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation PDF</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #000; padding: 4px; font-size: 12px; vertical-align: top; }
        .no-border td, .no-border th { border: none; }
        .center { text-align: center; }
        .right { text-align: right; }
        .grey { background: #f0f0f0; }
        .dark { background: rgb(147,145,145); color: #fff; }
        .pink { background: rgb(251,194,224); }
        .blackcell { background: #000; color: #000; }
        .wrap { border: 1px solid #ddd; border-radius: 5px; padding: 12px; background: #fff; }
    </style>
</head>
<body>

   

    @php
    $catalogMeta   = config('pricing._catalog');
    $catalogLabel  = is_array($catalogMeta)
        ? ($catalogMeta['version_name'] ?? ($catalogMeta['version_code'] ?? null))
        : null;
@endphp

<p style="font-size: 11px; margin: 0 0 6px;">
    Confidential | {{ now()->format('d/m/Y') }} | Quotation ID: {{ $quotation->id ?? '-' }}@if($catalogLabel) | Catalog Version: {{ $catalogLabel }} @endif
</p>

    <div class="wrap">
        

     



{{-- Header: logo + text di tengah, side-by-side --}}
<div class="pink" style="padding:14px 18px; text-align:center;">
    <table class="no-border" style="display:inline-table; width:auto; border-collapse:collapse;">
        <tr>
            <td style="border:none !important; padding:0 8px; vertical-align:middle;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Time Logo" style="height:22px; display:inline-block; vertical-align:middle;">
                @elseif(!empty($logoPath) && is_file($logoPath))
                    <img src="file://{{ $logoPath }}" alt="Time Logo" style="height:22px; display:inline-block; vertical-align:middle;">
                @endif
            </td>
            <td style="border:none !important; padding:0 8px; vertical-align:middle; white-space:nowrap;">
                <span style="font-size:18px; font-weight:700; display:inline-block; vertical-align:middle;">
                    CLOUD SERVICES
                </span>
            </td>
        </tr>
    </table>
</div>




        <!---<table class="no-border" style="margin-top:6px;">
            <tr class="dark">
                <td style="width: 120px; font-weight: bold;">Attention:</td>
                <td colspan="3">{{ $project->customer->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="grey" style="font-weight:bold;">Contract Duration:</td>
                <td>
                    {{-- PDF tak boleh interact; hanya tunjuk nilai --}}
                    {{ $duration }} Months
                </td>

                @if(($mode ?? 'monthly') === 'monthly')
                    <td class="grey" style="font-weight:bold;">Monthly Commitment<br>(Exclude SST):</td>
                    <td>{{ $monthlyTotal > 0 ? 'RM' . number_format($monthlyTotal, 2) : 'RM -' }}</td>
                @else
                    <td class="grey" style="font-weight:bold;">Annual Commitment<br>(Exclude SST):</td>
                    <td>{{ $monthlyTotal > 0 ? 'RM' . number_format($monthlyTotal * 12, 2) : 'RM -' }}</td>
                @endif
            </tr>
            <tr>
                <td class="grey" style="font-weight:bold;">One Time Charges (Exclude SST):</td>
                <td>RM{{ number_format(($totalProfessionalCharges ?? 0), 2) }}</td>

                <td class="grey" style="font-weight:bold;">TOTAL CONTRACT VALUE (WITH SST)</td>
                <td>RM{{ $contractTotal > 0 ? number_format($finalTotal, 2) : '-' }}</td>
            </tr>
        </table>--->
        <table class="no-border" style="margin-top:6px;">
    <tr class="dark">
        <td style="width: 120px; font-weight: bold;">Attention:</td>
        <td colspan="3">{{ $project->customer->name ?? 'N/A' }}</td>
    </tr>
</table>

@php
    $isMonthly       = ($mode ?? 'monthly') === 'monthly';
    $commitmentLabel = $isMonthly ? 'Monthly Commitment<br>(Exclude SST)' : 'Annual Commitment<br>(Exclude SST)';
    $commitmentValue = $isMonthly ? ($monthlyTotal ?? 0) : (($monthlyTotal ?? 0) * 12);
@endphp

<table style="width:100%; border-collapse: collapse; margin-top:6px;">
    <tr>
        <td class="grey" style="font-weight:bold; width: 180px; border:1px solid #000; padding:5px;">
            Contract Duration:
        </td>
        <td style="border:1px solid #000; padding:5px;">
            {{ $duration }} Months
        </td>

        {{-- Commitment di kanan dengan rowspan=2 (layout sama macam screen) --}}
        <td class="grey" style="font-weight:bold; width: 220px; border:1px solid #000; padding:5px;" rowspan="2">
            {!! $commitmentLabel !!}
        </td>
        <td style="border:1px solid #000; padding:5px; text-align:right; width: 220px;" rowspan="2">
            {{ $commitmentValue > 0 ? 'RM' . number_format($commitmentValue, 2) : 'RM -' }}
        </td>
    </tr>
    <tr>
        <td class="grey" style="font-weight:bold; border:1px solid #000; padding:5px;">
            One Time Charges <br>(Exclude SST):
        </td>
        <td style="border:1px solid #000; padding:5px;">
            RM{{ number_format(($totalProfessionalCharges ?? 0), 2) }}
        </td>
    </tr>
</table>

{{-- Kotak TOTAL CONTRACT VALUE (WITH SST) berasingan di bawah (macam layout screen) --}}
<div style="border: 1px solid #ccc; width: 100%; margin-top:8px;">
    <div style="background-color:#f0f0f0; padding:6px; text-align:center; font-weight:700;">
        TOTAL CONTRACT VALUE (WITH SST)
    </div>
    <div style="background-color:#fff; padding:8px; text-align:center; font-size:14px;">
        {{ $contractTotal > 0 ? 'RM' . number_format($finalTotal, 2) : 'RM -' }}
    </div>
</div>


@php
    $isMonthly = ($mode ?? 'monthly') === 'monthly';
    $chargesLabel = $isMonthly ? 'Monthly Charges' : 'Annual Charges';
@endphp


        {{-- Summary --}}
        <table style="margin-top:12px;">
            <tr class="pink"><th colspan="5">Summary of Quotation</th></tr>
            <tr class="dark">
                <th rowspan="2">Category</th>
                <th rowspan="2">One Time<br>Charges</th>
               <th colspan="2">{{ $chargesLabel }}</th>
                <th rowspan="2">Total Charges</th>
            </tr>


            
            <tr class="dark">
                <th>Region 1 (KL)</th>
                <th>Region 2 (CJ)</th>
            </tr>

            {{-- Professional Services --}}
            <tr>
                <td>Professional Services</td>
                <td class="right">RM{{ number_format(($totalProfessionalCharges ?? 0), 2) }}</td>
                <td class="blackcell">&nbsp;</td>
                <td class="blackcell">&nbsp;</td>
                <td class="right">RM{{ number_format(($totalProfessionalCharges ?? 0), 2) }}</td>
            </tr>

            {{-- Managed Services --}}
            @php
                $managedKL = collect($managedSummary)->sum('kl_price');
                $managedCJ = collect($managedSummary)->sum('cj_price');
            @endphp
            <tr>
                <td>Managed Services</td>
                <td></td>
                <td class="right">{{ $managedKL > 0 ? 'RM' . number_format($managedKL * $mult, 2) : 'RM -' }}</td>
                <td class="right">{{ $managedCJ > 0 ? 'RM' . number_format($managedCJ * $mult, 2) : 'RM -' }}</td>
                <td class="right">{{ ($totalManagedCharges ?? 0) > 0 ? 'RM' . number_format(($totalManagedCharges ?? 0) * $mult, 2) : 'RM -' }}</td>
            </tr>

            {{-- Network --}}
            @php $networkTotal = ($klTotal ?? 0) + ($cjTotal ?? 0); @endphp
            <tr>
                <td>Network</td>
                <td></td>
                <td class="right">{{ ($klTotal ?? 0) > 0 ? 'RM' . number_format(($klTotal ?? 0) * $mult, 2) : 'RM -' }}</td>
                <td class="right">{{ ($cjTotal ?? 0) > 0 ? 'RM' . number_format(($cjTotal ?? 0) * $mult, 2) : 'RM -' }}</td>
                <td class="right">{{ $networkTotal > 0 ? 'RM' . number_format($networkTotal * $mult, 2) : 'RM -' }}</td>
            </tr>

            {{-- Compute - ECS --}}
            @php
                $ecsKL = $klEcsTotal ?? 0;
                $ecsCJ = $cjEcsTotal ?? 0;
            @endphp
            <tr>
                <td>Compute - ECS</td>
                <td></td>
                <td class="right">{{ $ecsKL > 0 ? 'RM' . number_format($ecsKL * $mult, 2) : 'RM -' }}</td>
                <td class="right">{{ $ecsCJ > 0 ? 'RM' . number_format($ecsCJ * $mult, 2) : 'RM -' }}</td>
                <td class="right">{{ ($ecsKL + $ecsCJ) > 0 ? 'RM' . number_format(($ecsKL + $ecsCJ) * $mult, 2) : 'RM -' }}</td>
            </tr>

            {{-- Licenses --}}
            <tr>
                <td>Licenses</td>
                <td></td>
                <td class="right">{{ $licenseKL > 0 ? 'RM' . number_format($licenseKL * $mult, 2) : 'RM -' }}</td>
                <td class="right">{{ $licenseCJ > 0 ? 'RM' . number_format($licenseCJ * $mult, 2) : 'RM -' }}</td>
                <td class="right">{{ ($licenseKL + $licenseCJ) > 0 ? 'RM' . number_format(($licenseKL + $licenseCJ) * $mult, 2) : 'RM -' }}</td>
            </tr>

            {{-- Storage --}}
            @php
                $storageTotal = $totalStorageCharges ?? (collect($storageSummary)->sum('kl_price') + collect($storageSummary)->sum('cj_price'));
            @endphp
            <tr>
                <td>Storage</td>
                <td></td>
                <td class="right">{{ $klStorageTotal > 0 ? 'RM' . number_format($klStorageTotal * $mult, 2) : 'RM -' }}</td>
                <td class="right">{{ $cjStorageTotal > 0 ? 'RM' . number_format($cjStorageTotal * $mult, 2) : 'RM -' }}</td>
                <td class="right">{{ $storageTotal > 0 ? 'RM' . number_format($storageTotal * $mult, 2) : 'RM -' }}</td>
            </tr>

            {{-- Backup --}}
            @php
                $backupTotal = $totalBackupCharges ?? (collect($backupSummary)->sum('kl_price') + collect($backupSummary)->sum('cj_price'));
            @endphp
            <tr>
                <td>Backup</td>
                <td></td>
                <td class="right">{{ $klBackupTotal > 0 ? 'RM' . number_format($klBackupTotal * $mult, 2) : 'RM -' }}</td>
                <td class="right">{{ $cjBackupTotal > 0 ? 'RM' . number_format($cjBackupTotal * $mult, 2) : 'RM -' }}</td>
                <td class="right">{{ $backupTotal > 0 ? 'RM' . number_format($backupTotal * $mult, 2) : 'RM -' }}</td>
            </tr>

            {{-- DR (placeholder) --}}
            <tr>
                <td>DR</td>
                <td></td>
                <td class="right">RM -</td>
                <td class="right">RM -</td>
                <td class="right">RM -</td>
            </tr>

            {{-- Cloud Security --}}
            @php
                $cloudSecKL = collect($cloudSecuritySummary)->sum('kl_price');
                $cloudSecCJ = collect($cloudSecuritySummary)->sum('cj_price');
                $cloudSecTotal = $totalcloudSecurityCharges ?? ($cloudSecKL + $cloudSecCJ);
            @endphp
            <tr>
                <td>Cloud Security</td>
                <td></td>
                <td class="right">{{ $cloudSecKL > 0 ? 'RM' . number_format($cloudSecKL * $mult, 2) : 'RM -' }}</td>
                <td class="right">{{ $cloudSecCJ > 0 ? 'RM' . number_format($cloudSecCJ * $mult, 2) : 'RM -' }}</td>
                <td class="right">{{ $cloudSecTotal > 0 ? 'RM' . number_format($cloudSecTotal * $mult, 2) : 'RM -' }}</td>
            </tr>

            {{-- Monitoring --}}
            @php
                $monKL = collect($monitoringSummary)->sum('kl_price');
                $monCJ = collect($monitoringSummary)->sum('cj_price');
                $monTotal = $totalMonitoringCharges ?? ($monKL + $monCJ);
            @endphp
            <tr>
                <td>Additional Services - Monitoring</td>
                <td></td>
                <td class="right">{{ $monKL > 0 ? 'RM' . number_format($monKL * $mult, 2) : 'RM -' }}</td>
                <td class="right">{{ $monCJ > 0 ? 'RM' . number_format($monCJ * $mult, 2) : 'RM -' }}</td>
                <td class="right">{{ $monTotal > 0 ? 'RM' . number_format($monTotal * $mult, 2) : 'RM -' }}</td>
            </tr>

            {{-- Security Services --}}
            @php
                $secKL = collect($securitySummary)->sum('kl_price');
                $secCJ = collect($securitySummary)->sum('cj_price');
                $secTotal = $totalSecurityCharges ?? ($secKL + $secCJ);
            @endphp
            <tr>
                <td>Security Services</td>
                <td></td>
                <td class="right">{{ $secKL > 0 ? 'RM' . number_format($secKL * $mult, 2) : 'RM -' }}</td>
                <td class="right">{{ $secCJ > 0 ? 'RM' . number_format($secCJ * $mult, 2) : 'RM -' }}</td>
                <td class="right">{{ $secTotal > 0 ? 'RM' . number_format($secTotal * $mult, 2) : 'RM -' }}</td>
            </tr>
        </table>

        {{-- Totals (bawah) --}}
        @php
            $totalsLabel = $mult === 12 ? 'ANNUAL TOTAL' : 'MONTHLY TOTAL';
        @endphp
        <table class="no-border" style="margin-top:10px;">
            <tr>
                <td class="grey right" style="font-size:13px;">ONE TIME CHARGES TOTAL</td>
                <td class="right" style="width:160px; border:1px solid #ccc;">
                    RM{{ number_format(($totalProfessionalCharges ?? 0), 2) }}
                </td>
            </tr>
            <tr>
                <td class="grey right" style="font-size:13px;">{{ $totalsLabel }}</td>
                <td class="right" style="border:1px solid #ccc;">
                    {{ $monthlyTotal > 0 ? 'RM' . number_format($monthlyTotal * $mult, 2) : 'RM -' }}
                </td>
            </tr>
            <tr>
                <td class="grey right" style="font-size:13px;background:#ccc;">CONTRACT TOTAL</td>
                <td class="right" style="border:1px solid #ccc;">
                    {{ $contractTotal > 0 ? 'RM' . number_format($contractTotal, 2) : 'RM -' }}
                </td>
            </tr>
            <tr>
                <td class="grey right" style="font-size:13px;">SERVICE TAX (8%)</td>
                <td class="right" style="border:1px solid #ccc;">
                    {{ $contractTotal > 0 ? 'RM' . number_format($serviceTax, 2) : 'RM -' }}
                </td>
            </tr>
            <tr>
                <td class="right" style="font-size:13px; background: rgb(251,194,224);">
                    FINAL TOTAL (Include Tax)
                </td>
                <td class="right" style="border:1px solid #ccc;">
                    {{ $contractTotal > 0 ? 'RM' . number_format($finalTotal, 2) : 'RM -' }}
                </td>
            </tr>
        </table>

        {{-- T&C --}}
        <div style="margin-top:8px; font-size:10px; line-height:1.5; border:1px solid #000; padding:8px;">
            <strong>Terms and Conditions:</strong>
            <ol style="margin:4px 0 0 16px; padding:0;">
                <li>The delivery lead time is subject to availability of our capacity, infrastructure and upon our acknowledgement of signed Service Order form.</li>
                <li>Price quoted only valid for customer stated within this quotation for a duration of 60 days.</li>
                <li>All prices quoted shall be subjected to
                    <ul style="margin:0 0 0 16px;">
                        <li>Other charges and expenses incurred due to additional services not covered in the above quotation shall be charged based on actual amount incurred</li>
                    </ul>
                </li>
                <li>All agreements for the provision of the services are for a fixed period and in the event of termination prior to the completion of the fixed period, 100% of the rental or regular charges for the remaining contract period shall be imposed.</li>
                <li>SLA is 99.95% Availability. No Performance SLA and Credit Guarantee is provided unless specifically mentioned.</li>
                <li>TIME will only be providing Infrastructure as a service only (IaaS). Operating System and Application will be self-managed by customer unless relevant service is subscribed.</li>
                <li>The price quoted does not include any Professional services and managed service beyond infrastructure level. If required, Scope of work and contract to be agreed before any work commence.</li>
                <li>All sums due are exclusive of the taxes of any nature including but not limited to service tax withholding taxes and any other taxes and all other government fees and charges assessed upon or with respect to the service(s).</li>
            </ol>
        </div>
    </div>
</body>
</html>
