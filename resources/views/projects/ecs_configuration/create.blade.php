@extends('layouts.app')

@php
    $solution_type = $solution_type ?? $version->solution_type ?? null;
@endphp


@section('content')

@if($errors->any())
    <div class="alert alert-danger">
        <h5>Error!</h5>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<head>
    <style>
        /* Enable column resizing */
        table th, table td {
            position: relative;
            min-width: 100px; /* Adjust the minimum width as needed */
        }

        /* Add a resize handle on the right side of each table header */
        table th {
            cursor: ew-resize; /* Shows resize cursor on hover */
            position: relative;
        }

        .resize-handle {
            position: absolute;
            right: 0;
            top: 0;
            width: 10px;
            height: 100%;
            cursor: ew-resize;
        }
    </style>
</head>





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
             @if(($solution_type->solution_type ?? '') !== 'MP-DRaaS Only')
            <a href="{{ route('versions.ecs_configuration.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.ecs_configuration.create' ? 'active-link' : '' }}">ECS Configuration</a>
            <span class="breadcrumb-separator">»</span>
            @endif
               @if(($solution_type->solution_type ?? '') !== 'MP-DRaaS Only')
             <a href="{{ route('versions.backup.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.backup.create' ? 'active-link' : '' }}">Backup</a>
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
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif


         @if($errors->any())
    <div class="alert alert-danger">
        <h5>Error!</h5>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif


<!---<div class="alert alert-warning small" role="alert">
    <strong>Note:</strong> Table will automatically be replaced once a file is uploaded! (Optional)
</div>--->

     <div class="mb-4">
                <h6 class="fw-bold">Project</h6>
                <div class="mb-3">
                    <input type="text" class="form-control bg-light" value="{{ $project->name }}" readonly>
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="version_id" value="{{ $version->id }}">
<input type="hidden" name="customer_id" value="{{ $project->customer_id }}">


                   
            </div>




  <br>






@php
    $isEdit = isset($ecs_configuration) && $ecs_configuration->exists;
@endphp


        <form method="POST" action="{{ route('versions.ecs_configuration.store', $version->id) }}">
            @csrf
            @if(isset($ecs_configuration))
                @method('PUT')
            @endif

<!-- {{-- Debugging Information --}}  -->
<!--<div class="alert alert-info"> -->
    <!--<pre>{{ print_r($ecs_configuration, true) }}</pre> -->
    <!--<pre>{{ print_r(old(), true) }}</pre> -->
<!--</div> -->






        
            <!-- Table -->
            <div class="table-responsive mb-4" style="overflow-x: auto; white-space: nowrap;">
                <table class="table table-bordered" style="min-width: 2000px;">
                    <thead class="table-dark">
                        <tr>
                           <td colspan="9"><strong>Production VM</strong></td>
                            <td colspan="2"><strong>Storage</strong></td>
                             <td colspan="3"><strong>License</strong></td>
                             <td colspan="3"><strong>Image and Snapshot</strong></td>
                          
                        
                           
                        </tr>
                        <tr>
                            <th colspan="3"></th>
                            <th colspan="6" style="font-weight: normal;">ECS</th>
                            <th colspan="2" style="font-weight: normal;">Storage</th>
                            <th colspan="3" style="font-weight: normal;">License</th>
                            <th colspan="3" style="font-weight: normal;">Image and Snapshot</th>
                        
                        </tr>


                         <thead class="table-light">
                        <tr>

                              <td>No</td>
                                <td style="font-weight: bold;">Region<span style="color: red">*</span></td>
                              <td style="font-weight: bold;">VM Name<span style="color: red">*</span></td>
                    <td style="font-weight: bold;">Pin<span style="color: red">*</span></td>
                    <td style="font-weight: bold;">GPU<span style="color: red">*</span></td>
                    <td style="font-weight: bold;">DDH<span style="color: red">*</span></td>
                    <td style="font-weight: bold;">vCPU<span style="color: red">*</span></td>
                    <td style="font-weight: bold;">vRAM<span style="color: red">*</span></td>
                    <td style="font-weight: bold;">Flavour Mapping<span style="color: red">*</span></td>
                    <td style="font-weight: bold;">System Disk (GB)<span style="color: red">*</span></td>
                     <td>Data Disk (GB)</td>
                    <td>Operating System</td>
                     <td>RDS License</td>
                      <td>Microsoft SQL</td>
                       <td>Snapshot Copies</td>
                        <td>Additional Capacity (GB)</td>
                         <td>Image Copies</td>
                         
                        <tr>











                    </thead>

                    <tbody class="table-light">

                        <tr>
                            <td>1</td>
                            <td>
                                <select name="region" class="form-select">
                                    <option value="Kuala Lumpur" @selected(old('region', $ecs_configuration->region ?? '') == 'Kuala Lumpur')>Kuala Lumpur</option>
                                    <option value="Cyberjaya" @selected(old('region', $ecs_configuration->region ?? '') == 'Cyberjaya')>Cyberjaya</option>
                                </select>
                            </td>
                            <td><input type="text" name="vm_name" class="form-control" value="{{ old('vm_name', $ecs_configuration->vm_name ?? '') }}"></td>
                            <td>
                                <select name="ecs_pin" class="form-select">
                                    <option value="No" @selected(old('ecs_pin', $ecs_configuration->ecs_pin ?? '') == 'No')>No</option>
                                    <option value="Yes" @selected(old('ecs_pin', $ecs_configuration->ecs_pin ?? '') == 'Yes')>Yes</option>
                                </select>
                            </td>
                            <td>
                                <select name="ecs_gpu" class="form-select">
                                    <option value="No" @selected(old('ecs_gpu', $ecs_configuration->ecs_gpu ?? '') == 'No')>No</option>
                                    <option value="Yes" @selected(old('ecs_gpu', $ecs_configuration->ecs_gpu ?? '') == 'Yes')>Yes</option>
                                </select>
                            </td>
                            <td>
                                <select name="ecs_ddh" class="form-select">
                                    <option value="No" @selected(old('ecs_ddh', $ecs_configuration->ecs_ddh ?? '') == 'No')>No</option>
                                    <option value="Yes" @selected(old('ecs_ddh', $ecs_configuration->ecs_ddh ?? '') == 'Yes')>Yes</option>
                                </select>
                            </td>
                            <td><input type="number" name="ecs_vcpu" class="form-control" value="{{ old('ecs_vcpu', $ecs_configuration->ecs_vcpu ?? '') }}"  min="0"></td>
                            <td><input type="number" name="ecs_vram" class="form-control" value="{{ old('ecs_vram', $ecs_configuration->ecs_vram ?? '') }}"  min="0"></td>
                            <td><input  name="ecs_flavour_mapping" class="form-control" value="{{ old('ecs_flavour_mapping', $ecs_configuration->ecs_flavour_mapping ?? '') }}" readonly style="background-color: black;color: white;"></td>

                       <!--- <input type="hidden" name="vcpu_count" value=""> --->
 <!---<input type="hidden" name="vram_count" value=""> --->
 <!----<input type="hidden" name="worker_flavour_mapping" value=""> --->
                          
                            <td><input type="number" name="storage_system_disk" class="form-control" value="{{ old('storage_system_disk', $ecs_configuration->storage_system_disk ?? 0) }}"  min="40"></td>
                            <td><input type="number" name="storage_data_disk" class="form-control" value="{{ old('storage_data_disk', $ecs_configuration->storage_data_disk ?? 0) }}"  min="0"></td>
                           

                            <td>
                  <select name="license_operating_system" class="form-select">
                     <option value="Linux" @selected(old('license_operating_system', $ecs_configuration->license_operating_system ?? '') == 'Linux')>Linux</option>
                    <option value="Microsoft Windows Std" @selected(old('license_operating_system', $ecs_configuration->license_operating_system ?? '') == 'Microsoft Windows Std')>Microsoft Windows Std</option>
                    <option value="Microsoft Windows DC" @selected(old('license_operating_system', $ecs_configuration->license_operating_system ?? '') == 'Microsoft Windows DC')>Microsoft Windows DC</option>
                     <option value="Red Hat Enterprise Linux" @selected(old('license_operating_system', $ecs_configuration->license_operating_system ?? '') == 'Red Hat Enterprise Linux')>Red Hat Enterprise Linux</option>
                
