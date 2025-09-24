<style>
    body {
        font-family: sans-serif;
        font-size: 12px;
    }

    h2 {
        text-align: center;
        margin-bottom: 15px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
        margin-top: 10px;
        page-break-inside: auto;
    }

    th, td {
        border: 1px solid #000;
        padding: 6px;
        text-align: center;
    }

    th.section-title {
        background-color: rgb(251, 194, 224);
        color: black;
        text-align: left;
        font-weight: bold;
        font-size: 14px;
    }

    .quotation-header {
        background-color: rgb(251, 194, 224);
        padding: 20px;
        text-align: center;
    }

    .quotation-header img {
        height: 24px;
        vertical-align: middle;
        margin-right: 10px;
    }

    .quotation-header span {
        font-size: 20px;
        font-weight: bold;
        vertical-align: middle;
    }

    .section-block-title {
        background-color: #f0f0f0;
        padding: 10px;
        font-size: 16px;
        font-weight: bold;
        text-align: center;
    }

    .total-block {
        font-size: 14px;
        margin-top: 15px;
    }

    .total-block td {
        padding: 8px;
        border: 1px solid #ccc;
    }

    .total-label {
        background: #f0f0f0;
        text-align: right;
        font-weight: bold;
    }

    .total-final {
        background-color: rgb(251, 194, 224);
    }

    @page {
        margin: 20px 30px 25px 30px;
    }
</style>



@php
    // License
    $licenseKL = collect($licenseRateCard)->sum('kl_price');
    $licenseCJ = collect($licenseRateCard)->sum('cj_price');

    // ECS
    $klEcsTotal = collect($ecsSummary)->sum('kl_price');
    $cjEcsTotal = collect($ecsSummary)->sum('cj_price');

    // Monthly Total
    $monthlyTotal = 
        ($totalManagedCharges ?? 0) +
        ($klTotal ?? 0) + ($cjTotal ?? 0) +
        ($klEcsTotal ?? 0) + ($cjEcsTotal ?? 0) +
        ($licenseKL + $licenseCJ) +
        ($totalStorageCharges ?? 0) +
        ($totalBackupCharges ?? 0) +
        ($totalcloudSecurityCharges ?? 0) +
        ($totalMonitoringCharges ?? 0) +
        ($totalSecurityCharges ?? 0);

    $duration = $quotation->contract_duration ?? 12;
    $contractTotal = ($monthlyTotal * $duration) + $totalProfessionalCharges;

    $serviceTax = $contractTotal * 0.08;
    $finalTotal = $contractTotal + $serviceTax;
@endphp

