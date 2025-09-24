@extends('layouts.app')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <h5>Error!</h5>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0">Edit ECS Flavour</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('ecs-flavours.update', $ecs_flavour->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="ecs_code" class="form-label">ECS Service Code</label>
                <input type="text" name="ecs_code" id="ecs_code" class="form-control" 
                       value="{{ old('ecs_code', $ecs_flavour->ecs_code) }}" required>
            </div>

            <div class="mb-3">
                <label for="flavour_name" class="form-label">Product Name</label>
                <input type="text" name="flavour_name" id="flavour_name" class="form-control" 
                       value="{{ old('flavour_name', $ecs_flavour->flavour_name) }}" required>
            </div>

            <div class="mb-3">
                <label for="vCPU" class="form-label">vCPU</label>
                <input type="number" name="vCPU" id="vCPU" class="form-control" min="0"
                       value="{{ old('vCPU', $ecs_flavour->vCPU) }}" required>
            </div>

            <div class="mb-3">
                <label for="RAM" class="form-label">RAM</label>
                <input type="number" name="RAM" id="RAM" class="form-control" min="0"
                       value="{{ old('RAM', $ecs_flavour->RAM) }}" required>
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <select name="type" id="type" class="form-select" required>
                    <option value="m" {{ old('type', $ecs_flavour->type) == 'm' ? 'selected' : '' }}>m</option>
                    <option value="c" {{ old('type', $ecs_flavour->type) == 'c' ? 'selected' : '' }}>c</option>
                    <option value="r" {{ old('type', $ecs_flavour->type) == 'r' ? 'selected' : '' }}>r</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="generation" class="form-label">Generation</label>
                <input type="text" name="generation" id="generation" class="form-control" 
                       value="{{ old('generation', $ecs_flavour->generation) }}" required>
            </div>

            <div class="mb-3">
                <label for="memory_label" class="form-label">Memory Label</label>
                <input type="text" name="memory_label" id="memory_label" class="form-control" 
                       value="{{ old('memory_label', $ecs_flavour->memory_label) }}" required>
            </div>

            <div class="mb-3">
                <label for="windows_license_count" class="form-label">Windows License Count</label>
                <input type="number" name="windows_license_count" id="windows_license_count" class="form-control" min="0"
                       value="{{ old('windows_license_count', $ecs_flavour->windows_license_count) }}" required>
            </div>

            <div class="mb-3">
                <label for="red_hat_enterprise_license_count" class="form-label">Red Hat Enterprise License Count</label>
                <input type="number" name="red_hat_enterprise_license_count" id="red_hat_enterprise_license_count" class="form-control" min="0"
                       value="{{ old('red_hat_enterprise_license_count', $ecs_flavour->red_hat_enterprise_license_count) }}" required>
            </div>

            <div class="form-check mb-2">
                <input type="checkbox" name="dr" id="dr" class="form-check-input"
                       {{ old('dr', $ecs_flavour->dr) ? 'checked' : '' }}>
                <label for="dr" class="form-check-label">DR</label>
            </div>

             <div class="form-check mb-2">
                <input type="checkbox" name="pin" id="pin" class="form-check-input"
                       {{ old('pin', $ecs_flavour->pin) ? 'checked' : '' }}>
                <label for="pin" class="form-check-label">Pin</label>
            </div>

            <div class="form-check mb-2">
                <input type="checkbox" name="gpu" id="gpu" class="form-check-input"
                       {{ old('gpu', $ecs_flavour->gpu) ? 'checked' : '' }}>
                <label for="gpu" class="form-check-label">GPU</label>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="dedicated_host" id="dedicated_host" class="form-check-input"
                       {{ old('dedicated_host', $ecs_flavour->dedicated_host) ? 'checked' : '' }}>
                <label for="dedicated_host" class="form-check-label">Dedicated Host</label>
            </div>

            <div class="mb-3">
                <label for="microsoft_sql_license_count" class="form-label">Microsoft SQL License Count</label>
                <input type="number" name="microsoft_sql_license_count" id="microsoft_sql_license_count" class="form-control" min="0"
                       value="{{ old('microsoft_sql_license_count', $ecs_flavour->microsoft_sql_license_count) }}" required>
            </div>

            <button type="submit" class="btn btn-pink">Update</button>
            <a href="{{ route('ecs-flavours.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
