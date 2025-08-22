<style>
    table.internal-summary {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 20px;
        font-size: 10px;
    }

    table.internal-summary th, table.internal-summary td {
        border: 1px solid #000;
        padding: 4px;
        text-align: center;
    }

    .section-header {
        background-color: #e76ccf;
        font-weight: bold;
        text-align: left;
        padding: 5px;
        margin-top: 20px;
        font-size: 11px;
    }
</style>

<!-- PROFESSIONAL SERVICES -->
<table class="internal-summary">
 
    <tr>
        <th style="background-color: #e76ccf; color: white;">
            Professional Services
        </th>
        <th style="background-color: #e76ccf; color: white;">Unit</th>
        <th style="background-color: #e76ccf; color: white;">KL.Qty</th>
        <th style="background-color: #e76ccf; color: white;">CJ.Qty</th>
    </tr>

    <tbody>
        <tr>
            <td>Professional Services (ONE TIME Provisioning)</td>
            <td>Days</td>
            <td colspan="2">{{ $region->mandays ?? '0' }}</td>
        </tr>
        <tr>
            <td>Migration Tools One Time Charge</td>
            <td>Unit Per Month*</td>
                                   <td>{{ $licenseTotal }} Unit</td>
                        <td>{{ $durationTotal }} Months</td>
        </tr>
    </tbody>


<!-- MANAGED SERVICES -->

    <tr>
        <th style="background-color: #e76ccf; color: white;">
            Managed Services
        </th>
         <th style="background-color: #e76ccf; color: white;">Unit</th>
        <th style="background-color: #e76ccf; color: white;">KL.Qty</th>
        <th style="background-color: #e76ccf; color: white;">CJ.Qty</th>
    </tr>

    <tbody>
        <tr><td>Managed Operating System</td><td>VM</td><td>{{ $managed_operating_system_kl }}</td><td>{{ $managed_operating_system_cyber }}</td></tr>
        <tr><td>Managed Backup and Restore</td><td>VM</td><td>{{ $managed_backup_and_restore_kl }}</td><td>{{ $managed_backup_and_restore_cyber }}</td></tr>
        <tr><td>Managed Patching</td><td>VM</td><td>{{ $managed_patching_kl }}</td><td>{{ $managed_patching_cyber }}</td></tr>
        <tr><td>Managed DR</td><td>VM</td><td>{{ $managed_dr_kl }}</td><td>{{ $managed_dr_cyber }}</td></tr>
    </tbody>


