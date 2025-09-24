@extends('layouts.app')

@php
    $solution_type = $solution_type ?? $version->solution_type ?? null;
@endphp

@section('content')
<div class="card shadow-sm protect">
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

    @php
    $wm = rawurlencode(
        trim((auth()->user()->name ?? 'User') . ' • ' . (auth()->user()->email ?? ''))
        . ' • ' . now()->format('Y-m-d H:i')
    );
@endphp

{{-- Watermark overlay & print blocker --}}
<div class="screen-watermark" aria-hidden="true"></div>
<div id="print-blocker" aria-hidden="true">Printing is disabled for this page.</div>


<!---<div class="sensitive revealed" id="ratecardSensitive">--->

<div id="ratecardSensitive">

    <div class="card-body">
        @if (in_array(auth()->user()->role, ['admin', 'product','presale']))
        <div class="table-responsive">
            <!---<table class="table table-bordered">--->

            <table class="table table-bordered grid-compact grid-strong-lines">
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
                       

                        <tr class="category-total">
  <td colspan="2">Total</td>
  <td class="text-end">{{ number_format($categoryTotalKL) }}</td>
  <td class="text-end">{{ number_format($categoryTotalCyber) }}</td>
  <td></td>
  <td></td>
</tr>

                    @endforeach
                </tbody>
                <tfoot>
                    <!---<tr class="table-success">--->
                        <tr class="category-total overall-total">

                        <td colspan="5"><strong>Final Total</strong></td>
                        <td class="text-end fw-bold"><strong>RM {{ number_format($grandTotal, 2) }}</strong></td>
                    </tr>
                </tfoot>
            </table>

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
        @else
            <div class="alert alert-warning d-flex justify-content-between align-items-center" role="alert">
                <div>⚠️ <strong>You don't have permission to view the rate card.</strong></div>
            </div>
        @endif
    
</div>

<!---<button id="revealBtn" class="btn btn-warning mt-2">Hold to view</button>--->

     
    </div>
</div>
@push('scripts')
<script>
  // Block Ctrl+P / Cmd+P
  document.addEventListener('keydown', function(e){
    const isMac = navigator.platform.toUpperCase().indexOf('MAC')>=0;
    const pPressed = (e.key || '').toLowerCase() === 'p';
    if (pPressed && ((isMac && e.metaKey) || (!isMac && e.ctrlKey))) {
      e.preventDefault();
      alert('Printing is disabled on this page.');
    }
    // Nota: PrintScreen (PrtSc) tak boleh benar-benar diblok oleh browser.
  });

  // Block right-click
  document.addEventListener('contextmenu', function(e){ e.preventDefault(); }, {capture:true});

  // (Pilihan) blurkan bila tab hilang fokus – deterrent lembut
  // document.addEventListener('visibilitychange', () => {
  //   document.body.style.filter = document.hidden ? 'blur(6px)' : '';
  // });
</script>


@endpush

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

<style>
/* ── Watermark skrin (ulang teks menyerong) ────────────────────────────── */
.screen-watermark{
  position: fixed; inset: 0; pointer-events: none; z-index: 2147483647;
  background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='600' height='400'><text x='0' y='220' font-size='22' fill='rgba(0,0,0,0.12)' transform='rotate(-30 0,200)'>{{ $wm }}</text></svg>");
  background-repeat: repeat;
  background-size: 600px 400px;
}



.sensitive { filter: blur(8px); transition: filter .15s ease; }
.sensitive.revealed { filter: none; }

/* ── Halang copy (page ini sahaja) ─────────────────────────────────────── */
.protect{
  -webkit-user-select: none; user-select: none;
}

/* ── Block printing: sembunyi semua, tunjuk mesej sahaja ───────────────── */
#print-blocker{ display:none; }
@media print{
  body *{ display: none !important; }
  #print-blocker{
    display: block !important;
    position: fixed; top: 40%; left: 50%; transform: translate(-50%,-50%);
    font-size: 22px; font-weight: 700; text-align: center;
  }
  /* Optional: matikan watermark masa print atau kekalkan — pilih satu */
  .screen-watermark{ display:none !important; }
}

/* ——— Table lebih kecil & padat ——— */
.table.grid-compact{
  font-size: 12px;            /* kecilkan font */
  border-collapse: collapse;  /* pastikan garis bersambung across */
}
.table.grid-compact th,
.table.grid-compact td{
  padding: 4px 6px;           /* rapatkan jarak */
  line-height: 1.2;
}

/* ——— Garisan grid lebih jelas (across penuh) ——— */
.table.grid-strong-lines th,
.table.grid-strong-lines td{
  border: 1px solid #bdbdbd !important;   /* kuatkan border sel */
}
.table.grid-strong-lines thead th{
  background: #f6f7f9;
  border-bottom: 2px solid #9aa0a6 !important;
}

/* baris selang-seli sikit supaya senang baca (optional) */
.table.grid-strong-lines tbody tr:nth-child(even){
  background-color: #fcfcfd;
}

/* baris “Total” kategori & “Final Total” lebih jelas */
.table.grid-strong-lines tr.category-total td{
  background: #f9e6f7;
  font-weight: 700;
}
.table.grid-strong-lines tfoot tr td{
  border-top: 2px solid #9aa0a6 !important;
  font-size: 13px;
}

/* Bar tindakan melekat bawah jadual */
.action-bar{
  position: sticky;   /* kekal nampak bila scroll jadual panjang */
  bottom: 0;
  background: #fff;
  padding: 8px 0;
  border-top: 1px solid #eee;
  z-index: 2;         /* atas grid table */
}
.action-bar .btn{ margin-left: .25rem; }


.table.grid-strong-lines tr.overall-total td{
  background: #f6f7f9;
  font-weight: 700;
}

</style>
@endpush
