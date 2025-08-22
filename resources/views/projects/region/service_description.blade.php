@extends('layouts.app')

@section('content')
<div class="container py-4">




    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Service Descriptions</h1>
    
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="serviceTabContent">
        <!-- Professional Services Tab -->
        <div class="tab-pane fade show active" id="professional" role="tabpanel">
            <div class="card shadow-sm mb-4">
                <div class="card-header text-white" style="background-color: #FF82E6;">
                    <h5 class="mb-0">Professional Services</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="30%">Professional Service</th>
                                    <th width="56%">Description</th>
                                    <th width="30%">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Professional Services (ONE TIME Provisioning)</td>
                                    <td>Customer can opt for the professional services to carry out services such as:<br>
                                        1) First time initial deployment<br>
                                        2) Migration
                                    </td>
                                    <td>Scope of work to be defined by the team</td>
                                </tr>
                                <tr>
                                    <td  class="text-danger">TSAM Setup One Time Charge</td>
                                    <td  class="text-danger">N/A for the time being due to commercial adjustment for this service
                                    </td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade show active" id="professional" role="tabpanel">
            <div class="card shadow-sm mb-4">
                <div class="card-header text-white" style="background-color: #FF82E6;">
                    <h5 class="mb-0">Migration Tools One Time Charge</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="30%">Managed Service</th>
                                    <th width="56%"></th>
                                    <th width="30%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Managed Operating System</td>
                                    <td>Details in column L & M includes:<br>
                                        -Incident Management<br>
                                        -Problem Management<br>
                                        -Change Management<br>
                                        -OS Management 
                                    </td>
                                    <td>Details to refer to MS Product Hanbook</td>
                                </tr>
                                <tr>
                                    <td>Managed Backup and Restore</td>
                                    <td>Details in column L & M includes:<br>
                                        -Incident Management<br>
                                        -Problem Management<br>
                                        -Change Management<br>
                                        -Backup Management<br>
                                        -OS Management 
                                    </td>
                                    <td>Details to refer to MS Product Hanbook</td>
                                </tr>
                                  <tr>
                                    <td>Managed Patching</td>
                                    <td>Details in column L & M includes:<br>
                                        -Incident Management<br>
                                        -Problem Management<br>
                                        -Change Management<br>
                                        -Patch Management 
                                    </td>
                                    <td>Details to refer to MS Product Hanbook</td>
                                </tr>

                                  <tr>
                                    <td>Managed DR</td>
                                    <td>Details in column L & M includes:<br>
                                        -Incident Management<br>
                                        -Problem Management<br>
                                        -Change Management<br>
                                        -DR Management 
                                    </td>
                                    <td>Details to refer to MS Product Hanbook</td>
                                </tr>




                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


<div class="tab-pane fade show active" id="professional" role="tabpanel">
            <div class="card shadow-sm mb-4">
                <div class="card-header text-white" style="background-color: #FF82E6;">
                    <h5 class="mb-0">Global Cloud Shared Services</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="30%">Global Cloud Shared Services</th>
                                    <th width="56%">Description</th>
                                    <th width="30%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Content Delivery Network</td>
                                    <td>N/A for the time being, if required use AVM
                                    </td>
                                    <td></td>
                                </tr>
                    
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade show active" id="professional" role="tabpanel">
            <div class="card shadow-sm mb-4">
                <div class="card-header text-white" style="background-color: #FF82E6;">
                    <h5 class="mb-0">Network</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="30%">Network</th>
                                    <th width="56%">Description</th>
                                    <th width="30%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Bandwidth</td>
                                    <td>Internet bandwidth that can be subscribed within the cloud portal to be used by cloud services. There are 2 modes of deployment , either to tie certain
                                        amount of bandwidth to one service limited by the total subscribed amount OR to create a shared bandwidth pool based on subscribed bandwidth and shared across all services
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Bandwidth with Anti-DDoS</td>
                                   
                                    <td>In-line anti-ddos that could protect against VOLUMETRIC network attacks</td>
                                 <td>
                                    </td>
                                </tr>

                                <tr>
                                    <td>Included Elastic IP (FOC)</td>
                                   
                                 <td>
                                    The Elastic IP (EIP) service provides static public IP addresses and scalable bandwidths that enable your cloud resources to communicate with the Internet. You can easily bind an EIP to an ECS, NAT gateway, or load balancer, enabling immediate Internet access. One EIP can only be assigned to one resource at ONE time and the IP address is randomly selected. The same EIP may not be available once EIP is released

This item is the EIP that is inclusive based on subscribed bandwidth
                                    </td>

                                    <td></td>
                                </tr>


                                 <tr>
                                    <td>Elastic IP</td>
                                   
                                    <td>The Elastic IP (EIP) service provides static public IP addresses and scalable bandwidths that enable your cloud resources to communicate with the Internet. You can easily bind an EIP to an ECS, NAT gateway, or load balancer, enabling immediate Internet access. One EIP can only be assigned to one resource at ONE time and the IP address is randomly selected. The same EIP may not be available once EIP is released