<!-- NETWORK -->

    <tr>
        <th style="background-color: #e76ccf; color: white;">
            Network
        </th>
         <th style="background-color: #e76ccf; color: white;">Unit</th>
        <th style="background-color: #e76ccf; color: white;" >KL.Qty</th>
        <th style="background-color: #e76ccf; color: white;">CJ.Qty</th>
    </tr>
   

    <tbody>
        <tr><td>Bandwidth</td><td>Mbps</td><td>{{ $region->kl_bandwidth ?? '0' }}</td><td>{{ $region->cyber_bandwidth ?? '0' }}</td></tr>
        <tr><td>Bandwidth with Anti-DDoS</td><td>Mbps</td><td>{{ $region->kl_bandwidth_with_antiddos ?? '0' }}</td><td>{{ $region->cyber_bandwidth_with_antiddos ?? '0' }}</td></tr>
        <tr><td>Included Elastic IP (FOC)</td><td>Unit</td><td>{{ $region->kl_included_elastic_ip ?? '0' }}</td><td>{{ $region->cyber_included_elastic_ip ?? '0' }}</td></tr>
        <tr><td>Elastic IP</td><td>Unit</td><td>{{ $region->kl_elastic_ip ?? '0' }}</td><td>{{ $region->cyber_elastic_ip ?? '0' }}</td></tr>
        <tr><td>Elastic Load Balancer (External)</td><td>Unit</td><td>{{ $region->kl_elastic_load_balancer ?? '0' }}</td><td>{{ $region->cyber_elastic_load_balancer ?? '0' }}</td></tr>
        <tr><td>Direct Connect Virtual Gateway</td><td>Unit</td><td>{{ $region->kl_direct_connect_virtual ?? '0' }}</td><td>{{ $region->cyber_direct_connect_virtual ?? '0' }}</td></tr>
        <tr><td>L2BR instance</td><td>Unit</td><td>{{ $region->kl_l2br_instance ?? '0' }}</td><td>{{ $region->cyber_l2br_instance ?? '0' }}</td></tr>
        <tr><td>Virtual Private Leased Line (vPLL)</td><td>Mbps</td><td>{{ $region->kl_virtual_private_leased_line ?? '0' }}</td><td>-</td></tr>
        <tr><td>vPLL L2BR</td><td>Pair</td><td>{{ $region->kl_vpll_l2br ?? '0' }}</td><td>-</td></tr>
        <tr><td>NAT Gateway (Small)</td><td>Unit</td><td> {{ $region->kl_nat_gateway_small ?? '0' }}</td><td>{{ $region->cyber_nat_gateway_small ?? '0' }}</td></tr>
        <tr><td>NAT Gateway (Medium)</td><td>Unit</td><td>{{ $region->kl_nat_gateway_medium ?? '0' }}</td><td>{{ $region->cyber_nat_gateway_medium ?? '0' }}</td></tr>
        <tr><td>NAT Gateway (Large)</td><td>Unit</td><td>{{ $region->kl_nat_gateway_large ?? '0' }}</td><td>{{ $region->cyber_nat_gateway_large ?? '0' }}</td></tr>
        <tr><td>NAT Gateway (Extra-Large)</td><td>Unit</td><td>{{ $region->kl_nat_gateway_xlarge ?? '0' }}</td><td>{{ $region->cyber_nat_gateway_xlarge ?? '0' }}</td></tr>
        <tr><td>Managed Global Server Load Balancer (GSLB)</td><td>Domain</td><td>{{ $security_service->kl_gslb ?? '0' }}</td><td>{{ $security_service->cyber_gslb ?? '0' }}</td></tr>
    
        </tbody>




<!-- Computing -->
 
 
    <tr>
        <th colspan="4" style="background-color: #e76ccf; color: white; text-align: left;">
            Computing
        </th>
    </tr>

    <tr>
        <th>Compute - Elastic Cloud Server (ECS)</th>
        <th>Sizing</th>
        <th>KL.Qty</th>
        <th>CJ.Qty</th>
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
    @php
        $klQty = $ecsFlavourCount[$ecs['name']]['kl'] ?? 0;
        $cjQty = $ecsFlavourCount[$ecs['name']]['cyber'] ?? 0;
        $hasQty = $klQty > 0 || $cjQty > 0;
    @endphp

    @if ($hasQty)
        <tr >
            <td>{{ $ecs['name'] }}</td>
            <td>{{ $ecs['vcpu'] }} core, {{ $ecs['vram'] }} GB</td>
            <td>{{ $klQty }}</td>
            <td>{{ $cjQty }}</td>
        </tr>
    @endif
@endforeach

    


    </tbody>





<!-- License -->
 
    <tr>
        <th colspan="4" style="background-color: #e76ccf; color: white; text-align: left;">
            License
        </th>
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



  
                    <tr>
                        <th>Red Hat Enterprise License</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
             


                   <tr>
    <td>RHEL (1-8vCPU)</td>
    <td>Unit</td>
    <td>{{ $licenseSummary['rhel_small']['Kuala Lumpur'] ?? 0 }}</td>
    <td>{{ $licenseSummary['rhel_small']['Cyberjaya'] ?? 0 }}</td>
</tr>

<tr>
    <td>RHEL (9-127vCPU)</td>
    <td>Unit</td>
    <td>{{ $licenseSummary['rhel_large']['Kuala Lumpur'] ?? 0 }}</td>
    <td>{{ $licenseSummary['rhel_large']['Cyberjaya'] ?? 0 }}</td>
</tr>





                



