@extends('layouts.app')

@php
    $solution_type = $solution_type ?? $version->solution_type ?? null;
    $locked = !empty($summary) && ($summary->is_logged ?? false);

    $custom  = $custom ?? [];
    $canEdit = true;
    $v = fn($k,$d='') => old("custom.$k", $custom[$k] ?? $d);

    use Illuminate\Support\Str;

    // Text inputs (ghost) for unit/period notes saved under "custom[...]"
    $unit = function(string $key, string $ph='e.g. Month/Year') use ($v) {
        return '<input type="text" name="custom['.e($key).']" class="form-control-plaintext ghost-input" placeholder="'.e($ph).'" value="'.e($v($key,'')).'">';
    };
    $period = function(string $key, string $ph='e.g. 12') use ($v) {
        return '<input type="text" name="custom['.e($key).']" class="form-control-plaintext ghost-input" placeholder="'.e($ph).'" value="'.e($v($key,'')).'">';
    };

    $unitOptions = ['Day','Week','Month','Year'];
@endphp

@section('content')
<div class="card shadow-sm">
    <div class="card-header d-flex justify-between align-items-center">
        <div class="breadcrumb-text">

            <a href="{{ route('versions.solution_type.create', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.solution_type.create' ? 'active-link' : '' }}">
               Solution Type
            </a>
            <span class="breadcrumb-separator">»</span>

            @if(($solution_type->solution_type ?? '') !== 'MP-DRaaS Only')
                <a href="{{ route('versions.region.create', $version->id) }}"
                   class="breadcrumb-link {{ Route::currentRouteName() === 'versions.region.create' ? 'active-link' : '' }}">
                   Professional Services
                </a>
                <span class="breadcrumb-separator">»</span>
            @endif

            <a href="{{ route('versions.region.network.create', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.region.network.create' ? 'active-link' : '' }}">
               Network & Global Services
            </a>
            <span class="breadcrumb-separator">»</span>

            @if(($solution_type->solution_type ?? '') !== 'MP-DRaaS Only')
                <a href="{{ route('versions.backup.create', $version->id) }}"
                   class="breadcrumb-link {{ Route::currentRouteName() === 'versions.backup.create' ? 'active-link' : '' }}">
                   ECS & Backup
                </a>
                <span class="breadcrumb-separator">»</span>
            @endif

            @if(($solution_type->solution_type ?? '') !== 'MP-DRaaS Only')
                <a href="{{ route('versions.region.dr.create', $version->id) }}"
                   class="breadcrumb-link {{ Route::currentRouteName() === 'versions.region.dr.create' ? 'active-link' : '' }}">
                   DR Settings
                </a>
                <span class="breadcrumb-separator">»</span>
            @endif

            @if(($solution_type->solution_type ?? '') !== 'TCS Only')
                <a href="{{ route('versions.mpdraas.create', $version->id) }}"
                   class="breadcrumb-link {{ Route::currentRouteName() === 'versions.mpdraas.create' ? 'active-link' : '' }}">
                   MP-DRaaS
                </a>
                <span class="breadcrumb-separator">»</span>
            @endif

            <a href="{{ route('versions.security_service.create', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.security_service.create' ? 'active-link' : '' }}">
               Cloud Security
            </a>
            <span class="breadcrumb-separator">»</span>

            <a href="{{ route('versions.security_service.time.create', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.security_service.time.create' ? 'active-link' : '' }}">
               Time Security Services
            </a>
            <span class="breadcrumb-separator">»</span>


   <a href="{{ route('versions.non_standard_offerings.create', $version->id) }}"
   class="breadcrumb-link {{ Route::currentRouteName() === 'versions.non_standard_offerings.create' ? 'active-link' : '' }}">
  Standard Services
