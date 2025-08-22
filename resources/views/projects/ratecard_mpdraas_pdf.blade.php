<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>MP-DRaaS Rate Card</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header .left-text {
            font-weight: bold;
        }
        .logo img {
            height: 40px;
        }
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            background-color: #ccc;
            padding: 6px;
            border: 1px solid #000;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
        }
        thead {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <!-- Header with Confidential text and logo top-right -->
    <div class="header" style="position: relative; height: 40px; margin-bottom: 20px;">
        <div>Confidential | {{ now()->format('d M Y') }}</div>
        <div style="position: absolute; top: 0; right: 0;">
            <img src="{{ public_path('assets/time_logo.png') }}" alt="Logo" height="35">
        </div>
    </div>


    <div class="title">MP-DRaaS Rate Card</div>

    @php
        $sections = [
            'professional_services' => 'Professional Services',
            'compute' => 'Compute (During DR Activation)',
            'licenses' => 'Licenses (Per Month)',
            'network_services' => 'Network Services (During DR Activation)',
            'security_services' => 'Security Services (During DR Activation)',
            'dr_replication' => 'DR Replication',
            'dr_storage' => 'DR Storage',
            'dr_network' => 'DR Network',
            'dr_security' => 'DR Security'
        ];
        $pricing = config('pricing.mpdraas');
    @endphp

    @foreach($sections as $key => $label)
        <div class="section-title">{{ $label }}</div>
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Unit</th>
                    <th>Measurement</th>
                    <th>Price (RM)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pricing[$key] ?? [] as $item)
                    <tr>
                        <td>{{ $item['description'] }}</td>
                        <td>{{ $item['unit'] }}</td>
                        <td>{{ $item['measurement'] }}</td>
                        <td>RM {{ number_format($item['price'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

</body>
</html>
