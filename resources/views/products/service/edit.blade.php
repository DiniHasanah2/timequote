@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h5>Edit Service</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('services.update', $service->id) }}" method="POST">
            @csrf
            @method('PUT')

             <div class="mb-3">
                <label for="id" class="form-label">SKU UUID</label>
                <!---<input type="text" name="id" class="form-control bg-light" value="{{ $service->id }}" readonly required>--->
                <input type="text" class="form-control bg-light" value="{{ $service->id }}" readonly required>

            </div>

            <div class="mb-3">
                <label for="category_name" class="form-label">Category Name</label>
                <select name="category_name" id="category_name" class="form-select" onchange="setCategoryData()" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->name }}" 
                            data-id="{{ $category->id }}" 
                            data-code="{{ $category->category_code }}"
                            {{ $service->category_name == $category->name ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <input type="hidden" name="category_id" id="category_id" value="{{ $service->category_id }}">
            <div class="mb-3">
                <label for="category_code" class="form-label">Category Code</label>
                <input type="text" name="category_code" id="category_code" class="form-control bg-light" value="{{ $service->category_code }}" readonly required>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control" value="{{ $service->name }}" required>
            </div>

            <div class="mb-3">
                <label for="code" class="form-label">Service Code</label>
                <input type="text" name="code" class="form-control" value="{{ $service->code }}" required>
            </div>

            <div class="mb-3">
                <label for="measurement_unit" class="form-label">Measurement Unit</label>
                <select name="measurement_unit" class="form-select" required>
                    <option value="Unit" {{ $service->measurement_unit == 'Unit' ? 'selected' : '' }}>Unit</option>
                    <option value="Cluster" {{ $service->measurement_unit == 'Cluster' ? 'selected' : '' }}>Cluster</option>
                    <option value="GB" {{ $service->measurement_unit == 'GB' ? 'selected' : '' }}>GB</option>
                    <option value="Mbps" {{ $service->measurement_unit == 'Mbps' ? 'selected' : '' }}>Mbps</option>
                </select>
            </div>

              <div class="mb-3">
                <label for="charge_duration" class="form-label">Charge Duration</label>
                <input type="text" name="charge_duration" id="charge_duration" class="form-control bg-light" value="{{ $service->charge_duration }}" readonly required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Product Description</label>
                <input type="text" name="description" class="form-control" value="{{ $service->description }}">
            </div>

          
            <div class="mb-3">
    <label for="price_per_unit" class="form-label">Price Per Unit</label>
    <input type="number" name="price_per_unit" id="price_per_unit" class="form-control" value="{{ $service->price_per_unit }}" required min="0" step="any">
</div>

   <div class="mb-3">
    <label for="rate_card_price_per_unit" class="form-label">Rate Card Price Per Unit</label>
    <input type="number" name="rate_card_price_per_unit" id="rate_card_price_per_unit" class="form-control" value="{{ $service->rate_card_price_per_unit }}" required min="0" step="any">
</div>


   <div class="mb-3">
    <label for="transfer_price_per_unit" class="form-label">Transfer Price Per Unit</label>
    <input type="number" name="transfer_price_per_unit" id="transfer_price_per_unit" class="form-control" value="{{ $service->transfer_price_per_unit }}" required min="0" step="any">
</div>

            <button type="submit" class="btn btn-pink">Update</button>
            <a href="{{ route('services.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<!-- Audit Logs Section -->
<div class="card shadow-sm mt-4">
    <div class="card-header">
        <h5>Change History - {{ $service->name }}</h5>
    </div>
    <div class="card-body">
        @php
            $logs = \App\Services\ServiceAuditService::getServiceLogs($service->id)->take(5);
            $totalLogs = \App\Services\ServiceAuditService::getServiceLogs($service->id)->count();
        @endphp
        
        @if($logs->isEmpty())
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No changes have been logged for this service yet.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-sm table-hover">
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
                                <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $log->user_name }}</td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ ucwords(str_replace('_', ' ', $log->field_name)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($log->old_value === null || $log->old_value === '')
                                        <em class="text-muted">-</em>
                                    @else
                                        {{ $log->old_value }}
                                    @endif
                                </td>
                                <td>
                                    @if($log->new_value === null || $log->new_value === '')
                                        <em class="text-muted">-</em>
                                    @else
                                        <strong>{{ $log->new_value }}</strong>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                @if($totalLogs > 5)
                    <div class="text-center mt-3">
                        <a href="{{ route('services.audit-logs', $service->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-history"></i> View All {{ $totalLogs }} Changes
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

<script>
    function setCategoryData() {
        const select = document.getElementById('category_name');
        const selectedOption = select.options[select.selectedIndex];
        document.getElementById('category_id').value = selectedOption.getAttribute('data-id');
        document.getElementById('category_code').value = selectedOption.getAttribute('data-code');
    }
</script>
@endsection
