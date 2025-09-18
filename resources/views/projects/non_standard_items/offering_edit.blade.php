@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Edit Non-Standard Offering</h5>
    <a href="{{ route('versions.non_standard_items.create', $versionId) }}" class="btn btn-secondary">Back</a>
  </div>
  <div class="card-body">
    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('versions.non_standard_offerings.update', [$versionId, $offering->id]) }}" class="row g-3">
      @csrf @method('PUT')

      <div class="col-md-3">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select">
          <option value="">— Select —</option>
          @foreach($categories as $c)
            <option value="{{ $c->id }}" @selected($offering->category_id === $c->id)>{{ $c->name }} ({{ $c->category_code }})</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-5">
        <label class="form-label">Service</label>
        <select name="service_id" class="form-select">
          <option value="">— Select —</option>
          @foreach($services as $s)
            <option value="{{ $s->id }}" @selected($offering->service_id === $s->id)>{{ $s->name }} {{ $s->code ? '(' . $s->code . ')' : '' }}</option>
          @endforeach
        </select>
      </div>

      <div class="col-md-2">
        <label class="form-label">Unit</label>
        <input type="text" name="unit" class="form-control bg-light" readonly value="{{ $offering->unit }}">
      </div>

      <div class="col-md-2">
        <label class="form-label">Quantity</label>
        <input type="number" name="quantity" class="form-control" min="1" value="{{ $offering->quantity }}" required>
      </div>

      <div class="col-md-2">
        <label class="form-label">Months</label>
        <input type="number" name="months" class="form-control" min="1" value="{{ $offering->months }}" required>
      </div>

      <div class="col-md-3">
        <label class="form-label">Unit Price / month (RM)</label>
        <input type="number" name="unit_price_per_month" class="form-control" step="0.0001" min="0" value="{{ number_format($offering->unit_price_per_month, 4, '.', '') }}" required>
      </div>

      <div class="col-md-2">
        <label class="form-label">Markup %</label>
        <input type="number" name="mark_up" class="form-control" min="0" value="{{ number_format($offering->mark_up, 2, '.', '') }}">
      </div>

      <div class="col-12">
        <button class="btn btn-pink" type="submit">Update</button>
        <a href="{{ route('versions.non_standard_items.create', $versionId) }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
