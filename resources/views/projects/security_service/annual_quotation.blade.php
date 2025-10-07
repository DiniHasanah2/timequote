
@extends('layouts.app')

@php
    $solution_type = $solution_type ?? $version->solution_type ?? null;
@endphp



@php
    $mode = $mode ?? 'annual';
    $isPrescaled = $is_prescaled ?? false; 
    $mult = $isPrescaled ? 1 : (($mode === 'annual') ? 12 : 1);
    $periodLabel = ($mode === 'annual') ? 'Annual' : 'Monthly';
@endphp



@php
    $adjustment = 12; // annual = 12 bulan
@endphp



@php
    // Rate Card Values for KL and Cyberjaya
    $licenseKL = collect($licenseRateCard)->sum('kl_price');
    $licenseCJ = collect($licenseRateCard)->sum('cj_price');
    
    // ECS Values for KL and Cyberjaya
    $klEcsTotal = collect($ecsSummary)->sum('kl_price');
    $cjEcsTotal = collect($ecsSummary)->sum('cj_price');
    
    // Storage Values for KL and Cyberjaya
    $klStorageTotal = collect($storageSummary)->sum('kl_price');
    $cjStorageTotal = collect($storageSummary)->sum('cj_price');
    
    // Backup Values for KL and Cyberjaya
    $klBackupTotal = collect($backupSummary)->sum('kl_price');
    $cjBackupTotal = collect($backupSummary)->sum('cj_price');
    
    // Security Services Values for KL and Cyberjaya
    $klSecurityTotal = collect($securitySummary)->sum('kl_price');
    $cjSecurityTotal = collect($securitySummary)->sum('cj_price');
    

   

    $licenseKL = collect($licenseRateCard)->sum('kl_price');
    $licenseCJ = collect($licenseRateCard)->sum('cj_price');
    $klEcsTotal = collect($ecsSummary)->sum('kl_price');
    $cjEcsTotal = collect($ecsSummary)->sum('cj_price');

    $computedMonthlyOrAnnual = 
        ($totalManagedCharges ?? 0) +
        (($klTotal ?? 0) + ($cjTotal ?? 0)) +
        (($klEcsTotal ?? 0) + ($cjEcsTotal ?? 0)) +
        ($totalLicenseCharges ?? ($licenseKL + $licenseCJ)) +
        ($totalStorageCharges ?? 0) +
        ($totalBackupCharges ?? 0) +
        ($totalcloudSecurityCharges ?? 0) +
        ($totalMonitoringCharges ?? 0) +
        ($totalSecurityCharges ?? 0);

    $annualCommitment = $isPrescaled
        ? $computedMonthlyOrAnnual                    
        : ($computedMonthlyOrAnnual * 12);             

  
@endphp






@section('content')



<div class="card shadow-sm">


    <div class="card-header d-flex justify-between align-items-center">




@php
    $isViewOnly = isset($viewOnly) && $viewOnly == 1;
@endphp