This item is the additional EIP that will need to be subscribed</td>
                                 <td>Remarks:need to check on the port speed control
                                    </td>
                                </tr>


                                 <tr>
                                    <td>Elastic Load Balancer</td>
                                   
                                    <td>Elastic Load Balance (ELB) automatically distributes incoming traffic across multiple servers to balance their workloads, increasing service capabilities and fault tolerance of your applications.</td>
                                 <td>
                                    </td>
                                </tr>

                                 <tr>
                                    <td>Direct Connect Virtual Gateway</td>
                                   
                                    <td>This refers to the Virtual Gateway within Direct Connect Service that will allow a public IP subnet to be connected to the cloud VPC. This component includes the charges for the virtual gateway required together with the physical port charges. Data Centre Cross Connect and Connectivity component are NOT included</td>
                                 
                                 <td>
   <a href="https://support.huaweicloud.com/intl/en-us/productdesc-natgateway/en-us_topic_0086739763.html
                                " 
      target="_blank">
    
    https://support.huaweicloud.com/intl/en-us/productdesc-natgateway/en-us_topic_0086739763.html
                                    
   </a>
</td>
                                 
                                 
                                 
                                </tr>

                                 <tr>
                                    <td>L2BR instance</td>
                                   
                                    <td>L2BR enables high-speed and secure Layer 2 communication between a VPC and an on-premises IP address range. If the CIDR block of a VPC subnet and an on-premises IP address range belong to the same IP address range, L2BR can enable Layer 2 communication between the VPC subnet and the on-premises IP address range. If the CIDR block of a VPC subnet and an on-premises IP address range belong to different IP address ranges, L2BR can enable Layer 3 communication between them. Data Centre Cross Connect and Connectivity component are NOT included</td>
                                 <td>
                                    </td>
                                </tr>

                                <tr>

                                    <td>Virtual Private Leased Line</td>
                                   
                                    <td>This is a private leased line that will allow communication to the other region through a L2BR Gateway (Without the physical ports). This component includes the virtual leased line and the L2BR Gateway component</td>
                                 <td>
                                    </td>
                                </tr>

                                 <tr>

                                    <td>vPLL L2BR instance</td>
                                   
                                    <td>Each vPLL will require a pair of L2BR by default. For each L2BR can only be linked to 1x VLAN and 1x VPC. If multiple VPC connection cross region is required, we will need to subscribe to multiple pairs of L2BR</td>
                                 <td>
                                    </td>
                                </tr>

                                 <tr>

                                    <td>NAT Gateway (Small)</td>
                                   
                                   <td>Public NAT gateways translate private IP addresses into EIPs, and are used by cloud servers in a VPC for secure, cost-effective Internet access. Private NAT gateways translate between private IP addresses, and are used between VPCs or your VPC and on-premises data center to keep legacy networks running after cloud migration. Please refer to the table for NAT Gateway Performance. 

</td>
<td></td>
                                </tr>


                                 <tr>

                                    <td>NAT Gateway (Medium)</td>
                                   
                                  
<td></td>
<td></td>
                                </tr>

                                 <tr>

                                    <td>NAT Gateway (Large)</td>
                                   
                                  
<td></td>
<td></td>

                                </tr>

                                 <tr>

                                    <td>NAT Gateway (Extra-Large)</td>
                                   
                                  
<td></td>
<td></td>
                                </tr>

                                 <tr>

                                    <td>Managed Global Server Load Balancer (GSLB)</td>
                                   
                                  
<td>Managed GSLB by TDC In house to address load balancing across multiple regions. Calculated based on per domain, where each domain is limited to 1000QPS</td>
<td></td>
                                </tr>



                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade show active" id="professional" role="tabpanel">
            <div class="card shadow-sm mb-4">
                <div class="card-header text-white" style="background-color: #FF82E6;">
                    <h5 class="mb-0">License</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <!---<thead class="table-light">
                                <tr>
                                    <th width="30%">License</th>
                                    <th width="56%"></th>
                                    <th width="30%"></th>
                                </tr>
                            </thead>--->
                            <tbody>
                                <tr>

                                    <td class="table-light">Microsoft</td>
                                    <td>Microsoft Windows Licensing. Only support those that are still supported within the list. 
                                    <a href="https://learn.microsoft.com/en-us/lifecycle/faq/extended-security-updates#esu-availability-and-end-dates
                                " 
      target="_blank">
    
 https://learn.microsoft.com/en-us/lifecycle/faq/extended-security-updates#esu-availability-and-end-dates
                                    
   </a>
                                
                                
                                
                                </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Microsoft Windows Server (Core Pack)- Standard </td>
                                    <td>A licensing model for Windows Server OS designed to run server applications, manage network services, and provide various server-related functionalities with a licensing package based on the number of CPU cores.
Each Core Pack consists of 2x vCPU, minimum 4 packs
                                    </td>
                                    <td></td>
                                </tr>

                                 <tr>
                                    <td>Microsoft Windows Server (Core Pack)- Data Center </td>
                                    <td>
                                     <strong>ONLY suitable for BARE Metal Services</strong><br>
    A licensing model for Windows Server OS designed to run server applications, manage network services, and provide various server-related functionalities with a licensing package based on the number of CPU cores.<br>
    Each Core Pack consists of 2x vCPU, minimum 4 packs



                                    </td>
                                    <td></td>
                                </tr>

                                     <tr>
                                    <td>Microsoft Remote Desktop Services (SAL) </td>
                                    <td>A licensing model used for accessing RDS, where users or devices require a valid SAL to access RDS functionality over virtual desktop environments.
