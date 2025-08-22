@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">{{ $customer->name }} Details</h5>
    </div>
    <div class="card-body">
        <p><strong>Industry:</strong> {{ $customer->industry }}</p>
        <p><strong>Client Manager:</strong> {{ $customer->client_manager }}</p>

        <hr>
        <h6>Projects ({{ $customer->projects->count() }})</h6>
        <ul>
            @foreach ($customer->projects as $project)
                <li>{{ $project->name }} - Quotations: {{ $project->quotations->count() }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