</select>

                </td> 

                    <td><input type="number" name="license_rds_license" class="form-control" value="{{ old('license_rds_license', $ecs_configuration->license_rds_license ?? 0) }}"  min="0"></td>
             
             
                
                

                 <td>
                  <select name="license_microsoft_sql" class="form-select">
                     <option value="None" @selected(old('license_microsoft_sql', $ecs_configuration->license_microsoft_sql ?? '') == 'None')>None</option>
                    <option value="Web" @selected(old('license_microsoft_sql', $ecs_configuration->license_microsoft_sql ?? '') == 'Web')>Web</option>
                    <option value="Standard" @selected(old('license_microsoft_sql', $ecs_configuration->license_microsoft_sql ?? '') == 'Standard')>Standard</option>
                     <option value="Enterprise" @selected(old('license_microsoft_sql', $ecs_configuration->license_microsoft_sql ?? '') == 'Enterprise')>Enterprise</option>
              
                
</select>

                </td> 
                

                  <td>   <input type="number" name="snapshot_copies" class="form-control" value="{{ old('snapshot_copies', $ecs_configuration->snapshot_copies ?? 0) }}" min="0">
                </td>

                 <td>   <input type="number" name="additional_capacity" class="form-control" value="{{ old('additional_capacity', $ecs_configuration->additional_capacity ?? 0) }}" min="0">
                </td>
               

                    <td>   <input type="number" name="image_copies" class="form-control" value="{{ old('image_copies', $ecs_configuration->image_copies ?? 0) }}" min="0">
                </td>

                <input type="hidden" name="csdr_needed" value="No">


                
                

















                        </tr>
                        






                        <!-- <tr>
                            <td>1</td>
                            <td>
                                <select name="region" class="form-select">
                                    <option value="Kuala Lumpur" @selected(old('region', $ecs_configuration->region ?? '') == 'Kuala Lumpur')>Kuala Lumpur</option>
                                    <option value="Cyberjaya" @selected(old('region', $ecs_configuration->region ?? '') == 'Cyberjaya')>Cyberjaya</option>
                                </select>
                            </td>
                            <td><input type="text" name="vm_name" class="form-control" value="{{ old('vm_name', $ecs_configuration->vm_name ?? '') }}"></td>
                            <td>
                                <select name="ecs_pin" class="form-select">
                                    <option value="No" @selected(old('ecs_pin', $ecs_configuration->ecs_pin ?? '') == 'No')>No</option>
                                    <option value="Yes" @selected(old('ecs_pin', $ecs_configuration->ecs_pin ?? '') == 'Yes')>Yes</option>
                                </select>
                            </td>
                            <td>
                                <select name="ecs_gpu" class="form-select">
                                    <option value="No" @selected(old('ecs_gpu', $ecs_configuration->ecs_gpu ?? '') == 'No')>No</option>
                                    <option value="Yes" @selected(old('ecs_gpu', $ecs_configuration->ecs_gpu ?? '') == 'Yes')>Yes</option>
                                </select>
                            </td>
                            <td>
                                <select name="ecs_ddh" class="form-select">
                                    <option value="No" @selected(old('ecs_ddh', $ecs_configuration->ecs_ddh ?? '') == 'No')>No</option>
                                    <option value="Yes" @selected(old('ecs_ddh', $ecs_configuration->ecs_ddh ?? '') == 'Yes')>Yes</option>
                                </select>
                            </td>
                            <td><input type="number" name="ecs_vcpu" class="form-control" value="{{ old('ecs_vcpu', $ecs_configuration->ecs_vcpu ?? '') }}"  min="0"></td>
                            <td><input type="number" name="ecs_vram" class="form-control" value="{{ old('ecs_vram', $ecs_configuration->ecs_vram ?? '') }}"  min="0"></td>
                            <td><input  name="ecs_flavour_mapping" class="form-control" value="{{ old('ecs_flavour_mapping', $ecs_configuration->ecs_flavour_mapping ?? '') }}" readonly style="background-color: black;color: white;"></td>

                    // <input type="hidden" name="vcpu_count" value="">
 //<input type="hidden" name="vram_count" value="">
 //<input type="hidden" name="worker_flavour_mapping" value="">
                          
                            <td><input type="number" name="storage_system_disk" class="form-control" value="{{ old('storage_system_disk', $ecs_configuration->storage_system_disk ?? 0) }}"  min="40"></td>
                            <td><input type="number" name="storage_data_disk" class="form-control" value="{{ old('storage_data_disk', $ecs_configuration->storage_data_disk ?? 0) }}"  min="0"></td>
                           

                            <td>
                  <select name="license_operating_system" class="form-select">
                     <option value="Linux" @selected(old('license_operating_system', $ecs_configuration->license_operating_system ?? '') == 'Linux')>Linux</option>
                    <option value="Microsoft Windows Std" @selected(old('license_operating_system', $ecs_configuration->license_operating_system ?? '') == 'Microsoft Windows Std')>Microsoft Windows Std</option>
                    <option value="Microsoft Windows DC" @selected(old('license_operating_system', $ecs_configuration->license_operating_system ?? '') == 'Microsoft Windows DC')>Microsoft Windows DC</option>
                     <option value="Red Hat Enterprise Linux" @selected(old('license_operating_system', $ecs_configuration->license_operating_system ?? '') == 'Red Hat Enterprise Linux')>Red Hat Enterprise Linux</option>
                