Each SAL = 1 remote login user
                                    </td>
                                    <td></td>
                                </tr>

                                     <tr>
                                    <td>Microsoft SQL (Web)(Core Pack) </td>
                                    <td>
                                        Web edition of SQL Server license optimized for web applications and hosting environments, with a licensing package that provides core-based licensing for SQL Server. 
Each Core Pack consists of 2x vCPU, minimum 2 packs
                                    </td>
                                    <td></td>
                                </tr>

                                  <tr>
                                    <td>Microsoft SQL (Standard)(Core Pack) </td>
                                    <td>SQL Server licensing that offers essential database management capabilities with a licensing package based on the number of CPU cores.
Each Core Pack consists of 2x vCPU, minimum 2 packs
                                    </td>
                                    <td></td>
                                </tr>

                                  <tr>
                                    <td>Microsoft SQL (Enterprise)(Core Pack) </td>
                                    <td>Enterprise-level SQL Server license, designed for large-scale and mission-critical database applications; with licensing package based on the number of CPU cores.
Each Core Pack consists of 2x vCPU, minimum 2 packs
                                    </td>
                                    <td></td>
                                </tr>


                                 <tr>

                                   <td class="table-light">Red Hat Enterprise License</td>
                                    <td></td>
                                     <td></td>
                                    
                                </tr>



                                     <tr>
                                    <td>RHEL (1-8vCPU)</td>
                                    <td>A specific configuration or subscription package of RHEL optimized for small VMs (below 5vCPU)
                                    </td>
                                    <td></td>
                                </tr>



                                <tr>
                                    <td>RHEL (9-127vCPU)</td>
                                    <td>A specific configuration or subscription package of RHEL optimized for large VMs (above 4vCPU)
                                    </td>
                                    <td></td>
                                </tr>






















                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>



        <div class="tab-pane fade show active" id="professional" role="tabpanel">
            <div class="card shadow-sm mb-4">
                <div class="card-header text-white" style="background-color: #FF82E6;">
                    <h5 class="mb-0">Storage</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <!---<thead class="table-light">
                                <tr>
                                    <th width="30%">Storage</th>
                                    <th width="56%"></th>
                                    <th width="30%"></th>
                                </tr>
                            </thead>--->
                            <tbody>
                                <tr>

                                    <td class="table-light">Storage Type</td>
                                       <td class="table-light"></td>
                                   
                                         <td class="table-light"></td>
                                </tr>
                                <tr>
                                    <td>Elastic Volume Service (EVS)</td>
                                    <td>Provides block storage space for ECS and BMS, for the system and data disks. Maximum capacity of a single disk is 64 TB.
                                    </td>
                                    <td></td>
                                </tr>

                                 <tr>
                                    <td>Scalable File Service (SFS)</td>
                                    <td>
                                   Provides scalable and high-performance file storage that can be shared on-demand with multiple Elastic Cloud Servers (ECSs). SFS follows standard file protocols, and can be integrated with users' existing applications and tools. With robust reliability and high availability, SFS performance improves as file system capacity increases.



                                    </td>
                                    <td></td>
                                </tr>

                                     <tr>
                                         <td>Object Storage Service (OBS)</td>
                                          <td>Provides users with unlimited storage capacity, to store unstructured data in any format and access it using HTTP and HTTPS.</td>
                                           <td></td>
                                    
                                </tr>

                                     


                                 <tr>

                                   <td class="table-light">Image Management Service (IMS)</td>
                                    <td></td>
                                     <td></td>
                                    
                                </tr>



                                     <tr>

                                     <td>Snapshot Storage</td>
                                     <td>Capture and save the state of ECS instance, including its data, configuration, and operating system at a specific point in time. </td>
                                      <td></td>
                                 
                                </tr>



                                <tr>

                                   <td>Image Storage</td>
                                     <td>Create and manage private images for system or data disk from an ECS/BMS server</td>
                                      <td></td>
                                 
                                </tr>






















                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>




        


    </div>

    <!-- Footer Buttons -->
    <div class="d-flex justify-content-end gap-3">
        <div>
            <a href="{{ route('projects.index') }}" class="btn btn-pink">
                <i class=""></i> Back to Project

            </a>
            <button class="btn btn-pink">
                <i class="bi bi-printer"></i> Print
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Custom styling */
    .table {
        border: 1px solid #dee2e6;
    }
    .table th, .table td {
        border: 1px solid #dee2e6;
        vertical-align: top;
    }
    .fixed-bottom {
        position: fixed;
        bottom: 0;
        z-index: 1030;
    }
</style>
@endpush

@push('scripts')
<script>
    // Print functionality
    document.getElementById('printBtn').addEventListener('click', function() {
        window.print();
    });
</script>
@endpush
@endsection