@extends('layouts.app')

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
            <a href="{{ route('versions.security_service.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.security_service.create' ? 'active-link' : '' }}">Cloud Security</a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.security_service.time.create', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.security_service.time.create' ? 'active-link' : '' }}">
               Time Security Services
            </a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.non_standard_items.create', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.non_standard_items.create' ? 'active-link' : '' }}">Non-Standard Services</a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.internal_summary.show', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.internal_summary.show' ? 'active-link' : '' }}">Internal Summary</a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.quotation.ratecard', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.quotation.ratecard' ? 'active-link' : '' }}">Breakdown Price</a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.quotation.preview', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.quotation.preview' ? 'active-link' : '' }}">Quotation (Monthly)</a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.quotation.annual', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.quotation.annual' ? 'active-link' : '' }}">Quotation (Annual)</a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.download_zip', $version->id) }}" class="breadcrumb-link {{ Route::currentRouteName() === 'versions.download_zip' ? 'active-link' : '' }}">Download Zip File</a>
        </div>
        <button type="button" class="btn-close" style="margin-left: auto;" onclick="window.location.href='{{ route('projects.index') }}'"></button>
    </div>

    <div class="card-body">
        @if (in_array(auth()->user()->role, ['admin', 'product','presale']))
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th></th>
                        <th>Unit</th>
                        <th>Kuala Lumpur</th>
                        <th>Cyberjaya</th>
                        <th>Price per Unit (RM)</th>
                        <th>Total Price (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // === Grouping: guna $item['category'] kalau ada; kalau tak, fallback ke perkataan pertama nama ===
                        $groupedItems = [];
                        foreach($rateCardItems as $item) {
                            $category = $item['category'] ?? explode(' ', $item['name'])[0];
                            $groupedItems[$category][] = $item;
                        }
                        $grandTotal = 0;
                    @endphp

                    @foreach($groupedItems as $category => $items)
                        <tr style="background-color: #e76ccf; font-weight: bold;">
                            <td colspan="6">{{ $category }} Services</td>
                        </tr>
                        @php
                            $categoryTotalKL = 0;
                            $categoryTotalCyber = 0;
                        @endphp
                        @foreach($items as $item)
                            @php
                                $categoryTotalKL   += ($item['region'] === 'Kuala Lumpur') ? (float)$item['quantity'] : 0;
                                $categoryTotalCyber+= ($item['region'] === 'Cyberjaya')   ? (float)$item['quantity'] : 0;
                                $grandTotal        += (float)$item['total_price'];
                            @endphp
                            <tr>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['unit'] }}</td>
                                <td class="text-end">{{ $item['region'] === 'Kuala Lumpur' ? number_format($item['quantity']) : '' }}</td>
                                <td class="text-end">{{ $item['region'] === 'Cyberjaya' ? number_format($item['quantity']) : '' }}</td>
                                <td class="text-end">RM {{ number_format($item['price_per_unit'], 2) }}</td>
                                <td class="text-end fw-bold">RM {{ number_format($item['total_price'], 2) }}</td>
                            </tr>
                        @endforeach
                        <tr style="background-color: #f9e6f7; font-weight: bold;">
                            <td colspan="2">Total</td>
                            <td class="text-end">{{ number_format($categoryTotalKL) }}</td>
                            <td class="text-end">{{ number_format($categoryTotalCyber) }}</td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-success">
                        <td colspan="5"><strong>Final Total</strong></td>
                        <td class="text-end fw-bold"><strong>RM {{ number_format($grandTotal, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
            <div class="alert alert-warning d-flex justify-content-between align-items-center" role="alert">
                <div>⚠️ <strong>You don't have permission to view the rate card.</strong></div>
            </div>
        @endif

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('versions.internal_summary.show', $version->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Previous Step
            </a>
            <div>
                <a href="{{ route('versions.ratecard.pdf', $version->id) }}" class="btn btn-pink me-2">
                    <i class="bi bi-download"></i> Download Rate Card
                </a>
                <a href="{{ route('versions.quotation.preview', $version->id) }}" class="btn btn-secondary">
                    Preview Quotation <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .breadcrumb-link { color: rgb(105, 103, 103); text-decoration: none; }
    .breadcrumb-link:hover { text-decoration: underline; }
    .active-link { font-weight: bold; color: #FF82E6 !important; text-decoration: underline; }
    .breadcrumb-separator { color: #999; }
    .table-responsive { overflow-x: auto; }
    .table th, .table td { vertical-align: middle; }
    .card { box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
</style>
@endpush
