@extends('layouts.app')

@section('content')
<div class="container">
    
       
   
<div class="d-flex flex-nowrap gap-5 overflow-auto">
        <!-- LEFT TABLE -->
        <div class="flex-shrink-0" style="min-width: 900px;">
             <h2></h2>
    <table class="table table-bordered">
          <thead class="table-dark">
            <tr>
                <th colspan="13" class="text-center text-white bg-dark">
    P.Flavour Map
</th>
            </tr>
        </thead>
        <thead class="table-light">
            <tr>
                <th>Flavour</th>
                <th>vCPU</th>
                <th>vRAM</th>
                <th>Type</th>
                <th>Generation</th>
                <th>Memory Label</th>
                <th>Windows License Count</th>
                <th>RHEL</th>
                <th>DR</th>
                <th>Pin</th>
                <th>GPU</th>
                <th>DDH</th>
                <th>MSSQL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($flavours as $row)
    @php
        $isHighlighted = request('highlight') === $row->flavour;
    @endphp
    <tr class="{{ $isHighlighted ? 'table-danger' : '' }}">
        <td>{{ $row->flavour }}</td>
        <td>{{ $row->vcpu }}</td>
        <td>{{ $row->vram }}</td>
        <td>{{ $row->type }}</td>
        <td>{{ $row->generation }}</td>
        <td>{{ $row->memory_label }}</td>
        <td>{{ $row->windows_license_count }}</td>
        <td>{{ $row->rhel }}</td>
        <td>{{ $row->dr }}</td>
        <td>{{ $row->pin }}</td>
        <td>{{ $row->gpu }}</td>
        <td>{{ $row->ddh }}</td>
        <td>{{ $row->mssql }}</td>
    </tr>
@endforeach

        </tbody>
    </table>
</div>


 
          <!-- RIGHT TABLE -->
        <div class="flex-shrink-0" style="min-width: 400px;">
            <h2></h2>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>CCE Master Flavour</th>
                        <th>vCPU</th>
                        <th>vRAM</th>
                        <th>Disk</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>3x Master Nodes (Cluster Size:1-50)</td>
                        <td>12</td>
                        <td>24</td>
                        <td>450</td>
                    </tr>

                    <tr>
    <td>3x Master Nodes (Cluster Size:51-200)</td>
    <td>24</td>
    <td>48</td>
    <td>450</td>
</tr>
<tr>
    <td>3x Master Nodes (Cluster Size:201-1000)</td>
    <td>48</td>
    <td>96</td>
    <td>450</td>
</tr>
<tr>
    <td>1x Master Nodes (Cluster Size:1-50)</td>
    <td>4</td>
    <td>8</td>
    <td>150</td>
</tr>
<tr>
    <td>1x Master Nodes (Cluster Size:51-200)</td>
    <td>8</td>
    <td>16</td>
    <td>150</td>
</tr>
<tr>
    <td>1x Master Nodes (Cluster Size:201-1000)</td>
    <td>16</td>
    <td>32</td>
    <td>150</td>
</tr>

                    
                </tbody>
            </table>

            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>CCE Worker Flavour</th>
                        <th>vCPU</th>
                        <th>vRAM</th>
                        <th>Type</th>
                        <th>Generation</th>
                        <th>Memory</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>m3.large</td>
                        <td>2</td>
                        <td>8</td>
                        <td>m</td>
                        <td>3</td>
                        <td>large</td>
                    </tr>

                    <tr>
    <td>c3.large</td>
    <td>2</td>
    <td>4</td>
    <td>c</td>
    <td>3</td>
    <td>large</td>
</tr>
<tr>
    <td>c3.xlarge</td>
    <td>4</td>
    <td>8</td>
    <td>c</td>
    <td>3</td>
    <td>xlarge</td>
</tr>
<tr>
    <td>m3.xlarge</td>
    <td>4</td>
    <td>16</td>
    <td>m</td>
    <td>3</td>
    <td>xlarge</td>
</tr>
<tr>
    <td>c3.2xlarge</td>
    <td>8</td>
    <td>16</td>
    <td>c</td>
    <td>3</td>
    <td>2xlarge</td>
</tr>
<tr>
    <td>m3.2xlarge</td>
    <td>8</td>
    <td>32</td>
    <td>m</td>
    <td>3</td>
    <td>2xlarge</td>
</tr>
<tr>
    <td>m3.3xlarge</td>
    <td>12</td>
    <td>48</td>
    <td>m</td>
    <td>3</td>
    <td>3xlarge</td>
</tr>
<tr>
    <td>c3.4xlarge</td>
    <td>16</td>
    <td>32</td>
    <td>c</td>
    <td>3</td>
    <td>4xlarge</td>
</tr>
<tr>
    <td>m3.4xlarge</td>
    <td>16</td>
    <td>64</td>
    <td>m</td>
    <td>3</td>
    <td>4xlarge</td>
</tr>
<tr>
    <td>c3.8xlarge</td>
    <td>32</td>
    <td>64</td>
    <td>c</td>
    <td>3</td>
    <td>8xlarge</td>