</select>

                </td> 

                    <td><input type="number" name="license_rds_license" class="form-control" value="{{ old('license_rds_license', $ecs_configuration->license_rds_license ?? 0) }}"  min="0"></td>
             
             
                
                

                 <td>
                  <select name="license_microsoft_sql" class="form-select">
                     <option value="None" @selected(old('license_microsoft_sql', $ecs_configuration->license_microsoft_sql ?? '') == 'None')>None</option>
                    <option value="Web" @selected(old('license_microsoft_sql', $ecs_configuration->license_microsoft_sql ?? '') == 'Web')>Web</option>
                    <option value="Standard" @selected(old('license_microsoft_sql', $ecs_configuration->license_microsoft_sql ?? '') == 'Standard')>Standard</option>
                     <option value="Enterprise" @selected(old('license_microsoft_sql', $ecs_configuration->license_microsoft_sql ?? '') == 'Enterprise')>Enterprise</option>
              
                
</select>

                </td> 
                

                  <td>   <input type="number" name="snapshot_copies" class="form-control" value="{{ old('snapshot_copies', $ecs_configuration->snapshot_copies ?? 0) }}" min="0">
                </td>

                 <td>   <input type="number" name="additional_capacity" class="form-control" value="{{ old('additional_capacity', $ecs_configuration->additional_capacity ?? 0) }}" min="0">
                </td>
               

                    <td>   <input type="number" name="image_copies" class="form-control" value="{{ old('image_copies', $ecs_configuration->image_copies ?? 0) }}" min="0">
                </td>

                <input type="hidden" name="csdr_needed" value="No">


                
                

















                        </tr>--->
                        
                    </tbody>


                    
                </table>
                <!-- <button type="button" class="btn btn-pink" onclick="addRow()">Add Row</button> -->
            </div>



               <div class="d-flex justify-content-between gap-3"> 
    
   
    <a href="{{ route('versions.region.network.create', $version->id) }}" class="btn btn-secondary" role="button">
        <i class="bi bi-arrow-left"></i> Previous<br>Step
    </a>

    <div class="d-flex flex-column align-items-centre gap-2">



            <div class="d-flex justify-content-end gap-3">


                <button type="submit" class="btn btn-pink">Save ECS Configuration</button>

                

         <a href="{{ route('versions.backup.create', $version->id) }}"  
   class="btn btn-secondary me-2" 
   role="button">
   Next: Backup <i class="bi bi-arrow-right"></i>
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
let rowCount = 1;

