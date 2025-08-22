@extends('layouts.app')

@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

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

    <div class="card-body">
         @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    
       <form method="POST" action="{{ route('versions.region.professional.store', $version->id) }}">
            @csrf
            @if(isset($region) && $region)
                @method('PUT')
            @endif


            <div class="mb-4">
                <h6 class="fw-bold">Project</h6>
                <div class="mb-3">
                    <input type="text" class="form-control bg-light" value="{{ $project->name }}" readonly>
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="version_id" value="{{ $version->id }}">
<input type="hidden" name="customer_id" value="{{ $project->customer_id }}">


                   
            </div>
            
            <!-- Professional Services Table -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th colspan="4">Professional Services</th>  
                        </tr>
                    </thead>                    <tbody>
                        <tr>
    <td>Region</td>
    <td colspan="3"> 
          
    <div class="input-group">
    <input name="region" 
           class="form-control bg-light auto-save"  data-field="region" 
       data-version-id="{{ $version->id }}" 
           value="{{ $solution_type->production_region ?? '' }}" 
           readonly style="border-radius: 0;">
    <input type="hidden" name="region" value="{{ $solution_type->production_region ?? '' }}">
</div>


    </td>

     <!---<div class="input-group">
            <select name="region" class="form-select">
                <option value="Kuala Lumpur" @selected(old('region', $region->region ?? '') == 'Kuala Lumpur')>Kuala Lumpur</option>
                <option value="Cyberjaya" @selected(old('region', $region->region ?? '') == 'Cyberjaya')>Cyberjaya</option>
            </select>
        </div>--->  
</tr>


                <tr>
                            <td>Deployment Method</td>
                    <td>
                     
        
                        <div class="input-group" style="width:210px;">
                            <select name="deployment_method" class="form-select auto-save" data-field="deployment_method"
        data-version-id="{{ $version->id }}">
                                <option value="self-provisioning" @selected(old('deployment_method', $region->deployment_method ?? '') == 'self-provisioning')>Self Provisioning</option>
                                <option value="professional-services" @selected(old('deployment_method', $region->deployment_method ?? '') == 'professional-services')>Professional Services</option>
                            </select>

                        </div>
                       
                    </td>

                    <td>Mandays</td>
                    <td>
                        <input type="number" name="mandays" class="form-control auto-save"  data-field="mandays"
       data-version-id="{{ $version->id }}"value="{{ old('mandays', $region->mandays ?? '') }}" min="0">
                        <small class="text-muted">Please get the estimated mandays required from Professional Services Team</small>
                    </td>
                </tr>


                   <tr>
    <td>Scope of Work</td>
    <td colspan="3"> 
    <input type="text" name="scope_of_work" class="form-control auto-save"  data-field="scope_of_work"
       data-version-id="{{ $version->id }}"
                               value="{{ old('scope_of_work', $region->scope_of_work ?? '') }}" min="5000">
                      

    </td>

</tr>

                     
                      

            


                    </tbody>

                         
                        <thead class="table-dark">
                            <tr>
                                <th colspan="2">Migration OTC</th>
                                <th colspan="2">Quantity</th>
                                
                            </tr>
                        </thead>
                        <thead class="table-light">
                            <tr>
                                <th colspan="2"></th>
                                <th>KL</th>
                                <th>Cyber</th>
                            </tr>
                        </thead>

                        <tr>
                            <td>License Count</td>
                            <td>VM</td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_license_count" class="form-control auto-save"  data-field="kl_license_count" 
       data-version-id="{{ $version->id }}" value="{{ old('kl_license_count', $region->kl_license_count ?? '') }}" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="cyber_license_count" class="form-control auto-save"  data-field="cyber_license_count" 
       data-version-id="{{ $version->id }}" value="{{ old('cyber_license_count', $region->cyber_license_count ?? '') }}" min="0">
                                </div>
                            </td>
                        </tr>

                          <tr>
                            <td>Duration</td>
                            <td>Months</td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="kl_duration" class="form-control auto-save"  data-field="kl_duration" 
       data-version-id="{{ $version->id }}" value="{{ old('kl_duration', $region->kl_duration ?? '') }}" min="0">
                                </div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="number" name="cyber_duration" class="form-control auto-save"  data-field="cyber_duration" 
       data-version-id="{{ $version->id }}" value="{{ old('cyber_duration', $region->cyber_duration ?? '') }}" min="0">
                                </div>  
                            </td>
                        </tr>
                </table>
            </div>

    <div class="d-flex justify-content-between gap-3"> 
    <div>
   
    <a href="{{ route('versions.solution_type.create', $version->id) }}" class="btn btn-secondary" role="button">
        <i class="bi bi-arrow-left"></i> Previous Step
    </a>
    <a href="{{ route('projects.service_description', ['project' => $project->id]) }}" class="btn btn-info">
    <i class="bi bi-info-circle"></i> Service Description
</a>
    </div>
    

                         

            <div class="d-flex justify-content-end gap-3"> <!-- Added gap-3 for spacing -->
                <!---<button type="submit" class="btn btn-pink">Save Region</button>--->

                
                <a href="{{ route('versions.region.network.create', $version->id) }}"   
                   class="btn btn-secondary me-2" 
                   role="button">
                   Next: Network <i class="bi bi-arrow-right"></i>
                </a> 
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



@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Handle deployment method and mandays relationship
    const deploymentMethodSelect = document.querySelector('select[name="deployment_method"]');
    const mandaysInput = document.querySelector('input[name="mandays"]');

    function handleDeploymentMethodChange() {
        const selectedMethod = deploymentMethodSelect.value;
        
        if (selectedMethod === 'self-provisioning') {
            mandaysInput.value = 0;
            mandaysInput.disabled = true;
            mandaysInput.setAttribute('readonly', 'readonly');
            
            // Trigger auto-save for mandays when it gets set to 0
            const versionId = mandaysInput.dataset.versionId;
            fetch(`/autosave/region/${versionId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    mandays: 0
                })
            })
            .then(response => {
                console.log('Status:', response.status);
                if (!response.ok) throw new Error('Save failed');
                console.log('Saved: mandays = 0 (auto-set for self-provisioning)');
            })
            .catch(err => {
                console.error(err);
                alert("Auto-save failed!");
            });
        } else {
            mandaysInput.disabled = false;
            mandaysInput.removeAttribute('readonly');
            
            // If switching from self-provisioning and mandays is 0, clear it
            if (mandaysInput.value === '0') {
                mandaysInput.value = '';
            }
        }
    }

    // Initial setup on page load
    handleDeploymentMethodChange();

    // Add event listener for deployment method changes
    deploymentMethodSelect.addEventListener('change', handleDeploymentMethodChange);

    // Existing auto-save functionality
    document.querySelectorAll('.auto-save').forEach(function (element) {
        element.addEventListener('change', function () {
            const field = this.dataset.field;
            const value = this.value;
            const versionId = this.dataset.versionId;

            // Skip if disabled (like mandays when self-provisioning is selected)
            if (this.disabled) {
                return;
            }

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
