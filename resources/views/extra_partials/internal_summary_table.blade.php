<style>
    table.internal-summary td, table.internal-summary th {
        border: 1px solid #ddd;
        padding: 4px;
        font-size: 10px;
    }

    table.internal-summary {
        border-collapse: collapse;
        width: 100%;
    }

    .section-header {
        background-color: #e76ccf;
        font-weight: bold;
    }
</style>

 <div class="card-body">
        <!-- Project Information Header -->
        <div class="card mb-4">
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
        </div>

        <!-- Summary Table -->
         @php
    function highlightRow($kl, $cj) {
        return ($kl ?? 0) > 0 || ($cj ?? 0) > 0 ? ' style="background-color: rgb(251, 194, 224);"' : '';
    }
@endphp

        <div class="table-responsive">
            <table class="table table-bordered">
            
                    <tr class="table-light">
                    <tr>
                        <th></th>
                        <th></th>
                        <th>Kuala Lumpur</th>
                        <th>Cyberjaya</th>
                    </tr>
                </tr>
                <tbody>
                    <!-- Professional Services -->
                    <tr>
                        <td style="background-color: #e76ccf;font-weight: bold;">Professional Services</td>
                        <td style="background-color: #e76ccf;font-weight: bold;">Unit</td>
                        <td style="background-color: #e76ccf;font-weight: bold;">Qty</td>
                        <td style="background-color: #e76ccf;font-weight: bold;">Qty</td>
                    </tr>
                    <tr>
                        <td>Professional Services (ONE TIME Provisioning)</td>
                        <td>Days</td>
                       <td colspan="2" class="center">{{ $region->mandays ?? '0' }}</td>

                    </tr>
                    <tr>
                        <td>Migration Tools One Time Charge</td>
                        <td>Unit Per Month*</td>
                        <td>0 Unit</td>
                        <td>0 Months</td>
                    </tr>
                    
                    <!-- Managed Services -->
                    <tr>
                        <td style="background-color: #e76ccf;font-weight: bold;">Managed Services</td>
                        <td style="background-color: #e76ccf;font-weight: bold;">Unit</td>
                        <td style="background-color: #e76ccf;font-weight: bold;">KL.Qty</td>
                        <td style="background-color: #e76ccf;font-weight: bold;">CJ.Qty</td>
                    </tr>

                    <tr>
    <td>Managed Operating System</td>
    <td>VM</td>
    <td>{{ $managed_operating_system_kl }}</td>
    <td>{{ $managed_operating_system_cyber }}</td>
</tr>
<tr>
    <td>Managed Backup and Restore</td>
    <td>VM</td>
    <td>{{ $managed_backup_and_restore_kl }}</td>
    <td>{{ $managed_backup_and_restore_cyber }}</td>
</tr>
<tr>
    <td>Managed Patching</td>
    <td>VM</td>
    <td>{{ $managed_patching_kl }}</td>
    <td>{{ $managed_patching_cyber }}</td>
</tr>
<tr>
    <td>Managed DR</td>
    <td>VM</td>
    <td>{{ $managed_dr_kl }}</td>
    <td>{{ $managed_dr_cyber }}</td>
</tr>


                  

                    
                    <!-- Network -->
                    <tr>
                        <td style="background-color: #e76ccf;font-weight: bold;">Network</td>
                        <td style="background-color: #e76ccf;font-weight: bold;">Unit</td>
                        <td style="background-color: #e76ccf;font-weight: bold;">KL.Qty</td>
                        <td style="background-color: #e76ccf;font-weight: bold;">CJ.Qty</td>
                    </tr>
                    <tr>
                        <td>Bandwidth</td>
                        <td>Mbps</td>
                        <td>{{ $region->kl_bandwidth ?? '0' }}</td>
                        <td>{{ $region->cyber_bandwidth ?? '0' }}</td>
                    </tr>

                    <tr>
                        <td>Bandwidth with Anti-DDoS</td>  
                        <td>Mbps</td>
                       <td>{{ $region->kl_bandwidth_with_antiddos ?? '0' }}</td>
                <td>{{ $region->cyber_bandwidth_with_antiddos ?? '0' }}</td>
            </tr>
                    <tr>
                        <td>Included Elastic IP (FOC)</td>
                        <td>Unit</td>
                       <td>{{ $region->kl_included_elastic_ip ?? '0' }}</td>
                        <td>{{ $region->cyber_included_elastic_ip ?? '0' }}</td>
                    </tr>


                      <tr>
                        <td>Elastic IP</td>
                        <td>Unit</td>
                        <td>{{ $region->kl_elastic_ip ?? '0' }}</td>
                        <td>{{ $region->cyber_elastic_ip ?? '0' }}</td>