function addRow() {
    rowCount++;

    // Get the table body and the first data row
    let table = document.querySelector(".table tbody");
    let firstDataRow = table.querySelector("tr");
    let newRow = firstDataRow.cloneNode(true);

    // Update the row number (No column)
    newRow.cells[0].innerText = rowCount;

    // Update input/select name + reset value
    let inputs = newRow.querySelectorAll("input, select");
    inputs.forEach(input => {
        let name = input.getAttribute('name');
        if (name) {
            // Replace rows[0] or rows[n] with current rowCount-1
            name = name.replace(/\[0\]/, `[${rowCount - 1}]`);
            name = name.replace(/\[\d+\]/, `[${rowCount - 1}]`);
            input.setAttribute('name', name);
        }

        // Reset value
        if (input.tagName === "INPUT") {
            input.value = "";
        } else if (input.tagName === "SELECT") {
            input.selectedIndex = 0;
        }
    });

    // Get inputs by their new name
    let vcpuInput = newRow.querySelector(`input[name="rows[${rowCount - 1}][ecs_vcpu]"]`);
    let vramInput = newRow.querySelector(`input[name="rows[${rowCount - 1}][ecs_vram]"]`);
    let flavourMappingInput = newRow.querySelector(`input[name="rows[${rowCount - 1}][ecs_flavour_mapping]"]`);

    vcpuInput.addEventListener('input', () => {
        const flavour = calculateFlavourMapping(parseInt(vcpuInput.value), parseInt(vramInput.value));
        flavourMappingInput.value = flavour || 'No suitable flavour';
    });

    vramInput.addEventListener('input', () => {
        const flavour = calculateFlavourMapping(parseInt(vcpuInput.value), parseInt(vramInput.value));
        flavourMappingInput.value = flavour || 'No suitable flavour';
    });

    table.appendChild(newRow);
}
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
        const vcpuInput = document.querySelector('input[name="ecs_vcpu"]');
        const vramInput = document.querySelector('input[name="ecs_vram"]');
        const flavourMappingInput = document.querySelector('input[name="ecs_flavour_mapping"]');

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

    


    // Perhitungan otomatis untuk backup dan storage
