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
    <div class="card-body">
         @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    
       <form method="POST" action="{{ route('versions.region.dr.store', $version->id) }}">
            @csrf
      


            <div class="mb-4">
                <h6 class="fw-bold">Project</h6>
                <div class="mb-3">
                    <input type="text" class="form-control bg-light" value="{{ $project->name }}" readonly>
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="version_id" value="{{ $version->id }}">
<input type="hidden" name="customer_id" value="{{ $project->customer_id }}">


                   
            </div>
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                      
                        <thead class="table-dark">
                            <tr>
                                <th colspan="4">DR Settings</th>
                            </tr>
                        </thead>
                        
                        <tr>
                            <td colspan="2">Number of DR Activation Days</td>
                           
                           <td>
    <select name="dr_activation_days" class="form-select auto-save"  data-field="dr_activation_days" 
       data-version-id="{{ $version->id }}">
        @php
            $drValue = old('dr_activation_days', 
                $region 
                ? ($region->dr_location === 'Kuala Lumpur' 
                    ? $region->kl_dr_activation_days 
                    : ($region->dr_location === 'Cyberjaya' 
                        ? $region->cyber_dr_activation_days 
                        : ''))
                : ''
            );
        @endphp


        @foreach([0, 15, 30, 45, 60] as $option)
            <option value="{{ $option }}" @selected($drValue == $option)>
                {{ $option }}
            </option>
        @endforeach
    </select>
</td>

                            <td>Days/annum</td>
                            
                        <tr>
                            <td colspan="2">DR Location</td>
                            <td colspan="2">

                            <div class="input-group">
    <input name="dr_location" 
           class="form-control bg-light auto-save"  data-field="dr_location" 
       data-version-id="{{ $version->id }}" 
           value="{{ $solution_type->dr_region ?? '' }}" 
           readonly style="border-radius: 0;">
    <input type="hidden" name="region" value="{{ $solution_type->dr_region ?? '' }}">
</div>

                    
        
   
                               
                            </td>
                        </tr>

                        
                        <tr>
                            <td>DR Bandwidth</td>
                          
                           
                                <td>

                                 <input type="number" name="db_bandwidth" class="form-control auto-save"  data-field="db_bandwidth" 
       data-version-id="{{ $version->id }}"
    
         
     
value="{{ old('db_bandwidth',
        	$region
        	? ($region->dr_location === 'Kuala Lumpur'
            	? $region->kl_db_bandwidth
            	: ($region->dr_location === 'Cyberjaya'
                	? $region->cyber_db_bandwidth
                	: ''))
        	: ''
    	) }}"




        
        
        min="0"></td>
                             <td>Mbps</td>

                              <td>
                                <div class="input-group">
                                    <select name="dr_bandwidth_type" class="form-select auto-save"  data-field="dr_bandwidth_type" 
       data-version-id="{{ $version->id }}">
    <option value="bandwidth" @selected(old('dr_bandwidth_type', $region->dr_bandwidth_type ?? '') == 'bandwidth')>Bandwidth</option>
    <option value="anti-ddos" @selected(old('dr_bandwidth_type', $region->dr_bandwidth_type ?? '') == 'anti-ddos')>Anti-DDoS</option>
</select>
                                </div>
                            </td>



                        </tr>

                      

                        <tr>
                            <td>Elastic IP during DR</td>

                            <td>
                                

                              <input type="number" name="elastic_ip_dr" class="form-control auto-save"  data-field="elastic_ip_dr" 
       data-version-id="{{ $version->id }}"
       
         
     value="{{ old('elastic_ip_dr',
        	$region
        	? ($region->dr_location === 'Kuala Lumpur'
            	? $region->kl_elastic_ip_dr
            	: ($region->dr_location === 'Cyberjaya'
                	? $region->cyber_elastic_ip_dr
                	: ''))
        	: ''
    	) }}"


min="0"></td>

                            <td>Unit</td>
                            <td>*Only Available during DR Declaration and Random Picked</td>
                            <tr>
        
                            <td></td>
                                <td>Tier 1</td>
                                <td>Tier 2</td>
                                <td class="bg-light"></td>
                            </tr>

                            <tr>
                                <td>DR Security</td>
    
                                <td>
                                    <div class="input-group">
                                       <select name="tier1_dr_security" class="form-select auto-save"  data-field="tier1_dr_security" 
       data-version-id="{{ $version->id }}">
    <option value="none" @selected(old('tier1_dr_security', $region->tier1_dr_security ?? '') == 'none')>None</option>
    <option value="fortigate" @selected(old('tier1_dr_security', $region->tier1_dr_security ?? '') == 'fortigate')>Fortigate</option>
    <option value="opn_sense" @selected(old('tier1_dr_security', $region->tier1_dr_security ?? '') == 'opn_sense')>OPNSense</option>
</select>
                                    </div>
                                </td>

                                <td>
                                    <div class="input-group">
                                      <select name="tier2_dr_security" class="form-select auto-save"  data-field="tier2_dr_security" 
       data-version-id="{{ $version->id }}">
    <option value="none" @selected(old('tier2_dr_security', $region->tier2_dr_security ?? '') == 'none')>None</option>
    <option value="fortigate" @selected(old('tier2_dr_security', $region->tier2_dr_security ?? '') == 'fortigate')>Fortigate</option>
    <option value="opn_sense" @selected(old('tier2_dr_security', $region->tier2_dr_security ?? '') == 'opn_sense')>OPNSense</option>