</tr>


                    <tr>
                        <td>Elastic Load Balancer (External)</td>
                        <td>Unit</td>
                        <td>{{ $region->kl_elastic_load_balancer ?? '0' }}</td>
                        <td>{{ $region->cyber_elastic_load_balancer ?? '0' }}</td>
                    </tr>



                    <tr>
                        <td>Direct Connect Virtual Gateway</td>
                        <td>Unit</td>
                        <td>{{ $region->kl_direct_connect_virtual ?? '0' }}</td>
                        <td>{{ $region->cyber_direct_connect_virtual ?? '0' }}</td>
                    </tr>


                     <tr>
                        <td>L2BR instance</td>
                        <td>Unit</td>
                        <td>{{ $region->kl_l2br_instance ?? '0' }}</td>
                        <td>{{ $region->cyber_l2br_instance ?? '0' }}</td>
                    </tr>


                     <tr>
                        <td>Virtual Private Leased Line (vPLL)</td>
                        <td>Mbps</td>
                       <td>{{ $region->kl_virtual_private_leased_line ?? '0' }}</td>
                        <td> <div class="input-group">
                                    <input name="" 
                                           class="form-control bg-light text-muted" 
                                           value=""
                                           disabled
                                           style="cursor: not-allowed;">
                                </div></td>
                    </tr>


                     <tr>
                        <td>vPLL L2BR</td>
                        <td>Pair</td>
                        <td>{{ $region->kl_vpll_l2br ?? '0' }} </td>
                        <td> <div class="input-group">
                                    <input name="" 
                                           class="form-control bg-light text-muted" 
                                           value=""
                                           disabled
                                           style="cursor: not-allowed;">
                                </div></td>
                    </tr>


                     <tr>
                        <td>NAT Gateway (Small)</td>
                        <td>Unit</td>
                        <td> {{ $region->kl_nat_gateway_small ?? '0' }}</td>
                        <td>{{ $region->cyber_nat_gateway_small ?? '0' }}</td>
                    </tr>


                      <tr>
                        <td>NAT Gateway (Medium)</td>
                        <td>Unit</td>
                      <td>{{ $region->kl_nat_gateway_medium ?? '0' }}</td>
                        <td>{{ $region->cyber_nat_gateway_medium ?? '0' }}</td>
                    </tr>
                  

                      <tr>
                        <td>NAT Gateway (Large)</td>
                        <td>Unit</td>
                        <td>{{ $region->kl_nat_gateway_large ?? '0' }}</td>
                        <td>{{ $region->cyber_nat_gateway_large ?? '0' }}</td>
                    </tr>

                      <tr>
                        <td>NAT Gateway (Extra-Large)</td>
                        <td>Unit</td>
                       <td>{{ $region->kl_nat_gateway_xlarge ?? '0' }}</td>
                        <td>{{ $region->cyber_nat_gateway_xlarge ?? '0' }}</td>
                    </tr>


                        <tr>
                        <td>Managed Global Server Load Balancer (GSLB)</td>
                        <td>Domain</td>
                      <td>{{ $security_service->kl_gslb ?? '0' }}</td>
                        <td>{{ $security_service->cyber_gslb ?? '0' }}</td>
                    </tr>


                    <tr>
                        <td style="background-color: #e76ccf;font-weight: bold;">Additional Services</td>
                        <td style="background-color: #e76ccf;font-weight: bold;"></td>
                        <td style="background-color: #e76ccf;font-weight: bold;"></td>
                        <td style="background-color: #e76ccf;font-weight: bold;"></td>
                    </tr> 




                      <tr class="table-light">
                    <tr>
                        <th>Monitoring Service</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
                </tr>
                    

                        <tr>
                        <td>TCS inSight vMonitoring</td>
                        <td>Unit</td>
                      <td> {{ $security_service->kl_insight_vmonitoring ?? '0' }}</td>
                        <td>{{ $security_service->cyber_insight_vmonitoring ?? '0' }}</td>
                    </tr>


                    <tr class="table-light">
                    <tr>
                        <th>Security Services</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
                </tr>
                    <tr>
                        <td>Cloud Vulnerability Assessment (Per IP)</td>
                        <td>Mbps</td>
                      <td>{{ $security_service->kl_cloud_vulnerability ?? '0' }}</td>
                        <td>{{ $security_service->cyber_cloud_vulnerability ?? '0' }}</td>
                    </tr>




                 
                        <tr class="table-light">
                    <tr>
                        <th>Cloud Security</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
                </tr>
                    <tr>
                        <td>Cloud Firewall (Fortigate)</td>
                        <td>Unit</td>
                      <td>{{ $security_service->kl_firewall_fortigate ?? '0' }}</td>
                        <td>{{ $security_service->cyber_firewall_fortigate ?? '0' }}</td>
                    </tr>

                      <tr>
                        <td>Cloud Firewall (OPNSense)</td>
                        <td>Unit</td>
                      <td>{{ $security_service->kl_firewall_opnsense ?? '0' }}</td>
                        <td>{{ $security_service->cyber_firewall_opnsense ?? '0' }}</td>
                    </tr>


                     <tr>
                        <td>Cloud Shared WAF (Mbps)</td>
                        <td>Mbps</td>
                      <td>{{ $security_service->kl_shared_waf ?? '0' }}</td>
                        <td>{{ $security_service->cyber_shared_waf ?? '0' }}</td>
                    </tr>


                     <tr>
                        <td>Anti-Virus (Panda)</td>
                        <td>Unit</td>
                      <td>{{ $security_service->kl_antivirus ?? '0' }}</td>
                        <td>{{ $security_service->cyber_antivirus ?? '0' }}</td>
                    </tr>


                    
                


                  
                  
                  
                 
                 








