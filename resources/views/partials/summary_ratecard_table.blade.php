


         <table class="table table-bordered">
            
       

            {{-- Professional Services --}}
           <thead class="table-light">
    <tr>
        <th>Professional Services</th>
        <th>Unit</th>
        <th>KL Price (RM)</th>
        <th>CJ Price (RM)</th>
    </tr>
</thead>
<tbody>
    @foreach($professionalSummary as $item)
    <tr>
        <td>{{ $item['name'] }}</td>
        <td>{{ $item['unit'] }}</td>
        <td>{{ number_format($item['kl_price'], 2) }}</td>
        <td>{{ number_format($item['cj_price'], 2) }}</td>
    </tr>
    @endforeach
</tbody>

    <tr>
        <td colspan="2"><strong>Total Professional Services</strong></td>
        <td><strong>RM{{ number_format(collect($professionalSummary)->sum('kl_price'), 2) }}</strong></td>
        <td><strong>RM{{ number_format(collect($professionalSummary)->sum('cj_price'), 2) }}</strong></td>
    </tr>




            {{-- Managed Services --}}
           <thead class="table-light">
    <tr>
        <th>Managed Services</th>
        <th>Unit</th>
        <th>KL Price (RM)</th>
        <th>CJ Price (RM)</th>
    </tr>
</thead>
<tbody>
    @foreach($managedSummary as $item)
    <tr>
        <td>{{ $item['name'] }}</td>
        <td>{{ $item['unit'] }}</td>
        <td>{{ number_format($item['kl_price'], 2) }}</td>
        <td>{{ number_format($item['cj_price'], 2) }}</td>
    </tr>
    @endforeach
</tbody>

    <tr>
        <td colspan="2"><strong>Total Managed Services</strong></td>
     

        <td><strong>RM{{ number_format(collect($managedSummary)->sum('kl_price'), 2) }}</strong></td>
        <td><strong>RM{{ number_format(collect($managedSummary)->sum('cj_price'), 2) }}</strong></td>
    </tr>


         <thead class="table-light">
    <tr>
        <th>Compute - Elastic Cloud Server (ECS)</th>
        <th>Sizing</th>
        <th>KL Price (RM)</th>
        <th>CJ Price (RM)</th>
    </tr>
</thead>
<tbody>
    @foreach($ecsSummary as $item)
    <tr>
        <td>{{ $item['name'] }}</td>
        <td>{{ $item['unit'] }}</td>
        <td>{{ number_format($item['kl_price'], 2) }}</td>
        <td>{{ number_format($item['cj_price'], 2) }}</td>
    </tr>
    @endforeach
</tbody>

    <tr>
        <td colspan="2"><strong>Total Compute (ECS)</strong></td>
     

        <td><strong>RM{{ number_format(collect($ecsSummary)->sum('kl_price'), 2) }}</strong></td>
        <td><strong>RM{{ number_format(collect($ecsSummary)->sum('cj_price'), 2) }}</strong></td>
    </tr>
          
            


       

            {{-- Network --}}
            <thead class="table-light">
                <tr>
                    <th>Network</th>
                    <th>Unit</th>.
                    <!--<th>Unit Price (RM)</th>-->
                    <!--<th>KL Qty</th>-->
                    <!--<th>CJ Qty</th>-->
                    <th>KL Price (RM)</th>
                    <th>CJ Price (RM)</th>
                </tr>
            </thead>

            
            <tbody>
                @foreach($networkSummary as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['unit'] ?? '' }}</td>
                    <!--<td>{{ number_format($item['price'], 2) }}</td>-->
                    <!--<td>{{ $item['kl_qty'] }}</td>-->
                    <!--<td>{{ $item['cj_qty'] }}</td>-->
                    <td>{{ number_format($item['kl_price'], 2) }}</td>
                    <td>{{ number_format($item['cj_price'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <th>Total Network</th>
                    <th></th>
                    <th>{{ number_format($klTotal, 2) }}</th>
                     <th>{{ number_format($cjTotal, 2) }}</th>
                </tr>
            </tfoot>
        </table>