<!-- STORAGE -->
 
    <tr>
        <th colspan="4" style="background-color: #e76ccf; color: white; text-align: left;">
            Storage
        </th>
    </tr>
    <tr>
        <th>Storage Type</th>
        <th>Unit</th>
        <th>KL.Qty</th>
        <th>CJ.Qty</th>
    </tr>
 

    <tbody>
        <tr><td>Elastic Volume Service (EVS)</td><td>GB</td><td>{{ $evs_kl }}</td><td>{{ $evs_cyber }}</td></tr>
        <tr><td>Scalable File Service (SFS)</td><td>GB</td><td>{{ $region->kl_scalable_file_service ?? '0' }}</td><td>{{ $region->cyber_scalable_file_service ?? '0' }}</td></tr>
        <tr><td>Object Storage Service (OBS)</td><td>GB</td><td>{{ $region->kl_object_storage_service ?? '0' }}</td><td>{{ $region->cyber_object_storage_service ?? '0' }}</td></tr>
    </tbody>

                
                    <tr>
                        <th>Image Management Service (IMS)</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
               
<tbody>

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
</tbody>





<!-- Backup and DR -->
 
    <tr>
        <th colspan="4" style="background-color: #e76ccf; color: white; text-align: left;">
            Backup and DR
        </th>
    </tr>
    <tr>
        <th>Backup Service in VPC</th>
        <th>Unit</th>
        <th>KL.Qty</th>
        <th>CJ.Qty</th>
    </tr>




<tbody>
                    

                     <tr>
    <td>Cloud Server Backup Service - Full Backup Capacity</td>
    <td>GB</td>
    <td>{{ $fullBackupKL }}</td>
    <td>{{ $fullBackupCyber }}</td>
</tr>
<tr>
    <td>Cloud Server Backup Service - Incremental Backup Capacity</td>
    <td>GB</td>
    <td>{{ $incrementalBackupKL }}</td>
    <td>{{ $incrementalBackupCyber }}</td>
</tr>


                    

                     <tr>
    <td>Cloud Server Replication Service - Retention Capacity</td>
    <td>GB</td>
    <td>{{ $retentionKL }}</td>
    <td>{{ $retentionCyber }}</td>
</tr>

</tbody>

                
    <tr>
        <th>Disaster Recovery in VPC</th>
        <th>Unit</th>
        <th>KL.Qty</th>
        <th>CJ.Qty</th>
    </tr>
 
<tbody>


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
    <td>{{ $csdrNeededKL }}</td>
    <td>{{ $csdrNeededCyber }}</td>
</tr>

                     

                                          @if ($csdrNeededKL > 0 || $csdrNeededCyber > 0)
<tr>
    <td>Cloud Server Disaster Recovery Days (DR Declaration)</td>
    <td>Days</td>
    <td>{{ $region->kl_dr_activation_days ?? '0' }}</td>
    <td>{{ $region->cyber_dr_activation_days ?? '0' }}</td>
</tr>
@endif

                                   <tr>
    <td>Cloud Server Disaster Recovery Managed Service Per Day</td>
    <td>Unit</td>
    <td>{{ $csdrManagedPerDayKL }}</td>
    <td>{{ $csdrManagedPerDayCyber }}</td>
</tr>


</tbody>

                               
    <tr>
        <th>Disaster Recovery Network and Security</th>
        <th>Unit</th>
        <th>KL.Qty</th>
        <th>CJ.Qty</th>
    </tr>
 

<tbody>

                

                     <tr>
    <td>Cloud Server Disaster Recovery (vPLL)</td>
    <td>Mbps</td>
    <td>{{ $vpllQtyKL }}</td>
    <td>{{ $vpllQtyCyber }}</td>
</tr>



                      <tr>
                        <td>DR Elastic IP</td>
                        <td>Unit Per Day</td>
                        <td>{{ $region->kl_elastic_ip_dr ?? '0' }}</td>
                       <td>{{ $region->cyber_elastic_ip_dr ?? '0' }}</td>
                     </tr>

                     <tr>
    <td>DR Bandwidth</td>
    <td>Mbps Per Day</td>
    <td>{{ $drBandwidthKL }}</td>
    <td>{{ $drBandwidthCyber }}</td>