<tr>
    <td style="background-color: #e76ccf; font-weight: bold;">Storage</td>
    <td style="background-color: #e76ccf; font-weight: bold;"></td>
    <td style="background-color: #e76ccf; font-weight: bold;"></td>
    <td style="background-color: #e76ccf; font-weight: bold;"></td>
</tr>



                     <tr class="table-light">
                    <tr>
                        <th>Storage Type</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
                </tr>


                    <tr>
                        <td>Elastic Volume Service (EVS)</td>
                        <td>GB</td>
                        <td>{{ $evs_kl }}</td>
                        <td>{{ $evs_cyber }}</td>
                     </tr>



                    <tr>
                        <td>Scalable File Service (SFS)</td>
                        <td>GB</td>
                        <td>{{ $region->kl_scalable_file_service ?? '0' }}</td>
                        <td>{{ $region->cyber_scalable_file_service ?? '0' }}</td>
                     </tr>

                     <tr>
                        <td>Object Storage Service (OBS)</td>
                        <td>GB</td>
                        <td>{{ $region->kl_object_storage_service ?? '0' }}</td>
                        <td>{{ $region->cyber_object_storage_service ?? '0' }}</td>
                     </tr>


               
                            <tr class="table-light">
                    <tr>
                        <th>Image Management Service (IMS)</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
                </tr>


                <tr>
    <td>Snapshot Storage</td>
    <td>GB</td>
    <td>{{ $snapshotStorageKL }}</td>
    <td>{{ $snapshotStorageCyber }}</td>