<div style="border: 1px solid #ddd; border-radius: 5px; padding: 20px; background: #fff;">
    <p style="font-size: 14px;">
        Confidential | {{ now()->format('d/m/Y') }} | Quotation ID: {{ (string) $quotation->id ?? 'N/A' }}
    </p>

    <div class="quotation-header">
        <img src="{{ public_path('assets/time_logo.png') }}" alt="Time Logo">
        <span>CLOUD SERVICES</span>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 13px;">
        <tr style="background: #999; color: #fff;">
            <td style="padding: 10px; font-weight: bold; width: 100px;">Attention:</td>
            <td colspan="3">{{ $project->customer->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; background: #f0f0f0;">Contract Duration:</td>
    <td>{{ $quotation->contract_duration ?? 12 }} Months</td>
            <td style="font-weight: bold; background: #f0f0f0;">Monthly Commitment (Exclude SST):</td>
            <td>{{ $monthlyTotal > 0 ? 'RM' . number_format($monthlyTotal, 2) : 'RM -' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; background: #f0f0f0;">One Time Charges (Exclude SST):</td>
            <td>RM{{ number_format($totalProfessionalCharges, 2) }}</td>
            <td style="font-weight: bold; background: #f0f0f0;">Annual Commitment (Exclude SST):</td>
            <td>{{ $monthlyTotal > 0 ? 'RM' . number_format($monthlyTotal * 12, 2) : 'RM -' }}</td>
        </tr>
    </table>

   

    <div class="section-block">
        <div class="section-block-title">TOTAL CONTRACT VALUE (WITH SST)</div>
        <div style="padding: 10px; text-align: center; font-size: 16px;">RM{{ $contractTotal > 0 ? number_format($finalTotal, 2) : '-' }}</div>
    </div>

    <table class="quotation-summary">
        <tr style="background: #e76ccf; color: white;">
            <th colspan="5" class="section-title">Summary of Quotation</th>
        </tr>
        <tr style="background: #939191; color: white;">
            <th rowspan="2">Category</th>
            <th rowspan="2">One Time Charges</th>
            <th colspan="2">Monthly Charges</th>
            <th rowspan="2">Total Charges</th>
        </tr>
        <tr style="background: #939191; color: white;">
            <th>Region 1 (KL)</th>
            <th>Region 2 (CJ)</th>
        </tr>

        <tr>
            <td>Professional Services</td>
            <td>RM{{ number_format($totalProfessionalCharges, 2) }}</td>
            <td style="background: #000;">&nbsp;</td>
            <td style="background: #000;">&nbsp;</td>
            <td>RM{{ number_format($totalProfessionalCharges, 2) }}</td>
        </tr>
        

                      <tr>
                         <td style="border: 1px solid #000; padding: 4px;">Managed Services</td>
                        <td></td>
    <td style="border: 1px solid #000; padding: 4px;">
    @php $t = collect($managedSummary)->sum('kl_price'); @endphp
    RM{{ $t > 0 ? number_format($t, 2) : '-' }}
</td>

<td style="border: 1px solid #000; padding: 4px;">
    @php $t = collect($managedSummary)->sum('cj_price'); @endphp
    RM{{ $t > 0 ? number_format($t, 2) : '-' }}
</td>
<td style="border: 1px solid #000; padding: 4px;">
    RM{{ ($totalManagedCharges > 0) ? number_format($totalManagedCharges, 2) : '-' }}
</td>
        </tr>
        

        <tr>
    <td style="border: 1px solid #000; padding: 4px;">Network</td>
    <td style="border: 1px solid #000; padding: 4px;"></td>

    <td style="border: 1px solid #000; padding: 4px;">
        RM{{ ($klTotal ?? 0) > 0 ? number_format($klTotal, 2) : '-' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        RM{{ ($cjTotal ?? 0) > 0 ? number_format($cjTotal, 2) : '-' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        @php
            $total = ($klTotal ?? 0) + ($cjTotal ?? 0);
        @endphp
        RM{{ $total > 0 ? number_format($total, 2) : '-' }}
    </td>
</tr>


<tr>
    <td style="border: 1px solid #000; padding: 4px;">Compute - ECS</td>
    <td style="border: 1px solid #000; padding: 4px;"></td>
    <td style="border: 1px solid #000; padding: 4px;">{{ $klEcsTotal > 0 ? 'RM' . number_format($klEcsTotal, 2) : 'RM -' }}</td>
    <td style="border: 1px solid #000; padding: 4px;">{{ $cjEcsTotal > 0 ? 'RM' . number_format($cjEcsTotal, 2) : 'RM -' }}</td>
    <td style="border: 1px solid #000; padding: 4px;">{{ ($klEcsTotal + $cjEcsTotal) > 0 ? 'RM' . number_format($klEcsTotal + $cjEcsTotal, 2) : 'RM -' }}</td>
</tr>
         <tr>
            <td>Compute - CCE</td><td></td><td>RM -</td><td>RM -</td><td>RM -</td>
        </tr>
        
         <tr>
    <td style="border: 1px solid #000; padding: 4px;">Licenses</td>
    <td style="border: 1px solid #000; padding: 4px;"></td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ collect($licenseRateCard)->sum('kl_price') > 0 
            ? 'RM' . number_format(collect($licenseRateCard)->sum('kl_price'), 2) 
            : 'RM-' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ collect($licenseRateCard)->sum('cj_price') > 0 
            ? 'RM' . number_format(collect($licenseRateCard)->sum('cj_price'), 2) 
            : 'RM-' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ (collect($licenseRateCard)->sum('kl_price') + collect($licenseRateCard)->sum('cj_price')) > 0 
            ? 'RM' . number_format(collect($licenseRateCard)->sum('kl_price') + collect($licenseRateCard)->sum('cj_price'), 2)
            : 'RM-' }}
    </td>
</tr>
         
           <tr>
    <td style="border: 1px solid #000; padding: 4px;">Storage</td>
    <td style="border: 1px solid #000; padding: 4px;"></td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ collect($storageSummary)->sum('kl_price') > 0 
            ? 'RM' . number_format(collect($storageSummary)->sum('kl_price'), 2) 
            : 'RM -' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ collect($storageSummary)->sum('cj_price') > 0 
            ? 'RM' . number_format(collect($storageSummary)->sum('cj_price'), 2) 
            : 'RM -' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ $totalStorageCharges > 0 
            ? 'RM' . number_format($totalStorageCharges, 2) 
            : 'RM -' }}
    </td>
</tr>


       


<tr>
	<td style="border: 1px solid #000; padding: 4px;">Backup</td>
	<td style="border: 1px solid #000; padding: 4px;"></td>

	<td style="border: 1px solid #000; padding: 4px;">
    	{{ collect($backupSummary)->sum('kl_price') > 0
        	? 'RM' . number_format(collect($backupSummary)->sum('kl_price'), 2)
        	: 'RM -' }}
	</td>

	<td style="border: 1px solid #000; padding: 4px;">
    	{{ collect($backupSummary)->sum('cj_price') > 0
        	? 'RM' . number_format(collect($backupSummary)->sum('cj_price'), 2)
        	: 'RM -' }}
	</td>

	<td style="border: 1px solid #000; padding: 4px;">
    	{{ $totalBackupCharges > 0
        	? 'RM' . number_format($totalBackupCharges, 2)
        	: 'RM -' }}
	</td>
</tr>

         <tr>
            <td>DR</td><td></td><td>RM -</td><td>RM -</td><td>RM -</td>
        </tr>
         

                      
   

     <tr>
    <td style="border: 1px solid #000; padding: 4px;">Cloud Security</td>
    <td style="border: 1px solid #000; padding: 4px;"></td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ collect($cloudSecuritySummary)->sum('kl_price') > 0 
            ? 'RM' . number_format(collect($cloudSecuritySummary)->sum('kl_price'), 2) 
            : 'RM -' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ collect($cloudSecuritySummary)->sum('cj_price') > 0 
            ? 'RM' . number_format(collect($cloudSecuritySummary)->sum('cj_price'), 2) 
            : 'RM -' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ $totalcloudSecurityCharges > 0 
            ? 'RM' . number_format($totalcloudSecurityCharges, 2) 
            : 'RM -' }}
    </td>
</tr>
         <tr>
            <td>Additional Services - Data Protection</td><td></td><td>RM -</td><td>RM -</td><td>RM -</td>
        </tr>
        
                      
   

     <tr>
    <td style="border: 1px solid #000; padding: 4px;">Additional Services - Monitoring</td>
    <td style="border: 1px solid #000; padding: 4px;"></td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ collect($monitoringSummary)->sum('kl_price') > 0 
            ? 'RM' . number_format(collect($monitoringSummary)->sum('kl_price'), 2) 
            : 'RM -' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ collect($monitoringSummary)->sum('cj_price') > 0 
            ? 'RM' . number_format(collect($monitoringSummary)->sum('cj_price'), 2) 
            : 'RM -' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ $totalMonitoringCharges > 0 
            ? 'RM' . number_format($totalMonitoringCharges, 2) 
            : 'RM -' }}
    </td>
