<!DOCTYPE html>
<html>



<div class="header">
    Confidential | {{ now()->format('d M Y') }}
</div>


<head>
    <title>Rate Card</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #000; color: #fff; font-weight: bold; }
        .section-header { background-color: #FF82E6; font-weight: bold; }
        .logo { text-align: right; }
        .footer { font-size: 10px; position: fixed; bottom: 10px; left: 20px; }


         .table-light {
            background-color: #f0f0f0; 
        }
    </style>
</head>
<body>
<div>
    <div class="header" style="margin-bottom: 20px;"> 
        <div></div>
        <div class="logo">
            <img src="{{ public_path('assets/time_logo.png') }}" height="40">
        </div>
    </div>
</div>

<div>
    <table style="margin-top: 10px;"> 
        <!-- table content -->
    </table>
</div>

<table>
    <tr>
        <th colspan="3">Rate Card (Minimum 1 Month Subscription)</th>
    </tr>
    <tr>
        <td colspan="3">Details of Monthly Service Subscription by Category</td>
    </tr>

    <!-- Professional Services -->
    <tr class="section-header">
        <td>Professional Services</td>
        <td>Unit</td>
        <td>Rate Card (MRC)</td>
    </tr>
    <tr>
        <td>Professional Services (ONE TIME Provisioning)</td>
        <td>Days</td>
        <td>RM 1430.00</td>
    </tr>
    <tr>
        <td>Migration Tools One Time Charge</td>
        <td>Unit Per Month*</td>
        <td>RM 140.80</td>
    </tr>

    <!-- Managed Services -->
    <tr class="section-header">
        <td>Managed Services</td>
        <td>Unit</td>
        <td>Rate Card (MRC)</td>
    </tr>
    <tr>
         <td>{{ $pricing['CMNS-MOS-NOD-STD']['name'] }}</td>
    <td>{{ $pricing['CMNS-MOS-NOD-STD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CMNS-MOS-NOD-STD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>
    <tr>
        <td>{{ $pricing['CMNS-MBR-NOD-STD']['name'] }}</td>
    <td>{{ $pricing['CMNS-MBR-NOD-STD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CMNS-MBR-NOD-STD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>
    <tr>
         <td>{{ $pricing['CMNS-MPT-NOD-STD']['name'] }}</td>
    <td>{{ $pricing['CMNS-MPT-NOD-STD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CMNS-MPT-NOD-STD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>
    <tr>
          <td>{{ $pricing['CMNS-MDR-NOD-STD']['name'] }}</td>
    <td>{{ $pricing['CMNS-MDR-NOD-STD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CMNS-MDR-NOD-STD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>

    <!-- Network -->
    <tr class="section-header">
      <td>Network</td>
       <td>Unit</td>
        <td>Rate Card (MRC)</td>
    </tr>
    <tr>
         <td>Bandwidth</td>
    <td>{{ $pricing['CNET-BWS-CIA-80']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CNET-BWS-CIA-80']['rate_card_price_per_unit'], 2) }}</td>
    </tr>
    <tr>
        <td>Bandwidth with Anti-DDoS</td>
    <td>{{ $pricing['CNET-BWD-CIA-80']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CNET-BWD-CIA-80']['rate_card_price_per_unit'], 2) }}</td>
    </tr>
    <tr>
        <td>Included Elastic IP (FOC)</td>
        <td>Unit</td>
        <td>N/A</td>
    </tr>
    <tr>
        <td>{{ $pricing['CNET-EIP-SHR-STD']['name'] }}</td>
    <td>{{ $pricing['CNET-EIP-SHR-STD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CNET-EIP-SHR-STD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>
    <tr>
         <td>{{ $pricing['CNET-ELB-SHR-STD']['name'] }}</td>
    <td>{{ $pricing['CNET-ELB-SHR-STD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CNET-ELB-SHR-STD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>
    <tr>
      <td>{{ $pricing['CNET-DGW-SHR-EXT']['name'] }}</td>
    <td>{{ $pricing['CNET-DGW-SHR-EXT']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CNET-DGW-SHR-EXT']['rate_card_price_per_unit'], 2) }}</td>
    </tr>
    <tr>
      <td>{{ $pricing['CNET-L2BR-SHR-EXT']['name'] }}</td>
    <td>{{ $pricing['CNET-L2BR-SHR-EXT']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CNET-L2BR-SHR-EXT']['rate_card_price_per_unit'], 2) }}</td>
    </tr>
    <tr>
     <td>{{ $pricing['CNET-PLL-SHR-30']['name'] }}</td>
    <td>{{ $pricing['CNET-PLL-SHR-30']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CNET-PLL-SHR-30']['rate_card_price_per_unit'], 2) }}</td>
    </tr>
    <tr>
        <td>{{ $pricing['CNET-L2BR-SHR-INT']['name'] }}</td>
    <td>{{ $pricing['CNET-L2BR-SHR-INT']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CNET-L2BR-SHR-INT']['rate_card_price_per_unit'], 2) }}</td>
    </tr>
    <tr>
      <td>{{ $pricing['CNET-NAT-SHR-S']['name'] }}</td>
    <td>{{ $pricing['CNET-NAT-SHR-S']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CNET-NAT-SHR-S']['rate_card_price_per_unit'], 2) }}</td>
    </tr>
    <tr>
       <td>{{ $pricing['CNET-NAT-SHR-M']['name'] }}</td>
    <td>{{ $pricing['CNET-NAT-SHR-M']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CNET-NAT-SHR-M']['rate_card_price_per_unit'], 2) }}</td>
    </tr>
    <tr>
       <td>{{ $pricing['CNET-NAT-SHR-L']['name'] }}</td>
    <td>{{ $pricing['CNET-NAT-SHR-L']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CNET-NAT-SHR-L']['rate_card_price_per_unit'], 2) }}</td>
    </tr>
    <tr>
       <td>{{ $pricing['CNET-NAT-SHR-XL']['name'] }}</td>
    <td>{{ $pricing['CNET-NAT-SHR-XL']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CNET-NAT-SHR-XL']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CNET-GLB-SHR-DOMAIN']['name'] }}</td>
    <td>{{ $pricing['CNET-GLB-SHR-DOMAIN']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CNET-GLB-SHR-DOMAIN']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


        <!-- Compute -->
     <tr class="section-header">
        <td colspan="3">Compute</td>
    </tr>


     <tr class="table-light">
        <td>Compute - Elastic Cloud Server (ECS)</td>
        <td>Sizing</td>
        <td>Rate Card (MRC)</td>
    </tr>
    


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-M3.MICRO']['name'] }}</td>
   <td>1 core,1 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-M3.MICRO']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-M3.SMALL']['name'] }}</td>
    <td>1 core,2 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-M3.SMALL']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-C3.LARGE']['name'] }}</td>
    <td>2 core,4 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-C3.LARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-M3.LARGE']['name'] }}</td>
    <td>2 core,8 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-M3.LARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-R3.LARGE']['name'] }}</td>
     <td>2 core,16 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-R3.LARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-C3.XLARGE']['name'] }}</td>
    <td>4 core,8 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-C3.XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-M3.XLARGE']['name'] }}</td>
    <td>4 core,16 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-M3.XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-R3.XLARGE']['name'] }}</td>
    <td>4 core,32 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-R3.XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-C3.2XLARGE']['name'] }}</td>
     <td>8 core,16 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-C3.2XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-M3.2XLARGE']['name'] }}</td>
    <td>8 core, 32 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-M3.2XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>










    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-R3.2XLARGE']['name'] }}</td>
    <td>8 core,64 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-R3.2XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-M3.3XLARGE']['name'] }}</td>
    <td>12 core,48 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-M3.3XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-C3.4XLARGE']['name'] }}</td>
    <td>16 core,32 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-C3.4XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-M3.4XLARGE']['name'] }}</td>
     <td>16 core,64 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-M3.4XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-R3.4XLARGE']['name'] }}</td>
     <td>16 core,128 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-R3.4XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>









    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-M3.6XLARGE']['name'] }}</td>
    <td>24 core,96 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-M3.6XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-C3.8XLARGE']['name'] }}</td>
     <td>32 core,64 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-C3.8XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-M3.8XLARGE']['name'] }}</td>
     <td>32 core,128 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-M3.8XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-R3.8XLARGE']['name'] }}</td>
    <td>32 core,256 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-R3.8XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-R3.12XLARGE']['name'] }}</td>
     <td>48 core,384 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-R3.12XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>









    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-C3.16XLARGE']['name'] }}</td>
     <td>64 core,128 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-C3.16XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-M3.16XLARGE']['name'] }}</td>
    <td>64 core,256 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-M3.16XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-R3.16XLARGE']['name'] }}</td>
     <td>64 core,512 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-R3.16XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-C3P.XLARGE']['name'] }}</td>
    <td>4 core,8 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-C3P.XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-M3P.XLARGE']['name'] }}</td>
     <td>4 core,16 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-M3P.XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>







    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-R3P.XLARGE']['name'] }}</td>
     <td>4 core,32 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-R3P.XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-C3P.2XLARGE']['name'] }}</td>
     <td>8 core,16 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-C3P.2XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-M3P.2XLARGE']['name'] }}</td>
     <td>8 core,32 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-M3P.2XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-R3P.2XLARGE']['name'] }}</td>
    <td>8 core,64 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-R3P.2XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-M3P.3XLARGE']['name'] }}</td>
    <td>12 core,48 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-M3P.3XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>






    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-C3P.4XLARGE']['name'] }}</td>
     <td>16 core,32 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-C3P.4XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-M3P.4XLARGE']['name'] }}</td>
    <td>16 core,64 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-M3P.4XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>





    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-R3P.4XLARGE']['name'] }}</td>
    <td>16 core,128 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-R3P.4XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>





    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-M3P.6XLARGE']['name'] }}</td>
    <td>24 core,96 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-M3P.6XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>





    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-C3P.8XLARGE']['name'] }}</td>
    <td>32 core,64 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-C3P.8XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>







    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-M3P.8XLARGE']['name'] }}</td>
    <td>32 core,128 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-M3P.8XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>







    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-R3P.8XLARGE']['name'] }}</td>
     <td>32 core,256 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-R3P.8XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>








    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-R3P.12XLARGE']['name'] }}</td>
    <td>48 core,384 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-R3P.12XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>








    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-C3P.16XLARGE']['name'] }}</td>
    <td>64 core,128 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-C3P.16XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>







    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-M3P.16XLARGE']['name'] }}</td>
    <td>64 core,256 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-M3P.16XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-R3P.16XLARGE']['name'] }}</td>
    <td>64 core,512 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-R3P.16XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHG-M3GNT4.XLARGE']['name'] }}</td>
    <td>4 core,16 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHG-M3GNT4.XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CMPT-ECS-SHG-M3GNT4.2XLARGE']['name'] }}</td>
    <td>8 core,32 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHG-M3GNT4.2XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>






    <tr>
       <td>{{ $pricing['CMPT-ECS-SHG-M3GNT4.4XLARGE']['name'] }}</td>
    <td>16 core,64 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHG-M3GNT4.4XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>






    <tr>
       <td>{{ $pricing['CMPT-ECS-SHG-M3GNT4.8XLARGE']['name'] }}</td>
    <td>32 core,128 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHG-M3GNT4.8XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>








    <tr>
       <td>{{ $pricing['CMPT-ECS-SHG-M3GNT4.16XLARGE']['name'] }}</td>
    <td>64 core,256 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHG-M3GNT4.16XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>







    <tr>
       <td>{{ $pricing['CMPT-DDH-DDT-R3P.46XLARGE']['name'] }}</td>
    <td>342 core,1480 GB</td>
    <td>RM {{ number_format($pricing['CMPT-DDH-DDT-R3P.46XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



     <tr class="section-header">
        <td colspan="3">Compute - Cloud Container Engine (CCE)</td>
    </tr>


     <tr class="table-light">
        <td>Master Nodes for CCE</td>
        <td>Sizing</td>
        <td>Rate Card (MRC)</td>
    </tr>

    <tr>
       <td>{{ $pricing['CMPT-CCE-SHR-MM50']['name'] }}</td>
    <td>{{ $pricing['CMPT-CCE-SHR-MM50']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CMPT-CCE-SHR-MM50']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-CCE-SHR-MM200']['name'] }}</td>
    <td>{{ $pricing['CMPT-CCE-SHR-MM200']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CMPT-CCE-SHR-MM200']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



    <tr>
       <td>{{ $pricing['CMPT-CCE-SHR-MM1000']['name'] }}</td>
    <td>{{ $pricing['CMPT-CCE-SHR-MM1000']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CMPT-CCE-SHR-MM1000']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



    <tr>
       <td>{{ $pricing['CMPT-CCE-SHR-SM50']['name'] }}</td>
    <td>{{ $pricing['CMPT-CCE-SHR-SM50']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CMPT-CCE-SHR-SM50']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-CCE-SHR-SM200']['name'] }}</td>
    <td>{{ $pricing['CMPT-CCE-SHR-SM200']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CMPT-CCE-SHR-SM200']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



    <tr>
       <td>{{ $pricing['CMPT-CCE-SHR-SM1000']['name'] }}</td>
    <td>{{ $pricing['CMPT-CCE-SHR-SM1000']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CMPT-CCE-SHR-SM1000']['rate_card_price_per_unit'], 2) }}</td>
    </tr>
   

     <tr class="table-light">
        <td>Worker Nodes for CCE</td>
        <td>Sizing</td>
        <td>Rate Card (MRC)</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-M3.LARGE']['name'] }}</td>
    <td>2 core,8 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-M3.LARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-C3.LARGE']['name'] }}</td>
    <td>2 core,4 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-C3.LARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-C3.XLARGE']['name'] }}</td>
     <td>4 core,8 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-C3.XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-M3.XLARGE']['name'] }}</td>
   <td>4 core,16 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-M3.XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-C3.2XLARGE']['name'] }}</td>
    <td>8 core,16 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-C3.2XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-M3.2XLARGE']['name'] }}</td>
    <td>8 core,32 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-M3.2XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-M3.3XLARGE']['name'] }}</td>
    <td>12 core,48 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-M3.3XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-C3.4XLARGE']['name'] }}</td>
    <td>16 core,32 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-C3.4XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-M3.4XLARGE']['name'] }}</td>
    <td>16 core,64 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-M3.4XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-C3.8XLARGE']['name'] }}</td>
    <td>32 core,64 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-C3.8XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>

    
    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-M3.8XLARGE']['name'] }}</td>
    <td>32 core,128 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-M3.8XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-M3.16XLARGE']['name'] }}</td>
    <td>64 core,256 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-M3.16XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHR-C3.16XLARGE']['name'] }}</td>
     <td>64 core,128 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHR-C3.16XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr class="section-header">
        <td colspan="3">License</td>
    </tr>


     <tr class="table-light">
        <td>Microsoft</td>
        <td>Unit</td>
        <td>Rate Card (MRC)</td>
    </tr>

      <tr>
       <td>{{ $pricing['CLIC-WIN-COR-SRVSTD']['name'] }}</td>
    <td>{{ $pricing['CLIC-WIN-COR-SRVSTD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CLIC-WIN-COR-SRVSTD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



      <tr>
       <td>{{ $pricing['CLIC-WIN-COR-SRVDC']['name'] }}</td>
    <td>{{ $pricing['CLIC-WIN-COR-SRVDC']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CLIC-WIN-COR-SRVDC']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


      <tr>
       <td>{{ $pricing['CLIC-WIN-USR-RDSSAL']['name'] }}</td>
    <td>{{ $pricing['CLIC-WIN-USR-RDSSAL']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CLIC-WIN-USR-RDSSAL']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


      <tr>
       <td>{{ $pricing['CLIC-WIN-DRCOR-SQLWEB']['name'] }}</td>
    <td>{{ $pricing['CLIC-WIN-DRCOR-SQLWEB']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CLIC-WIN-DRCOR-SQLWEB']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


      <tr>
       <td>{{ $pricing['CLIC-WIN-DRCOR-SQLSTD']['name'] }}</td>
    <td>{{ $pricing['CLIC-WIN-DRCOR-SQLSTD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CLIC-WIN-DRCOR-SQLSTD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>
 
   
      <tr>
       <td>{{ $pricing['CLIC-WIN-DRCOR-SQLENT']['name'] }}</td>
    <td>{{ $pricing['CLIC-WIN-DRCOR-SQLENT']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CLIC-WIN-DRCOR-SQLENT']['rate_card_price_per_unit'], 2) }}</td>
    </tr>

   
     <tr class="table-light">
        <td>Red Hat Enterprise License</td>
        <td>Unit</td>
        <td>Rate Card (MRC)</td>
    </tr>


      <tr>
       <td>{{ $pricing['CLIC-RHL-COR-8']['name'] }}</td>
    <td>{{ $pricing['CLIC-RHL-COR-8']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CLIC-RHL-COR-8']['rate_card_price_per_unit'], 2) }}</td>
    </tr>

   

      <tr>
       <td>{{ $pricing['CLIC-RHL-COR-127']['name'] }}</td>
    <td>{{ $pricing['CLIC-RHL-COR-127']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CLIC-RHL-COR-127']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


     <!-- Storage -->
      <tr class="section-header">
        <td colspan="3">Storage</td>
    </tr>


     <tr class="table-light">
        <td>Storage Type</td>
        <td>Unit</td>
        <td>Rate Card (MRC)</td>
    </tr>




      <tr>
       <td>{{ $pricing['CSTG-EVS-SHR-STD']['name'] }}</td>
    <td>{{ $pricing['CSTG-EVS-SHR-STD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CSTG-EVS-SHR-STD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



      <tr>
       <td>{{ $pricing['CSTG-SFS-SHR-STD']['name'] }}</td>
    <td>{{ $pricing['CSTG-SFS-SHR-STD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CSTG-SFS-SHR-STD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



      <tr>
       <td>{{ $pricing['CSTG-OBS-SHR-STD']['name'] }}</td>
    <td>{{ $pricing['CSTG-OBS-SHR-STD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CSTG-OBS-SHR-STD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>
 


     <tr class="table-light">
        <td>Image Management Service (IMS)</td>
        <td>Unit</td>
        <td>Rate Card (MRC)</td>
    </tr>
 



      <tr>
       <td>{{ $pricing['CSTG-BCK-SHR-STD']['name'] }}</td>
    <td>{{ $pricing['CSTG-BCK-SHR-STD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CSTG-BCK-SHR-STD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




      <tr>
       <td>{{ $pricing['CSTG-OBS-SHR-IMG']['name'] }}</td>
    <td>{{ $pricing['CSTG-OBS-SHR-IMG']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CSTG-OBS-SHR-IMG']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


     <!-- Backup and DR -->
    <tr class="section-header">
        <td colspan="3">Backup and DR</td>
    </tr>


     <tr class="table-light">
        <td>Backup Service in VPC</td>
        <td>Unit</td>
        <td>Rate Card (MRC)</td>
    </tr>


      <tr>
       <td>{{ $pricing['CSBS-STRG-BCK-CSBSF']['name'] }}</td>
    <td>{{ $pricing['CSBS-STRG-BCK-CSBSF']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CSBS-STRG-BCK-CSBSF']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



      <tr>
       <td>{{ $pricing['CSBS-STRG-BCK-CSBSI']['name'] }}</td>
    <td>{{ $pricing['CSBS-STRG-BCK-CSBSI']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CSBS-STRG-BCK-CSBSI']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



      <tr>
       <td>{{ $pricing['CSBS-STRG-BCK-REPS']['name'] }}</td>
    <td>{{ $pricing['CSBS-STRG-BCK-REPS']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CSBS-STRG-BCK-REPS']['rate_card_price_per_unit'], 2) }}</td>
    </tr>
    


    <tr class="table-light">
        <td>Disaster Recovery in VPC</td>
        <td>Unit</td>
        <td>Rate Card (MRC)</td>
    </tr>
    
       <tr>
       <td>Cold DR Days</td>
    <td>Days</td>
    <td>N/A</td>
    </tr>


       <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-SEEDVM.DR']['name'] }}</td>
    <td>{{ $pricing['CDRV-CMPT-ECS-SEEDVM.DR']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-SEEDVM.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



       <tr>
       <td>{{ $pricing['CDRV-STRG-EVS-SHRSTD']['name'] }}</td>
    <td>{{ $pricing['CDRV-STRG-EVS-SHRSTD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CDRV-STRG-EVS-SHRSTD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



       <tr>
       <td>{{ $pricing['CDRV-CLIC-CSDR-NOD']['name'] }}</td>
    <td>{{ $pricing['CDRV-CLIC-CSDR-NOD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CDRV-CLIC-CSDR-NOD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



       <tr>
       <td>Cloud Server Disaster Recovery Days (DR Declaration)</td>
    <td>Days</td>
    <td>N/A</td>
    </tr>
    
     


    <tr class="table-light">
        <td>Disaster Recovery Network and Security</td>
        <td>Unit</td>
        <td>Rate Card (MRC)</td>
    </tr>

     <tr>
       <td>Cloud Server Disaster Recovery (vPLL)</td>
    <td>{{ $pricing['CNET-PLL-SHR-100']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CNET-PLL-SHR-100']['rate_card_price_per_unit'], 2) }}</td>
    </tr>

     <tr>
       <td>{{ $pricing['CDRV-CNET-EIP-SHRDRD']['name'] }}</td>
    <td>{{ $pricing['CDRV-CNET-EIP-SHRDRD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CDRV-CNET-EIP-SHRDRD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>

     <tr>
       <td>{{ $pricing['CDRV-CNET-BWS-SHRDRD']['name'] }}</td>
    <td>{{ $pricing['CDRV-CNET-BWS-SHRDRD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CDRV-CNET-BWS-SHRDRD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>

     <tr>
       <td>{{ $pricing['CDRV-CNET-BWD-SHRDRD']['name'] }}</td>
    <td>{{ $pricing['CDRV-CNET-BWD-SHRDRD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CDRV-CNET-BWD-SHRDRD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>

     <tr>
       <td>{{ $pricing['CDRV-CSEC-VFW-DDTFGDRD']['name'] }}</td>
    <td>{{ $pricing['CDRV-CSEC-VFW-DDTFGDRD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CDRV-CSEC-VFW-DDTFGDRD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>

     <tr>
       <td>{{ $pricing['CDRV-CSEC-VFW-DDTOSDRD']['name'] }}</td>
    <td>{{ $pricing['CDRV-CSEC-VFW-DDTOSDRD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CDRV-CSEC-VFW-DDTOSDRD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>

 


    <tr class="table-light">
        <td>Disaster Recovery Resources (During DR Activation)</td>
        <td>Unit</td>
        <td>Rate Card (MRC)</td>
    </tr>



       <tr>
       <td>{{ $pricing['CDRV-STRG-EVS-SHRDRD']['name'] }}</td>
    <td>{{ $pricing['CDRV-STRG-EVS-SHRDRD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CDRV-STRG-EVS-SHRDRD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>

      <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-M3.MICRO.DR']['name'] }}</td>
    <td>1 vCPU,1 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-M3.MICRO.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>

     <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-M3.SMALL.DR']['name'] }}</td>
    <td>1 vCPU,2 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-M3.SMALL.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>






    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-C3.LARGE.DR']['name'] }}</td>
    <td>2 vCPU,4 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-C3.LARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-M3.LARGE.DR']['name'] }}</td>
    <td>2 vCPU,8 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-M3.LARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-R3.LARGE.DR']['name'] }}</td>
    <td>2 vCPU,16 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-R3.LARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-C3.XLARGE.DR']['name'] }}</td>
    <td>4 vCPU,8 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-C3.XLARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-M3.XLARGE.DR']['name'] }}</td>
    <td>4 vCPU,16 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-M3.XLARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-R3.XLARGE.DR']['name'] }}</td>
    <td>4 vCPU,32 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-R3.XLARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-C3.2XLARGE.DR']['name'] }}</td>
    <td>8 vCPU,16 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-C3.2XLARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-M3.2XLARGE.DR']['name'] }}</td>
    <td>8 vCPU,32 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-M3.2XLARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-R3.2XLARGE.DR']['name'] }}</td>
    <td>8 vCPU,64 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-R3.2XLARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-M3.3XLARGE.DR']['name'] }}</td>
    <td>12 vCPU,48 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-M3.3XLARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>








    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-C3.4XLARGE.DR']['name'] }}</td>
    <td>16 vCPU,32 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-C3.4XLARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-M3.4XLARGE.DR']['name'] }}</td>
    <td>16 vCPU,64 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-M3.4XLARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-R3.4XLARGE.DR']['name'] }}</td>
    <td>16 vCPU,128 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-R3.4XLARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-C3.8XLARGE.DR']['name'] }}</td>
    <td>32 vCPU,64 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-C3.8XLARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-M3.8XLARGE.DR']['name'] }}</td>
    <td>32 vCPU,128 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-M3.8XLARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>













    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-R3.8XLARGE.DR']['name'] }}</td>
    <td>32 vCPU,256 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-R3.8XLARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-R3.12XLARGE.DR']['name'] }}</td>
    <td>48 vCPU,384 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-R3.12XLARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-C3.16XLARGE.DR']['name'] }}</td>
    <td>64 vCPU,128 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-C3.16XLARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-M3.16XLARGE.DR']['name'] }}</td>
    <td>64 vCPU,256 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-M3.16XLARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>







    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-R3.16XLARGE.DR']['name'] }}</td>
    <td>64 vCPU,512 RAM</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-R3.16XLARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>





    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-C3P.XLARGE.DR']['name'] }}</td>
    <td>4 core,8 GB</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-C3P.XLARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>





    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-M3P.XLARGE']['name'] }}</td>
    <td>4 core,16 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-M3P.XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-R3P.XLARGE']['name'] }}</td>
    <td>4 core,32 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-R3P.XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-C3P.2XLARGE']['name'] }}</td>
    <td>8 core,16 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-C3P.2XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-M3P.2XLARGE']['name'] }}</td>
    <td>8 core,32 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-M3P.2XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>





    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-R3P.2XLARGE']['name'] }}</td>
    <td>8 core,64 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-R3P.2XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>





    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-M3P.3XLARGE']['name'] }}</td>
    <td>12 core,48 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-M3P.3XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-C3P.4XLARGE']['name'] }}</td>
    <td>16 core,32 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-C3P.4XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-M3P.4XLARGE']['name'] }}</td>
    <td>16 core,64 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-M3P.4XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-R3P.4XLARGE']['name'] }}</td>
    <td>16 core,128 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-R3P.4XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>






    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-C3P.8XLARGE']['name'] }}</td>
    <td>32 core,64 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-C3P.8XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>





    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-M3P.8XLARGE']['name'] }}</td>
    <td>32 core,128 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-M3P.8XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-R3P.8XLARGE']['name'] }}</td>
    <td>32 core,256 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-R3P.8XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-R3P.12XLARGE']['name'] }}</td>
    <td>48 core,384 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-R3P.12XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


    <tr>
       <td>{{ $pricing['CMPT-ECS-SHP-C3P.16XLARGE']['name'] }}</td>
    <td>64 core,128 GB</td>
    <td>RM {{ number_format($pricing['CMPT-ECS-SHP-C3P.16XLARGE']['rate_card_price_per_unit'], 2) }}</td>
    </tr>






    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-M3P.16XLARGE.DR']['name'] }}</td>
    <td>64 core,256 GB</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-M3P.16XLARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




    <tr>
       <td>{{ $pricing['CDRV-CMPT-ECS-R3P.16XLARGE.DR']['name'] }}</td>
    <td>64 core,512 GB</td>
    <td>RM {{ number_format($pricing['CDRV-CMPT-ECS-R3P.16XLARGE.DR']['rate_card_price_per_unit'], 2) }}</td>
    </tr>




  










     <tr class="table-light">
        <td>Disaster Recovery Licenses</td>
        <td>Unit</td>
        <td>Rate Card (MRC)</td>
    </tr>


       <tr>
       <td>License Month</td>
    <td>Month(s)</td>
    <td>N/A</td>
    </tr>


       <tr>
       <td>{{ $pricing['CLIC-WIN-DRCOR-SRVSTD']['name'] }}</td>
    <td>{{ $pricing['CLIC-WIN-DRCOR-SRVSTD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CLIC-WIN-DRCOR-SRVSTD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>

    
       <tr>
       <td>{{ $pricing['CLIC-WIN-DRCOR-SRVDC']['name'] }}</td>
    <td>{{ $pricing['CLIC-WIN-DRCOR-SRVDC']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CLIC-WIN-DRCOR-SRVDC']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


       <tr>
       <td>{{ $pricing['CLIC-WIN-DRCOR-RDSSAL']['name'] }}</td>
    <td>{{ $pricing['CLIC-WIN-DRCOR-RDSSAL']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CLIC-WIN-DRCOR-RDSSAL']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


       <tr>
       <td>{{ $pricing['CLIC-WIN-DRCOR-SQLWEB']['name'] }}</td>
    <td>{{ $pricing['CLIC-WIN-DRCOR-SQLWEB']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CLIC-WIN-DRCOR-SQLWEB']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


       <tr>
       <td>{{ $pricing['CLIC-WIN-DRCOR-SQLSTD']['name'] }}</td>
    <td>{{ $pricing['CLIC-WIN-DRCOR-SQLSTD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CLIC-WIN-DRCOR-SQLSTD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


       <tr>
       <td>{{ $pricing['CLIC-WIN-DRCOR-SQLENT']['name'] }}</td>
    <td>{{ $pricing['CLIC-WIN-DRCOR-SQLENT']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CLIC-WIN-DRCOR-SQLENT']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


       <tr>
       <td>{{ $pricing['CLIC-RHL-DRCOR-8OTC']['name'] }}</td>
    <td>{{ $pricing['CLIC-RHL-DRCOR-8OTC']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CLIC-RHL-DRCOR-8OTC']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


       <tr>
       <td>{{ $pricing['CLIC-RHL-DRCOR-127OTC']['name'] }}</td>
    <td>{{ $pricing['CLIC-RHL-DRCOR-127OTC']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CLIC-RHL-DRCOR-127OTC']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



    
        <!-- Compute -->
    <tr class="section-header">
        <td colspan="3">Additional Services</td>

    </tr>



     <tr class="table-light">
        <td>Cloud Security</td>
        <td>Unit</td>
        <td>Rate Card (MRC)</td>
    </tr>

     <tr>
       <td>{{ $pricing['CSEC-VFW-DDT-FG']['name'] }}</td>
    <td>{{ $pricing['CSEC-VFW-DDT-FG']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CSEC-VFW-DDT-FG']['rate_card_price_per_unit'], 2) }}</td>
    </tr>

     <tr>
       <td>{{ $pricing['CSEC-VFW-DDT-OS']['name'] }}</td>
    <td>{{ $pricing['CSEC-VFW-DDT-OS']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CSEC-VFW-DDT-OS']['rate_card_price_per_unit'], 2) }}</td>
    </tr>

     <tr>
       <td>{{ $pricing['CSEC-WAF-SHR-HA']['name'] }}</td>
    <td>{{ $pricing['CSEC-WAF-SHR-HA']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CSEC-WAF-SHR-HA']['rate_card_price_per_unit'], 2) }}</td>
    </tr>

     <tr>
       <td>{{ $pricing['CSEC-EDR-NOD-STD']['name'] }}</td>
    <td>{{ $pricing['CSEC-EDR-NOD-STD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CSEC-EDR-NOD-STD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>
    


   <tr class="table-light">
        <td>Security Services</td>
        <td>Unit</td>
        <td>Rate Card (MRC)</td>
    </tr>
    

 <tr>
       <td>{{ $pricing['SECT-VAS-EIP-STD']['name'] }}</td>
    <td>{{ $pricing['SECT-VAS-EIP-STD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['SECT-VAS-EIP-STD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>



   <tr class="table-light">
        <td>Monitoring Services</td>
        <td>Unit</td>
        <td>Rate Card (MRC)</td>
    </tr>
    <tr>
       <td>{{ $pricing['CMON-TIS-NOD-STD']['name'] }}</td>
    <td>{{ $pricing['CMON-TIS-NOD-STD']['measurement_unit'] }}</td>
    <td>RM {{ number_format($pricing['CMON-TIS-NOD-STD']['rate_card_price_per_unit'], 2) }}</td>
    </tr>


</table>


</body>
</html>