<div class="breadcrumb-text">
    <a href="{{ route('versions.solution_type.create', $version->id) }}"
       class="breadcrumb-link {{ Route::currentRouteName() === 'versions.solution_type.create' ? 'active-link' : '' }} {{ $isViewOnly ? 'disabled-link' : '' }}">
       Solution Type
    </a>

    <span class="breadcrumb-separator">»</span>

     <a href="{{ route('versions.region.create', $version->id) }}"
       class="breadcrumb-link {{ Route::currentRouteName() === 'versions.region.create' ? 'active-link' : '' }} {{ $isViewOnly ? 'disabled-link' : '' }}">
       Professional Services
    </a>
    <span class="breadcrumb-separator">»</span>

     <a href="{{ route('versions.region.network.create', $version->id) }}"
       class="breadcrumb-link {{ Route::currentRouteName() === 'versions.region.network.create' ? 'active-link' : '' }} {{ $isViewOnly ? 'disabled-link' : '' }}">
       Network & Global Services
    </a>
    <span class="breadcrumb-separator">»</span>
      @if(($solution_type->solution_type ?? '') !== 'MP-DRaaS Only')
             <a href="{{ route('versions.backup.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.backup.create' ? 'active-link' : '' }} {{ $isViewOnly ? 'disabled-link' : '' }}">ECS & Backup</a>
    <span class="breadcrumb-separator">»</span>
            @endif
           @if(($solution_type->solution_type ?? '') !== 'MP-DRaaS Only')
            <a href="{{ route('versions.region.dr.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.region.dr.create' ? 'active-link' : '' }} {{ $isViewOnly ? 'disabled-link' : '' }}">DR Settings</a>
            <span class="breadcrumb-separator">»</span>
            @endif
              @if(($solution_type->solution_type ?? '') !== 'TCS Only')
            <a href="{{ route('versions.mpdraas.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.mpdraas.create' ? 'active-link' : '' }} {{ $isViewOnly ? 'disabled-link' : '' }}">MP-DRaaS</a>
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

            <a href="{{ route('versions.non_standard_items.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.non_standard_items.create' ? 'active-link' : '' }} {{ $isViewOnly ? 'disabled-link' : '' }}">3rd Party (Non-Standard)</a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.internal_summary.show', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.internal_summary.show' ? 'active-link' : '' }} {{ $isViewOnly ? 'disabled-link' : '' }}">Internal Summary</a>
              <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.quotation.ratecard', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.quotation.ratecard' ? 'active-link' : '' }} {{ $isViewOnly ? 'disabled-link' : '' }}">Breakdown Price</a>
              <span class="breadcrumb-separator">»</span>
            
              <a href="{{ route('versions.quotation.preview', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.quotation.preview' ? 'active-link' : '' }}">Quotation (Monthly)</a>
              <span class="breadcrumb-separator">»</span>
               <a href="{{ route('versions.quotation.annual', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.quotation.annual' ? 'active-link' : '' }} {{ $isViewOnly ? 'disabled-link' : '' }}">Quotation (Annual)</a>
              <span class="breadcrumb-separator">»</span>
               
             
            <a href=" {{ route('versions.download_zip', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.download_zip' ? 'active-link' : '' }}">Download Zip File</a> 
        </div>
        <button type="button" class="btn-close" style="margin-left: auto;" onclick="window.location.href='{{ route('projects.index') }}'"></button>

    </div>

@push('styles')
<style>
    .disabled-link {
        pointer-events: none;
        opacity: 0.5;
        color: grey !important;
        text-decoration: none !important;
    }
</style>
@endpush



    <div class="card-body">
      


        <div style="border: 1px solid #ddd; border-radius: 5px; padding: 20px; background: #fff;">
               



@php
    $catalogMeta   = config('pricing._catalog');
    $catalogLabel  = is_array($catalogMeta)
        ? ($catalogMeta['version_name'] ?? ($catalogMeta['version_code'] ?? null))
        : null;
@endphp

<p style="font-size: 15px; margin-top: 5px;">
    Confidential | {{ now()->format('d/m/Y') }} | Quotation ID: {{ $quotation->id ?? $quotationId }}@if($catalogLabel) | Catalog Version: {{ $catalogLabel }} @endif
</p>




           <div style="background-color:rgb(251, 194, 224); padding: 30px; display: flex; align-items: center; justify-content: center;">
    <img src="{{ asset('assets/time_logo.png') }}" alt="Time Logo" style="height: 29px; margin-right: 10px;">
    <span style="font-size: 30px; font-weight: bold; color: #000; line-height: 1;">CLOUD SERVICES</span>
<!---/div style="margin: 0 auto; width: 800px;">--->