</tr>
              <tr>
    <td style="border: 1px solid #000; padding: 4px;">Security Services</td>
    <td style="border: 1px solid #000; padding: 4px;"></td>
   <td style="border: 1px solid #000; padding: 4px;">
        {{ collect($securitySummary)->sum('kl_price') > 0 
            ? 'RM' . number_format(collect($securitySummary)->sum('kl_price'), 2) 
            : 'RM -' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ collect($securitySummary)->sum('cj_price') > 0 
            ? 'RM' . number_format(collect($securitySummary)->sum('cj_price'), 2) 
            : 'RM -' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ $totalSecurityCharges > 0 
            ? 'RM' . number_format($totalSecurityCharges, 2) 
            : 'RM -' }}</td>
</tr>
         <tr>
            <td>Other Services (OTC)</td><td></td><td></td><td></td><td></td>
        </tr>
         <tr>
            <td>Other Services (MRC)</td><td></td><td>RM -</td><td>RM -</td><td>RM -</td>
        </tr>
          <tr>
            <td>3rd Party Services</td><td></td><td></td><td></td><td></td>
        </tr>
    </table>

    <table class="total-block" style="width: 100%;">
        <tr>
            <td class="total-label">ONE TIME CHARGES TOTAL</td>
            <td>RM{{ number_format($totalProfessionalCharges, 2) }}</td>
        </tr>
       

        @php
    $monthlyTotal = 
        ($totalManagedCharges ?? 0) +
        ($klTotal ?? 0) + ($cjTotal ?? 0) +
        ($klEcsTotal ?? 0) + ($cjEcsTotal ?? 0) +
        (collect($licenseRateCard)->sum('kl_price') + collect($licenseRateCard)->sum('cj_price')) +
        ($totalStorageCharges ?? 0) +
        ($totalBackupCharges ?? 0) +
        ($totalcloudSecurityCharges ?? 0) +
        ($totalMonitoringCharges ?? 0) +
        ($totalSecurityCharges ?? 0);