</tr>
<tr>
    <td>Image Storage</td>
    <td>GB</td>
    <td>{{ $imageStorageKL }}</td>
    <td>{{ $imageStorageCyber }}</td>
</tr>







<tr>
    <td style="background-color: #e76ccf; font-weight: bold;">Backup and DR</td>
    <td style="background-color: #e76ccf; font-weight: bold;"></td>
    <td style="background-color: #e76ccf; font-weight: bold;"></td>
    <td style="background-color: #e76ccf; font-weight: bold;"></td>
</tr>


          
                   <tr class="table-light">
    <tr>
        <th>Backup Service in VPC</th>
        <th>Unit</th>
        <th>KL.Qty</th>
        <th>CJ.Qty</th>
    </tr>
</tr>



                    <tr>
                        <td>Cloud Server Backup Service - Full Backup Capacity</td>
                        <td>GB</td>
                        <td>0</td>
                       <td>0</td>
                     </tr>


                      <tr>
                        <td>Cloud Server Backup Service - Incremental Backup Capacity</td>
                        <td>GB</td>
                        <td>0</td>
                       <td>0</td>
                     </tr>

                      <tr>
                        <td>Cloud Server Replication Service - Retention Capacity</td>
                        <td>GB</td>
                        <td>0</td>
                       <td>0</td>
                     </tr>


              
                    <tr class="table-light">
    <tr>
        <th>Disaster Recovery in VPC</th>
        <th>Unit</th>
        <th>KL.Qty</th>
        <th>CJ.Qty</th>
    </tr>
</tr>



                    <tr>
                        <td>Cold DR Days</td>
                        <td>Days</td>
                        <td>{{ $region->kl_dr_activation_days ?? '0' }}</td>
                       <td>{{ $region->cyber_dr_activation_days ?? '0' }}</td>
                     </tr>


                       <tr>
                        <td>Cold DR - Seeding VM</td>
                        <td>Days</td>
                         <td>0</td>
                        <td>0</td>
                     </tr>

                        <tr>
                        <td>Cloud Server Disaster Recovery Storage</td>
                        <td>GB</td>
                        <td>0</td>
                         <td>0</td>
                     </tr>

                        <tr>
                        <td>Cloud Server Disaster Recovery Replication</td>
                        <td>Unit</td>
                        <td>0</td>
                          <td>0</td>
                     </tr>

                      <tr>
                        <td>Cloud Server Disaster Recovery Days (DR Declaration)</td>
                        <td>Days</td>
                        <td>{{ $region->kl_dr_activation_days ?? '0' }}</td>
                       <td>{{ $region->cyber_dr_activation_days ?? '0' }}</td>
                     </tr>

                        <tr>
                        <td>Cloud Server Disaster Recovery Managed Service -Per Day</td>
                        <td>Unit</td>
                          <td>0</td>
                            <td>0</td>
                     </tr>



                               
                                        <tr class="table-light">
    <tr>
        <th>Disaster Recovery Network and Security</th>
        <th>Unit</th>
        <th>KL.Qty</th>
        <th>CJ.Qty</th>
    </tr>
</tr>



                    <tr>
                        <td>Cloud Server Disaster Recovery (vPLL)</td>
                        <td>Mbps</td>
                        <td>0</td>
                       <td>0</td>
                     </tr>


                      <tr>
                        <td>DR Elastic IP</td>
                        <td>Unit Per Day</td>
                        <td>0</td>
                       <td>0</td>
                     </tr>

                        <tr>
                        <td>DR Bandwidth</td>
                        <td>Mbps Per Day</td>
                        <td>0</td>
                       <td>0</td>
                     </tr>

                      <tr>
                        <td>DR Bandwidth + AntiDDoS</td>
                        <td>Mbps Per Day</td>
                        <td>0</td>
                       <td>0</td>
                     </tr>

                      <tr>
                        <td>DR Cloud Firewall (Fortigate)</td>
                        <td>Unit Per Day</td>
                        <td>0</td>
                       <td>0</td>
                     </tr>

                       <tr>
                        <td>DR Cloud Firewall (OPNSense)</td>
                        <td>Unit Per Day</td>
                        <td>0</td>
                       <td>0</td>
                     </tr>



               