</a>
<span class="breadcrumb-separator">»</span>

            <a href="{{ route('versions.non_standard_items.create', $version->id) }}"
               class="breadcrumb-link {{ Route::currentRouteName() === 'versions.non_standard_items.create' ? 'active-link' : '' }}">
               3rd Party (Non-Standard)
            </a>
            <span class="breadcrumb-separator">»</span>

            <a href="{{ route('versions.internal_summary.show', $version->id) }}"
               class="breadcrumb-link">
               Internal Summary
            </a>
            <span class="breadcrumb-separator">»</span>

            <a href="{{ route('versions.customization.show', $version->id) }}"
               class="breadcrumb-link active-link">
               Customization
            </a>

            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.quotation.ratecard', $version->id) }}"
               class="breadcrumb-link">
               Breakdown Price
            </a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.quotation.preview', $version->id) }}"
               class="breadcrumb-link">
               Quotation (Monthly)
            </a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.quotation.annual', $version->id) }}"
               class="breadcrumb-link">
               Quotation (Annual)
            </a>
            <span class="breadcrumb-separator">»</span>
            <a href="{{ route('versions.download_zip', $version->id) }}"
               class="breadcrumb-link">
               Download Zip File
            </a>
        </div>

        <button type="button" class="btn-close" style="margin-left: auto;" onclick="window.location.href='{{ route('projects.index') }}'"></button>
    </div>

    @if(session('status'))
        <div class="alert alert-success m-3">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger m-3">{{ session('error') }}</div>
    @endif

    <div class="card-body">
        @if(empty($missing ?? []))
            <div class="table-responsive">

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 border-end">
                            <div class="text-muted small">PROJECT</div>
                            <div class="fw-bold">{{ $project->name }}</div>
                            <div class="text-muted small mt-1">ID: {{ $project->id }}</div>
                        </div>

                        <div class="col-md-3 border-end">
                            <div class="text-muted small">CUSTOMER</div>
                            <div class="fw-bold">{{ $project->customer->name ?? 'N/A' }}</div>
                            <div class="text-muted small mt-1">ID: {{ $project->customer_id }}</div>
                        </div>

                        <div class="col-md-3 border-end">
                            <div class="text-muted small">VERSION</div>
                            <div class="fw-bold">{{ $version->version_name }}</div>
                            <div class="text-muted small mt-1">v{{ $version->version_number }}</div>
                        </div>

                        <div class="col-md-3">
                            <div class="text-muted small">PRESALE</div>
                            <div class="fw-bold">{{ $project->presale->name ?? $project->presale->email ?? 'Unassigned' }}</div>
                            <div class="text-muted small mt-1">{{ $project->created_at->format('d M Y') }}</div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('versions.customization.save', $version->id) }}">
                    @csrf

                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th colspan="2">Customization</th>
                                <th>Duration Unit</th>
                                <th>Kuala Lumpur</th>
                                <th>Cyberjaya</th>
                                <th>Duration Required</th>
                            </tr>
                        </thead>
                        <tbody>

                           

                            <tr class="table-secondary"><td colspan="6" style="background-color: #e76ccf;font-weight: bold;">Professional Services</td></tr>

                            <tr>
                                <td>Professional Services (ONE TIME Provisioning)</td>
                                
                                <td>Days</td>
                                <td>
                                    @php
                                        $current = old('ps_one_time_unit', optional($region)->ps_one_time_unit ?? 'Month');
                                    @endphp
                                    <select
                                        name="ps_one_time_unit"
                                        class="form-select auto-save"
                                        data-field="ps_one_time_unit"
                                        data-version-id="{{ $version->id }}"
                                    >
                                        @foreach ($unitOptions as $opt)
                                            <option value="{{ $opt }}" {{ $current === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td colspan="2">{{ $summary->mandays ?? 0 }}</td>
                                <td>{!! $period('ps_one_time_period') !!}</td>
                            </tr>

                            @php
                                $migUnit = (int)($summary->kl_license_count ?? 0);
                                if ($migUnit <= 0) $migUnit = (int)($summary->cyber_license_count ?? 0);
                                if ($migUnit <= 0) $migUnit = 1;

                                $migMonths = (int)($summary->kl_duration ?? 0);
                                if ($migMonths <= 0) $migMonths = (int)($summary->cyber_duration ?? 0);
                                if ($migMonths <= 0) $migMonths = 1;
                            @endphp

                            <tr>
                                <td>Migration Tools One Time Charge</td>
                                <td>Unit</td>
                                <td>
                                    @php
                                        $field = 'migration_unit';
                                        $current = old($field, optional($region)->$field ?? 'Month');
                                    @endphp
                                    <select
                                        name="{{ $field }}"
                                        class="form-select auto-save"
                                        data-field="{{ $field }}"
                                        data-version-id="{{ $version->id }}"
                                    >
                                        @foreach ($unitOptions as $opt)
                                            <option value="{{ $opt }}" @selected($current === $opt)>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>{{ $migUnit }} Unit</td>
                                <td>{{ $migMonths }} Months</td>
                                <td>{!! $period('migration_period') !!}</td>
                            </tr>

                            <tr class="table-secondary"><td colspan="6" style="background-color: #e76ccf;font-weight: bold;">Managed Services</td></tr>

                            @php
                                $services = [
                                    'Managed Operating System',
                                    'Managed Backup and Restore',
                                    'Managed Patching',
                                    'Managed DR',
                                ];
                            @endphp
                            @foreach($services as $service)
                                @php
                                    $slug = Str::slug($service, '_');
                                    $field = "ms_{$slug}_unit";
                                    $current = old($field, optional($security_service)->$field ?? 'Month');
                                @endphp
                                <tr>
                                    <td>{{ $service }}</td>
                                    <td>VM</td>
                                    <td>
                                        <select
                                            name="{{ $field }}"
                                            class="form-select auto-save"
                                            data-field="{{ $field }}"
                                            data-version-id="{{ $version->id }}"
                                        >
                                            @foreach ($unitOptions as $opt)
                                                <option value="{{ $opt }}" @selected($current === $opt)>{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>{{ $klManagedServices[$service] ?? 0 }}</td>
                                    <td>{{ $cyberManagedServices[$service] ?? 0 }}</td>
                                    <td>{!! $period("ms_{$slug}_period") !!}</td>
                                </tr>
                            @endforeach

                            <tr class="table-secondary"><td colspan="6" style="background-color: #e76ccf;font-weight: bold;">Network</td></tr>

                            <tr>
                                <td>Bandwidth</td>
                                <td>Mbps</td>
                                <td>
                                    @php $f = 'bandwidth_unit'; $cur = old($f, optional($region)->$f ?? 'Month'); @endphp
                                    <select name="{{ $f }}" class="form-select auto-save" data-field="{{ $f }}" data-version-id="{{ $version->id }}">
                                        @foreach($unitOptions as $opt)<option value="{{ $opt }}" @selected($cur===$opt)>{{ $opt }}</option>@endforeach
                                    </select>
                                </td>
                                <td>{{ $summary->kl_bandwidth ?? 0 }}</td>
                                <td>{{ $summary->cyber_bandwidth ?? 0 }}</td>
                                <td>{!! $period('bandwidth_period') !!}</td>
                            </tr>
                            <tr>
                                <td>Bandwidth with Anti-DDoS</td>
                                <td>Mbps</td>
                                <td>
                                    @php $f = 'bandwidth_antiddos_unit'; $cur = old($f, optional($region)->$f ?? 'Month'); @endphp
                                    <select name="{{ $f }}" class="form-select auto-save" data-field="{{ $f }}" data-version-id="{{ $version->id }}">
                                        @foreach($unitOptions as $opt)<option value="{{ $opt }}" @selected($cur===$opt)>{{ $opt }}</option>@endforeach
                                    </select>
                                </td>
                                <td>{{ $summary->kl_bandwidth_with_antiddos ?? 0 }}</td>
                                <td>{{ $summary->cyber_bandwidth_with_antiddos ?? 0 }}</td>
                                <td>{!! $period('bandwidth_antiddos_period') !!}</td>
                            </tr>
                            <tr>
                                <td>Included Elastic IP (FOC)</td>
                                <td>Unit</td>
                                <td>
                                    @php $f = 'included_elastic_ip_unit'; $cur = old($f, optional($region)->$f ?? 'Month'); @endphp
                                    <select name="{{ $f }}" class="form-select auto-save" data-field="{{ $f }}" data-version-id="{{ $version->id }}">
                                        @foreach($unitOptions as $opt)<option value="{{ $opt }}" @selected($cur===$opt)>{{ $opt }}</option>@endforeach
                                    </select>
                                </td>
                                <td>{{ $summary->kl_included_elastic_ip ?? 0 }}</td>
                                <td>{{ $summary->cyber_included_elastic_ip ?? 0 }}</td>
                                <td>{!! $period('included_elastic_ip_period') !!}</td>
                            </tr>
                            <tr>
                                <td>Elastic IP</td>
                                <td>Unit</td>
                                <td>
                                    @php $f = 'elastic_ip_unit'; $cur = old($f, optional($region)->$f ?? 'Month'); @endphp
                                    <select name="{{ $f }}" class="form-select auto-save" data-field="{{ $f }}" data-version-id="{{ $version->id }}">
                                        @foreach($unitOptions as $opt)<option value="{{ $opt }}" @selected($cur===$opt)>{{ $opt }}</option>@endforeach
                                    </select>
                                </td>
                                <td>{{ $summary->kl_elastic_ip ?? 0 }}</td>
                                <td>{{ $summary->cyber_elastic_ip ?? 0 }}</td>
                                <td>{!! $period('elastic_ip_period') !!}</td>
                            </tr>
                            <tr>
                                <td>Elastic Load Balancer (External)</td>
                                <td>Unit</td>
                                <td>
                                    @php $f = 'elb_external_unit'; $cur = old($f, optional($region)->$f ?? 'Month'); @endphp
                                    <select name="{{ $f }}" class="form-select auto-save" data-field="{{ $f }}" data-version-id="{{ $version->id }}">
                                        @foreach($unitOptions as $opt)<option value="{{ $opt }}" @selected($cur===$opt)>{{ $opt }}</option>@endforeach
                                    </select>
                                </td>
                                <td>{{ $summary->kl_elastic_load_balancer ?? 0 }}</td>
                                <td>{{ $summary->cyber_elastic_load_balancer ?? 0 }}</td>
                                <td>{!! $period('elb_external_period') !!}</td>
                            </tr>
                            <tr>
                                <td>Direct Connect Virtual Gateway</td>
                                <td>Unit</td>
                                <td>
                                    @php $f = 'direct_connect_virtual_unit'; $cur = old($f, optional($region)->$f ?? 'Month'); @endphp
                                    <select name="{{ $f }}" class="form-select auto-save" data-field="{{ $f }}" data-version-id="{{ $version->id }}">
                                        @foreach($unitOptions as $opt)<option value="{{ $opt }}" @selected($cur===$opt)>{{ $opt }}</option>@endforeach
                                    </select>
                                </td>
                                <td>{{ $summary->kl_direct_connect_virtual ?? 0 }}</td>
                                <td>{{ $summary->cyber_direct_connect_virtual ?? 0 }}</td>
                                <td>{!! $period('direct_connect_virtual_period') !!}</td>
                            </tr>
                            <tr>
                                <td>L2BR instance</td>
                                <td>Unit</td>
                                <td>
                                    @php $f = 'l2br_instance_unit'; $cur = old($f, optional($region)->$f ?? 'Month'); @endphp
                                    <select name="{{ $f }}" class="form-select auto-save" data-field="{{ $f }}" data-version-id="{{ $version->id }}">
                                        @foreach($unitOptions as $opt)<option value="{{ $opt }}" @selected($cur===$opt)>{{ $opt }}</option>@endforeach
                                    </select>
                                </td>
                                <td>{{ $summary->kl_l2br_instance ?? 0 }}</td>
                                <td>{{ $summary->cyber_l2br_instance ?? 0 }}</td>
                                <td>{!! $period('l2br_instance_period') !!}</td>
                            </tr>
                            <tr>
                                <td>Virtual Private Leased Line (vPLL)</td>
                                <td>Mbps</td>
                                <td>
                                    @php $f = 'vpll_unit'; $cur = old($f, optional($region)->$f ?? 'Month'); @endphp
                                    <select name="{{ $f }}" class="form-select auto-save" data-field="{{ $f }}" data-version-id="{{ $version->id }}">
                                        @foreach($unitOptions as $opt)<option value="{{ $opt }}" @selected($cur===$opt)>{{ $opt }}</option>@endforeach
                                    </select>
                                </td>
                                <td>{{ $summary->kl_virtual_private_leased_line ?? 0 }}</td>
                                <td><input class="form-control bg-light text-muted" disabled></td>
                                <td>{!! $period('vpll_period') !!}</td>
                            </tr>
                            <tr>
                                <td>vPLL L2BR</td>
                                <td>Pair</td>
                                <td>
                                    @php $f = 'vpll_l2br_unit'; $cur = old($f, optional($region)->$f ?? 'Month'); @endphp
                                    <select name="{{ $f }}" class="form-select auto-save" data-field="{{ $f }}" data-version-id="{{ $version->id }}">
                                        @foreach($unitOptions as $opt)<option value="{{ $opt }}" @selected($cur===$opt)>{{ $opt }}</option>@endforeach
                                    </select>
                                </td>
                                <td>{{ $summary->kl_vpll_l2br ?? 0 }}</td>
                                <td><input class="form-control bg-light text-muted" disabled></td>
                                <td>{!! $period('vpll_l2br_period') !!}</td>
                            </tr>
                            <tr>
                                <td>NAT Gateway (Small)</td>
                                <td>Unit</td>
                                <td>
                                    @php $f = 'nat_small_unit'; $cur = old($f, optional($region)->$f ?? 'Month'); @endphp
                                    <select name="{{ $f }}" class="form-select auto-save" data-field="{{ $f }}" data-version-id="{{ $version->id }}">
                                        @foreach($unitOptions as $opt)<option value="{{ $opt }}" @selected($cur===$opt)>{{ $opt }}</option>@endforeach
                                    </select>
                                </td>
                                <td>{{ $summary->kl_nat_gateway_small ?? 0 }}</td>
                                <td>{{ $summary->cyber_nat_gateway_small ?? 0 }}</td>
                                <td>{!! $period('nat_small_period') !!}</td>
                            </tr>
                            <tr>
                                <td>NAT Gateway (Medium)</td>
                                <td>Unit</td>
                                <td>
                                    @php $f = 'nat_medium_unit'; $cur = old($f, optional($region)->$f ?? 'Month'); @endphp
                                    <select name="{{ $f }}" class="form-select auto-save" data-field="{{ $f }}" data-version-id="{{ $version->id }}">
                                        @foreach($unitOptions as $opt)<option value="{{ $opt }}" @selected($cur===$opt)>{{ $opt }}</option>@endforeach
                                    </select>
                                </td>
                                <td>{{ $summary->kl_nat_gateway_medium ?? 0 }}</td>
                                <td>{{ $summary->cyber_nat_gateway_medium ?? 0 }}</td>
                                <td>{!! $period('nat_medium_period') !!}</td>
                            </tr>
                            <tr>
                                <td>NAT Gateway (Large)</td>
                                <td>Unit</td>
                                <td>
                                    @php $f = 'nat_large_unit'; $cur = old($f, optional($region)->$f ?? 'Month'); @endphp
                                    <select name="{{ $f }}" class="form-select auto-save" data-field="{{ $f }}" data-version-id="{{ $version->id }}">
                                        @foreach($unitOptions as $opt)<option value="{{ $opt }}" @selected($cur===$opt)>{{ $opt }}</option>@endforeach
                                    </select>
                                </td>
                                <td>{{ $summary->kl_nat_gateway_large ?? 0 }}</td>
                                <td>{{ $summary->cyber_nat_gateway_large ?? 0 }}</td>
                                <td>{!! $period('nat_large_period') !!}</td>
                            </tr>
                            <tr>
                                <td>NAT Gateway (Extra-Large)</td>
                                <td>Unit</td>
                                <td>
                                    @php $f = 'nat_xlarge_unit'; $cur = old($f, optional($region)->$f ?? 'Month'); @endphp
                                    <select name="{{ $f }}" class="form-select auto-save" data-field="{{ $f }}" data-version-id="{{ $version->id }}">
                                        @foreach($unitOptions as $opt)<option value="{{ $opt }}" @selected($cur===$opt)>{{ $opt }}</option>@endforeach
                                    </select>
                                </td>
                                <td>{{ $summary->kl_nat_gateway_xlarge ?? 0 }}</td>
                                <td>{{ $summary->cyber_nat_gateway_xlarge ?? 0 }}</td>
                                <td>{!! $period('nat_xlarge_period') !!}</td>
                            </tr>
                            <tr>
                                <td>Managed Global Server Load Balancer (GSLB)</td>
                                <td>Domain</td>
                                <td>
                                    @php $f = 'gslb_unit'; $cur = old($f, optional($region)->$f ?? 'Month'); @endphp
                                    <select name="{{ $f }}" class="form-select auto-save" data-field="{{ $f }}" data-version-id="{{ $version->id }}">
                                        @foreach($unitOptions as $opt)<option value="{{ $opt }}" @selected($cur===$opt)>{{ $opt }}</option>@endforeach
                                    </select>
                                </td>
                                <td>{{ $summary->kl_gslb ?? 0 }}</td>
                                <td>{{ $summary->cyber_gslb ?? 0 }}</td>
                                <td>{!! $period('gslb_period') !!}</td>
                            </tr>

                            <tr class="table-secondary"><td colspan="6" style="background-color: #e76ccf;font-weight: bold;">Computing</td></tr>

                            <thead class="table-light">
                                <tr>
                                    <th>Compute - Elastic Cloud Server (ECS)</th>
                                    <th>Sizing</th>
                                    <th></th>
                                    <th>KL.Qty</th>
                                    <th>CJ.Qty</th>
                                    <th></th>
                                </tr>
                            </thead>


                            <tr class="table-secondary"><td colspan="6" style="background-color: #e76ccf;font-weight: bold;">License</td></tr>

                            <thead class="table-light">
                                <tr>
                                    <th>Microsoft</th>
                                    <th>Unit</th>
                                    <th></th>
                                    <th>KL.Qty</th>
                                    <th>CJ.Qty</th>
                                    <th></th>
                                </tr>
                            </thead>

                            @php
                                $licRows = [
                                    ['label' => 'Microsoft Windows Server (Core Pack) - Standard',   'field' => 'ms_windows_std_unit', 'kl' => $licenseSummary['windows_std']['Kuala Lumpur'] ?? 0, 'cj' => $licenseSummary['windows_std']['Cyberjaya'] ?? 0, 'pkey' => 'ms_windows_std_period'],
                                    ['label' => 'Microsoft Windows Server (Core Pack) - Data Center','field' => 'ms_windows_dc_unit', 'kl' => $licenseSummary['windows_dc']['Kuala Lumpur'] ?? 0, 'cj' => $licenseSummary['windows_dc']['Cyberjaya'] ?? 0, 'pkey' => 'ms_windows_dc_period'],
                                    ['label' => 'Microsoft Remote Desktop Services (SAL)',          'field' => 'ms_rds_unit',         'kl' => $licenseSummary['rds']['Kuala Lumpur'] ?? 0,         'cj' => $licenseSummary['rds']['Cyberjaya'] ?? 0,         'pkey' => 'ms_rds_period'],
                                    ['label' => 'Microsoft SQL (Web) (Core Pack)',                  'field' => 'ms_sql_web_unit',     'kl' => $licenseSummary['sql_web']['Kuala Lumpur'] ?? 0,     'cj' => $licenseSummary['sql_web']['Cyberjaya'] ?? 0,     'pkey' => 'ms_sql_web_period'],
                                    ['label' => 'Microsoft SQL (Standard) (Core Pack)',             'field' => 'ms_sql_std_unit',     'kl' => $licenseSummary['sql_std']['Kuala Lumpur'] ?? 0,     'cj' => $licenseSummary['sql_std']['Cyberjaya'] ?? 0,     'pkey' => 'ms_sql_std_period'],
                                    ['label' => 'Microsoft SQL (Enterprise) (Core Pack)',           'field' => 'ms_sql_ent_unit',     'kl' => $licenseSummary['sql_ent']['Kuala Lumpur'] ?? 0,     'cj' => $licenseSummary['sql_ent']['Cyberjaya'] ?? 0,     'pkey' => 'ms_sql_ent_period'],
                                ];
                            @endphp
                            @foreach($licRows as $r)
                                @php $field = $r['field']; $current = old($field, optional($security_service)->$field ?? 'Month'); @endphp
                                <tr>
                                    <td>{{ $r['label'] }}</td>
                                    <td>Unit</td>
                                    <td>
                                        <select name="{{ $field }}" class="form-select auto-save" data-field="{{ $field }}" data-version-id="{{ $version->id }}">
                                            @foreach ($unitOptions as $opt)<option value="{{ $opt }}" @selected($current===$opt)>{{ $opt }}</option>@endforeach
                                        </select>
                                    </td>
                                    <td>{{ $r['kl'] }}</td>
                                    <td>{{ $r['cj'] }}</td>
                                    <td>{!! $period($r['pkey']) !!}</td>
                                </tr>
                            @endforeach

                            <thead class="table-light mt-2">
                                <tr>
                                    <th>Red Hat Enterprise License</th>
                                    <th>Unit</th>
                                    <th></th>
                                    <th>KL.Qty</th>
                                    <th>CJ.Qty</th>
                                    <th></th>
                                </tr>
                            </thead>
                            @php
                                $rhelRows = [
                                    ['label' => 'RHEL (1-8vCPU)',   'field' => 'rhel_1_8_unit',   'kl' => $licenseSummary['rhel_1_8']['Kuala Lumpur'] ?? 0,   'cj' => $licenseSummary['rhel_1_8']['Cyberjaya'] ?? 0,   'pkey' => 'rhel_1_8_period'],
                                    ['label' => 'RHEL (9-127vCPU)', 'field' => 'rhel_9_127_unit', 'kl' => $licenseSummary['rhel_9_127']['Kuala Lumpur'] ?? 0, 'cj' => $licenseSummary['rhel_9_127']['Cyberjaya'] ?? 0, 'pkey' => 'rhel_9_127_period'],
                                ];
                            @endphp
                            @foreach($rhelRows as $r)
                                @php $field = $r['field']; $current = old($field, optional($security_service)->$field ?? 'Month'); @endphp
                                <tr>
                                    <td>{{ $r['label'] }}</td>
                                    <td>Unit</td>
                                    <td>
                                        <select name="{{ $field }}" class="form-select auto-save" data-field="{{ $field }}" data-version-id="{{ $version->id }}">
                                            @foreach ($unitOptions as $opt)<option value="{{ $opt }}" @selected($current===$opt)>{{ $opt }}</option>@endforeach
                                        </select>
                                    </td>
                                    <td>{{ $r['kl'] }}</td>
                                    <td>{{ $r['cj'] }}</td>
                                    <td>{!! $period($r['pkey']) !!}</td>
                                </tr>
                            @endforeach

                            <tr class="table-secondary"><td colspan="6" style="background-color: #e76ccf;font-weight: bold;">Storage</td></tr>

                            <thead class="table-light">
                                <tr>
                                    <th>Storage Type</th>
                                    <th>Unit</th>
                                    <th></th>
                                    <th>KL.Qty</th>
                                    <th>CJ.Qty</th>
                                    <th></th>
                                </tr>
                            </thead>
                            @php
                                $storRows = [
                                    ['label' => 'Elastic Volume Service (EVS)', 'field' => 'evs_unit', 'kl' => $summary->kl_evs ?? 0, 'cj' => $summary->cyber_evs ?? 0, 'pkey' => 'evs_period', 'u'=>'GB'],
                                    ['label' => 'Scalable File Service (SFS)', 'field' => 'sfs_unit', 'kl' => $summary->kl_scalable_file_service ?? 0, 'cj' => $summary->cyber_scalable_file_service ?? 0, 'pkey' => 'sfs_period', 'u'=>'GB'],
                                    ['label' => 'Object Storage Service (OBS)', 'field' => 'obs_unit', 'kl' => $summary->kl_object_storage_service ?? 0, 'cj' => $summary->cyber_object_storage_service ?? 0, 'pkey' => 'obs_period', 'u'=>'GB'],
                                ];
                            @endphp
                            @foreach($storRows as $r)
                                @php $field = $r['field']; $current = old($field, optional($security_service)->$field ?? 'Month'); @endphp
                                <tr>
                                    <td>{{ $r['label'] }}</td>
                                    <td>{{ $r['u'] }}</td>
                                    <td>
                                        <select name="{{ $field }}" class="form-select auto-save" data-field="{{ $field }}" data-version-id="{{ $version->id }}">
                                            @foreach ($unitOptions as $opt)<option value="{{ $opt }}" @selected($current===$opt)>{{ $opt }}</option>@endforeach
                                        </select>
                                    </td>
                                    <td>{{ $r['kl'] }}</td>
                                    <td>{{ $r['cj'] }}</td>
                                    <td>{!! $period($r['pkey']) !!}</td>
                                </tr>
                            @endforeach

                            <thead class="table-light mt-2">
                                <tr>
                                    <th>Image Management Service (IMS)</th>
                                    <th>Unit</th>
                                    <th></th>
                                    <th>KL.Qty</th>
                                    <th>CJ.Qty</th>
                                    <th></th>
                                </tr>
                            </thead>
                            @php
                                $imsRows = [
                                    ['label'=>'Snapshot Storage','field'=>'snapshot_storage_unit','kl'=>$summary->kl_snapshot_storage ?? 0,'cj'=>$summary->cyber_snapshot_storage ?? 0,'pkey'=>'snapshot_storage_period','u'=>'GB'],
                                    ['label'=>'Image Storage','field'=>'image_storage_unit','kl'=>$summary->kl_image_storage ?? 0,'cj'=>$summary->cyber_image_storage ?? 0,'pkey'=>'image_storage_period','u'=>'GB'],
                                ];
                            @endphp
                            @foreach($imsRows as $r)
                                @php $field = $r['field']; $current = old($field, optional($security_service)->$field ?? 'Month'); @endphp
                                <tr>
                                    <td>{{ $r['label'] }}</td>
                                    <td>{{ $r['u'] }}</td>
                                    <td>
                                        <select name="{{ $field }}" class="form-select auto-save" data-field="{{ $field }}" data-version-id="{{ $version->id }}">
                                            @foreach ($unitOptions as $opt)<option value="{{ $opt }}" @selected($current===$opt)>{{ $opt }}</option>@endforeach
                                        </select>
                                    </td>
                                    <td>{{ $r['kl'] }}</td>
                                    <td>{{ $r['cj'] }}</td>
                                    <td>{!! $period($r['pkey']) !!}</td>
                                </tr>
                            @endforeach

                            <tr class="table-secondary"><td colspan="6" style="background-color: #e76ccf;font-weight: bold;">Backup and DR</td></tr>

                            <thead class="table-light">
                                <tr>
                                    <th>Backup Service in VPC</th>
                                    <th>Unit</th>
                                    <th></th>
                                    <th>KL.Qty</th>
                                    <th>CJ.Qty</th>
                                    <th></th>
                                </tr>
                            </thead>
                            @php
                                $bkRows = [
                                    ['label'=>'Cloud Server Backup Service - Full Backup Capacity','field'=>'backup_full_unit','kl'=>number_format($summary->kl_full_backup_capacity ?? 0),'cj'=>number_format($summary->cyber_full_backup_capacity ?? 0),'pkey'=>'backup_full_period','u'=>'GB'],
                                    ['label'=>'Cloud Server Backup Service - Incremental Backup Capacity','field'=>'backup_incremental_unit','kl'=>number_format($summary->kl_incremental_backup_capacity ?? 0),'cj'=>number_format($summary->cyber_incremental_backup_capacity ?? 0),'pkey'=>'backup_incremental_period','u'=>'GB'],
                                    ['label'=>'Cloud Server Replication Service - Retention Capacity','field'=>'replication_retention_unit','kl'=>number_format($summary->kl_replication_retention_capacity ?? 0),'cj'=>number_format($summary->cyber_replication_retention_capacity ?? 0),'pkey'=>'replication_retention_period','u'=>'GB'],
                                ];
                            @endphp
                            @foreach($bkRows as $r)
                                @php $field = $r['field']; $current = old($field, optional($security_service)->$field ?? 'Month'); @endphp
                                <tr>
                                    <td>{{ $r['label'] }}</td>
                                    <td>{{ $r['u'] }}</td>
                                    <td>
                                        <select name="{{ $field }}" class="form-select auto-save" data-field="{{ $field }}" data-version-id="{{ $version->id }}">
                                            @foreach ($unitOptions as $opt)<option value="{{ $opt }}" @selected($current===$opt)>{{ $opt }}</option>@endforeach
                                        </select>
                                    </td>
                                    <td>{{ $r['kl'] }}</td>
                                    <td>{{ $r['cj'] }}</td>
                                    <td>{!! $period($r['pkey']) !!}</td>
                                </tr>
                            @endforeach

                            <thead class="table-light mt-2">
                                <tr>
                                    <th>Disaster Recovery in VPC</th>
                                    <th>Unit</th>
                                    <th></th>
                                    <th>KL.Qty</th>
                                    <th>CJ.Qty</th>
                                    <th></th>
                                </tr>
                            </thead>
                            @php
                                $drvpcRows = [
                                    ['label'=>'Cold DR Days','field'=>'cold_dr_days_unit','kl'=>number_format($summary->kl_cold_dr_days ?? 0,0),'cj'=>number_format($summary->cyber_cold_dr_days ?? 0,0),'pkey'=>'cold_dr_days_period','u'=>'Days'],
                                    ['label'=>'Cold DR - Seeding VM','field'=>'cold_dr_seeding_vm_unit','kl'=>$summary->kl_cold_dr_seeding_vm ?? 0,'cj'=>$summary->cyber_cold_dr_seeding_vm ?? 0,'pkey'=>'cold_dr_seeding_vm_period','u'=>'Unit'],
                                    ['label'=>'Cloud Server Disaster Recovery Storage','field'=>'dr_storage_unit','kl'=>number_format($summary->kl_dr_storage ?? 0,0),'cj'=>number_format($summary->cyber_dr_storage ?? 0,0),'pkey'=>'dr_storage_period','u'=>'GB'],
                                    ['label'=>'Cloud Server Disaster Recovery Replication','field'=>'dr_replication_unit','kl'=>$summary->kl_dr_replication ?? 0,'cj'=>$summary->cyber_dr_replication ?? 0,'pkey'=>'dr_replication_period','u'=>'Unit'],
                                    ['label'=>'Cloud Server Disaster Recovery Days (DR Declaration)','field'=>'dr_declaration_unit','kl'=>number_format($summary->kl_dr_declaration ?? 0,0),'cj'=>number_format($summary->cyber_dr_declaration ?? 0,0),'pkey'=>'dr_declaration_period','u'=>'Days'],
                                    ['label'=>'Cloud Server Disaster Recovery Managed Service - Per Day','field'=>'dr_managed_service_unit','kl'=>$summary->kl_dr_managed_service ?? 0,'cj'=>$summary->cyber_dr_managed_service ?? 0,'pkey'=>'dr_managed_service_period','u'=>'Unit'],
                                ];
                            @endphp
                            @foreach($drvpcRows as $r)
                                @php $field = $r['field']; $current = old($field, optional($security_service)->$field ?? 'Month'); @endphp
                                <tr>
                                    <td>{{ $r['label'] }}</td>
                                    <td>{{ $r['u'] }}</td>
                                    <td>
                                        <select name="{{ $field }}" class="form-select auto-save" data-field="{{ $field }}" data-version-id="{{ $version->id }}">
                                            @foreach ($unitOptions as $opt)<option value="{{ $opt }}" @selected($current===$opt)>{{ $opt }}</option>@endforeach
                                        </select>
                                    </td>
                                    <td>{{ $r['kl'] }}</td>
                                    <td>{{ $r['cj'] }}</td>
                                    <td>{!! $period($r['pkey']) !!}</td>
                                </tr>
                            @endforeach

                            <thead class="table-light mt-2">
                                <tr>
                                    <th>Disaster Recovery Network and Security</th>
                                    <th>Unit</th>
                                    <th></th>
                                    <th>KL.Qty</th>
                                    <th>CJ.Qty</th>
                                    <th></th>
                                </tr>
                            </thead>
                            @php
                                $drnsRows = [
                                    ['label'=>'Cloud Server Disaster Recovery (vPLL)','field'=>'dr_vpll_unit','kl'=>$summary->kl_dr_vpll ?? 0,'cj'=>$summary->cyber_dr_vpll ?? 0,'pkey'=>'dr_vpll_period','u'=>'Mbps'],
                                    ['label'=>'DR Elastic IP','field'=>'dr_elastic_ip_unit','kl'=>$summary->kl_dr_elastic_ip ?? 0,'cj'=>$summary->cyber_dr_elastic_ip ?? 0,'pkey'=>'dr_elastic_ip_period','u'=>'Unit Per Day'],
                                    ['label'=>'DR Bandwidth','field'=>'dr_bandwidth_unit','kl'=>$summary->kl_dr_bandwidth ?? 0,'cj'=>$summary->cyber_dr_bandwidth ?? 0,'pkey'=>'dr_bandwidth_period','u'=>'Mbps Per Day'],
                                    ['label'=>'DR Bandwidth + Anti-DDoS','field'=>'dr_bandwidth_antiddos_unit','kl'=>$summary->kl_dr_bandwidth_antiddos ?? 0,'cj'=>$summary->cyber_dr_bandwidth_antiddos ?? 0,'pkey'=>'dr_bandwidth_antiddos_period','u'=>'Mbps Per Day'],
                                    ['label'=>'DR Cloud Firewall (Fortigate)','field'=>'dr_firewall_fortigate_unit','kl'=>$summary->kl_dr_firewall_fortigate ?? 0,'cj'=>$summary->cyber_dr_firewall_fortigate ?? 0,'pkey'=>'dr_firewall_fortigate_period','u'=>'Unit Per Day'],
                                    ['label'=>'DR Cloud Firewall (OPNSense)','field'=>'dr_firewall_opnsense_unit','kl'=>$summary->kl_dr_firewall_opnsense ?? 0,'cj'=>$summary->cyber_dr_firewall_opnsense ?? 0,'pkey'=>'dr_firewall_opnsense_period','u'=>'Unit Per Day'],
                                ];
                            @endphp
                            @foreach($drnsRows as $r)
                                @php $field = $r['field']; $current = old($field, optional($security_service)->$field ?? 'Month'); @endphp
                                <tr>
                                    <td>{{ $r['label'] }}</td>
                                    <td>{{ $r['u'] }}</td>
                                    <td>
                                        <select name="{{ $field }}" class="form-select auto-save" data-field="{{ $field }}" data-version-id="{{ $version->id }}">
                                            @foreach ($unitOptions as $opt)<option value="{{ $opt }}" @selected($current===$opt)>{{ $opt }}</option>@endforeach
                                        </select>
                                    </td>
                                    <td>{{ $r['kl'] }}</td>
                                    <td>{{ $r['cj'] }}</td>
                                    <td>{!! $period($r['pkey']) !!}</td>
                                </tr>
                            @endforeach

                            <thead class="table-light mt-2">
                                <tr>
                                    <th>Disaster Recovery Resources (During DR Activation)</th>
                                    <th>Unit</th>
                                    <th></th>
                                    <th>KL.Qty</th>
                                    <th>CJ.Qty</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tr>
                                <td>DR Elastic Volume Service (EVS)</td>
                                <td>GB</td>
                                <td>
                                    @php $field='dr_evs_unit'; $current = old($field, optional($security_service)->$field ?? 'Month'); @endphp
                                    <select name="{{ $field }}" class="form-select auto-save" data-field="{{ $field }}" data-version-id="{{ $version->id }}">
                                        @foreach ($unitOptions as $opt)<option value="{{ $opt }}" @selected($current===$opt)>{{ $opt }}</option>@endforeach
                                    </select>
                                </td>
                                <td>{{ $klEvsDR ?? 0 }}</td>
                                <td>{{ $cyberEvsDR ?? 0 }}</td>
                                <td>{!! $period('dr_evs_period') !!}</td>
                            </tr>

                          
                            @foreach(($usedFlavours ?? collect()) as $flavour)
                                @php
                                    $flavourWithDR = $flavour . '.dr';
                                    $details = $flavourDetails->get($flavourWithDR);
                                    $sizing = $details ? "{$details['vcpu']} vCPU , {$details['vram']} vRAM" : '-';
                                    $klQty = $drCountsKL[$flavour] ?? 0;
                                    $cjQty = $drCountsCJ[$flavour] ?? 0;
                                    $slugF = Str::slug($flavour, '_');

                                    $drUnitParent   = 'ecs_dr_units';
                                    $drPeriodParent = 'ecs_dr_periods';
                                    $unitOpts       = $unitOptions;
                                    $currentDrUnit   = data_get($summary ?? null, "{$drUnitParent}.{$slugF}", 'Month');
                                @endphp
                                <tr style="background-color: rgb(251, 194, 224);">
                                    <td>
                                        <a href="{{ route('flavour.index', ['highlight' => $flavourWithDR]) }}">
                                            {{ $flavourWithDR }}
                                        </a>
                                    </td>
                                    <td>{{ $sizing }}</td>
                                    <td>
                                        <select
                                            name="{{ $drUnitParent }}[{{ $slugF }}]"
                                            class="form-select auto-save"
                                            data-json-parent="{{ $drUnitParent }}"
                                            data-json-key="{{ $slugF }}"
                                            data-version-id="{{ $version->id }}"
                                        >
                                            @foreach($unitOpts as $opt)
                                                <option value="{{ $opt }}" @selected($currentDrUnit === $opt)>{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>{{ $klQty }}</td>
                                    <td>{{ $cjQty }}</td>
                                    <td>{!! $period("ecs_dr_{$slugF}_period") !!}</td>
                                </tr>
                            @endforeach

                            <thead class="table-light mt-2">
                                <tr>
                                    <th>Disaster Recovery Licenses</th>
                                    <th>Unit</th>
                                    <th></th>
                                    <th>KL.Qty</th>
                                    <th>CJ.Qty</th>
                                    <th></th>
                                </tr>
                            </thead>
                            @php
                                $drlRows = [
                                    ['label'=>'License Month','field'=>'dr_license_months_unit','kl'=>$summary->kl_dr_license_months ?? 0,'cj'=>$summary->cyber_dr_license_months ?? 0,'pkey'=>'dr_license_months_period','u'=>'Month(s)'],
                                    ['label'=>'DR Per Month - Microsoft Windows Server (Core Pack) - Standard','field'=>'dr_windows_std_unit','kl'=>$summary->kl_dr_windows_std ?? 0,'cj'=>$summary->cyber_dr_windows_std ?? 0,'pkey'=>'dr_windows_std_period','u'=>'Unit Per Month'],
                                    ['label'=>'DR Per Month - Microsoft Windows Server (Core Pack) - Data Center','field'=>'dr_windows_dc_unit','kl'=>$summary->kl_dr_windows_dc ?? 0,'cj'=>$summary->cyber_dr_windows_dc ?? 0,'pkey'=>'dr_windows_dc_period','u'=>'Unit Per Month'],
                                    ['label'=>'DR Per Month - Microsoft Remote Desktop Services (SAL)','field'=>'dr_rds_unit','kl'=>$summary->kl_dr_rds ?? 0,'cj'=>$summary->cyber_dr_rds ?? 0,'pkey'=>'dr_rds_period','u'=>'Unit Per Month'],
                                    ['label'=>'DR Per Month - Microsoft SQL (Web) (Core Pack)','field'=>'dr_sql_web_unit','kl'=>$summary->kl_dr_sql_web ?? 0,'cj'=>$summary->cyber_dr_sql_web ?? 0,'pkey'=>'dr_sql_web_period','u'=>'Unit Per Month'],
                                    ['label'=>'DR Per Month - Microsoft SQL (Standard) (Core Pack)','field'=>'dr_sql_std_unit','kl'=>$summary->kl_dr_sql_std ?? 0,'cj'=>$summary->cyber_dr_sql_std ?? 0,'pkey'=>'dr_sql_std_period','u'=>'Unit Per Month'],
                                    ['label'=>'DR Per Month - Microsoft SQL (Enterprise) (Core Pack)','field'=>'dr_sql_ent_unit','kl'=>$summary->kl_dr_sql_ent ?? 0,'cj'=>$summary->cyber_dr_sql_ent ?? 0,'pkey'=>'dr_sql_ent_period','u'=>'Unit Per Month'],
                                    ['label'=>'DR Per Month - RHEL (1–8vCPU)','field'=>'dr_rhel_1_8_unit','kl'=>$summary->kl_dr_rhel_1_8 ?? 0,'cj'=>$summary->cyber_dr_rhel_1_8 ?? 0,'pkey'=>'dr_rhel_1_8_period','u'=>'Unit Per Month'],
                                    ['label'=>'DR Per Month - RHEL (9–127vCPU)','field'=>'dr_rhel_9_127_unit','kl'=>$summary->kl_dr_rhel_9_127 ?? 0,'cj'=>$summary->cyber_dr_rhel_9_127 ?? 0,'pkey'=>'dr_rhel_9_127_period','u'=>'Unit Per Month'],
                                ];
                            @endphp
                            @foreach($drlRows as $r)
                                @php $field = $r['field']; $current = old($field, optional($security_service)->$field ?? 'Month'); @endphp
                                <tr>
                                    <td>{{ $r['label'] }}</td>
                                    <td>{{ $r['u'] }}</td>
                                    <td>
                                        <select name="{{ $field }}" class="form-select auto-save" data-field="{{ $field }}" data-version-id="{{ $version->id }}">
                                            @foreach ($unitOptions as $opt)<option value="{{ $opt }}" @selected($current===$opt)>{{ $opt }}</option>@endforeach
                                        </select>
                                    </td>
                                    <td>{{ $r['kl'] }}</td>
                                    <td>{{ $r['cj'] }}</td>
                                    <td>{!! $period($r['pkey']) !!}</td>
                                </tr>
                            @endforeach

                            <tr class="table-secondary"><td colspan="6" style="background-color: #e76ccf;font-weight: bold;">Additional Services</td></tr>

                            <thead class="table-light">
                                <tr>
                                    <th>Cloud Security</th>
                                    <th>Unit</th>
                                    <th></th>
                                    <th>KL.Qty</th>
                                    <th>CJ.Qty</th>
                                    <th></th>
                                </tr>
                            </thead>
                            @php
                                $secRows = [
                                    ['label'=>'Cloud Firewall (Fortigate)','field'=>'fw_fortigate_unit','kl'=>$summary->kl_firewall_fortigate ?? 0,'cj'=>$summary->cyber_firewall_fortigate ?? 0,'pkey'=>'fw_fortigate_period','u'=>'Unit'],
                                    ['label'=>'Cloud Firewall (OPNSense)','field'=>'fw_opnsense_unit','kl'=>$summary->kl_firewall_opnsense ?? 0,'cj'=>$summary->cyber_firewall_opnsense ?? 0,'pkey'=>'fw_opnsense_period','u'=>'Unit'],
                                    ['label'=>'Cloud Shared WAF (Mbps)','field'=>'shared_waf_unit','kl'=>$summary->kl_shared_waf ?? 0,'cj'=>$summary->cyber_shared_waf ?? 0,'pkey'=>'shared_waf_period','u'=>'Mbps'],
                                    ['label'=>'Anti-Virus (Panda)','field'=>'antivirus_unit','kl'=>$summary->kl_antivirus ?? 0,'cj'=>$summary->cyber_antivirus ?? 0,'pkey'=>'antivirus_period','u'=>'Unit'],
                                ];
                            @endphp
                            @foreach($secRows as $r)
                                @php $field = $r['field']; $current = old($field, optional($security_service)->$field ?? 'Month'); @endphp
                                <tr>
                                    <td>{{ $r['label'] }}</td>
                                    <td>{{ $r['u'] }}</td>
                                    <td>
                                        <select name="{{ $field }}" class="form-select auto-save" data-field="{{ $field }}" data-version-id="{{ $version->id }}">
                                            @foreach ($unitOptions as $opt)<option value="{{ $opt }}" @selected($current===$opt)>{{ $opt }}</option>@endforeach
                                        </select>
                                    </td>
                                    <td>{{ $r['kl'] }}</td>
                                    <td>{{ $r['cj'] }}</td>
                                    <td>{!! $period($r['pkey']) !!}</td>
                                </tr>
                            @endforeach

                            <thead class="table-light mt-2">
                                <tr>
                                    <th>Security Services</th>
                                    <th>Unit</th>
                                    <th></th>
                                    <th>KL.Qty</th>
                                    <th>CJ.Qty</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tr>
                                <td>Cloud Vulnerability Assessment (Per IP)</td>
                                <td>Mbps</td>
                                <td>
                                    @php $field='cloud_va_unit'; $current = old($field, optional($security_service)->$field ?? 'Month'); @endphp
                                    <select name="{{ $field }}" class="form-select auto-save" data-field="{{ $field }}" data-version-id="{{ $version->id }}">
                                        @foreach ($unitOptions as $opt)<option value="{{ $opt }}" @selected($current===$opt)>{{ $opt }}</option>@endforeach
                                    </select>
                                </td>
                                <td>{{ $summary->kl_cloud_vulnerability ?? 0 }}</td>
                                <td>{{ $summary->cyber_cloud_vulnerability ?? 0 }}</td>
                                <td>{!! $period('cloud_va_period') !!}</td>
                            </tr>

                            <thead class="table-light mt-2">
                                <tr>
                                    <th>Monitoring Service</th>
                                    <th>Unit</th>
                                    <th></th>
                                    <th>KL.Qty</th>
                                    <th>CJ.Qty</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tr>
                                <td>TCS inSight vMonitoring</td>
                                <td>Unit</td>
                                <td>
                                    @php $field='insight_vmonitoring_unit'; $current = old($field, optional($security_service)->$field ?? 'Month'); @endphp
                                    <select name="{{ $field }}" class="form-select auto-save" data-field="{{ $field }}" data-version-id="{{ $version->id }}">
                                        @foreach ($unitOptions as $opt)<option value="{{ $opt }}" @selected($current===$opt)>{{ $opt }}</option>@endforeach
                                    </select>
                                </td>
                                <td>{{ ($summary->kl_insight_vmonitoring ?? 0) == 1 ? 1 : 0 }}</td>
                                <td>{{ ($summary->cyber_insight_vmonitoring ?? 0) == 1 ? 1 : 0 }}</td>
                                <td>{!! $period('insight_vmonitoring_period') !!}</td>
                            </tr>

                            @if($nonStandardItems && $nonStandardItems->count())
                                <tr class="table-secondary"><td colspan="6" class="p-1"></td></tr>
                                <thead class="table-light">
                                    <tr>
                                        <th colspan="6">Non-Standard Item Services</th>
                                    </tr>
                                    <tr>
                                        <th>Item</th>
                                        <th>Unit</th>
                                        <th></th>
                                        <th>Quantity</th>
                                        <th>Selling Price (RM)</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                @foreach($nonStandardItems as $item)
                                    @php
                                        $idKey = 'nsi_'.$item->id;

                                        // local helper for NSI unit select (optional)
                                        $notes = $notes ?? [];
                                        $unitSel = function (string $name, string $mode = 'required') use ($version, $notes, $unitOptions) {
                                            $current = old($name, $notes[$name] ?? 'Month');
                                            $requiredAttr = $mode === 'required' ? 'required' : '';
                                            $html  = '<select name="'.e($name).'" class="form-select auto-save" data-field="'.e($name).'" data-version-id="'.e($version->id).'" '.$requiredAttr.'>';
                                            foreach ($unitOptions as $opt) {
                                                $sel = ($current === $opt) ? ' selected' : '';
                                                $html .= '<option value="'.e($opt).'"'.$sel.'>'.$opt.'</option>';
                                            }
                                            $html .= '</select>';
                                            return $html;
                                        };
                                    @endphp
                                    <tr>
                                        <td>{{ $item->item_name }}</td>
                                        <td>{{ $item->unit }}</td>
                                        <td>{!! $unitSel($idKey.'_unit_note','optional') !!}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->selling_price, 2) }}</td>
                                        <td>{!! $period($idKey.'_period_note','optional') !!}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-pink">Save Customization</button>
                    </div>
                </form>

            </div>
        @else
            <div class="alert alert-warning d-flex justify-content-between align-items-center" role="alert">
                <div>
                    ⚠️ <strong>Please fill all required sections before viewing Internal Summary.</strong>
                </div>
            </div>
        @endif

        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('versions.internal_summary.show', $version->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Internal Summary
            </a>
            <span></span>
            <a href="{{ route('versions.quotation.ratecard', $version->id) }}" class="btn btn-secondary">
                Preview Rate Card <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
  .breadcrumb-link { color:#696767; text-decoration:none; }
  .breadcrumb-link:hover { text-decoration:underline; }
  .active-link { font-weight:bold; color:#ff66cc; text-decoration: underline; }
  .breadcrumb-separator { color:#999; }

  .ghost-input{
    outline: none !important;
    box-shadow: none !important;
    border: 0 !important;
    background: transparent !important;
    padding: 0 !important;
    min-width: 90px;
  }
  .ghost-input::placeholder{ color:#bdbdbd; }
</style>
@endpush