document.addEventListener('DOMContentLoaded', function() {
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    // Fungsi untuk menghitung incremental data change
    function calculateIncrementalDataChange() {
        const initialSize = parseInt(document.querySelector('input[name="csbs_initial_data_size"]').value) || 0;
        const incrementalChange = parseInt(document.querySelector('input[name="csbs_incremental_change"]').value) || 0;
        
        if (initialSize > 0 && incrementalChange > 0) {
            const result = initialSize * (incrementalChange / 100);
            document.querySelector('input[name="csbs_estimated_incremental_data_change"]').value = Math.round(result);
        }
    }

    // Fungsi untuk menghitung total retention full copies
    function calculateTotalRetentionFullCopies() {
        const daily = parseInt(document.querySelector('input[name="full_backup_daily"]').value) || 0;
        const weekly = parseInt(document.querySelector('input[name="full_backup_weekly"]').value) || 0;
        const monthly = parseInt(document.querySelector('input[name="full_backup_monthly"]').value) || 0;
        const yearly = parseInt(document.querySelector('input[name="full_backup_yearly"]').value) || 0;
        
        document.querySelector('input[name="full_backup_total_retention_full_copies"]').value = daily + weekly + monthly + yearly;
    }

    // Fungsi untuk menghitung total retention incremental copies
    function calculateTotalRetentionIncrementalCopies() {
        const daily = parseInt(document.querySelector('input[name="incremental_backup_daily"]').value) || 0;
        const weekly = parseInt(document.querySelector('input[name="incremental_backup_weekly"]').value) || 0;
        const monthly = parseInt(document.querySelector('input[name="incremental_backup_monthly"]').value) || 0;
        const yearly = parseInt(document.querySelector('input[name="incremental_backup_yearly"]').value) || 0;
        
        document.querySelector('input[name="incremental_backup_total_retention_incremental_copies"]').value = daily + weekly + monthly + yearly;
    }

    // Tambahkan event listener untuk semua input terkait
    document.querySelector('input[name="csbs_initial_data_size"]').addEventListener('input', calculateIncrementalDataChange);
    document.querySelector('input[name="csbs_incremental_change"]').addEventListener('input', calculateIncrementalDataChange);
    
    document.querySelector('input[name="full_backup_daily"]').addEventListener('input', calculateTotalRetentionFullCopies);
    document.querySelector('input[name="full_backup_weekly"]').addEventListener('input', calculateTotalRetentionFullCopies);
    document.querySelector('input[name="full_backup_monthly"]').addEventListener('input', calculateTotalRetentionFullCopies);
    document.querySelector('input[name="full_backup_yearly"]').addEventListener('input', calculateTotalRetentionFullCopies);
    
    document.querySelector('input[name="incremental_backup_daily"]').addEventListener('input', calculateTotalRetentionIncrementalCopies);
    document.querySelector('input[name="incremental_backup_weekly"]').addEventListener('input', calculateTotalRetentionIncrementalCopies);
    document.querySelector('input[name="incremental_backup_monthly"]').addEventListener('input', calculateTotalRetentionIncrementalCopies);
    document.querySelector('input[name="incremental_backup_yearly"]').addEventListener('input', calculateTotalRetentionIncrementalCopies);

    // Add these inside DOMContentLoaded
document.querySelector('select[name="csbs_standard_policy"]')?.addEventListener('change', calculateAllAutoFields);
document.querySelector('select[name="required"]')?.addEventListener('change', calculateAllAutoFields);
document.querySelector('select[name="csdr_needed"]')?.addEventListener('change', calculateAllAutoFields);
document.querySelector('input[name="rto"]')?.addEventListener('input', calculateAllAutoFields);


function calculateAllAutoFields() {
    // Get all required elements
    const csbsPolicy = document.querySelector('select[name="csbs_standard_policy"]');
    const requiredSelect = document.querySelector('select[name="required"]');
    const csdrNeeded = document.querySelector('select[name="csdr_needed"]');
    
    // Step 1: csbs_local_retention_copies
    let localRetention = 0;
    if (csbsPolicy.value === 'No Backup') {
        localRetention = 0;
    } else if (csbsPolicy.value === 'Custom') {
        const fullCopies = parseInt(document.querySelector('input[name="full_backup_total_retention_full_copies"]').value) || 0;
        const incrementalCopies = parseInt(document.querySelector('input[name="incremental_backup_total_retention_incremental_copies"]').value) || 0;
        localRetention = fullCopies + incrementalCopies + 1;
    }
    document.querySelector('input[name="csbs_local_retention_copies"]').value = localRetention;

    // Step 3: csbs_estimated_incremental_data_change
    const initialSize = parseInt(document.querySelector('input[name="csbs_initial_data_size"]').value) || 0;
    const changePercent = parseInt(document.querySelector('input[name="csbs_incremental_change"]').value) || 0;
    const estimatedChange = Math.round(initialSize * (changePercent / 100));
    document.querySelector('input[name="csbs_estimated_incremental_data_change"]').value = estimatedChange;

    // Step 2: csbs_total_storage
    const fullCopies = parseInt(document.querySelector('input[name="full_backup_total_retention_full_copies"]').value) || 0;
    const incrementalCopies = parseInt(document.querySelector('input[name="incremental_backup_total_retention_incremental_copies"]').value) || 0;
    const totalStorage = Math.ceil(initialSize + (initialSize * fullCopies) + (estimatedChange * incrementalCopies));
    document.querySelector('input[name="csbs_total_storage"]').value = totalStorage;

    // Step 6: total_replication_copy_retained_second_site
    const replicationCopies = (requiredSelect.value === 'Yes') ? localRetention : 0;
    document.querySelector('input[name="total_replication_copy_retained_second_site"]').value = replicationCopies;

    // Step 7: RPO
    const rtoInput = document.querySelector('input[name="rto"]');
    const rpoInput = document.querySelector('input[name="rpo"]');
    if (rtoInput.value && rtoInput.value !== 'N/A') {
        rpoInput.value = '24 hours';
    } else {
        rpoInput.value = 'N/A';
    }

    // Step 8: csdr_storage
    const systemDisk = parseInt(document.querySelector('input[name="storage_system_disk"]').value) || 0;
    const dataDisk = parseInt(document.querySelector('input[name="storage_data_disk"]').value) || 0;
    const csdrStorage = (csdrNeeded.value === 'Yes') ? systemDisk + dataDisk : 0;
    document.querySelector('input[name="csdr_storage"]').value = csdrStorage;
}



        // Event listeners untuk semua input yang berkaitan
        const autoCalcFields = [
            'full_backup_total_retention_full_copies',
            'incremental_backup_total_retention_incremental_copies',
            'csbs_initial_data_size',
            'csbs_estimated_incremental_data_change',
            'storage_system_disk',
            'storage_data_disk',
            'required',
            'csdr_needed'
        ];

        // Tambahkan event listener untuk setiap field yang memicu perhitungan
        autoCalcFields.forEach(field => {
            const input = document.querySelector(`[name="${field}"]`);
            if (input) {
                input.addEventListener('input', calculateAllAutoFields);
            }
        });

        // Juga tambahkan event listener untuk select 'required' dan 'csdr_needed'
        document.querySelector('select[name="required"]')?.addEventListener('change', calculateAllAutoFields);
        document.querySelector('select[name="csdr_needed"]')?.addEventListener('change', calculateAllAutoFields);

        // Panggil pertama kali
        calculateAllAutoFields();
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