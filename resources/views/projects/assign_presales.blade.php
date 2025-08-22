@extends('layouts.app')

@section('content')


    
    <div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Assign Presales to {{ $project->name }}</h5>
        <a href="{{ route('projects.index') }}" class="btn-close" aria-label="Close"></a>
    </div>
    
      <div class="card-body">
        <form method="POST" action="{{ route('projects.assignPresales', $project->id) }}">
            @csrf
            <div class="mb-3">
                <label>Presales</label>
                @foreach($presales as $presale)
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" 
                               name="presale_ids[]" 
                               value="{{ $presale->id }}"
                               {{ in_array($presale->id, $project->assigned_presales->pluck('id')->toArray()) ? 'checked' : '' }}>
                        <label class="form-check-label">{{ $presale->name }}</label>
                    </div>
                @endforeach
            </div>
            <button type="submit" class="btn btn-pink">Save Changes</button>
        </form>
    </div>
</div>
@endsection
