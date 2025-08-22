@extends('layouts.app')

@section('content')
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card bg-light text-center p-3">
        <div class="text-muted">Total Quotations</div>
        <h2>{{ $totalQuotations }}</h2>
        @if(session('debug_user'))
<div class="alert alert-info">
    Debug Info:<br>
    User: {{ session('debug_user') }}<br>
    Session ID: {{ session('debug_session') }}
</div>
@endif
      </div>
    </div>
    <div class="col-md-3">
      <div class="card bg-light text-center p-3">
        <div class="text-muted">Pending</div>
        <h2>{{ $pendingQuotations }}</h2>
      </div>
    </div>
  </div>

  <div class="table-responsive bg-white">
    <table class="table table-bordered">
      <thead class="table-secondary">
        <tr>
          <th>Customer Name</th>
          <th>Number of Projects</th>
          <th>Number of Quotations</th>
        </tr>
      </thead>
      <tbody>
        @foreach($customers as $customer)
        <tr>
          <td>
            <a href="{{ route('customers.show', $customer->id) }}">
              {{ $customer->name }}
            </a>
          </td>
          
          <td>{{ $customer->projects_count }}</td>
          <td>{{ $customer->quotations_count }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endsection



