@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-light text-center p-3">
                <div class="text-muted">Total Quotations</div>
                <h2>{{ $totalQuotations }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light text-center p-3">
                <div class="text-muted">Pending Quotations</div>
                <h2>{{ $pendingQuotations }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light text-center p-3">
                <div class="text-muted">Total Users</div>
                <h2>{{ $totalUsers }}</h2>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="table-secondary">
                        <tr>
                         
                            <th>Customer Name</th>
                               <th>Username</th>
                              <th>Projects</th>
                              
                            
                          
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
                              <td>
                                @if($customer->presale)
                                    {{ $customer->presale->name }}
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </td>
                           
                            
                           
                               <td>{{ $customer->projects_count }}</td>
                              
                          
                         
                            
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection