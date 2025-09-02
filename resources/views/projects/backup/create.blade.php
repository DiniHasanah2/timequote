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

        /* Limit input height in table cells */
.table td .form-control,
.table td .form-select {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    height: 30px; /* or adjust as needed */
    line-height: 1;
}
.table td .form-control,
.table td .form-select {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    height: 30px;
    line-height: 1;
}

.table td {
    vertical-align: middle;
    padding-top: 0.25rem;
    padding-bottom: 0.25rem;
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
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif


         <!---@if($errors->any())
    <div class="alert alert-danger">
        <h5>Error!</h5>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif--->


<div class="alert alert-warning small" role="alert">
    <strong>Note:</strong> Data will automatically be replaced once a file is uploaded! (Optional)
</div>

     <div class="mb-4">
                <h6 class="fw-bold">Project</h6>
                <div class="mb-3">
                    <input type="text" class="form-control bg-light" value="{{ $project->name }}" readonly>
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="version_id" value="{{ $version->id }}">
<input type="hidden" name="customer_id" value="{{ $project->customer_id }}">


                   
            </div>




                   
    <table class="table table-bordered w-auto">

         <tr>
            <td class="bg-light fw-bold text-center" style="font-size:14px;">Production</td>
           <td>
    <div class="input-group" style="width:135px;">
        <input name="region" 
               class="form-control bg-white border-0 auto-save"  
               data-field="region" 
               data-version-id="{{ $version->id }}" 
               value="{{ $solution_type->production_region ?? '' }}" 
               readonly style="font-size:14px;">
        <input type="hidden" name="region" value="{{ $solution_type->production_region ?? '' }}">
    </div>
</td>

        </tr>
         <tr>
            <td class="bg-light fw-bold text-center" style="font-size:14px;">DR</td>
             <td>
                <div class="input-group" style="width:135px;">
        <input name="region" 
               class="form-control bg-white border-0 auto-save"  
               data-field="region" 
               data-version-id="{{ $version->id }}" 
               value="{{ $solution_type->dr_region ?? '' }}" 
               readonly style="font-size:14px;">
        <input type="hidden" name="region" value="{{ $solution_type->dr_region ?? '' }}">
    </div>
             </td>
        </tr>






        
    </table>

















      <form action="{{ route('ecs_configurations.import') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="version_id" value="{{ $version->id }}">
    <input type="hidden" name="source" value="backup"> {{-- ini penting! --}}
    
   

    
        <input type="file" name="import_file" accept=".xlsx" class="form-control" required>
     

        <br>
      <button type="submit" class="btn btn-pink"><i class="bi bi-upload"></i> Attach File</button>
   
        <!---"{{ asset('assets/ECS_Configuration_Template.csv') }}"--->
        
         <a href="{{ asset('assets/ECSandBackup_Template_AutoCalculation_v1.250804 (UPDATE FORMULA).xlsx') }}"  class="btn btn-pink" download>
    <i class="bi bi-download"></i> Download Template </a>
  

</form>





<br>
<!---@if(session('importPreview'))


    <div class="alert alert-info">Preview from imported Excel:</div>

<pre>{{ print_r(session('importPreview'), true) }}</pre>

    <div class="table-responsive">
        <table class="table table-bordered table-sm small">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    @foreach(array_keys(session('importPreview')[0]) as $col)
                        <th>{{ ucwords(str_replace('_', ' ', $col)) }}</th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @foreach(session('importPreview') as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        @foreach($row as $value)
                            <td>{{ $value }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif






@if(session('importPreview'))
    <form action="{{ route('ecs_configurations.store_preview', $version->id) }}" method="POST">
        @csrf
        <input type="hidden" name="source" value="backup">
        <button type="submit" class="btn btn-success mt-2">
            <i class="bi bi-save"></i> Save Imported Backup Data
        </button>
    </form>
@endif--->

@php $preview = session('importPreview'); @endphp
@if(is_array($preview) && count($preview))
  @php $firstRow = $preview[0] ?? (is_array($preview) ? reset($preview) : []); @endphp

  <div class="alert alert-info">Preview from imported Excel:</div>

  <div class="table-responsive">
    <table class="table table-bordered table-sm small">
      <thead class="table-light">
        <tr>
          <th>No</th>
          @foreach(array_keys($firstRow ?? []) as $col)
            <th>{{ ucwords(str_replace('_',' ',$col)) }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @foreach($preview as $i => $row)
          <tr>
            <td>{{ $i+1 }}</td>
            @foreach($row as $value)
              <td>{{ $value }}</td>
            @endforeach
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <form action="{{ route('ecs_configurations.store_preview', $version->id) }}" method="POST" class="mt-2">
    @csrf
    <input type="hidden" name="source" value="backup">
    <button type="submit" class="btn btn-success">
      <i class="bi bi-save"></i> Save Imported Backup Data
    </button>
  </form>
@endif




  <br>









       <form method="POST" action="{{ route('versions.ecs_configuration.store', $version->id) }}">
            @csrf
            @if(isset($ecs_configuration))
                @method('PUT')
            @endif



<div class="table-responsive" style="overflow-x:auto">
                <!---<table class="table table-bordered table-sm-small" style="min-width: 2000px;" >--->

                <table id="ecsBackupTable" class="table table-bordered table-sm-small" style="min-width: 2000px;">

                    <!---<thead class="table-dark">
                        <tr>

                        <td colspan="3"><strong>Production VM</strong></td>
                             <td colspan="6"><strong>ECS</strong></td>
                             <td colspan="2"><strong>Storage</strong></td>
                            <td colspan="3"><strong>License</strong></td>
                           <td colspan="3"><strong>Image and Snapshots</strong></td>
                             
                            <td colspan="27"><strong>Backup</strong></td>
                          
                            <td><strong>DR Requirement</strong></td>
                             <td><strong>Cold DR</strong></td>
                            <td colspan="2"><strong>Warm DR<strong></td>
                            <td><strong>DR Flavour Mapping<strong></td>
                            <td><strong>Action<strong></td>
                            
                        
                           
                        </tr>
                        <tr>

                        <td colspan="3" style="font-weight: normal;"></td>
                             <td colspan="6" style="font-weight: normal;">ECS</td>
                             <td colspan="2" style="font-weight: normal;">Storage</td>
                            <td colspan="3" style="font-weight: normal;">License</td>
                           <td colspan="3" style="font-weight: normal;">Image and Snapshots</td>
                         
                            
                            <th colspan="6" style="font-weight: normal;">Cloud Server Backup Service (CSBS)</th>
                            <th colspan="7" style="font-weight: normal;">Full Backup</th>
                            <th colspan="7" style="font-weight: normal;">Incremental Backup</th>
                            <th colspan="7" style="font-weight: normal;">CSBS Replication</th>
                            
                            <th colspan="2" style="font-weight: normal;">DR Requirement</th>
                            <th colspan="2" style="font-weight: normal;">Replication using CSDR</th>
                            <th colspan="2" style="font-weight: normal;"></th>
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
    
                        <td>Standard Policy</td>
                            <td>Local Retention Copies</td>
                              <td>Total Storage</td>
                                <td>Initial Data Size (GB)</td>
                                  <td>Incremental Change (%)</td>
                                  <td>Estimated Incremental Data Change (GB)</td>
                                  <td>Daily</td>
                                  <td>Weekly</td>
                                  <td>Monthly</td>
                                  <td>Yearly</td>
                                  <td>Total Retention Full Copies</td>
                                   <td>Estimated Storage for Full Backup<br><strong>(Suggestion)</strong></td>
                                   <td>Estimated Storage for Full Backup</td>
                                   <td>Daily2</td>
                                  <td>Weekly2</td>
                                  <td>Monthly2</td>
                                  <td>Yearly2</td>
                                  <td>Total Retention Increment Copies</td>
                                   <td>Estimated Storage for Incremental Backup<br><strong>(Suggestion)</strong></td>
                                   <td>Estimated Storage for Incremental Backup</td>
                                <td>CSBS Required?</td>
                                 <td>Total Replication Copy Retained Second Site</td>
                                  <td>Additional Storage (GB)</td>
                                   <td>RTO</td>
                                    <td>RPO</td>
                                     <td>Estimated Storage for CSBS Replication<br><strong>(Suggestion)</strong></td>
                                     <td>Estimated Storage for CSBS Replication</td>
                                    
                                     <td>DR Activation Required?</td>
                                      <td>Seed VM Required?</td>
                                       <td>CSDR Needed?</td>
                                        <td>CSDR Storage</td>
                                         <td>DR Flavour Mapping</td>
                                         <td>Action</td>
                        <tr>











                    </thead>--->



                    <thead>
  <tr class="table-dark">
    <th colspan="3"><strong>Production VM</strong></th>
    <th colspan="6"><strong>ECS</strong></th>
    <th colspan="2"><strong>Storage</strong></th>
    <th colspan="3"><strong>License</strong></th>
    <th colspan="3"><strong>Image and Snapshots</strong></th>
    <th colspan="27"><strong>Backup</strong></th>
    <th><strong>DR Requirement</strong></th>
    <th><strong>Cold DR</strong></th>
    <th colspan="2"><strong>Warm DR</strong></th>
    <th><strong>DR Flavour Mapping</strong></th>
    <th><strong>Action</strong></th>
  </tr>

  <tr class="table-dark">
    <th colspan="3"></th>
    <th colspan="6">ECS</th>
    <th colspan="2">Storage</th>
    <th colspan="3">License</th>
    <th colspan="3">Image and Snapshots</th>
    <th colspan="6">Cloud Server Backup Service (CSBS)</th>
    <th colspan="7">Full Backup</th>
    <th colspan="7">Incremental Backup</th>
    <th colspan="7">CSBS Replication</th>
    <th colspan="2">DR Requirement</th>
    <th colspan="2">Replication using CSDR</th>
    <th colspan="2"></th>
  </tr>

  <tr class="table-light">
    <th>No</th>
    <th>Region<span style="color:red">*</span></th>
    <th>VM Name<span style="color:red">*</span></th>
    <th>Pin<span style="color:red">*</span></th>
    <th>GPU<span style="color:red">*</span></th>
    <th>DDH<span style="color:red">*</span></th>
    <th>vCPU<span style="color:red">*</span></th>
    <th>vRAM<span style="color:red">*</span></th>
    <th>Flavour Mapping<span style="color:red">*</span></th>
    <th>System Disk (GB)<span style="color:red">*</span></th>
    <th>Data Disk (GB)</th>
    <th>Operating System</th>
    <th>RDS License</th>
    <th>Microsoft SQL</th>
    <th>Snapshot Copies</th>
    <th>Additional Capacity (GB)</th>
    <th>Image Copies</th>

    <th>Standard Policy</th>
    <th>Local Retention Copies</th>
    <th>Total Storage</th>
    <th>Initial Data Size (GB)</th>
    <th>Incremental Change (%)</th>
    <th>Estimated Incremental Data Change (GB)</th>

    <th>Daily</th>
    <th>Weekly</th>
    <th>Monthly</th>
    <th>Yearly</th>
    <th>Total Retention Full Copies</th>
    <th>Estimated Storage for Full Backup<br><strong>(Suggestion)</strong></th>
    <th>Estimated Storage for Full Backup</th>

    <th>Daily2</th>
    <th>Weekly2</th>
    <th>Monthly2</th>
    <th>Yearly2</th>
    <th>Total Retention Increment Copies</th>
    <th>Estimated Storage for Incremental Backup<br><strong>(Suggestion)</strong></th>
    <th>Estimated Storage for Incremental Backup</th>

    <th>CSBS Required?</th>
    <th>Total Replication Copy Retained Second Site</th>
    <th>Additional Storage (GB)</th>
    <th>RTO</th>
    <th>RPO</th>
    <th>Estimated Storage for CSBS Replication<br><strong>(Suggestion)</strong></th>
    <th>Estimated Storage for CSBS Replication</th>

    <th>DR Activation Required?</th>
    <th>Seed VM Required?</th>
    <th>CSDR Needed?</th>
    <th>CSDR Storage</th>
    <th>DR Flavour Mapping</th>
    <th>Action</th>
  </tr>
</thead>


                    <tbody class="table-light">



@php
    $rows = (isset($ecs_configurations) && count($ecs_configurations) > 0)
        ? $ecs_configurations
        : [new \App\Models\ECSConfiguration]; // fallback kalau kosong
@endphp

@foreach($rows as $i => $row)
<tr>
    <td style="font-weight: normal;">{{ $i + 1 }}</td>


     <td style="display: none;">
        <input type="hidden" name="rows[{{ $i }}][id]" value="{{ $row->id ?? '' }}">
    </td>


                                 <td>
                                <select name="rows[{{ $i }}][region]" class="form-select" style="min-width: 120px; height: 1.5; line-height: 1.5;">
                                    <option value="Kuala Lumpur" @selected(old("rows.$i.region", $row->region ?? '') == 'Kuala Lumpur')>Kuala Lumpur</option>
                                    <option value="Cyberjaya" @selected(old("rows.$i.region", $row->region ?? '') == 'Cyberjaya')>Cyberjaya</option>
                                </select>
                            </td>
                            <td><input type="text" name="rows[{{ $i }}][vm_name]" class="form-control" value="{{ old("rows.$i.vm_name", $row->vm_name ?? '') }}"></td>
                            <td>
                                <select name="rows[{{ $i }}][ecs_pin]" class="form-select">
                                    <option value="No" @selected(old("rows.$i.ecs_pin", $row->ecs_pin ?? '') == 'No')>No</option>
                                    <option value="Yes" @selected(old("rows.$i.ecs_pin", $row->ecs_pin ?? '') == 'Yes')>Yes</option>
                                </select>
                            </td>
                            <td>
                                <select name="rows[{{ $i }}][ecs_gpu]" class="form-select">
                                    <option value="No" @selected(old("rows.$i.ecs_gpu", $row->ecs_gpu ?? '') == 'No')>No</option>
                                    <option value="Yes" @selected(old("rows.$i.ecs_gpu", $row->ecs_gpu ?? '') == 'Yes')>Yes</option>
                                </select>
                            </td>
                            <td>
                                <select name="rows[{{ $i }}][ecs_ddh]" class="form-select">
                                    <option value="No" @selected(old("rows.$i.ecs_ddh", $row->ecs_ddh ?? '') == 'No')>No</option>
                                    <option value="Yes" @selected(old("rows.$i.ecs_ddh", $row->ecs_ddh ?? '') == 'Yes')>Yes</option>
                                </select>
                            </td>
                            <td><input type="number" name="rows[{{ $i }}][ecs_vcpu]" class="form-control" value="{{ old("rows.$i.ecs_vcpu", $row->ecs_vcpu ?? '') }}"  min="0"></td>
                            <td><input type="number" name="rows[{{ $i }}][ecs_vram]" class="form-control" value="{{ old("rows.$i.ecs_vram", $row->ecs_vram ?? '') }}"  min="0"></td>
                            <td><input  name="rows[{{ $i }}][ecs_flavour_mapping]" class="form-control" value="{{ old("rows.$i.ecs_flavour_mapping", $row->ecs_flavour_mapping ?? '') }}" readonly style="background-color: black;color: white; width: 150px;"></td>

                       <!--- <input type="hidden" name="vcpu_count" value=""> --->
 <!---<input type="hidden" name="vram_count" value=""> --->
 <!----<input type="hidden" name="worker_flavour_mapping" value=""> --->
                          
                            <td><input type="number" name="rows[{{ $i }}][storage_system_disk]" class="form-control" value="{{ old("rows.$i.storage_system_disk", $row->storage_system_disk ?? 0) }}"  min="40"></td>
                            <td><input type="number" name="rows[{{ $i }}][storage_data_disk]" class="form-control" value="{{ old("rows.$i.storage_data_disk", $row->storage_data_disk ?? 0) }}"  min="0"></td>
                           

                            <td>
                  <select name="rows[{{ $i }}][license_operating_system]" class="form-select" style="width: 180px;">
                     <option value="Linux" @selected(old("rows.$i.license_operating_system", $row->license_operating_system ?? '') == 'Linux')>Linux</option>
                    <option value="Microsoft Windows Std" @selected(old("rows.$i.license_operating_system", $row->license_operating_system ?? '') == 'Microsoft Windows Std')>Microsoft Windows Std</option>
                    <option value="Microsoft Windows DC" @selected(old("rows.$i.license_operating_system", $row->license_operating_system ?? '') == 'Microsoft Windows DC')>Microsoft Windows DC</option>
                     <option value="Red Hat Enterprise Linux" @selected(old("rows.$i.license_operating_system", $row->license_operating_system ?? '') == 'Red Hat Enterprise Linux')>Red Hat Enterprise Linux</option>
                
</select>

                </td> 

                    <td><input type="number" name="rows[{{ $i }}][license_rds_license]" class="form-control" value="{{ old("rows.$i.license_rds_license", $row->license_rds_license ?? 0) }}"  min="0"></td>
             
             
                
                

                 <td>
                  <select name="rows[{{ $i }}][license_microsoft_sql]" class="form-select" style="width: 100px;">
                     <option value="None" @selected(old("rows.$i.license_microsoft_sql", $row->license_microsoft_sql ?? '') == 'None')>None</option>
                    <option value="Web" @selected(old("rows.$i.license_microsoft_sql", $row->license_microsoft_sql ?? '') == 'Web')>Web</option>
                    <option value="Standard" @selected(old("rows.$i.license_microsoft_sql", $row->license_microsoft_sql ?? '') == 'Standard')>Standard</option>
                     <option value="Enterprise" @selected(old("rows.$i.license_microsoft_sql", $row->license_microsoft_sql ?? '') == 'Enterprise')>Enterprise</option>
              
                
</select>

                </td> 
                

                  <td>   <input type="number" name="rows[{{ $i }}][snapshot_copies]" class="form-control" value="{{ old("rows.$i.snapshot_copies", $row->snapshot_copies ?? 0) }}" min="0">
                </td>

                 <td>   <input type="number" name="rows[{{ $i }}][additional_capacity]" class="form-control" value="{{ old("rows.$i.additional_capacity", $row->additional_capacity ?? 0) }}" min="0">
                </td>
               

                    <td>   <input type="number" name="rows[{{ $i }}][image_copies]" class="form-control" value="{{ old("rows.$i.image_copies", $row->image_copies ?? 0) }}" min="0">
                </td>
                            
                    


                   <td>
                  <select name="rows[{{ $i }}][csbs_standard_policy]" class="form-select" style="min-width: 120px;">
                    <option value="No Backup" @selected(old("rows.$i.csbs_standard_policy", $row->csbs_standard_policy ?? '') == 'No Backup')>No Backup</option>
                     <option value="Custom" @selected(old("rows.$i.csbs_standard_policy", $row->csbs_standard_policy ?? '') == 'Custom')>Custom</option>
</select>

                </td> 

                   <td>   <input  name="rows[{{ $i }}][csbs_local_retention_copies]" class="form-control" value="{{ old("rows.$i.csbs_local_retention_copies", $row->csbs_local_retention_copies ?? '') }}" readonly style="background-color: black;color: white;">
                </td>



                   <td>   <input  name="rows[{{ $i }}][csbs_total_storage]" class="form-control" value="{{ old("rows.$i.csbs_total_storage", $row->csbs_total_storage ?? '') }}" readonly style="background-color: black;color: white;">
                </td>


                   <td>   <input type="number" name="rows[{{ $i }}][csbs_initial_data_size]" class="form-control" value="{{ old("rows.$i.csbs_initial_data_size", $row->csbs_initial_data_size ?? 0) }}" min="0">
                </td>

                 <td>   <input type="number" name="rows[{{ $i }}][csbs_incremental_change]" class="form-control" value="{{ old("rows.$i.csbs_incremental_change", $row->csbs_incremental_change ?? 0) }}"  min="0">
                </td>



                  <td>   <input name="rows[{{ $i }}][csbs_estimated_incremental_data_change]" class="form-control" value="{{ old("rows.$i.csbs_estimated_incremental_data_change", $row->csbs_estimated_incremental_data_change ?? '') }}" readonly style="background-color: black;color: white;">
                </td>




                
                  <td>   <input type="number" name="rows[{{ $i }}][full_backup_daily]" class="form-control" value="{{ old("rows.$i.full_backup_daily", $row->full_backup_daily ?? 0) }}" min="0">
                </td>



                   <td>   <input type="number" name="rows[{{ $i }}][full_backup_weekly]" class="form-control" value="{{ old("rows.$i.full_backup_weekly", $row->full_backup_weekly ?? 0) }}" min="0">
                </td>


                   <td>   <input type="number" name="rows[{{ $i }}][full_backup_monthly]" class="form-control" value="{{ old("rows.$i.full_backup_monthly", $row->full_backup_monthly ?? 0) }}" min="0">
                </td>


                 <td>   <input type="number" name="rows[{{ $i }}][full_backup_yearly]" class="form-control" value="{{ old("rows.$i.full_backup_yearly", $row->full_backup_yearly ?? 0) }}" min="0">
                </td>



                  <td>   <input  name="rows[{{ $i }}][full_backup_total_retention_full_copies]" class="form-control" value="{{ old("rows.$i.full_backup_total_retention_full_copies", $row->full_backup_total_retention_full_copies ?? '') }}" readonly style="background-color: black;color: white;">
                </td>

               

               <td>
  <input name="rows[{{ $i }}][suggestion_estimated_storage_full_backup]"
         class="form-control"
         value="{{ old("rows.$i.suggestion_estimated_storage_full_backup", $row->suggestion_estimated_storage_full_backup ?? 0) }}"
         readonly style="background-color:black;color:white;">
</td>

                 <td><input type="number" name="rows[{{ $i }}][estimated_storage_full_backup]" class="form-control" value="{{ old("rows.$i.estimated_storage_full_backup", $row->estimated_storage_full_backup ?? '') }}" min="0"></td>


                
                  <td>   <input type="number" name="rows[{{ $i }}][incremental_backup_daily]" class="form-control" value="{{ old("rows.$i.incremental_backup_daily", $row->incremental_backup_daily ?? 0) }}" min="0">
                </td>



                   <td>   <input type="number" name="rows[{{ $i }}][incremental_backup_weekly]" class="form-control" value="{{ old("rows.$i.incremental_backup_weekly", $row->incremental_backup_weekly ?? 0) }}" min="0">
                </td>


                   <td>   <input type="number" name="rows[{{ $i }}][incremental_backup_monthly]" class="form-control" value="{{ old("rows.$i.incremental_backup_monthly", $row->incremental_backup_monthly ?? 0) }}" min="0">
                </td>

                 <td>   <input type="number" name="rows[{{ $i }}][incremental_backup_yearly]" class="form-control" value="{{ old("rows.$i.incremental_backup_yearly", $row->incremental_backup_yearly ?? 0) }}" min="0">
                </td>



                  <td>   <input  name="rows[{{ $i }}][incremental_backup_total_retention_incremental_copies]" class="form-control" value="{{ old("rows.$i.incremental_backup_total_retention_incremental_copies", $row->incremental_backup_total_retention_incremental_copies ?? '') }}" readonly style="background-color: black;color: white;">
                </td>

                <td>
  <input name="rows[{{ $i }}][suggestion_estimated_storage_incremental_backup]"
         class="form-control"
         value="{{ old("rows.$i.suggestion_estimated_storage_incremental_backup", $row->suggestion_estimated_storage_incremental_backup ?? 0) }}"
         readonly style="background-color:black;color:white;">
</td>


                                     <td><input type="number" name="rows[{{ $i }}][estimated_storage_incremental_backup]" class="form-control" value="{{ old("rows.$i.estimated_storage_incremental_backup", $row->estimated_storage_incremental_backup ?? '') }}" min="0"></td>

                 <td> 
              
                <select name="rows[{{ $i }}][required]" class="form-select">
                    <option value="No" @selected(old("rows.$i.required", $row->required ?? '') == 'No')>No</option>
                    <option value="Yes" @selected(old("rows.$i.required", $row->required ?? '') == 'Yes')>Yes</option>
</select>

                </td> 



                   <td>   <input  name="rows[{{ $i }}][total_replication_copy_retained_second_site]" class="form-control" value="{{ old("rows.$i.total_replication_copy_retained_second_site", $row->total_replication_copy_retained_second_site ?? '') }}" readonly style="background-color: black;color: white;">
                </td>


                   <td>   <input type="number" name="rows[{{ $i }}][additional_storage]" class="form-control" value="{{ old("rows.$i.additional_storage", $row->additional_storage ?? 0) }}" min="0">
                </td>


                
                <td><input type="number" name="rows[{{ $i }}][rto]" class="form-control" value="{{ old("rows.$i.rto", $row->rto ?? '') }}" placeholder="Enter hours"  min="0"></td>


                <td><input type="text" name="rows[{{ $i }}][rpo]" class="form-control" value="{{ old("rows.$i.rpo", $row->rpo ?? '') }}" readonly style="background-color: black;color: white;"></td>

                
                <td>
  <input name="rows[{{ $i }}][suggestion_estimated_storage_csbs_replication]"
         class="form-control"
         value="{{ old("rows.$i.suggestion_estimated_storage_csbs_replication", $row->suggestion_estimated_storage_csbs_replication ?? 0) }}"
         readonly style="background-color:black;color:white;">
</td>



                   <td><input type="number" name="rows[{{ $i }}][estimated_storage_csbs_replication]" class="form-control" value="{{ old("rows.$i.estimated_storage_csbs_replication", $row->estimated_storage_csbs_replication ?? '') }}" min="0"></td>
                
                 
 <td> 
              
                <select name="rows[{{ $i }}][dr_activation]" class="form-select">
                    <option value="No" @selected(old("rows.$i.dr_activation", $row->dr_activation ?? '') == 'No')>No</option>
                    <option value="Yes" @selected(old("rows.$i.dr_activation", $row->dr_activation ?? '') == 'Yes')>Yes</option>
</select>

                </td> 

             

                <td> 
              
                <select name="rows[{{ $i }}][seed_vm_required]" class="form-select">
                    <option value="No" @selected(old("rows.$i.seed_vm_required", $row->seed_vm_required ?? '') == 'No')>No</option>
                    <option value="Yes" @selected(old("rows.$i.seed_vm_required", $row->seed_vm_required ?? '') == 'Yes')>Yes</option>
</select>

                </td> 

                 <td>   
                <select name="rows[{{ $i }}][csdr_needed]" class="form-select">
                     <option value="No" @selected(old("rows.$i.csdr_needed", $row->csdr_needed ?? '') == 'No')>No</option>
                    <option value="Yes" @selected(old("rows.$i.csdr_needed", $row->csdr_needed ?? '') == 'Yes')>Yes</option>
                   
</select>

                </td> 
                

                 <td>   <input  name="rows[{{ $i }}][csdr_storage]" class="form-control" value="{{ old("rows.$i.csdr_storage", $row->csdr_storage ?? '') }}" readonly style="background-color: black;color: white;">
                </td>

                  


<td>
  <input
    name="rows[{{ $i }}][ecs_dr]"
    class="form-control ecs-dr-input"
    value="{{ old("rows.$i.ecs_dr", $row->ecs_dr ?? '') }}" 
    readonly
    style="background-color:black;color:white;width:150px;"
    data-index="{{ $i }}">
</td>






<td>
    <button type="button" class="btn btn-sm btn-outline-danger delete-row d-flex align-items-center gap-1" title="Delete">
        <i class="bi bi-trash"></i> Delete
    </button>
</td>



                

















                        </tr>
                                
@endforeach
                    
                          
                         

                     
                    </tbody>



                    
                </table>






                <button type="button" class="btn btn-pink" id="addRowBtn">
    <i></i> Add Row
    
</button>


            
            </div>




<br>



    @php
    $kl_vcpu = data_get($ddhSummary, 'kl.total_vcpu', 0);
    $cj_vcpu = data_get($ddhSummary, 'cj.total_vcpu', 0);
    $kl_vram = data_get($ddhSummary, 'kl.total_vram', 0);
    $cj_vram = data_get($ddhSummary, 'cj.total_vram', 0);
    $kl_ddh  = data_get($ddhSummary, 'kl.num_ddh', 0);
    $cj_ddh  = data_get($ddhSummary, 'cj.num_ddh', 0);
@endphp

<table style="border-collapse:collapse; font-size:0.9rem;">
  <tr>
    <th style="border:1px solid #cfd6e4; background:#dbe8ff; padding:.4rem .6rem; text-align:left; min-width:180px;">Dedicated Host</th>
    <th style="border:1px solid #cfd6e4; background:#dbe8ff; padding:.4rem .6rem; text-align:center; min-width:110px;">KL Count</th>
    <th style="border:1px solid #cfd6e4; background:#dbe8ff; padding:.4rem .6rem; text-align:center; min-width:110px;">CJ Count</th>
  </tr>

  <tr>
    <th style="border:1px solid #cfd6e4; background:#eaf1ff; padding:.4rem .6rem; text-align:left;">Total vCPU</th>
    <td style="border:1px solid #cfd6e4; text-align:center; padding:.4rem .6rem;">{{ number_format($kl_vcpu) }}</td>
    <td style="border:1px solid #cfd6e4; text-align:center; padding:.4rem .6rem;">{{ number_format($cj_vcpu) }}</td>
  </tr>

  <tr>
    <th style="border:1px solid #cfd6e4; background:#eaf1ff; padding:.4rem .6rem; text-align:left;">Total vRAM</th>
    <td style="border:1px solid #cfd6e4; text-align:center; padding:.4rem .6rem;">{{ number_format($kl_vram) }}</td>
    <td style="border:1px solid #cfd6e4; text-align:center; padding:.4rem .6rem;">{{ number_format($cj_vram) }}</td>
  </tr>

  <tr>
    <th style="border:1px solid #cfd6e4; background:#eaf1ff; padding:.4rem .6rem; text-align:left;"><strong>Num of DDH (min)</strong></th>
    <td style="border:1px solid #cfd6e4; text-align:center; padding:.4rem .6rem;"><strong>{{ $kl_ddh }}</strong></td>
    <td style="border:1px solid #cfd6e4; text-align:center; padding:.4rem .6rem;"><strong>{{ $cj_ddh }}</strong></td>
  </tr>
</table>


  

         <br>  

               <div class="d-flex align-items-center gap-2"> 
    
    <a href="{{ route('versions.region.network.create', $version->id) }}" class="btn btn-secondary" role="button">
        <i class="bi bi-arrow-left"></i> Previous<br>Step
    </a>
    <button type="submit" class="btn btn-pink">Save ECS & Backup</button>
       <!---<div class="alert alert-danger py-1 px-2 small mb-0" role="alert" style="font-size: 0.8rem;">
            ⚠️ Ensure you click <strong>Save</strong> before continuing to the next step!
    </div>--->



    
    

    <div class="ms-auto">
        <a href="{{ route('versions.region.dr.create', $version->id) }}"  
           class="btn btn-secondary" 
           role="button">
           Next: DR Settings <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    </div>



        </form>
    </div>
</div>



<script>
// ===========================
// 1) Flavour mapping helper
// ===========================
function calculateFlavourMapping(vcpuInput, vramInput, pinInput, gpuInput, ddhInput) {
  if (!vcpuInput || !vramInput) return '';

  const flavours = [
    { name: 'm3.micro', vcpu: 1, vram: 1, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'm3.small', vcpu: 1, vram: 2, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'c3.large', vcpu: 2, vram: 4, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'm3.large', vcpu: 2, vram: 8, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'r3.large', vcpu: 2, vram: 16, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'c3.xlarge', vcpu: 4, vram: 8, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'm3.xlarge', vcpu: 4, vram: 16, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'r3.xlarge', vcpu: 4, vram: 32, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'c3.2xlarge', vcpu: 8, vram: 16, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'm3.2xlarge', vcpu: 8, vram: 32, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'r3.2xlarge', vcpu: 8, vram: 64, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'm3.3xlarge', vcpu: 12, vram: 48, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'c3.4xlarge', vcpu: 16, vram: 32, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'm3.4xlarge', vcpu: 16, vram: 64, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'r3.4xlarge', vcpu: 16, vram: 128, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'm3.6xlarge', vcpu: 24, vram: 96, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'c3.8xlarge', vcpu: 32, vram: 64, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'm3.8xlarge', vcpu: 32, vram: 128, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'r3.8xlarge', vcpu: 32, vram: 256, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'r3.12xlarge', vcpu: 48, vram: 384, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'c3.16xlarge', vcpu: 64, vram: 128, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'm3.16xlarge', vcpu: 64, vram: 256, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'r3.16xlarge', vcpu: 64, vram: 512, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'c3p.xlarge', vcpu: 4, vram: 8, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'm3p.xlarge', vcpu: 4, vram: 16, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'r3p.xlarge', vcpu: 4, vram: 32, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'c3p.2xlarge', vcpu: 8, vram: 16, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'm3p.2xlarge', vcpu: 8, vram: 32, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'r3p.2xlarge', vcpu: 8, vram: 64, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'm3p.3xlarge', vcpu: 12, vram: 48, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'c3p.4xlarge', vcpu: 16, vram: 32, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'm3p.4xlarge', vcpu: 16, vram: 64, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'r3p.4xlarge', vcpu: 16, vram: 64, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'm3p.6xlarge', vcpu: 24, vram: 96, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'c3p.8xlarge', vcpu: 32, vram: 64, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'm3p.8xlarge', vcpu: 32, vram: 128, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'r3p.8xlarge', vcpu: 32, vram: 128, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'm3p.12xlarge', vcpu: 48, vram: 192, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'r3p.12xlarge', vcpu: 48, vram: 384, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'm3p.16xlarge', vcpu: 64, vram: 256, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'r3p.16xlarge', vcpu: 64, vram: 512, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'r3p.46xlarge.metal', vcpu: 64, vram: 1408, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'm3gnt4.xlarge', vcpu: 4, vram: 16, pin: 'No', gpu: 'Yes', ddh: 'No' },
    { name: 'm3gnt4.2xlarge', vcpu: 8, vram: 32, pin: 'No', gpu: 'Yes', ddh: 'No' },
    { name: 'm3gnt4.4xlarge', vcpu: 16, vram: 64, pin: 'No', gpu: 'Yes', ddh: 'No' },
    { name: 'm3gnt4.8xlarge', vcpu: 32, vram: 128, pin: 'No', gpu: 'Yes', ddh: 'No' },
    { name: 'm3gnt4.16xlarge', vcpu: 64, vram: 256, pin: 'No', gpu: 'Yes', ddh: 'No' },
    { name: 'r3p.46xlarge.ddh', vcpu: 342, vram: 1480, pin: 'No', gpu: 'No', ddh: 'Yes' }
  ];

  // cuba match exact (Pin/GPU/DDH sama)
  /*let suitable = flavours
    .filter(f => f.vcpu >= vcpuInput && f.vram >= vramInput && f.pin === pinInput && f.gpu === gpuInput && f.ddh === ddhInput)
    .sort((a, b) => (a.vcpu - b.vcpu) || (a.vram - b.vram));

  // fallback: kalau combo exact tak ada, longgarkan kepada vCPU/vRAM sahaja
  if (!suitable.length) {
    suitable = flavours
      .filter(f => f.vcpu >= vcpuInput && f.vram >= vramInput)
      .sort((a, b) => (a.vcpu - b.vcpu) || (a.vram - b.vram));
  }
  return suitable.length ? suitable[0].name : null;
}*/  // Base mapping MUST NOT use any DDH flavour
  const pool = flavours.filter(f => f.ddh === 'No');

  // Try exact match on PIN/GPU + capacity
  let suitable = pool
    .filter(f => f.vcpu >= vcpuInput && f.vram >= vramInput && f.pin === pinInput && f.gpu === gpuInput)
    .sort((a,b) => (a.vcpu - b.vcpu) || (a.vram - b.vram));

  // Fallback: only capacity
  if (!suitable.length) {
    suitable = pool
      .filter(f => f.vcpu >= vcpuInput && f.vram >= vramInput)
      .sort((a,b) => (a.vcpu - b.vcpu) || (a.vram - b.vram));
  }

  return suitable.length ? suitable[0].name : null;
}


function toInt(v){ const n = parseInt(v,10); return isNaN(n)?0:n; }

// ===========================
// 2) Attach listeners per-row
// ===========================
function attachDynamicListeners(row, index) {
  const q = sel => row.querySelector(sel);

  // ECS / flavour
  const vcpu = q(`input[name="rows[${index}][ecs_vcpu]"]`);
  const vram = q(`input[name="rows[${index}][ecs_vram]"]`);
  const flavour = q(`input[name="rows[${index}][ecs_flavour_mapping]"]`);
  const pinSelect = q(`select[name="rows[${index}][ecs_pin]"]`);
  const gpuSelect = q(`select[name="rows[${index}][ecs_gpu]"]`);
  const ddhSelect = q(`select[name="rows[${index}][ecs_ddh]"]`);

  // Storage
  const sysDisk = q(`input[name="rows[${index}][storage_system_disk]"]`);
  const dataDisk = q(`input[name="rows[${index}][storage_data_disk]"]`);

  // CSBS
  const csbsPolicy = q(`select[name="rows[${index}][csbs_standard_policy]"]`);
  const localRet   = q(`input[name="rows[${index}][csbs_local_retention_copies]"]`);
  const totalStorage = q(`input[name="rows[${index}][csbs_total_storage]"]`);
  const initialSize  = q(`input[name="rows[${index}][csbs_initial_data_size]"]`);
  const changePct    = q(`input[name="rows[${index}][csbs_incremental_change]"]`);
  const estChangeOut = q(`input[name="rows[${index}][csbs_estimated_incremental_data_change]"]`);

  // Retention FULL
  const fullDaily   = q(`input[name="rows[${index}][full_backup_daily]"]`);
  const fullWeekly  = q(`input[name="rows[${index}][full_backup_weekly]"]`);
  const fullMonthly = q(`input[name="rows[${index}][full_backup_monthly]"]`);
  const fullYearly  = q(`input[name="rows[${index}][full_backup_yearly]"]`);
  const fullTotal   = q(`input[name="rows[${index}][full_backup_total_retention_full_copies]"]`);

  // Retention INCREMENTAL
  const incDaily   = q(`input[name="rows[${index}][incremental_backup_daily]"]`);
  const incWeekly  = q(`input[name="rows[${index}][incremental_backup_weekly]"]`);
  const incMonthly = q(`input[name="rows[${index}][incremental_backup_monthly]"]`);
  const incYearly  = q(`input[name="rows[${index}][incremental_backup_yearly]"]`);
  const incTotal   = q(`input[name="rows[${index}][incremental_backup_total_retention_incremental_copies]"]`);

  // Replication / DR
  const requiredSel = q(`select[name="rows[${index}][required]"]`);
  const replCopies  = q(`input[name="rows[${index}][total_replication_copy_retained_second_site]"]`);
  const rto         = q(`input[name="rows[${index}][rto]"]`);
  const rpo         = q(`input[name="rows[${index}][rpo]"]`);

  // CSDR
  const csdrNeeded  = q(`select[name="rows[${index}][csdr_needed]"]`);
  const csdrStorage = q(`input[name="rows[${index}][csdr_storage]"]`);

  // Suggestion fields (exist in Blade)
  const suggFull   = q(`input[name="rows[${index}][suggestion_estimated_storage_full_backup]"]`);
  const suggInc    = q(`input[name="rows[${index}][suggestion_estimated_storage_incremental_backup]"]`);
  const suggRepl   = q(`input[name="rows[${index}][suggestion_estimated_storage_csbs_replication]"]`);

  function recalcRow() {
    const fullT = toInt(fullDaily?.value) + toInt(fullWeekly?.value) + toInt(fullMonthly?.value) + toInt(fullYearly?.value);
    const incT  = toInt(incDaily?.value)  + toInt(incWeekly?.value)  + toInt(incMonthly?.value)  + toInt(incYearly?.value);

    if (fullTotal) fullTotal.value = fullT;
    if (incTotal)  incTotal.value  = incT;

    /*const init = toInt(initialSize?.value);
    //const estChange = Math.round(init * (toInt(changePct?.value)/100));
    const pct = parseFloat(changePct?.value || 0);
const estChange = Math.ceil(init * (pct/100));*/

const init = toInt(initialSize?.value);


const pctRaw = parseFloat(changePct?.value || 0);
const estChangeRaw = init * (pctRaw / 100);
if (estChangeOut) estChangeOut.value = estChangeRaw.toFixed(1);





    let local = 0;
    const policy = csbsPolicy?.value || 'No Backup';
    if (policy === 'Custom') local = fullT + incT + 1;
    if (localRet) localRet.value = local;

    //const totalStore = Math.ceil(init + (init * fullT) + (estChange * incT)); // sama formula asal

    /*const totalStore = init + (init * fullT) + (estChange * incT);

    if (totalStorage) totalStorage.value = (policy === 'No Backup') ? 0 : totalStore;*/


   


const totalStore = init + (init * fullT) + (estChangeRaw * incT);
if (totalStorage) totalStorage.value = (policy === 'No Backup') ? 0 : Math.ceil(totalStore);


    if (replCopies) replCopies.value = (requiredSel?.value === 'Yes') ? local : 0;

    if (rpo) {
      const rtoVal = rto?.value;
      rpo.value = (rtoVal !== '' && !isNaN(rtoVal)) ? '24 hours' : 'N/A';
    }

    const sys = toInt(sysDisk?.value);
    const dat = toInt(dataDisk?.value);
    if (csdrStorage) csdrStorage.value = (csdrNeeded?.value === 'Yes') ? (sys + dat) : 0;

    // === Suggestions ===
    if (suggFull) suggFull.value = init * fullT;
    if (suggInc)  suggInc.value  = estChangeRaw * incT;
    

const replSuggested = (init * fullT) + (estChangeRaw * incT);
if (suggRepl)
  suggRepl.value = (requiredSel?.value === 'Yes' && policy !== 'No Backup')
    ? Math.ceil(replSuggested)
    : 0;





  }

  function updateFlavour() {
    const v = toInt(vcpu?.value);
    const r = toInt(vram?.value);
    const pinVal = pinSelect?.value || 'No';
    const gpuVal = gpuSelect?.value || 'No';
    const ddhVal = ddhSelect?.value || 'No';

    if (v <= 0 || r <= 0) { if (flavour) flavour.value = ''; return; }
    const f = calculateFlavourMapping(v, r, pinVal, gpuVal, ddhVal);
    flavour.value = f || 'No suitable flavour';
  }

  // Listeners
  vcpu?.addEventListener('input', updateFlavour);
  vram?.addEventListener('input', updateFlavour);
  pinSelect?.addEventListener('change', updateFlavour);
  gpuSelect?.addEventListener('change', updateFlavour);
  ddhSelect?.addEventListener('change', updateFlavour);

  [
    fullDaily, fullWeekly, fullMonthly, fullYearly,
    incDaily, incWeekly, incMonthly, incYearly,
    initialSize, changePct, sysDisk, dataDisk,
    requiredSel, csbsPolicy, csdrNeeded, rto
  ].forEach(el => el && el.addEventListener(el.tagName === 'SELECT' ? 'change' : 'input', recalcRow));

  updateFlavour();
  recalcRow();
}

// ===========================
// 3) Add Row + attach handlers
// ===========================
document.addEventListener('DOMContentLoaded', function () {
  const tbody = document.querySelector('#ecsBackupTable tbody');
  if (!tbody) return;

  // attach untuk sedia ada
  Array.from(tbody.querySelectorAll('tr')).forEach((row, i) => attachDynamicListeners(row, i));

  function nextIndex(){
    let max = -1;
    tbody.querySelectorAll('[name^="rows["]').forEach(el => {
      const m = el.name.match(/^rows\[(\d+)\]/);
      if (m) max = Math.max(max, parseInt(m[1],10));
    });
    return max + 1;
  }

  document.getElementById('addRowBtn')?.addEventListener('click', function(){
    const template = tbody.querySelector('tr');
    if (!template) return;

    const idx = nextIndex();
    const newRow = template.cloneNode(true);

    // Update "No"
    const firstCell = newRow.querySelector('td');
    if (firstCell) firstCell.textContent = idx + 1;

    // Reindex + reset value
    newRow.querySelectorAll('input, select, textarea').forEach(el => {
      const old = el.getAttribute('name');
      if (!old) return;
      el.setAttribute('name', old.replace(/^rows\[\d+\]/, `rows[${idx}]`));

      if (el.tagName === 'SELECT') el.selectedIndex = 0;
      else el.value = '';
    });

    // Buang id tersembunyi kalau ada
    const hid = newRow.querySelector(`input[name="rows[${idx}][id]"]`);
    if (hid) hid.value = '';

    // Default penting supaya validasi pass
    const sys = newRow.querySelector(`input[name="rows[${idx}][storage_system_disk]"]`);
    if (sys) sys.value = 40;

    // Kosongkan semua output readonly
    [
      'ecs_flavour_mapping','csbs_local_retention_copies','csbs_total_storage',
      'csbs_estimated_incremental_data_change','full_backup_total_retention_full_copies',
      'incremental_backup_total_retention_incremental_copies','rpo','csdr_storage',
      'ecs_dr','suggestion_estimated_storage_full_backup',
      'suggestion_estimated_storage_incremental_backup',
      'suggestion_estimated_storage_csbs_replication',
      'total_replication_copy_retained_second_site'
    ].forEach(k => {
      const el = newRow.querySelector(`input[name="rows[${idx}][${k}]"]`);
      if (el) el.value = '';
    });

    tbody.appendChild(newRow);
    attachDynamicListeners(newRow, idx);
  });
});

// ===========================
// 4) Delete row (kekal)
// ===========================
document.addEventListener('click', function (e) {
  if (e.target.closest('.delete-row')) {
    if (!confirm('Are you sure you want to delete this row?')) return;
    const row = e.target.closest('tr');
    const hiddenInput = row.querySelector('input[name^="rows"][name$="[id]"]');
    const id = hiddenInput?.value;

    if (id) {
      fetch(`/ecs-configurations/${id}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      }).then(res => res.json()).then(data => {
        if (data.success) row.remove();
        else alert('Failed to delete from database.');
      }).catch(err => {
        alert('Error occurred while deleting.');
        console.error(err);
      });
    } else {
      row.remove();
    }
  }
});

// ===========================
// 5) DR flavour mapping (ecs_dr)
// ===========================
document.addEventListener('DOMContentLoaded', function () {
  const container = document.querySelector('#ecsBackupTable') || document;

  function updateRow(tr){
    if (!tr) return;
    const regionEl = tr.querySelector('select[name^="rows"][name$="[region]"], input[name^="rows"][name$="[region]"]');
    const drSel    = tr.querySelector('select[name^="rows"][name$="[dr_activation]"]');
    const baseEl   = tr.querySelector('input[name^="rows"][name$="[ecs_flavour_mapping]"]');
    const drOut    = tr.querySelector('input[name^="rows"][name$="[ecs_dr]"]');
    if (!drOut) return;

    const region = regionEl ? (regionEl.value || '').trim() : '';
    const drAct  = drSel ? (drSel.value || 'No') : 'No';
    const base   = baseEl ? (baseEl.value || '').trim() : '';

    drOut.value = (region === 'Cyberjaya' && drAct === 'Yes' && base) ? (base + '.dr') : '';
  }

  container.addEventListener('change', function (e) {
    if (
      e.target.matches('select[name^="rows"][name$="[dr_activation]"]') ||
      e.target.matches('select[name^="rows"][name$="[region]"], input[name^="rows"][name$="[region]"]') ||
      e.target.matches('input[name^="rows"][name$="[ecs_flavour_mapping]"]')
    ) {
      updateRow(e.target.closest('tr'));
    }
  });

  document.querySelectorAll('#ecsBackupTable tbody tr').forEach(updateRow);
});
</script>





<!---<script>








    function calculateFlavourMapping(vcpuInput, vramInput, pinInput, gpuInput, ddhInput) {
    if (!vcpuInput || !vramInput) {
        return '';
    }
   
    const flavours = [
    { name: 'm3.micro', vcpu: 1, vram: 1, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'm3.small', vcpu: 1, vram: 2, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'c3.large', vcpu: 2, vram: 4, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'm3.large', vcpu: 2, vram: 8, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'r3.large', vcpu: 2, vram: 16, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'c3.xlarge', vcpu: 4, vram: 8, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'm3.xlarge', vcpu: 4, vram: 16, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'r3.xlarge', vcpu: 4, vram: 32, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'c3.2xlarge', vcpu: 8, vram: 16, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'm3.2xlarge', vcpu: 8, vram: 32, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'r3.2xlarge', vcpu: 8, vram: 64, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'm3.3xlarge', vcpu: 12, vram: 48, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'c3.4xlarge', vcpu: 16, vram: 32, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'm3.4xlarge', vcpu: 16, vram: 64, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'r3.4xlarge', vcpu: 16, vram: 128, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'm3.6xlarge', vcpu: 24, vram: 96, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'c3.8xlarge', vcpu: 32, vram: 64, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'm3.8xlarge', vcpu: 32, vram: 128, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'r3.8xlarge', vcpu: 32, vram: 256, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'r3.12xlarge', vcpu: 48, vram: 384, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'c3.16xlarge', vcpu: 64, vram: 128, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'm3.16xlarge', vcpu: 64, vram: 256, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'r3.16xlarge', vcpu: 64, vram: 512, pin: 'No', gpu: 'No', ddh: 'No' },
    { name: 'c3p.xlarge', vcpu: 4, vram: 8, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'm3p.xlarge', vcpu: 4, vram: 16, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'r3p.xlarge', vcpu: 4, vram: 32, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'c3p.2xlarge', vcpu: 8, vram: 16, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'm3p.2xlarge', vcpu: 8, vram: 32, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'r3p.2xlarge', vcpu: 8, vram: 64, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'm3p.3xlarge', vcpu: 12, vram: 48, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'c3p.4xlarge', vcpu: 16, vram: 32, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'm3p.4xlarge', vcpu: 16, vram: 64, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'r3p.4xlarge', vcpu: 16, vram: 64, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'm3p.6xlarge', vcpu: 24, vram: 96, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'c3p.8xlarge', vcpu: 32, vram: 64, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'm3p.8xlarge', vcpu: 32, vram: 128, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'r3p.8xlarge', vcpu: 32, vram: 128, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'm3p.12xlarge', vcpu: 48, vram: 192, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'r3p.12xlarge', vcpu: 48, vram: 384, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'm3p.16xlarge', vcpu: 64, vram: 256, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'r3p.16xlarge', vcpu: 64, vram: 512, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'r3p.46xlarge.metal', vcpu: 64, vram: 1408, pin: 'Yes', gpu: 'No', ddh: 'No' },
    { name: 'm3gnt4.xlarge', vcpu: 4, vram: 16, pin: 'No', gpu: 'Yes', ddh: 'No' },
    { name: 'm3gnt4.2xlarge', vcpu: 8, vram: 32, pin: 'No', gpu: 'Yes', ddh: 'No' },
    { name: 'm3gnt4.4xlarge', vcpu: 16, vram: 64, pin: 'No', gpu: 'Yes', ddh: 'No' },
    { name: 'm3gnt4.8xlarge', vcpu: 32, vram: 128, pin: 'No', gpu: 'Yes', ddh: 'No' },
    { name: 'm3gnt4.16xlarge', vcpu: 64, vram: 256, pin: 'No', gpu: 'Yes', ddh: 'No' },
    { name: 'r3p.46xlarge.ddh', vcpu: 342, vram: 1480, pin: 'No', gpu: 'No', ddh: 'Yes' }
];


    /*const suitable = flavours
        .filter(f => f.vcpu >= (vcpuInput || 0) && f.vram >= (vramInput || 0))
        .sort((a, b) => (a.vcpu - b.vcpu) || (a.vram - b.vram));

    return suitable.length ? suitable[0].name : null;
}*/



const suitable = flavours
        .filter(f =>
            f.vcpu >= vcpuInput &&
            f.vram >= vramInput &&
            f.pin === pinInput &&
            f.gpu === gpuInput &&
            f.ddh === ddhInput
        )
        .sort((a, b) => (a.vcpu - b.vcpu) || (a.vram - b.vram));

    return suitable.length ? suitable[0].name : null;
}


function toInt(v) { 
    const n = parseInt(v, 10);
    return isNaN(n) ? 0 : n;
}






function attachDynamicListeners(row, index) {
    const q = (sel) => row.querySelector(sel);

    // === ECS / Flavour ===
    const vcpu    = q(`input[name="rows[${index}][ecs_vcpu]"]`);
    const vram    = q(`input[name="rows[${index}][ecs_vram]"]`);
    const flavour = q(`input[name="rows[${index}][ecs_flavour_mapping]"]`);
    const pinSelect = q(`select[name="rows[${index}][ecs_pin]"]`);
    const gpuSelect = q(`select[name="rows[${index}][ecs_gpu]"]`);
    const ddhSelect = q(`select[name="rows[${index}][ecs_ddh]"]`);

    // === Storage ===
    const sysDisk = q(`input[name="rows[${index}][storage_system_disk]"]`);
    const dataDisk= q(`input[name="rows[${index}][storage_data_disk]"]`);

    // === CSBS fields ===
    const csbsPolicy   = q(`select[name="rows[${index}][csbs_standard_policy]"]`);
    const localRet     = q(`input[name="rows[${index}][csbs_local_retention_copies]"]`);
    const totalStorage = q(`input[name="rows[${index}][csbs_total_storage]"]`);
    const initialSize  = q(`input[name="rows[${index}][csbs_initial_data_size]"]`);
    const changePct    = q(`input[name="rows[${index}][csbs_incremental_change]"]`);
    const estChangeOut = q(`input[name="rows[${index}][csbs_estimated_incremental_data_change]"]`);

    // === Full backup retention ===
    const fullDaily   = q(`input[name="rows[${index}][full_backup_daily]"]`);
    const fullWeekly  = q(`input[name="rows[${index}][full_backup_weekly]"]`);
    const fullMonthly = q(`input[name="rows[${index}][full_backup_monthly]"]`);
    const fullYearly  = q(`input[name="rows[${index}][full_backup_yearly]"]`);
    const fullTotal   = q(`input[name="rows[${index}][full_backup_total_retention_full_copies]"]`);

    // === Incremental backup retention ===
    const incDaily   = q(`input[name="rows[${index}][incremental_backup_daily]"]`);
    const incWeekly  = q(`input[name="rows[${index}][incremental_backup_weekly]"]`);
    const incMonthly = q(`input[name="rows[${index}][incremental_backup_monthly]"]`);
    const incYearly  = q(`input[name="rows[${index}][incremental_backup_yearly]"]`);
    const incTotal   = q(`input[name="rows[${index}][incremental_backup_total_retention_incremental_copies]"]`);

    // === Replication / DR ===
    const requiredSel = q(`select[name="rows[${index}][required]"]`);
    const replCopies  = q(`input[name="rows[${index}][total_replication_copy_retained_second_site]"]`);
    const rto         = q(`input[name="rows[${index}][rto]"]`);
    const rpo         = q(`input[name="rows[${index}][rpo]"]`);

    // === CSDR ===
    const csdrNeeded  = q(`select[name="rows[${index}][csdr_needed]"]`);
    const csdrStorage = q(`input[name="rows[${index}][csdr_storage]"]`);

    // === Kiraan baris ===
    function recalcRow() {
        const fullT = toInt(fullDaily?.value) + toInt(fullWeekly?.value) + toInt(fullMonthly?.value) + toInt(fullYearly?.value);
        
       


        // Multiply full backup total retention by initial data size
        const initialDataSize = toInt(initialSize?.value);
        const multipliedFullT = initialDataSize > 0 ? fullT * initialDataSize : fullT;
        
        if (fullTotal) fullTotal.value = multipliedFullT;

        const incT = toInt(incDaily?.value) + toInt(incWeekly?.value) + toInt(incMonthly?.value) + toInt(incYearly?.value);
        if (incTotal) incTotal.value = incT;

        const estChange = Math.round(toInt(initialSize?.value) * (toInt(changePct?.value) / 100));
        if (estChangeOut) estChangeOut.value = estChange;

        let local = 0;
        const policy = csbsPolicy?.value || 'No Backup';
        if (policy === 'Custom') {
            local = fullT + incT + 1;
        }
        if (localRet) localRet.value = local;

        const init = toInt(initialSize?.value);
        const totalStore = Math.ceil(init + (init * fullT) + (estChange * incT));
        if (totalStorage) totalStorage.value = (policy === 'No Backup') ? 0 : totalStore;

        if (replCopies) replCopies.value = (requiredSel?.value === 'Yes') ? local : 0;

        if (rpo) {
            const rtoVal = rto?.value;
            rpo.value = (rtoVal !== '' && !isNaN(rtoVal)) ? '24 hours' : 'N/A';
        }

        const sys = toInt(sysDisk?.value);
        const dat = toInt(dataDisk?.value);
        if (csdrStorage) csdrStorage.value = (csdrNeeded?.value === 'Yes') ? (sys + dat) : 0;
    }





    // === Flavour mapping ===
    function updateFlavour() {
        const v = toInt(vcpu?.value);
        const r = toInt(vram?.value);
        const pinVal = pinSelect?.value || 'No';
        const gpuVal = gpuSelect?.value || 'No';
        const ddhVal = ddhSelect?.value || 'No';

        if (v <= 0 || r <= 0) {
            if (flavour) flavour.value = '';
            return;
        }

        const f = calculateFlavourMapping(v, r, pinVal, gpuVal, ddhVal);
        flavour.value = f || 'No suitable flavour';
    }

    // === Event listeners ===
    vcpu?.addEventListener('input', updateFlavour);
    vram?.addEventListener('input', updateFlavour);
    pinSelect?.addEventListener('change', updateFlavour);
    gpuSelect?.addEventListener('change', updateFlavour);
    ddhSelect?.addEventListener('change', updateFlavour);

    [
        fullDaily, fullWeekly, fullMonthly, fullYearly,
        incDaily, incWeekly, incMonthly, incYearly,
        initialSize, changePct,
        sysDisk, dataDisk,
        requiredSel, csbsPolicy, csdrNeeded, rto
    ].forEach(el => el && el.addEventListener(
        el.tagName === 'SELECT' ? 'change' : 'input',
        recalcRow
    ));

    // === Initial calculation ===
    updateFlavour();
    recalcRow();
}


// =========================
// 4) Add Row + attach handlers
// =========================
document.addEventListener('DOMContentLoaded', function () {
    //const tbody = document.querySelector('.table tbody');
    const tbody = document.querySelector('#ecsBackupTable tbody');

    if (!tbody) return;

    // Attach untuk semua row sedia ada ikut index dalam name (rows[0], rows[1], ...)
    const rows = Array.from(tbody.querySelectorAll('tr'));
    rows.forEach((row, i) => attachDynamicListeners(row, i));

    // Row index seterusnya
    let rowIndex = rows.length; // contoh: kalau ada 1 row (index 0), next = 1

    const addBtn = document.getElementById('addRowBtn');
    if (!addBtn) return;

    addBtn.addEventListener('click', function () {
        const originalRow = tbody.querySelector('tr');
        if (!originalRow) return;

        const newRow = originalRow.cloneNode(true);

        // Update nombor "No"
        const firstCell = newRow.querySelector('td');
        if (firstCell) firstCell.innerText = rowIndex + 1;

        // Tukar name rows[0] -> rows[rowIndex] dan kosongkan value
        const fields = newRow.querySelectorAll('input, select, textarea');
        fields.forEach(input => {
            const oldName = input.getAttribute('name');
            if (!oldName) return;

            // Tukar indeks dalam name
            const newName = oldName
                .replace(/\[\d+\]/, `[${rowIndex}]`) // ganti index apa-apa kepada index baru
                .replace(/\[0\]/, `[${rowIndex}]`); // fallback kalau asal memang 0 sahaja
            input.setAttribute('name', newName);

            // Clear nilai (including readonly targets)
            if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            } else {
                input.value = '';
            }
        });

        tbody.appendChild(newRow);

        // Attach semua listener untuk row baru ni
        attachDynamicListeners(newRow, rowIndex);

        // Increment index untuk row seterusnya
        rowIndex++;
    });
});
</script>





<script>
document.addEventListener('click', function (e) {
    if (e.target.closest('.delete-row')) {
        if (confirm('Are you sure you want to delete this row?')) {
            const row = e.target.closest('tr');
            const hiddenInput = row.querySelector('input[name^="rows"][name$="[id]"]');
            const id = hiddenInput?.value;

            if (id) {
                // DELETE to backend
                fetch(`/ecs-configurations/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                }).then(res => res.json()).then(data => {
                    if (data.success) row.remove();
                    else alert('Failed to delete from database.');
                }).catch(err => {
                    alert('Error occurred while deleting.');
                    console.error(err);
                });
            } else {
                // frontend only (unsaved)
                row.remove();
            }
        }
    }
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
  // Guna delegation supaya row yang baru ditambah pun ter-cover
  //const container = document.querySelector('table') || document;
  const container = document.querySelector('#ecsBackupTable') || document;


  function updateRow(tr) {
    if (!tr) return;
    const regionEl = tr.querySelector('select[name^="rows"][name$="[region]"], input[name^="rows"][name$="[region]"]');
    const drElSel  = tr.querySelector('select[name^="rows"][name$="[dr_activation]"]');
    const baseEl   = tr.querySelector('input[name^="rows"][name$="[ecs_flavour_mapping]"]');
    const drOut    = tr.querySelector('input[name^="rows"][name$="[ecs_dr]"]');

    if (!drOut) return;

    const region = regionEl ? (regionEl.value || '').trim() : '';
    const drAct  = drElSel ? (drElSel.value || 'No') : 'No';
    const base   = baseEl ? (baseEl.value || '').trim() : '';

    // Formula COUNTIFS kau: Cyberjaya + DR=Yes → base + ".dr"
    if (region === 'Cyberjaya' && drAct === 'Yes' && base) {
      drOut.value = base + '.dr';
    } else {
      drOut.value = '';
    }
  }

  // Update bila user tukar dr_activation / region / flavour
  container.addEventListener('change', function (e) {
    if (
      e.target.matches('select[name^="rows"][name$="[dr_activation]"]') ||
      e.target.matches('select[name^="rows"][name$="[region]"], input[name^="rows"][name$="[region]"]') ||
      e.target.matches('input[name^="rows"][name$="[ecs_flavour_mapping]"]')
    ) {
      const tr = e.target.closest('tr');
      updateRow(tr);
    }
  });

  // Initial fill (untuk old() values masa page load)
  document.querySelectorAll('tr').forEach(updateRow);
});
</script>--->





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