</select>
                                    </div>
                                </td>
                                <td class="bg-light"></td>
                        </tr>

                    </tbody>
                </table>
            </div>
            

            <div class="d-flex justify-content-between gap-3"> 
                
    
   
    <a href="{{ route('versions.backup.create', $version->id) }}" class="btn btn-secondary" role="button">
        <i class="bi bi-arrow-left"></i> Previous<br>Step
    </a>
     

    

                         <div class="d-flex flex-column align-items-centre gap-2">

            <div class="d-flex justify-content-end gap-3"> <!-- Added gap-3 for spacing -->
                <button type="submit" class="btn btn-pink">Save DR Settings</button>


                @if(($solution_type->solution_type ?? '') === 'TCS Only')
         <a href="{{ route('versions.security_service.create', $version->id) }}"  
                   class="btn btn-secondary  me-2" 
                   role="button">
                   Next: Security Services <i class="bi bi-arrow-right"></i>
                </a> 
    @else
                
                <a href="{{ route('versions.mpdraas.create', $version->id) }}"  
                   class="btn btn-secondary  me-2" 
                   role="button">
                   Next: MP-DRaaS <i class="bi bi-arrow-right"></i>
                </a> 
                @endif
            </div>
                <div class="alert alert-danger py-1 px-2 small mb-0" role="alert" style="font-size: 0.8rem;">
            ⚠️ Ensure you click <strong>Save</strong> before continuing to the next step!
    </div>

    </div>
        </form>
    </div>
</div>



<script>
    document.getElementById('calculate').addEventListener('click', function() {
        // Add your calculation logic here
    });
</script>



<script>
    // Function to calculate included Elastic IP based on bandwidth
    function calculateIncludedEIP(val1, val2) {
    if (val1 >= 81 || val2 >= 81) return 8;
    if (val1 >= 51 || val2 >= 51) return 6;
    if (val1 >= 31 || val2 >= 31) return 4;
    if (val1 >= 11 || val2 >= 11) return 2;
    return 0;
}



    // Calculate when bandwidth fields change
    document.addEventListener('DOMContentLoaded', function() {
        // Get all input fields
        const klBandwidth = document.querySelector('[name="kl_bandwidth"]');
        const klAntiDDoS = document.querySelector('[name="kl_bandwidth_with_antiddos"]');
        const cyberBandwidth = document.querySelector('[name="cyber_bandwidth"]');
        const cyberAntiDDoS = document.querySelector('[name="cyber_bandwidth_with_antiddos"]');
        
        // Initialize values to 0
        document.querySelector('[name="kl_included_elastic_ip_display"]').value = 0;
        document.querySelector('[name="kl_included_elastic_ip"]').value = 0;
        document.querySelector('[name="cyber_included_elastic_ip_display"]').value = 0;
        document.querySelector('[name="cyber_included_elastic_ip"]').value = 0;

        function updateIncludedEIP() {
            // Calculate KL included EIP
            const klBandwidthVal = parseInt(klBandwidth.value) || 0;
            const klAntiDDoSVal = parseInt(klAntiDDoS.value) || 0;
            const klTotal = klBandwidthVal + klAntiDDoSVal;
            //const klTotal = Math.max(klBandwidthVal, klAntiDDoSVal);
            const klEIP = calculateIncludedEIP(klTotal);
            
            document.querySelector('[name="kl_included_elastic_ip"]').value = klEIP;
            document.querySelector('[name="kl_included_elastic_ip_display"]').value = klEIP;
            
            // Calculate Cyber included EIP
            const cyberBandwidthVal = parseInt(cyberBandwidth.value) || 0;
            const cyberAntiDDoSVal = parseInt(cyberAntiDDoS.value) || 0;
            const cyberTotal = cyberBandwidthVal + cyberAntiDDoSVal;
            //const cyberTotal = Math.max(cyberBandwidthVal, cyberAntiDDoSVal);

            const cyberEIP = calculateIncludedEIP(cyberTotal);
            
            document.querySelector('[name="cyber_included_elastic_ip"]').value = cyberEIP;
            document.querySelector('[name="cyber_included_elastic_ip_display"]').value = cyberEIP;
        }

        // Add event listeners
        [klBandwidth, klAntiDDoS, cyberBandwidth, cyberAntiDDoS].forEach(field => {
            field.addEventListener('input', updateIncludedEIP);
        });

        // Initial calculation (will set to 0 if no values exist)
        updateIncludedEIP();
    });
</script>

<script>
    let isSaved = false;

    document.querySelector('form').addEventListener('submit', function (e) {
        isSaved = true;
    });

    document.querySelectorAll('.next-step').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!isSaved) {
                e.preventDefault();
                alert('Please click "Save DR Settings" before proceeding to the next step.');
            }
        });
    });
</script>







@endsection



@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.querySelectorAll('.auto-save').forEach(function (element) {
        element.addEventListener('change', function () {
            const field = this.dataset.field;
            const value = this.value;
            const versionId = this.dataset.versionId;



            //fetch(`/autosave/region/professional-services/${versionId}`,
            fetch(`/autosave/region/${versionId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    [field]: value
                })
            })
            .then(response => {
                console.log('Status:', response.status);
                if (!response.ok) throw new Error('Save failed');
                console.log(`Saved: ${field} = ${value}`);
            })
            .catch(err => {
                console.error(err);
                alert("Auto-save failed!");
            });
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