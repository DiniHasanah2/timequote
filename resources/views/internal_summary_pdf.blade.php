<!DOCTYPE html>
<html>
<head>
    <title>Internal Summary</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }

        .header-table td {
            border: 1px solid #ccc;
            padding: 6px;
            vertical-align: top;
        }

        .header-title {
            color: #6c757d;
            font-size: 10px;
            font-weight: bold;
        }

        .header-value {
            font-weight: bold;
            font-size: 11px;
        }

        .header-id {
            font-size: 9px;
            color: #6c757d;
            margin-top: 3px;
        }

        h2 {
            text-align: center;
            margin-bottom: 0;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-top: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <h2>Internal Summary</h2>

    <!-- ✅ Quotation Header Section -->
    <table class="header-table">
        <tr>
            <td>
                <div class="header-title">PROJECT</div>
                <div class="header-value">{{ $project->name ?? '-' }}</div>
                <div class="header-id">ID: {{ $project->id ?? '-' }}</div>
            </td>
            <td>
                <div class="header-title">CUSTOMER</div>
                <div class="header-value">{{ $project->customer->name ?? '-' }}</div>
                <div class="header-id">ID: {{ $project->customer_id ?? '-' }}</div>
            </td>
            <td>
                <div class="header-title">VERSION</div>
                <div class="header-value">{{ $version->version_name ?? '-' }}</div>
                <div class="header-id">v{{ $version->version_number ?? '-' }}</div>
            </td>
            <td>
                <div class="header-title">PRESALE</div>
                <div class="header-value">{{ $project->presale->name ?? $project->presale->email ?? 'Unassigned' }}</div>
                <div class="header-id">{{ $project->created_at ? $project->created_at->format('d M Y') : '-' }}</div>
            </td>
        </tr>
    </table>

   

    @include('partials.internal_summary_table', [
    'project' => $project,
    'version' => $version,
    'region' => $region,
    'security_service' => $security_service,
    'ecs_configuration' => $ecs_configuration,
    'nonStandardItems' => $nonStandardItems ?? collect() 
])

</body>
</html>