</tr>

<tr>
    <td>DR Bandwidth + AntiDDoS</td>
    <td>Mbps Per Day</td>
    <td>{{ $drBandwidthAntiddosKL }}</td>
    <td>{{ $drBandwidthAntiddosCyber }}</td>
</tr>


                      
                     <tr>
    <td>DR Cloud Firewall (Fortigate)</td>
    <td>Unit Per Day</td>
     <td>{{ $drFortigateKL }}</td>
    <td>{{ $drFortigateCyber }}</td>
</tr>

<tr>
    <td>DR Cloud Firewall (OPNSense)</td>
    <td>Unit Per Day</td>
    <td>{{ $drOpnSenseKL }}</td>
    <td>{{ $drOpnSenseCyber }}</td>
</tr>

    <tr>
        <th>Disaster Recovery Resources (During DR)</th>
        <th>Unit</th>
        <th>KL.Qty</th>
        <th>CJ.Qty</th>
    </tr>




                    

                        <tr>
                        <td>DR Elastic Volume Service(EVS)</td>
                        <td>GB</td>
                           <td>{{ $evs_kl*2}}</td>
                        <td>{{ $evs_cyber*2 }}</td>
                     </tr>

                     
    
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
    @php
        $klQty = $ecsFlavourCount[$ecs['name']]['kl'] ?? 0;
        $cjQty = $ecsFlavourCount[$ecs['name']]['cyber'] ?? 0;
        $hasQty = $klQty > 0 || $cjQty > 0;
    @endphp

    @if ($hasQty)
        <tr>
            <td>{{ $ecs['name'] }}</td>
            <td>{{ $ecs['vcpu'] }} vCPU, {{ $ecs['vram'] }} vRAM</td>
            <td>{{ $klQty }}</td>
            <td>{{ $cjQty }}</td>
        </tr>
    @endif
@endforeach


 <tr>
                        <th>Disaster Recovery Licenses</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>

                    @php
    $klMonth = ($region->kl_duration ?? 0) > 0 ? 1 : 0;
    $cyberMonth = ($region->cyber_duration ?? 0) > 0 ? 1 : 0;
@endphp

<tr>
    <td>License Month</td>
    <td>Month(s)</td>
    <td>{{ $klMonth }}</td>     {{-- Kuala Lumpur --}}
    <td>{{ $cyberMonth }}</td>  {{-- Cyberjaya --}}
</tr>



                       <tr>
    <td>Microsoft Windows Server (Core Pack) - Standard</td>
    <td>Unit Per Month</td>
    <td>{{ $licenseSummary['windows_std']['Kuala Lumpur'] ?? 0 }}</td>
    <td>{{ $licenseSummary['windows_std']['Cyberjaya'] ?? 0 }}</td>
</tr>

<tr>
    <td>Microsoft Windows Server (Core Pack) - Data Center</td>
    <td>Unit Per Month</td>
    <td>{{ $licenseSummary['windows_dc']['Kuala Lumpur'] ?? 0 }}</td>
    <td>{{ $licenseSummary['windows_dc']['Cyberjaya'] ?? 0 }}</td>
</tr>

<tr>
    <td>Microsoft Remote Desktop Services (SAL)</td>
    <td>Unit Per Month</td>
    <td>{{ $licenseSummary['rds']['Kuala Lumpur'] ?? 0 }}</td>
    <td>{{ $licenseSummary['rds']['Cyberjaya'] ?? 0 }}</td>
</tr>

<tr>
    <td>Microsoft SQL (Web) (Core Pack)</td>
      <td>Unit Per Month</td>
    <td>{{ $licenseSummary['sql_web']['Kuala Lumpur'] ?? 0 }}</td>
    <td>{{ $licenseSummary['sql_web']['Cyberjaya'] ?? 0 }}</td>
</tr>