</div>


      <table style="width: 100%; border-collapse: collapse; font-size: 20px; margin-top: 0px;">
    <tr style="background:rgb(147, 145, 145); color: #fff;">
        <td style="padding: 20px; font-weight: bold; width: 100px;">Attention:</td>
        <td colspan="3" style="padding: 2px;">
            {{ $project->customer->name ?? 'N/A' }}
        </td>
    </tr>
               <table style="width: 100%; border-collapse: collapse; font-size: 18px; margin-top: 0px;">
   
        <!---<td style="font-weight: bold; background: #f0f0f0;padding: 5px;">Contract Duration:</td>
        <td style="background: #fff; padding: 5px;">12 Months</td>--->
         <tr>
        <td style="font-weight: bold; background: #f0f0f0;padding: 5px;">Contract Duration:</td>


<td style="background: #fff; padding: 5px;">
    12 Months
    <input type="hidden" name="contract_duration" value="12">
</td>










    <td rowspan="2" style="font-weight: bold; background: #f0f0f0; padding: 5px;">Annual Commitment<br>(Exclude SST):</td>
    <td rowspan="2" style="background: #fff; padding: 5px;">
       
       
       

        {{ $annualCommitment > 0 ? 'RM' . number_format($annualCommitment, 2) : 'RM -' }}


    </td>

    










        
    </tr>