<tr>
    <td style="background-color: #e76ccf; font-weight: bold;">Computing</td>
    <td style="background-color: #e76ccf; font-weight: bold;"></td>
    <td style="background-color: #e76ccf; font-weight: bold;"></td>
    <td style="background-color: #e76ccf; font-weight: bold;"></td>
</tr>


                
                    <tr class="table-light">
    <tr>
        <th>Compute - Elastic Cloud Server (ECS)</th>
        <th>Sizing</th>
        <th>KL.Qty</th>
        <th>CJ.Qty</th>
    </tr>
</tr>
<tbody>
    @php
        $ecsList = [
           ['name' => 'm3.micro', 'vcpu' => 1, 'vram' => 1],
        ['name'=> 'm3.small', 'vcpu'=>1, 'vram'=> 2 ],
        ['name' => 'c3.large', 'vcpu' => 2, 'vram' => 4 ],
        ['name'=> 'm3.large', 'vcpu' => 2, 'vram' => 8 ],
        ['name'=> 'r3.large', 'vcpu' => 2, 'vram' => 16 ],
        ['name' => 'c3.xlarge', 'vcpu' => 4, 'vram'=> 8 ],
        ['name'=> 'm3.xlarge', 'vcpu' => 4, 'vram' => 16 ],
        ['name' => 'r3.xlarge', 'vcpu'=> 4, 'vram'=> 32 ],
        
        ['name' => 'c3.2xlarge', 'vcpu'=> 8, 'vram'=> 16 ],
        ['name' => 'm3.2xlarge', 'vcpu'=> 8, 'vram'=> 32 ],
        ['name'=> 'r3.2xlarge', 'vcpu'=> 8, 'vram'=> 64 ],
        ['name'=> 'm3.3xlarge', 'vcpu'=> 12, 'vram'=> 48 ],

        ['name' => 'c3.4xlarge', 'vcpu' => 16, 'vram' => 32],
    ['name' => 'm3.4xlarge', 'vcpu' => 16, 'vram' => 64],
    ['name' => 'r3.4xlarge', 'vcpu' => 16, 'vram' => 128],
    ['name' => 'm3.6xlarge', 'vcpu' => 24, 'vram' => 96],
    ['name' => 'c3.8xlarge', 'vcpu' => 32, 'vram' => 64],
    ['name' => 'm3.8xlarge', 'vcpu' => 32, 'vram' => 128],
    ['name' => 'r3.8xlarge', 'vcpu' => 32, 'vram' => 256],
    ['name' => 'r3.12xlarge', 'vcpu' => 48, 'vram' => 384],
    ['name' => 'c3.16xlarge', 'vcpu' => 64, 'vram' => 128],
    ['name' => 'm3.16xlarge', 'vcpu' => 64, 'vram' => 256],
    ['name' => 'r3.16xlarge', 'vcpu' => 64, 'vram' => 512],
    ['name' => 'c3p.xlarge', 'vcpu' => 4, 'vram' => 8],
    ['name' => 'm3p.xlarge', 'vcpu' => 4, 'vram' => 16],
    ['name' => 'r3p.xlarge', 'vcpu' => 4, 'vram' => 32],
    ['name' => 'c3p.2xlarge', 'vcpu' => 8, 'vram' => 16],
    ['name' => 'm3p.2xlarge', 'vcpu' => 8, 'vram' => 32],
    ['name' => 'r3p.2xlarge', 'vcpu' => 8, 'vram' => 64],
    ['name' => 'm3p.3xlarge', 'vcpu' => 12, 'vram' => 48],
    ['name' => 'c3p.4xlarge', 'vcpu' => 16, 'vram' => 32],
    ['name' => 'm3p.4xlarge', 'vcpu' => 16, 'vram' => 64],
    ['name' => 'r3p.4xlarge', 'vcpu' => 16, 'vram' => 64],
    ['name' => 'm3p.6xlarge', 'vcpu' => 24, 'vram' => 96],
    ['name' => 'c3p.8xlarge', 'vcpu' => 32, 'vram' => 64],
    ['name' => 'm3p.8xlarge', 'vcpu' => 32, 'vram' => 128],
    ['name' => 'r3p.8xlarge', 'vcpu' => 32, 'vram' => 128],
    ['name' => 'm3p.12xlarge', 'vcpu' => 48, 'vram' => 192],
    ['name' => 'r3p.12xlarge', 'vcpu' => 48, 'vram' => 384],
    ['name' => 'm3p.16xlarge', 'vcpu' => 64, 'vram' => 256],
    ['name' => 'r3p.16xlarge', 'vcpu' => 64, 'vram' => 512],
   
    ['name' => 'm3gnt4.xlarge', 'vcpu' => 4, 'vram' => 16],
    ['name' => 'm3gnt4.2xlarge', 'vcpu' => 8, 'vram' => 32],
    ['name' => 'm3gnt4.4xlarge', 'vcpu' => 16, 'vram' => 64],
    ['name' => 'm3gnt4.8xlarge', 'vcpu' => 32, 'vram' => 128],
    ['name' => 'm3gnt4.16xlarge', 'vcpu' => 64, 'vram' => 256],
   
        ];
    @endphp

    @foreach ($ecsList as $ecs)
        <tr>
            <td>{{ $ecs['name'] }}</td>
            <td>{{ $ecs['vcpu'] }} core, {{ $ecs['vram'] }} GB</td>
            <td> 0</td>
            <td>0</td>
        </tr>
    @endforeach





