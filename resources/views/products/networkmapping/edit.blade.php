@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h5>Edit Network Mapping</h5>
    </div>

    <div class="card-body">
        {{-- Error bag --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>There were some problems with your input.</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('network-mappings.update', $network_mapping->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Readonly network_code --}}
            <div class="mb-3">
  <label class="form-label">Network Service Code</label>
  <input type="text" class="form-control bg-light" value="{{ $network_mapping->network_code }}" readonly>
 
</div>


            {{-- min_bw --}}
            <div class="mb-3">
                <label for="min_bw" class="form-label">Min BW</label>
                <input type="number" step="1" min="0"
                       name="min_bw" id="min_bw"
                       class="form-control @error('min_bw') is-invalid @enderror"
                       value="{{ old('min_bw', $network_mapping->min_bw) }}">
                @error('min_bw') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- max_bw --}}
            <div class="mb-3">
                <label for="max_bw" class="form-label">Max BW</label>
                <input type="number" step="1" min="0"
                       name="max_bw" id="max_bw"
                       class="form-control @error('max_bw') is-invalid @enderror"
                       value="{{ old('max_bw', $network_mapping->max_bw) }}">
                @error('max_bw') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="form-text">Must be greater than or equal to Min BW.</div>
            </div>

            {{-- eip_foc --}}
            <div class="mb-3">
                <label for="eip_foc" class="form-label">EIP FOC</label>
                <input type="number" step="1" min="0"
                       name="eip_foc" id="eip_foc"
                       class="form-control @error('eip_foc') is-invalid @enderror"
                       value="{{ old('eip_foc', $network_mapping->eip_foc) }}">
                @error('eip_foc') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            {{-- anti_ddos --}}
            <div class="mb-3 form-check">
                <input type="checkbox" name="anti_ddos" id="anti_ddos"
                       class="form-check-input"
                       {{ old('anti_ddos', $network_mapping->anti_ddos) ? 'checked' : '' }}>
                <label for="anti_ddos" class="form-check-label">Anti-DDoS</label>
            </div>

            <button type="submit" class="btn btn-pink">Update</button>
            <a href="{{ route('network-mappings.index') }}" class="btn btn-outline-secondary">Back</a>
        </form>
    </div>  
</div>




@if(($logs ?? collect())->isEmpty())
  <div class="card mt-4">
    <div class="card-body">
      <div class="alert alert-info mb-0">
        No changes have been logged for this network mapping yet.
      </div>
    </div>
  </div>
@else
  <div class="card mt-4 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h6 class="mb-0">Trail Logs — {{ $network_mapping->network_code }}</h6>
      <!---<a href="{{ route('network-mappings.index') }}" class="btn btn-secondary btn-sm">Back to List</a>--->
    </div>

    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="width: 180px;">Date &amp; Time</th>
              <th style="width: 200px;">User</th>
              <th style="width: 180px;">Field</th>
              <th>Old Value</th>
              <th>New Value</th>
            </tr>
          </thead>
          <tbody>
            @foreach($logs as $log)
              @php
                $old = $log->old_values ?? [];
                $new = $log->new_values ?? [];

                // Paparan nama user: cuba .user->name, kalau tiada guna user_id, kalau tiada 'System'
                $userLabel = optional($log->user)->name ?? ($log->user_id ?? 'System');

                // Label cantik untuk field
                $labels = [
                  'network_code' => 'Network Code',
                  'min_bw'       => 'Min BW',
                  'max_bw'       => 'Max BW',
                  'eip_foc'      => 'EIP FOC',
                  'anti_ddos'    => 'Anti-DDoS',
                  'rows_imported'=> 'Rows Imported',
                  'rows_exported'=> 'Rows Exported',
                ];

                // Format nilai (terutamanya boolean)
                $format = function ($key, $val) {
                  if ($key === 'anti_ddos') return $val ? 'Yes' : 'No';
                  return is_array($val) ? json_encode($val) : (string) $val;
                };

                // Kumpul perubahan field-level
                $fieldChanges = [];
                if (in_array($log->action, ['created','updated'])) {
                  foreach ($new as $k => $newVal) {
                    $oldVal = $old[$k] ?? null;
                    if ($oldVal !== $newVal) {
                      $fieldChanges[] = [
                        'key' => $k,
                        'old' => $format($k, $oldVal),
                        'new' => $format($k, $newVal),
                      ];
                    }
                  }
                }
              @endphp

              @if(in_array($log->action, ['created','updated']))
                @if(empty($fieldChanges))
                  {{-- jika tiada beza field (jarang, tapi handle) --}}
                  <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>{{ $userLabel }}</td>
                    <td colspan="3"><em class="text-muted">No field-level changes.</em></td>
                  </tr>
                @else
                  @foreach($fieldChanges as $change)
                    <tr>
                      <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                      <td>{{ $userLabel }}</td>
                      <td>
                        <span class="badge bg-primary">
                          {{ $labels[$change['key']] ?? ucwords(str_replace('_',' ',$change['key'])) }}
                        </span>
                      </td>
                      <td>
                        @php $ov = $change['old']; @endphp
                        @if($ov === null || $ov === '')
                          <em class="text-muted">Empty</em>
                        @else
                          {{ $ov }}
                        @endif
                      </td>
                      <td>
                        @php $nv = $change['new']; @endphp
                        @if($nv === null || $nv === '')
                          <em class="text-muted">Empty</em>
                        @else
                          {{ $nv }}
                        @endif
                      </td>
                    </tr>
                  @endforeach
                @endif
              @else
                {{-- import / export log (ringkas) --}}
                <tr>
                  <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                  <td>{{ $userLabel }}</td>
                  <td>
                    <span class="badge bg-secondary text-uppercase">{{ $log->action }}</span>
                  </td>
                  <td colspan="2">
                    @if($log->action === 'import')
                      Imported <strong>{{ $new['rows_imported'] ?? 0 }}</strong> rows.
                    @elseif($log->action === 'export')
                      Exported <strong>{{ $new['rows_exported'] ?? 0 }}</strong> rows.
                    @else
                      —
                    @endif
                  </td>
                </tr>
              @endif
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endif


@endsection


