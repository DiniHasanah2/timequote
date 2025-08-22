@extends('layouts.app')

@section('content')


<div class="card shadow-sm">
    <div class="card-header d-flex justify-between align-items-center">
        <div class="breadcrumb-text">
            <a href="{{ route('versions.region.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.region.create' ? 'active-link' : '' }}">Professional Services</a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.region.network.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.region.network.create' ? 'active-link' : '' }}">Network & Global Services</a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.ecs_configuration.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.ecs_configuration.create' ? 'active-link' : '' }}">ECS Configuration</a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.region.dr.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.region.dr.create' ? 'active-link' : '' }}">DR Settings</a>
            <span class="breadcrumb-separator">»</span>
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
            <a href=" {{ route('versions.download_zip', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.download_zip' ? 'active-link' : '' }}">Download Zip File</a>
        </div>
        <button type="button" class="btn-close" style="margin-left: auto;" onclick="window.location.href='{{ route('projects.index') }}'"></button>

    </div>

    <div class="card-body">



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
 </table>
  <table class="table table-bordered">


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
          
            

 </table>

  <table class="table table-bordered">
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
          </table>        
    <table class="table table-bordered">

            {{-- Network --}}
            <thead class="table-light">
                <tr>
                    <th>Network</th>
                    <th>Unit</th>
                    <th>KL Price (RM)</th>
                    <th>CJ Price (RM)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($networkSummary as $item)
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['unit'] ?? '' }}</td>
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
      


        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('versions.internal_summary.show', $version->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Previous Step
            </a>
            <div> 
                <a href="{{ url('versions/' . $version->id . '/generate-pdf') }}" class="btn btn-pink me-2">
                    <i class="bi bi-download"></i> Download Rate Card
                </a>
                <a href="{{ route('versions.quotation.preview', $version->id) }}" class="btn btn-secondary">
                 Preview Quotation  <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    
</div>
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