<tr>
    <td style="background-color: #e76ccf; font-weight: bold;">License</td>
    <td style="background-color: #e76ccf; font-weight: bold;"></td>
    <td style="background-color: #e76ccf; font-weight: bold;"></td>
    <td style="background-color: #e76ccf; font-weight: bold;"></td>
</tr>


                  
                        <tr class="table-light">
                    <tr>
                        <th>Microsoft</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
                </tr>


                    <tr>
                        <td>Microsoft Windows Server (Core Pack) - Standard</td>
                        <td>Unit</td>
                        <td>0</td>
                       <td>0</td>
                     </tr>




                    <tr>
                        <td>Microsoft Windows Server (Core Pack) - Data Center</td>
                        <td>Unit</td>
                        <td>0</td>
                       <td>0</td>
                     </tr>



                    <tr>
                        <td>Microsoft Remote Desktop Services (SAL)</td>
                        <td>Unit</td>
                        <td>0</td>
                       <td>0</td>
                     </tr>




                    <tr>
                        <td>Microsoft SQL (Web) (Core Pack)</td>
                        <td>Unit</td>
                        <td>0</td>
                       <td>0</td>
                     </tr>




                    <tr>
                        <td>Microsoft SQL (Standard) (Core Pack)</td>
                        <td>Unit</td>
                        <td>0</td>
                       <td>0</td>
                     </tr>


                    <tr>
                        <td>Microsoft SQL (Enterprise) (Core Pack)</td>
                        <td>Unit</td>
                        <td>0</td>
                       <td>0</td>
    </tr>
     <tr>
                        <td style="background-color: #e76ccf;font-weight: bold;">Non-Standard Item Services</td>
                        <td style="background-color: #e76ccf;font-weight: bold;"></td>
                        <td style="background-color: #e76ccf;font-weight: bold;"></td>
                        <td style="background-color: #e76ccf;font-weight: bold;"></td>
                    </tr> 



                     <tr>
                        <td>Item</td>
                        <td>Name</td>
                        <td>-</td>
                       <td>-</td>
                     </tr>


                       <tr>
                        <td></td>
                        <td>Unit</td>
                        <td>-</td>
                       <td>-</td>
                     </tr>





                       <tr>
                        <td></td>
                        <td>Quantity</td>
                        <td>-</td>
                       <td>-</td>
                     </tr>


</tbody>


                </tbody>
            </table>
        </div>