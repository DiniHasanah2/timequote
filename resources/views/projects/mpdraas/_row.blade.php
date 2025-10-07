
@php
  $v = $vm ?? (object)[];
  $isNumericIndex = is_numeric($i);
@endphp



<tr class="vm-row"
    data-index="{{ $i }}"
    data-id="{{ $v->id ?? '' }}"
    data-delete-url="{{ isset($v->id) ? route('versions.mpdraas.vms.destroy', [$version->id, $v->id]) : '' }}">

  <td class="row-no">
    @if($isNumericIndex)
      {{ (int)$i + 1 }}
    @else
      <span class="js-row-no"></span>
    @endif
  </td>

 

  <!---<td>
  <input name="rows[{{ $i }}][vm_name]" class="form-control vm_name w-100"
         value="{{ old("rows.$i.vm_name", $v->vm_name ?? '') }}">
</td>--->

<td class="vm-col-name" style="min-width: 150px;">
  <input name="rows[{{ $i }}][vm_name]" class="form-control vm_name w-100"
         value="{{ old("rows.$i.vm_name", $v->vm_name ?? '') }}">
</td>


  <td>
  <select name="rows[{{ $i }}][always_on]"
          class="form-select always_on w-100"
          style="min-width:140px;">
    @foreach(['No','Yes'] as $opt)
      <option value="{{ $opt }}"
        @selected(old("rows.$i.always_on", $v->always_on ?? 'No') === $opt)>
        {{ $opt }}
      </option>
    @endforeach
  </select>
</td>

<td>
  <select name="rows[{{ $i }}][pin]"
          class="form-select pin w-100"
          style="min-width: 140px;">
    @foreach(['No','Yes'] as $opt)
      <option value="{{ $opt }}" @selected(($v->pin ?? 'No') === $opt)>{{ $opt }}</option>
    @endforeach
  </select>
</td>


  <td>
    <input type="number" min="0" name="rows[{{ $i }}][vcpu]" class="form-control vcpu w-100"  style="min-width: 140px;"
           value="{{ old("rows.$i.vcpu", $v->vcpu ?? 0) }}">
  </td>

  <td>
    <input type="number" min="0" name="rows[{{ $i }}][vram]" class="form-control vram w-100"  style="min-width: 140px;"
           value="{{ old("rows.$i.vram", $v->vram ?? 0) }}">
  </td>

  <td>
    <input name="rows[{{ $i }}][flavour_mapping]" class="form-control flavour_mapping"
           value="{{ old("rows.$i.flavour_mapping", $v->flavour_mapping ?? '') }}" readonly style="background-color: black;color: white;">
  </td>

  <td>
    <input type="number" min="0" name="rows[{{ $i }}][system_disk]" class="form-control system_disk"
           value="{{ old("rows.$i.system_disk", $v->system_disk ?? 0) }}">
  </td>

  <td>
    <input type="number" min="0" name="rows[{{ $i }}][data_disk]" class="form-control data_disk"
           value="{{ old("rows.$i.data_disk", $v->data_disk ?? 0) }}">
  </td>


  




  <td class="vm-col-os" style="min-width: 250px;">
  <select name="rows[{{ $i }}][operating_system]"
          class="form-select operating_system w-100"
          style="min-width:140px;">
    @foreach(['Linux','Microsoft Windows Std','Microsoft Windows DC','Red Hat Enterprise Linux'] as $opt)
      <option value="{{ $opt }}"
        @selected(old("rows.$i.operating_system", $v->operating_system ?? 'Linux') === $opt)>
        {{ $opt }}
      </option>
    @endforeach
  </select>
</td>


  <td>
    <input type="number" min="0" name="rows[{{ $i }}][rds_count]" class="form-control rds_count"
           value="{{ old("rows.$i.rds_count", $v->rds_count ?? 0) }}">
  </td>

  <td>
  <select name="rows[{{ $i }}][m_sql]"
          class="form-select m_sql w-100"
          style="min-width:140px;">
    @foreach(['None','Web','Standard','Enterprise'] as $opt)
      <option value="{{ $opt }}"
        @selected(old("rows.$i.m_sql", $v->m_sql ?? 'None') === $opt)>
        {{ $opt }}
      </option>
    @endforeach
  </select>
</td>


  <td>
    <input name="rows[{{ $i }}][used_system_disk]" class="form-control used_system_disk" readonly style="background-color: black;color: white;"
           value="{{ old("rows.$i.used_system_disk", $v->used_system_disk ?? 0) }}">
  </td>

  <td>
    <input name="rows[{{ $i }}][used_data_disk]" class="form-control used_data_disk" readonly style="background-color: black;color: white;"
           value="{{ old("rows.$i.used_data_disk", $v->used_data_disk ?? 0) }}">
  </td>

  <td>
  <select name="rows[{{ $i }}][solution_type]"
          class="form-select solution_type w-100"
          style="min-width:140px;">
    @foreach(['None','EVS'] as $opt)
      <option value="{{ $opt }}"
        @selected(old("rows.$i.solution_type", $v->solution_type ?? 'None') === $opt)>
        {{ $opt }}
      </option>
    @endforeach
  </select>
</td>


  <td>
    <input type="number" min="0" name="rows[{{ $i }}][rto_expected]" class="form-control rto_expected"
           value="{{ old("rows.$i.rto_expected", $v->rto_expected ?? 0) }}">
  </td>

  <td>
    <input type="number" min="0" name="rows[{{ $i }}][dd_change]" class="form-control dd_change"
           value="{{ old("rows.$i.dd_change", $v->dd_change ?? 0) }}">
  </td>

  <td>
    <input name="rows[{{ $i }}][data_change]" class="form-control data_change" readonly style="background-color: black;color: white;"
           value="{{ old("rows.$i.data_change", $v->data_change ?? 0) }}">
  </td>

  <td>
    <input name="rows[{{ $i }}][data_change_size]" class="form-control data_change_size" readonly style="background-color: black;color: white;"
           value="{{ old("rows.$i.data_change_size", $v->data_change_size ?? 0) }}">
  </td>

  <td>
    <input type="number" min="0" name="rows[{{ $i }}][replication_frequency]" class="form-control replication_frequency"
           value="{{ old("rows.$i.replication_frequency", $v->replication_frequency ?? 0) }}">
  </td>

  <td>
    <input name="rows[{{ $i }}][num_replication]" class="form-control num_replication" readonly style="background-color: black;color: white;"
           value="{{ old("rows.$i.num_replication", $v->num_replication ?? 0) }}">
  </td>

  <td>
    <input name="rows[{{ $i }}][amount_data_change]" class="form-control amount_data_change" readonly style="background-color: black;color: white;"
           value="{{ old("rows.$i.amount_data_change", $v->amount_data_change ?? 0) }}">
  </td>

  <td>
    <input name="rows[{{ $i }}][replication_bandwidth]" class="form-control replication_bandwidth" readonly style="background-color: black;color: white;"
           value="{{ old("rows.$i.replication_bandwidth", $v->replication_bandwidth ?? 0) }}">
  </td>

  <td>
    <input name="rows[{{ $i }}][rpo_achieved]" class="form-control rpo_achieved" readonly style="background-color: black;color: white;"
           value="{{ old("rows.$i.rpo_achieved", $v->rpo_achieved ?? 0) }}">
  </td>

  <td class="text-center">
  <!---<input type="checkbox" class="form-check-input vm-check me-2">--->
  <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row">
    Delete
  </button>
</td>

</tr>