<tr>
    <td>Microsoft SQL (Standard) (Core Pack)</td>
      <td>Unit Per Month</td>
    <td>{{ $licenseSummary['sql_std']['Kuala Lumpur'] ?? 0 }}</td>
    <td>{{ $licenseSummary['sql_std']['Cyberjaya'] ?? 0 }}</td>
</tr>

<tr>
    <td>Microsoft SQL (Enterprise) (Core Pack)</td>
    <td>Unit Per Month</td>
    <td>{{ $licenseSummary['sql_ent']['Kuala Lumpur'] ?? 0 }}</td>
    <td>{{ $licenseSummary['sql_ent']['Cyberjaya'] ?? 0 }}</td>
</tr>
             


                   <tr>
    <td>RHEL (1-8vCPU)</td>
    <td>Unit Per Month</td>
    <td>{{ $licenseSummary['rhel_small']['Kuala Lumpur'] ?? 0 }}</td>
    <td>{{ $licenseSummary['rhel_small']['Cyberjaya'] ?? 0 }}</td>
</tr>

<tr>
    <td>RHEL (9-127vCPU)</td>
    <td>Unit Per Month</td>
    <td>{{ $licenseSummary['rhel_large']['Kuala Lumpur'] ?? 0 }}</td>
    <td>{{ $licenseSummary['rhel_large']['Cyberjaya'] ?? 0 }}</td>
</tr>



</tbody>





<!-- License -->
 
    <!---<tr>
        <th colspan="4" style="background-color: #e76ccf; color: white; text-align: left;">
            License
        </th>
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



  
                    <tr>
                        <th>Red Hat Enterprise License</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
             


                   <tr>
    <td>RHEL (1-8vCPU)</td>
    <td>Unit</td>
    <td>{{ $licenseSummary['rhel_small']['Kuala Lumpur'] ?? 0 }}</td>
    <td>{{ $licenseSummary['rhel_small']['Cyberjaya'] ?? 0 }}</td>
</tr>

<tr>
    <td>RHEL (9-127vCPU)</td>
    <td>Unit</td>
    <td>{{ $licenseSummary['rhel_large']['Kuala Lumpur'] ?? 0 }}</td>
    <td>{{ $licenseSummary['rhel_large']['Cyberjaya'] ?? 0 }}</td>
</tr>


    </tbody>--->





<!-- Additional Services -->

    <tr>
        <th colspan="4" style="background-color: #e76ccf; color: white; text-align: left;">
            Additional Services
        </th>
    </tr>
    <tr>
        <th>Monitoring Service</th>
        <th>Unit</th>
        <th>KL.Qty</th>
        <th>CJ.Qty</th>
    </tr>

    <tbody>

                        <tr>
    <td>TCS inSight vMonitoring</td>
    <td>Unit</td>
    <td>{{ ($security_service->kl_insight_vmonitoring ?? 'No') == 'Yes' ? 1 : 0 }}</td>
    <td>{{ ($security_service->cyber_insight_vmonitoring ?? 'No') == 'Yes' ? 1 : 0 }}</td>
</tr>
</tbody>


                    <tr>
                        <th>Security Services</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>

    <tbody>
     <tr>
                        <td>Cloud Vulnerability Assessment (Per IP)</td>
                        <td>Mbps</td>
                      <td>{{ $security_service->kl_cloud_vulnerability ?? '0' }}</td>
                        <td>{{ $security_service->cyber_cloud_vulnerability ?? '0' }}</td>
                    </tr>
</tbody>

                     
                    <tr>
                        <th>Cloud Security</th>
                        <th>Unit</th>
                        <th>KL.Qty</th>
                        <th>CJ.Qty</th>
                    </tr>
                
                <tbody>
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


</tbody>

                    

<!-- Non-Standard Item Services -->

    <tr>
        <th colspan="4" style="background-color: #e76ccf; color: white; text-align: left;">
            Non-Standard Item Services
        </th>
    </tr>
   


<tbody>
                     
@if(!empty($nonStandardItems) && count($nonStandardItems))

    
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
                <td>{{ number_format($item->selling_price, 2) }}</td>
            </tr>
        @endforeach
 
@endif





</tbody>
   
</table>














