@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Audit Logs - {{ $service->name }}</h5>
        <a href="{{ route('services.index') }}" class="btn btn-secondary btn-sm">Back to Services</a>
    </div>
    <div class="card-body">
        @if($logs->isEmpty())
            <div class="alert alert-info">
                No changes have been logged for this service yet.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>User</th>
                            <th>Field</th>
                            <th>Old Value</th>
                            <th>New Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>{{ $log->user_name }}</td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ ucwords(str_replace('_', ' ', $log->field_name)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->old_value === null || $log->old_value === '')
                                        <em class="text-muted">Empty</em>
                                    @else
                                        {{ $log->old_value }}
                                    @endif
                                </td>
                                <td>
                                    @if($log->new_value === null || $log->new_value === '')
                                        <em class="text-muted">Empty</em>
                                    @else
                                        {{ $log->new_value }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