<script>
    document.querySelectorAll('.auto-save').forEach(function (element) {
        element.addEventListener('change', function () {
            const field = this.dataset.field;
            const value = this.value;
            const versionId = this.dataset.versionId;

            fetch(`/autosave/quotation/${versionId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ field, value })
            })
            .then(res => res.json())
            .then(data => console.log('Auto-saved:', data))
            .catch(err => console.error('Auto-save error:', err));
        });
    });
</script>

    <tr>
        <td style="font-weight: bold; background: #f0f0f0; padding: 5px;">One Time Charges <br>(Exclude SST):</td>
        <td style="background: #fff; padding: 5px;">RM{{ number_format($totalProfessionalCharges, 2) }}</td>
        <!---<td style="font-weight: bold; background: #f0f0f0; padding: 5px;"> Annual Commitment<br>(Exclude SST):</td>
        <td style="background: #fff; padding: 5px;">{{ $monthlyTotal > 0 ? 'RM' . number_format($monthlyTotal * 12, 2) : 'RM -' }}</td>--->
        
        <!---<td style="font-weight: bold; background: #f0f0f0; padding: 5px;"> Annual Commitment<br>(Exclude SST):</td>
<td style="background: #fff; padding: 5px;">
    RM{{ $monthlyTotal > 0 ? number_format($monthlyTotal * $adjustment, 2) : '-' }}
</td>--->







     </tr>     
</table>

 </table>

            
<div style="border: 1px solid #ccc; width: 100%;">
           <div style="background-color: #f0f0f0; padding: 5px; display: flex; align-items: center; justify-content: center;">
    <span style="font-size: 18px; font-weight: normal; color: #000; line-height: 1;">TOTAL CONTRACT VALUE (WITH SST)</span>
    
</div>

  <div style="background-color:rgb(255, 255, 255); padding: 5px; display: flex; align-items: center; justify-content: center;">
   

<span style="font-size: 18px; font-weight: normal; color: #000; line-height: 1;">
   
    RM{{ isset($finalTotal) ? number_format($finalTotal, 2) : '-' }}

</span>




</div>





    
</div>


            </table>

            <table style="width: 100%; border: 1px solid #000; border-collapse: collapse; font-size: 18px; margin-top: 0px;">
                <thead>
                    <tr style="background: rgb(251, 194, 224);">
                        <th colspan="5" style="border: 1px solid #000; padding: 4px;">Summary of Quotation</th>
                    </tr>
                  <table style="width: 100%; border-collapse: collapse; font-size: 18px;">
    <tr style="background: rgb(147, 145, 145); color: #fff;">
        <th rowspan="2" style="border: 1px solid #000; padding: 4px; font-weight: normal;">Category</th>
        <th rowspan="2" style="border: 1px solid #000; padding: 4px; font-weight: normal;">One Time<br>Charges</th>
        <th colspan="2" style="border: 1px solid #000; padding: 4px; font-weight: normal;">{{ $periodLabel }} Charges</th>
        <th rowspan="2" style="border: 1px solid #000; padding: 4px; font-weight: normal;">Total Charges</th>
    </tr>
    <tr style="background: rgb(147, 145, 145); color: #fff;">
        <th style="border: 1px solid #000; padding: 4px; font-weight: normal;">Region 1 (KL)</th>
        <th style="border: 1px solid #000; padding: 4px; font-weight: normal;">Region 2 (CJ)</th>
    </tr>


                </thead>
                <tbody>
<tr>
    <td style="border: 1px solid #000; padding: 4px;">Professional Services</td>
    <td style="border: 1px solid #000; padding: 4px;">RM{{ number_format($totalProfessionalCharges, 2) }}</td>
    <td style="border: 1px solid #000; padding: 4px; background: #000;">&nbsp;</td>
    <td style="border: 1px solid #000; padding: 4px; background: #000;">&nbsp;</td>
    <td style="border: 1px solid #000; padding: 4px;">RM{{ number_format($totalProfessionalCharges, 2) }}</td>
</tr>



                      <tr>
                         <td style="border: 1px solid #000; padding: 4px;">Managed Services</td>
                        <td></td>
    <td style="border: 1px solid #000; padding: 4px;">
    @php $t = collect($managedSummary)->sum('kl_price'); @endphp
    RM{{ $t > 0 ? number_format($t * $mult, 2) : '-' }}
</td>

<td style="border: 1px solid #000; padding: 4px;">
    @php $t = collect($managedSummary)->sum('cj_price'); @endphp
    RM{{ $t > 0 ? number_format($t * $mult, 2) : '-' }}
</td>
<td style="border: 1px solid #000; padding: 4px;">
    RM{{ ($totalManagedCharges > 0) ? number_format($totalManagedCharges * $mult, 2) : '-' }}
</td>


</tr>
<tr>
    <td style="border: 1px solid #000; padding: 4px;">Network</td>
    <td style="border: 1px solid #000; padding: 4px;"></td>

    <td style="border: 1px solid #000; padding: 4px;">
        RM{{ ($klTotal ?? 0) > 0 ? number_format(($klTotal ?? 0) * $mult, 2) : '-' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        RM{{ ($cjTotal ?? 0) > 0 ? number_format(($cjTotal ?? 0) * $mult, 2) : '-' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        @php
            $total = ($klTotal ?? 0) + ($cjTotal ?? 0);
        @endphp
        RM{{ $total > 0 ? number_format($total * $mult, 2) : '-' }}
    </td>
</tr>



                        <!---<td style="border: 1px solid #000; padding: 4px;">RM{{ number_format($klTotal, 2) }}</td>--->
                        <!---<td style="border: 1px solid #000; padding: 4px;">RM{{ number_format($cjTotal, 2) }}</td>--->
                        <!---<td style="border: 1px solid #000; padding: 4px;">RM{{ number_format($klTotal + $cjTotal, 2) }}</td>--->


                        <!---<tr>
                        <td style="border: 1px solid #000; padding: 4px;">Compute - ECS</td>
                         <td style="border: 1px solid #000; padding: 4px;"></td>
                         <td style="border: 1px solid #000; padding: 4px;">RM -</td>
    <td style="border: 1px solid #000; padding: 4px;">RM -</td>
    <td style="border: 1px solid #000; padding: 4px;">RM -</td>
</tr>--->

<tr>
    <td style="border: 1px solid #000; padding: 4px;">Compute - ECS</td>
    <td style="border: 1px solid #000; padding: 4px;"></td>
    <td style="border: 1px solid #000; padding: 4px;">{{ $klEcsTotal > 0 ? 'RM' . number_format($klEcsTotal * $mult, 2) : 'RM -' }}</td>
    <td style="border: 1px solid #000; padding: 4px;">{{ $cjEcsTotal > 0 ? 'RM' . number_format($cjEcsTotal * $mult, 2) : 'RM -' }}</td>
    <td style="border: 1px solid #000; padding: 4px;">{{ ($klEcsTotal + $cjEcsTotal) > 0 ? 'RM' . number_format(($klEcsTotal + $cjEcsTotal) * $mult, 2) : 'RM -' }}</td>
</tr>





                         <!---<tr>
                        <td style="border: 1px solid #000; padding: 4px;">Compute - CCE</td>
                         <td style="border: 1px solid #000; padding: 4px;"></td>
                        <td style="border: 1px solid #000; padding: 4px;">RM -</td>
                         <td style="border: 1px solid #000; padding: 4px;">RM -</td>
                

                        <td style="border: 1px solid #000; padding: 4px;">RM -</td>
                    </tr>--->


                      

                    <tr>
    <td style="border: 1px solid #000; padding: 4px;">Licenses</td>
    <td style="border: 1px solid #000; padding: 4px;"></td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ $licenseKL > 0 
            ? 'RM' . number_format($licenseKL * $mult, 2) 
            : 'RM-' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ $licenseCJ > 0 
            ? 'RM' . number_format($licenseCJ * $mult, 2) 
            : 'RM-' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ ($licenseKL + $licenseCJ) > 0 
            ? 'RM' . number_format(($licenseKL + $licenseCJ) * $mult, 2)
            : 'RM-' }}
    </td>
</tr>

<tr>
    <td style="border: 1px solid #000; padding: 4px;">Storage</td>
    <td style="border: 1px solid #000; padding: 4px;"></td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ $klStorageTotal > 0 
            ? 'RM' . number_format($klStorageTotal * $mult, 2) 
            : 'RM -' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ $cjStorageTotal > 0 
            ? 'RM' . number_format($cjStorageTotal * $mult, 2) 
            : 'RM -' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ $totalStorageCharges > 0 
            ? 'RM' . number_format($totalStorageCharges * $mult, 2) 
            : 'RM -' }}
    </td>
</tr>



<tr>
	<td style="border: 1px solid #000; padding: 4px;">Backup</td>
	<td style="border: 1px solid #000; padding: 4px;"></td>

	<td style="border: 1px solid #000; padding: 4px;">
    	{{ $klBackupTotal > 0
        	? 'RM' . number_format($klBackupTotal * $mult, 2)
        	: 'RM -' }}
	</td>

	<td style="border: 1px solid #000; padding: 4px;">
    	{{ $cjBackupTotal > 0
        	? 'RM' . number_format($cjBackupTotal * $mult, 2)
        	: 'RM -' }}
	</td>

	<td style="border: 1px solid #000; padding: 4px;">
    	{{ $totalBackupCharges > 0
        	? 'RM' . number_format($totalBackupCharges * $mult, 2)
        	: 'RM -' }}
	</td>
</tr>



                        <tr>
    <td style="border: 1px solid #000; padding: 4px;">Cloud Security</td>
    <td style="border: 1px solid #000; padding: 4px;"></td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ $cloudSecuritySummary ? (collect($cloudSecuritySummary)->sum('kl_price') > 0 
            ? 'RM' . number_format(collect($cloudSecuritySummary)->sum('kl_price') * $mult, 2) 
            : 'RM -') : 'RM -' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ $cloudSecuritySummary ? (collect($cloudSecuritySummary)->sum('cj_price') > 0 
            ? 'RM' . number_format(collect($cloudSecuritySummary)->sum('cj_price') * $mult, 2) 
            : 'RM -') : 'RM -' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ $totalcloudSecurityCharges > 0 
            ? 'RM' . number_format($totalcloudSecurityCharges * $mult, 2) 
            : 'RM -' }}
    </td>
</tr>






                      
   

     <tr>
    <td style="border: 1px solid #000; padding: 4px;">Additional Services - Monitoring</td>
    <td style="border: 1px solid #000; padding: 4px;"></td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ $monitoringSummary ? (collect($monitoringSummary)->sum('kl_price') > 0 
            ? 'RM' . number_format(collect($monitoringSummary)->sum('kl_price') * $mult, 2) 
            : 'RM -') : 'RM -' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ $monitoringSummary ? (collect($monitoringSummary)->sum('cj_price') > 0 
            ? 'RM' . number_format(collect($monitoringSummary)->sum('cj_price') * $mult, 2) 
            : 'RM -') : 'RM -' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ $totalMonitoringCharges > 0 
            ? 'RM' . number_format($totalMonitoringCharges * $mult, 2) 
            : 'RM -' }}
    </td>
</tr>





<tr>
    <td style="border: 1px solid #000; padding: 4px;">Security Services</td>
    <td style="border: 1px solid #000; padding: 4px;"></td>
    
     <td style="border: 1px solid #000; padding: 4px;">
        {{ $securitySummary ? (collect($securitySummary)->sum('kl_price') > 0 
            ? 'RM' . number_format(collect($securitySummary)->sum('kl_price') * $mult, 2) 
            : 'RM -') : 'RM -' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ $securitySummary ? (collect($securitySummary)->sum('cj_price') > 0 
            ? 'RM' . number_format(collect($securitySummary)->sum('cj_price') * $mult, 2) 
            : 'RM -') : 'RM -' }}
    </td>

    <td style="border: 1px solid #000; padding: 4px;">
        {{ $totalSecurityCharges > 0 
            ? 'RM' . number_format($totalSecurityCharges * $mult, 2) 
            : 'RM -' }}</td>
</tr>


                    


                    
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
    <tr>
        <td style="background: #f0f0f0; color: #000; padding: 5px; text-align: right; font-size: 16px;">
            ONE TIME CHARGES TOTAL
        </td>
        <td style="background: #fff; padding: 5px; width: 120px; text-align: right; border: 1px solid #ccc;">
            RM{{ number_format($totalProfessionalCharges, 2) }}
        </td>
    </tr>
    


<!---<tr>
    <td style="background: #f0f0f0; color: #000; padding: 5px; text-align: right; font-size: 16px;">
        ANNUAL TOTAL
    </td>
    <td style="background: #fff; padding: 5px; text-align: right; border: 1px solid #ccc;">
   


   {{ $monthlyTotal > 0 ? 'RM' . number_format($monthlyTotal * $mult, 2) : 'RM -' }}

</td>
</tr>--->

@php
    // Guna nilai tahunan terus dari controller; fallback: monthly * 12
    $annualTotal = isset($annualRecurringTotal)
        ? (float) $annualRecurringTotal
        : (float) (($monthlyTotal ?? 0) * 12);
@endphp

<tr>
    <td style="background: #f0f0f0; color: #000; padding: 5px; text-align: right; font-size: 16px;">
        ANNUAL TOTAL
    </td>
    <td style="background: #fff; padding: 5px; text-align: right; border: 1px solid #ccc;">
        {{ $annualTotal > 0 ? 'RM' . number_format($annualTotal, 2) : 'RM -' }}
    </td>
</tr>





    <tr>
        <td style="background: #ccc; color: #000; padding: 5px; text-align: right; font-size: 16px;">
            CONTRACT TOTAL
        </td>
        <td style="background: #fff; padding: 5px; text-align: right; border: 1px solid #ccc;">
        RM{{ $contractTotal > 0 ? number_format($contractTotal, 2) : '-' }}
    </td>
    </tr>
   
    <tr>
        <td style="background: #f0f0f0; color: #000; padding: 5px; text-align: right; font-size: 16px;">
            SERVICE TAX (8%)
        </td>
        <td style="background: #fff; padding: 5px; text-align: right; border: 1px solid #ccc;">
        RM{{ $contractTotal > 0 ? number_format($serviceTax, 2) : '-' }}
    </td>
    </tr>


    <tr>
        <td style="background: rgb(251, 194, 224); color: #000; padding: 5px; text-align: right; font-size: 16px;">
            FINAL TOTAL (Include Tax)
        </td>
        <td style="background: #fff; padding: 5px; text-align: right; border: 1px solid #ccc;">
        RM{{ $contractTotal > 0 ? number_format($finalTotal, 2) : '-' }}
    </td>
    </tr>
</table>

        

<div style="margin-top: 0px; font-size: 12px; line-height: 1.5; border: 1px solid #000; padding: 10px; border-radius: 0px;">
    <h5 style="font-weight: normal; margin-bottom: 5px;">Terms and Conditions:</h5>
    <ol style="padding-left: 15px; margin: 0;">
        <li>The delivery lead time is subject to availability of our capacity, infrastructure and upon our acknowledgement of signed Service Order form.</li>
        <li>Price quoted only valid for customer stated within this quotation for a duration of 60 days.</li>
        <li>All prices quoted shall be subjected to
            <ul style="list-style-type: disc; margin-left: 15px; padding-left: 15px;">
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










                   
                </tbody>
            </table>




            
        </div>

       <div class="d-flex justify-content-between mt-4">
 


  @if(!$isViewOnly)
        <a href="{{ route('versions.quotation.ratecard', $version->id) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Rate Card
        </a>
    @endif










    <!--- rate card: {{ route('versions.quotation.generate_pdf', $version->id) }} --->
    <div class="d-flex gap-2 ms-auto">
        

      

<!---<a href="{{ route('versions.quotation.download_table_pdf', $version->id) }}" class="btn btn-pink">
    <i class="bi bi-download"></i> Download PDF
</a>--->


<a href="{{ route('versions.quotation.download_table_pdf', $version->id) }}?mode=annual" class="btn btn-pink">
    <i class="bi bi-download"></i> Download PDF
</a>







<a href="{{ route('versions.quotation.generate_xlsx', $version->id) }}?mode=annual" class="btn btn-pink">
    <i class="bi bi-download"></i> Download Excel (.xlsx)
</a>



        <a href="{{ route('versions.download_zip', $version->id) }}" class="btn btn-pink">
            <i class="bi bi-download"></i> Download Zip File
        </a>






<a href="{{ route('versions.export_link', $version->id) }}" 
   class="btn btn-outline-secondary ms-2">
  <i class="bi bi-link-45deg"></i> Generate Share Link (Commercial)
</a>


{{-- Bila controller flash 'share_link', kita paparkan link + butang Copy --}}
@if(session('share_link'))
  <br>
  <div class="mt-2 small">
    Share with Commercial:
    <a id="shareLinkA" href="{{ session('share_link') }}" target="_blank">
      {{ session('share_link') }}
    </a>

    <button type="button" id="copyShareBtn" class="btn btn-sm btn-light ms-1">
      Copy
    </button>

    

    <div id="copyMsg" class="alert alert-success py-1 px-2 d-none mt-2 mb-0" role="alert"
         style="display:inline-block;">
      ✅ Copied!
    </div>
  </div>
@endif
    </div>
</div>

</div>

@endsection

@if(session('share_link'))
<script>
document.addEventListener('DOMContentLoaded', function () {
  const btn  = document.getElementById('copyShareBtn');
  const msg  = document.getElementById('copyMsg');
  const href = document.getElementById('shareLinkA')?.href || @json(session('share_link'));

  async function copyWithFallback(text) {
    if (navigator.clipboard && window.isSecureContext) {
      await navigator.clipboard.writeText(text);
      return;
    }
    const tmp = document.createElement('input');
    tmp.value = text;
    document.body.appendChild(tmp);
    tmp.select();
    tmp.setSelectionRange(0, 99999);
    const ok = document.execCommand('copy');
    document.body.removeChild(tmp);
    if (!ok) window.prompt('Copy this link:', text);
  }

  btn?.addEventListener('click', async () => {
    try {
      await copyWithFallback(href);
      msg?.classList.remove('d-none');
      setTimeout(() => msg?.classList.add('d-none'), 2000);
    } catch (e) {
      console.error(e);
      alert('Failed to copy. Please copy manually.');
    }
  });
});
</script>
@endif


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