@endphp


<tr>
    <td class="total-label">MONTHLY TOTAL</td>
    <td>{{ $monthlyTotal > 0 ? 'RM' . number_format($monthlyTotal, 2) : 'RM -' }}</td>
</tr>
       


@php
    $duration = $quotation->contract_duration ?? 12;
    $contractTotal = ($monthlyTotal * $duration) + $totalProfessionalCharges;
@endphp


    <tr>
        <td class="total-label">CONTRACT TOTAL</td>
        <td>RM{{ $contractTotal > 0 ? number_format($contractTotal, 2) : '-' }}</td>
    </tr>
      
    @php
    $serviceTax = $contractTotal * 0.08;
@endphp

        <tr>
            <td class="total-label">SERVICE TAX (8%)</td>
            <td>
        RM{{ $contractTotal > 0 ? number_format($serviceTax, 2) : '-' }}
    </td>
        </tr>


        
    @php
    $finalTotal = $contractTotal + $serviceTax;
@endphp

     <tr class="total-final">
        <td class="total-label">FINAL TOTAL (Include Tax)</td>
        <td>
        RM{{ $contractTotal > 0 ? number_format($finalTotal, 2) : '-' }}
    </td>
    </tr>
       
    </table>
<div style="margin-top: 20px; font-size: 10px; line-height: 1.5; border: 1px solid #000; padding: 10px;">
    <strong>Terms and Conditions:</strong>
    <ol style="margin-left: 16px; margin-top: 6px;">
        <li>The delivery lead time is subject to availability of our capacity, infrastructure and upon our acknowledgement of signed Service Order form.</li>
        <li>Price quoted only valid for customer stated within this quotation for a duration of 60 days.</li>
        <li>All prices quoted shall be subjected to
            <br>*Other charges and expenses incurred due to additional services not covered in the above quotation shall be charged based on actual amount incurred.</li>
        <li>All agreements for the provision of the services are for a fixed period and in the event of termination prior to the completion of the fixed period, 100% of the rental or regular charges for the remaining contract period shall be imposed.</li>
        <li>SLA is 99.95% Availability. No Performance SLA and Credit Guarantee is provided unless specifically mentioned.</li>
        <li>TIME will only be providing Infrastructure as a service only (IaaS). Operating System and Application will be self-managed by customer unless relevant service is subscribed.</li>
        <li>The price quoted does not include any Professional services and managed service beyond infrastructure level. If required, Scope of work and contract to be agreed before any work commence.</li>
        <li>All sums due are exclusive of the taxes of any nature including but not limited to service tax withholding taxes and any other taxes and all other government fees and charges assessed upon or with respect to the service(s).</li>
    </ol>
</div>

   
  




                   
            