</tr>
<tr>
    <td>m3.8xlarge</td>
    <td>32</td>
    <td>128</td>
    <td>m</td>
    <td>3</td>
    <td>8xlarge</td>
</tr>
<tr>
    <td>m3.16xlarge</td>
    <td>64</td>
    <td>256</td>
    <td>m</td>
    <td>3</td>
    <td>16xlarge</td>
</tr>
<tr>
    <td>c3.16xlarge</td>
    <td>64</td>
    <td>128</td>
    <td>c</td>
    <td>3</td>
    <td>16xlarge</td>
</tr>

                    
                </tbody>
            </table>
        </div>

         <!-- RIGHT TABLE -->
        <div class="flex-shrink-0" style="min-width: 400px;">
            <h2></h2>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Security</th>
                        <th>vCPU</th>
                        <th>vRAM</th>
                        <th>Disk</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Cloud Firewall (Fortigate)</td>
                        <td>2</td>
                        <td>4</td>
                        <td>40</td>
                    </tr>
                    <tr>
                        <td>Cloud Firewall (OPNSense)</td>
                        <td>2</td>
                          <td>4</td>
                        <td>10</td>
                    </tr>
                </tbody>
            </table>
        </div>

         <div class="flex-shrink-0" style="min-width: 400px;">
            <h2></h2>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Ratio</th>
                        <th>Type</th>
                        <th>Label</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2</td>
                        <td>Compute Optimized</td>
                        <td>c</td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>General Purpose</td>
                          <td>m</td>
                    </tr>

                    <tr>
                        <td>8</td>
                        <td>Memory Optimized</td>
                          <td>r</td>
                    </tr>

                    <tr>
                        <td>10</td>
                         <td>Memory Optimized</td>
                          <td>r</td>
                    </tr>

                    <tr>
                        <td>22</td>
                        <td>Memory Optimized</td>
                          <td>r</td>
                    </tr>
                </tbody>
            </table>


             <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Label</th>
                        <th>Memory</th>
                        <th>RAM Label</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>m</td>
                        <td>1</td>
                        <td>micro</td>
                    </tr>
                    <tr><td>m</td><td>2</td><td>small</td></tr>
<tr><td>m</td><td>8</td><td>large</td></tr>
<tr><td>m</td><td>16</td><td>xlarge</td></tr>
<tr><td>m</td><td>32</td><td>2xlarge</td></tr>
<tr><td>m</td><td>48</td><td>3xlarge</td></tr>
<tr><td>m</td><td>64</td><td>4xlarge</td></tr>
<tr><td>m</td><td>96</td><td>6xlarge</td></tr>
<tr><td>m</td><td>128</td><td>8xlarge</td></tr>
<tr><td>m</td><td>256</td><td>16xlarge</td></tr>
<tr><td>c</td><td>4</td><td>large</td></tr>
<tr><td>c</td><td>8</td><td>xlarge</td></tr>
<tr><td>c</td><td>16</td><td>2xlarge</td></tr>
<tr><td>c</td><td>32</td><td>4xlarge</td></tr>
<tr><td>c</td><td>64</td><td>8xlarge</td></tr>
<tr><td>c</td><td>128</td><td>16xlarge</td></tr>
<tr><td>r</td><td>16</td><td>large</td></tr>
<tr><td>r</td><td>32</td><td>xlarge</td></tr>
<tr><td>r</td><td>64</td><td>2xlarge</td></tr>
<tr><td>r</td><td>128</td><td>4xlarge</td></tr>
<tr><td>r</td><td>256</td><td>8xlarge</td></tr>
<tr><td>r</td><td>384</td><td>12xlarge</td></tr>
<tr><td>r</td><td>512</td><td>16xlarge</td></tr>
<tr><td>r</td><td>1408</td><td>46xlarge.metal</td></tr>
<tr><td>r</td><td>1480</td><td>46xlarge.ddh</td></tr>

                   
                </tbody>
            </table>

        </div>


         <div class="flex-shrink-0" style="min-width: 400px;">
            <h2></h2>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Bandwidth (Mbps)</th>
                        <th>EIP</th>
                         <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>2</td>
                        <td>Bandwidth (1-10Mbps) + 2 EIP</td>
                       
                    </tr>

                      <tr>
                        <td>10</td>
                        <td>4</td>
                          <td>Bandwidth (11-30Mbps) + 4 EIP</td>
                       
                    </tr>

                      <tr>
                        <td>30</td>
                        <td>6</td>
                          <td>Bandwidth (31-50Mbps) + 6 EIP</td>
                       
                    </tr>

                      <tr>
                        <td>50</td>
                        <td>8</td>
                          <td>Bandwidth (51-80Mbps) + 8 EIP</td>
                       
                    </tr>

                      <tr>
                        <td>80</td>
                        <td>8</td>
                        <td>Bandwidth (81-100Mbps) + 8 EIP</td>
                       
                    </tr>
                    

                </tbody>
            </table>


           
    </div>
</div>

<!---@if(request()->has('highlight'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rows = document.querySelectorAll('table tr');
        rows.forEach(row => {
            if (row.innerText.includes("{{ request('highlight') }}")) {
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });
</script>
@endif--->


@endsection
