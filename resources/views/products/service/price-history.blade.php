@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
  <div class="card-header py-2">
    <h6 class="mb-0">
      Price History: {{ $service->name }} ({{ $service->code }})
    </h6>
  </div>
  <div class="card-body p-3">
    <table class="table table-sm table-striped align-middle">
      <thead class="table-light">
        <tr>
          <th style="min-width:140px">Version</th>
          <th style="min-width:120px">Effective From</th>
          <th class="text-end">Price/Unit</th>
          <th class="text-end">Price/Unit<br>(Changes)</th>
          <th class="text-end">Rate Card/Unit</th>
           <th class="text-end">Rate Card/Unit<br>(Changes)</th>
          <th class="text-end">Transfer/Unit</th>
          <th class="text-end">Transfer/Unit<br>(Changes)</th>
        </tr>
      </thead>
      <tbody>
      @forelse($versions as $v)
        <tr>
          <td><strong>{{ $v['row']->version_name }}</strong></td>
          <td>{{ optional(\Carbon\Carbon::parse($v['row']->effective_from))->format('d M Y') }}</td>
          <td class="text-end">{{ number_format((float)$v['row']->price_per_unit, 4) }}</td>
          <td class="text-end">
            @if(!is_null($v['delta']['ppu']))
              {{ number_format($v['delta']['ppu'], 4) }}
            @endif
          </td>
          <td class="text-end">{{ number_format((float)$v['row']->rate_card_price_per_unit, 4) }}</td>
          <td class="text-end">
            @if(!is_null($v['delta']['rcpu']))
              {{ number_format($v['delta']['rcpu'], 4) }}
            @endif
          </td>
          <td class="text-end">{{ number_format((float)$v['row']->transfer_price_per_unit, 4) }}</td>
          <td class="text-end">
            @if(!is_null($v['delta']['tpu']))
              {{ number_format($v['delta']['tpu'], 4) }}
            @endif
          </td>
        </tr>
      @empty
        <tr><td colspan="8" class="text-muted text-center">No history.</td></tr>
      @endforelse
      </tbody>
    </table>

    <a href="{{ route('services.index', ['catalog' => request('catalog')]) }}" class="btn btn-outline-secondary mt-2">
      Back
    </a>
  </div>
</div>
@endsection
