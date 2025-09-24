<?php

namespace App\Http\Controllers;

use App\Models\Version;
use App\Models\MPDRaaS;
use App\Models\Region;
use Illuminate\Http\Request;
use App\Models\MPDRaaSVM;
use Illuminate\Support\Facades\Validator;

class MPDRaaSController extends Controller
{
   
    public function create($versionId)
    {
        $version = Version::with(['project', 'mpdraas'])->findOrFail($versionId);
        $region  = Region::where('version_id', $versionId)->firstOrFail();

     
        $codes = [
            'CNET-BWS-SHR-DAY',   // (Per Day) DR Bandwidth
            'CNET-BWD-SHR-DAY',   // (Per Day) DR Bandwidth + AntiDDoS
            'CNET-EIP-SHR-DAY',   // (Per Day) DR Elastic IP
            'CMDR-ELB-DRD-STD',   // Elastic Load Balancer
            'CMDR-NAT-DRD-S',     // NAT GATEWAY (Small)
            'CMDR-NAT-DRD-M',     // NAT GATEWAY (Medium)
            'CMDR-NAT-DRD-L',     // NAT GATEWAY (Large)
            'CMDR-NAT-DRD-XL',    // NAT GATEWAY (Extra-Large)
            'CSEC-VFW-DDT-FGDAY', // (Per Day) DR Cloud Firewall (Fortigate)
            'CSEC-VFW-DDT-OSDAY', // (Per Day) DR Cloud Firewall (OPNSense)
        ];

        $pricing = config('pricing');

       
        $drNetworkRows = [];
        foreach ($codes as $code) {
            if (!isset($pricing[$code])) continue;
            $p = $pricing[$code];
            $drNetworkRows[] = [
                'code'  => $code,
                'name'  => $p['name'] ?? $code,
                'unit'  => $p['measurement_unit'] ?? '',
              
                'price' => (float)($p['price_per_unit'] ?? $p['price_per_unit'] ?? 0),
            ];
        }

        return view('projects.mpdraas.create', [
            'project'       => $version->project,
            'version'       => $version,
            'mpdraas'       => $version->mpdraas,
            'drNetworkRows' => $drNetworkRows,
        ]);
    }

   public function store(Request $request, $versionId)
    {
        $version = Version::with('project')->findOrFail($versionId);

        $validated = $request->validate([
            'mpdraas_activation_days' => 'nullable|integer|min:0',
            'starter_promotion'       => 'nullable|in:Yes,No',
            'mpdraas_location'        => 'required|in:None,Kuala Lumpur,Cyberjaya',
            'num_proxy'               => 'nullable|integer',
            'vm_name'                 => 'nullable|string|max:255',
            'always_on'               => 'required|in:Yes,No',
            'pin'                     => 'required|in:Yes,No',
            'vcpu'                    => 'nullable|integer|min:0',
            'vram'                    => 'nullable|integer|min:0',
            'flavour_mapping'         => 'nullable|string|max:255',
            'system_disk'             => 'nullable|integer|min:0',
            'data_disk'               => 'nullable|integer|min:0',
            'operating_system'        => 'required|in:Linux,Microsoft Windows Std,Microsoft Windows DC,Red Hat Enterprise Linux',
            'rds_count'               => 'nullable|integer|min:0',
            'm_sql'                   => 'required|in:None,Web,Standard,Enterprise',
            'used_system_disk'        => 'nullable|integer|min:0',
            'used_data_disk'          => 'nullable|integer|min:0',
            'solution_type'           => 'required|in:None,EVS,OBS',
            'rto_expected'            => 'nullable|integer|min:0',
            'dd_change'               => 'nullable|integer|min:0',
            'data_change'             => 'nullable|numeric|min:0',
            'data_change_size'        => 'nullable|numeric|min:0',
            'replication_frequency'   => 'nullable|integer|min:0',
            'num_replication'         => 'nullable|numeric|min:0',
            'amount_data_change'      => 'nullable|numeric|min:0',
            'replication_bandwidth'   => 'nullable|numeric|min:0',
            'rpo_achieved'            => 'nullable|numeric|min:0',
            'ddos_requirement'        => 'required|in:Yes,No',
            'bandwidth_requirement'   => 'nullable|numeric|min:0',
            'main'                    => 'nullable|numeric|min:0',
            'used'                    => 'nullable|numeric|min:0',
            'delta'                   => 'nullable|numeric|min:0',
            'total_replication'       => 'nullable|numeric|min:0',
        ]);

        // Extra
        $validated['project_id']  = $version->project_id;
        $validated['customer_id'] = $version->project->customer_id;
        $validated['presale_id']  = $version->project->presale_id;

        MPDRaaS::updateOrCreate(
            ['version_id' => $version->id],
            $validated
        );

        return redirect()->back()->with('success', 'MPDRaaS data saved successfully!');
    }

    /**
     * Autosave untuk satu baris DR Network (dipanggil oleh AJAX bila user ubah quantity).
     * Body JSON: { code: "...", kl_qty: 1, cj_qty: 2 }
     * save in column JSON mpdraas.dr_network[code]
     */
    public function autosave(Request $request, $versionId)
    {
        $version = Version::with('project')->findOrFail($versionId);

        $v = Validator::make($request->all(), [
            'code'   => 'required|string',
            'kl_qty' => 'nullable|numeric|min:0',
            'cj_qty' => 'nullable|numeric|min:0',
        ]);
        if ($v->fails()) {
            return response()->json(['ok' => false, 'errors' => $v->errors()], 422);
        }

        $code    = $request->input('code');
        $pricing = config('pricing')[$code] ?? null;
        if (!$pricing) {
            return response()->json(['ok' => false, 'message' => 'Unknown code'], 404);
        }

        //$price = (float)($pricing['rate_card_price_per_unit'] ?? $pricing['price_per_unit'] ?? 0);

        $price = (float)($pricing['price_per_unit'] ?? 0);
        $klQty = (float)$request->input('kl_qty', 0);
        $cjQty = (float)$request->input('cj_qty', 0);

        // Kiraan asas: qty × price (kalau nak darab activation days, boleh tambah * $days)
        $klAmount = $klQty * $price;
        $cjAmount = $cjQty * $price;

        // Pastikan ada rekod MPDRaaS untuk version ni
        $mp = MPDRaaS::firstOrCreate(
            ['version_id' => $version->id],
            [
                'project_id'  => $version->project_id,
                'customer_id' => $version->project->customer_id,
                'presale_id'  => $version->project->presale_id,
            ]
        );

        $data = $mp->dr_network ?? [];
        $data[$code] = [
            'name'       => $pricing['name'] ?? $code,
            'unit'       => $pricing['measurement_unit'] ?? '',
            'price'      => $price,
            'kl_qty'     => $klQty,
            'cj_qty'     => $cjQty,
            'kl_amount'  => round($klAmount, 2),
            'cj_amount'  => round($cjAmount, 2),
            'total'      => round($klAmount + $cjAmount, 2),
        ];

        $mp->dr_network = $data;
        $mp->save();

        // Summary total cepat
        $klSum = 0; $cjSum = 0; $grand = 0;
        foreach ($data as $row) {
            $klSum += $row['kl_amount'] ?? 0;
            $cjSum += $row['cj_amount'] ?? 0;
            $grand += $row['total'] ?? 0;
        }

        return response()->json([
            'ok'   => true,
            'line' => $data[$code],
            'summary' => [
                'kl_total'    => round($klSum, 2),
                'cj_total'    => round($cjSum, 2),
                'grand_total' => round($grand, 2),
            ],
        ]);
    }

    /**
     * (Kekalkan helper ni kalau kau guna untuk benda lain)
     */
    private function calculateFlavourMapping(?int $vcpu, ?int $vram): string
    {
        $vcpu = $vcpu ?? 0;
        $vram = $vram ?? 0;

        $flavours = [
            ['name' => 'm3.micro', 'vcpu' => 1, 'vram' => 1],
            ['name'=> 'm3.small', 'vcpu'=>1, 'vram'=> 2 ],
            ['name' => 'c3.large', 'vcpu' => 2, 'vram' => 4 ],
            ['name'=> 'm3.large', 'vcpu' => 2, 'vram' => 8 ],
            ['name'=> 'r3.large', 'vcpu' => 2, 'vram' => 16 ],
            ['name' => 'c3.xlarge', 'vcpu' => 4, 'vram'=> 8 ],
            ['name'=> 'm3.xlarge', 'vcpu' => 4, 'vram' => 16 ],
            ['name' => 'r3.xlarge', 'vcpu'=> 4, 'vram'=> 32 ],
            ['name' => 'c3.2xlarge', 'vcpu'=> 8, 'vram'=> 16 ],
            ['name' => 'm3.2xlarge', 'vcpu'=> 8, 'vram'=> 32 ],
            ['name'=> 'r3.2xlarge', 'vcpu'=> 8, 'vram'=> 64 ],
            ['name'=> 'm3.3xlarge', 'vcpu'=> 12, 'vram'=> 48 ],
            ['name' => 'c3.4xlarge', 'vcpu' => 16, 'vram' => 32],
            ['name' => 'm3.4xlarge', 'vcpu' => 16, 'vram' => 64],
            ['name' => 'r3.4xlarge', 'vcpu' => 16, 'vram' => 128],
            ['name' => 'm3.6xlarge', 'vcpu' => 24, 'vram' => 96],
            ['name' => 'c3.8xlarge', 'vcpu' => 32, 'vram' => 64],
            ['name' => 'm3.8xlarge', 'vcpu' => 32, 'vram' => 128],
            ['name' => 'r3.8xlarge', 'vcpu' => 32, 'vram' => 256],
            ['name' => 'r3.12xlarge', 'vcpu' => 48, 'vram' => 384],
            ['name' => 'c3.16xlarge', 'vcpu' => 64, 'vram' => 128],
            ['name' => 'm3.16xlarge', 'vcpu' => 64, 'vram' => 256],
            ['name' => 'r3.16xlarge', 'vcpu' => 64, 'vram' => 512],
            ['name' => 'c3p.xlarge', 'vcpu' => 4, 'vram' => 8],
            ['name' => 'm3p.xlarge', 'vcpu' => 4, 'vram' => 16],
            ['name' => 'r3p.xlarge', 'vcpu' => 4, 'vram' => 32],
            ['name' => 'c3p.2xlarge', 'vcpu' => 8, 'vram' => 16],
            ['name' => 'm3p.2xlarge', 'vcpu' => 8, 'vram' => 32],
            ['name' => 'r3p.2xlarge', 'vcpu' => 8, 'vram' => 64],
            ['name' => 'm3p.3xlarge', 'vcpu' => 12, 'vram' => 48],
            ['name' => 'c3p.4xlarge', 'vcpu' => 16, 'vram' => 32],
            ['name' => 'm3p.4xlarge', 'vcpu' => 16, 'vram' => 64],
            ['name' => 'r3p.4xlarge', 'vcpu' => 16, 'vram' => 64],
            ['name' => 'm3p.6xlarge', 'vcpu' => 24, 'vram' => 96],
            ['name' => 'c3p.8xlarge', 'vcpu' => 32, 'vram' => 64],
            ['name' => 'm3p.8xlarge', 'vcpu' => 32, 'vram' => 128],
            ['name' => 'r3p.8xlarge', 'vcpu' => 32, 'vram' => 128],
            ['name' => 'm3p.12xlarge', 'vcpu' => 48, 'vram' => 192],
            ['name' => 'r3p.12xlarge', 'vcpu' => 48, 'vram' => 384],
            ['name' => 'm3p.16xlarge', 'vcpu' => 64, 'vram' => 256],
            ['name' => 'r3p.16xlarge', 'vcpu' => 64, 'vram' => 512],
            ['name' => 'r3p.46xlarge.metal', 'vcpu' => 64, 'vram' => 1408],
            ['name' => 'm3gnt4.xlarge', 'vcpu' => 4, 'vram' => 16],
            ['name' => 'm3gnt4.2xlarge', 'vcpu' => 8, 'vram' => 32],
            ['name' => 'm3gnt4.4xlarge', 'vcpu' => 16, 'vram' => 64],
            ['name' => 'm3gnt4.8xlarge', 'vcpu' => 32, 'vram' => 128],
            ['name' => 'm3gnt4.16xlarge', 'vcpu' => 64, 'vram' => 256],
            ['name' => 'r3p.46xlarge.ddh', 'vcpu' => 342, 'vram' => 1480],
        ];

        
        $suitable = collect($flavours)
            ->where('vcpu', '>=', $vcpu)
            ->where('vram', '>=', $vram)
            ->sortBy([['vcpu', 'asc'], ['vram', 'asc']])
            ->first();

        return $suitable['name'] ?? 'No suitable flavour';
    }




    private function flavourMap(int $vcpu = 0, int $vram = 0): string
{
    $vcpu = $vcpu ?? 0;
    $vram = $vram ?? 0;

    $flavours = [
        ['name'=>'m3.micro','vcpu'=>1,'vram'=>1],
        ['name'=>'m3.small','vcpu'=>1,'vram'=>2],
        ['name'=>'c3.large','vcpu'=>2,'vram'=>4],
        ['name'=>'m3.large','vcpu'=>2,'vram'=>8],
        ['name'=>'r3.large','vcpu'=>2,'vram'=>16],
        ['name'=>'c3.xlarge','vcpu'=>4,'vram'=>8],
        ['name'=>'m3.xlarge','vcpu'=>4,'vram'=>16],
        ['name'=>'r3.xlarge','vcpu'=>4,'vram'=>32],
        ['name'=>'c3.2xlarge','vcpu'=>8,'vram'=>16],
        ['name'=>'m3.2xlarge','vcpu'=>8,'vram'=>32],
        ['name'=>'r3.2xlarge','vcpu'=>8,'vram'=>64],
        ['name'=>'m3.3xlarge','vcpu'=>12,'vram'=>48],
        ['name'=>'c3.4xlarge','vcpu'=>16,'vram'=>32],
        ['name'=>'m3.4xlarge','vcpu'=>16,'vram'=>64],
        ['name'=>'r3.4xlarge','vcpu'=>16,'vram'=>128],
        ['name'=>'m3.6xlarge','vcpu'=>24,'vram'=>96],
        ['name'=>'c3.8xlarge','vcpu'=>32,'vram'=>64],
        ['name'=>'m3.8xlarge','vcpu'=>32,'vram'=>128],
        ['name'=>'r3.8xlarge','vcpu'=>32,'vram'=>256],
        ['name'=>'r3.12xlarge','vcpu'=>48,'vram'=>384],
        ['name'=>'c3.16xlarge','vcpu'=>64,'vram'=>128],
        ['name'=>'m3.16xlarge','vcpu'=>64,'vram'=>256],
        ['name'=>'r3.16xlarge','vcpu'=>64,'vram'=>512],
        ['name'=>'c3p.xlarge','vcpu'=>4,'vram'=>8],
        ['name'=>'m3p.xlarge','vcpu'=>4,'vram'=>16],
        ['name'=>'r3p.xlarge','vcpu'=>4,'vram'=>32],
        ['name'=>'c3p.2xlarge','vcpu'=>8,'vram'=>16],
        ['name'=>'m3p.2xlarge','vcpu'=>8,'vram'=>32],
        ['name'=>'r3p.2xlarge','vcpu'=>8,'vram'=>64],
        ['name'=>'m3p.3xlarge','vcpu'=>12,'vram'=>48],
        ['name'=>'c3p.4xlarge','vcpu'=>16,'vram'=>32],
        ['name'=>'m3p.4xlarge','vcpu'=>16,'vram'=>64],
        ['name'=>'r3p.4xlarge','vcpu'=>16,'vram'=>64],
        ['name'=>'m3p.6xlarge','vcpu'=>24,'vram'=>96],
        ['name'=>'c3p.8xlarge','vcpu'=>32,'vram'=>64],
        ['name'=>'m3p.8xlarge','vcpu'=>32,'vram'=>128],
        ['name'=>'r3p.8xlarge','vcpu'=>32,'vram'=>128],
        ['name'=>'m3p.12xlarge','vcpu'=>48,'vram'=>192],
        ['name'=>'r3p.12xlarge','vcpu'=>48,'vram'=>384],
        ['name'=>'m3p.16xlarge','vcpu'=>64,'vram'=>256],
        ['name'=>'r3p.16xlarge','vcpu'=>64,'vram'=>512],
        ['name'=>'r3p.46xlarge.metal','vcpu'=>64,'vram'=>1408],
        ['name'=>'m3gnt4.xlarge','vcpu'=>4,'vram'=>16],
        ['name'=>'m3gnt4.2xlarge','vcpu'=>8,'vram'=>32],
        ['name'=>'m3gnt4.4xlarge','vcpu'=>16,'vram'=>64],
        ['name'=>'m3gnt4.8xlarge','vcpu'=>32,'vram'=>128],
        ['name'=>'m3gnt4.16xlarge','vcpu'=>64,'vram'=>256],
        ['name'=>'r3p.46xlarge.ddh','vcpu'=>342,'vram'=>1480],
    ];

    $match = collect($flavours)
        ->where('vcpu', '>=', $vcpu)
        ->where('vram', '>=', $vram)
        ->sortBy([['vcpu','asc'], ['vram','asc']])
        ->first();

    return $match['name'] ?? 'No suitable flavour';
}

public function autosaveField(Request $request, $versionId)
{
    $version = Version::with('project')->findOrFail($versionId);

    $field = $request->input('field');   // contoh: 'mpdraas_activation_days'
    $value = $request->input('value');   // contoh: 30

    // whitelist supaya selamat
    $allowed = [
        'mpdraas_activation_days' => 'integer|min:0|nullable',
        'starter_promotion'       => 'in:Yes,No|nullable',
        'mpdraas_location'        => 'in:None,Kuala Lumpur,Cyberjaya|required',
        'num_proxy'               => 'integer|min:0|nullable',
        'vm_name'                 => 'string|max:255|nullable',
        'always_on'               => 'in:Yes,No|nullable',
        'pin'                     => 'in:Yes,No|nullable',
        'vcpu'                    => 'integer|min:0|nullable',
        'vram'                    => 'integer|min:0|nullable',
        'flavour_mapping'         => 'string|max:255|nullable',
        'system_disk'             => 'numeric|min:0|nullable',
        'data_disk'               => 'numeric|min:0|nullable',
        'operating_system'        => 'in:Linux,Microsoft Windows Std,Microsoft Windows DC,Red Hat Enterprise Linux|required',
        'rds_count'               => 'integer|min:0|nullable',
        'm_sql'                   => 'in:None,Web,Standard,Enterprise|required',
        'used_system_disk'        => 'numeric|min:0|nullable',
        'used_data_disk'          => 'numeric|min:0|nullable',
        'solution_type'           => 'in:None,EVS,OBS|required',
        'rto_expected'            => 'integer|min:0|nullable',
        'dd_change'               => 'numeric|min:0|nullable',
        'data_change'             => 'numeric|min:0|nullable',
        'data_change_size'        => 'numeric|min:0|nullable',
        'replication_frequency'   => 'integer|min:0|nullable',
        'num_replication'         => 'numeric|min:0|nullable',
        'amount_data_change'      => 'numeric|min:0|nullable',
        'replication_bandwidth'   => 'numeric|min:0|nullable',
        'rpo_achieved'            => 'numeric|min:0|nullable',
        'ddos_requirement'        => 'in:Yes,No|required',
        'bandwidth_requirement'   => 'numeric|min:0|nullable',
        'main'                    => 'numeric|min:0|nullable',
        'used'                    => 'numeric|min:0|nullable',
        'delta'                   => 'numeric|min:0|nullable',
        'total_replication'       => 'numeric|min:0|nullable',
    ];

    if (!isset($allowed[$field])) {
        return response()->json(['ok'=>false,'message'=>'Field not allowed'], 422);
    }

    $this->validate($request, ['value' => $allowed[$field]]);

    // Pastikan row MPDRaaS wujud
    $mp = MPDRaaS::firstOrCreate(
        ['version_id' => $version->id],
        [
            'project_id'  => $version->project_id,
            'customer_id' => $version->project->customer_id,
            'presale_id'  => $version->project->presale_id,
            'mpdraas_location' => 'None',
            'm_sql' => 'None',
            'operating_system' => 'Linux',
            'solution_type' => 'None',
            'ddos_requirement' => 'No',
        ]
    );

    // Khas: jika user ubah vcpu/vram, auto isi flavour_mapping server-side (fallback)
    if (in_array($field, ['vcpu','vram'])) {
        $data = $mp->toArray();
        $data[$field] = is_numeric($value) ? (int)$value : 0;
        $mapped = $this->flavourMap((int)($data['vcpu'] ?? 0), (int)($data['vram'] ?? 0));
        $mp->flavour_mapping = $mapped;
    }

    $mp->{$field} = $value;
    $mp->save();

    return response()->json(['ok'=>true, 'field'=>$field, 'value'=>$mp->{$field}, 'flavour_mapping'=>$mp->flavour_mapping ?? null]);
}




public function upsertVm(Request $request, $versionId)
{
    $version = Version::with('project')->findOrFail($versionId);

    $data = $request->validate([
        'id' => 'nullable|string',
        'row_no' => 'nullable|integer|min:0',
        'vm_name' => 'nullable|string|max:255',
        'always_on' => 'nullable|in:Yes,No',
        'pin' => 'nullable|in:Yes,No',
        'vcpu' => 'nullable|integer|min:0',
        'vram' => 'nullable|integer|min:0',
        'flavour_mapping' => 'nullable|string|max:255',
        'system_disk' => 'nullable|numeric|min:0',
        'data_disk' => 'nullable|numeric|min:0',
        'operating_system' => 'nullable|in:Linux,Microsoft Windows Std,Microsoft Windows DC,Red Hat Enterprise Linux',
        'rds_count' => 'nullable|integer|min:0',
        'm_sql' => 'nullable|in:None,Web,Standard,Enterprise',
        'used_system_disk' => 'nullable|numeric|min:0',
        'used_data_disk' => 'nullable|numeric|min:0',
        'solution_type' => 'nullable|in:None,EVS,OBS',
        'rto_expected' => 'nullable|integer|min:0',
        'dd_change' => 'nullable|numeric|min:0',
        'data_change' => 'nullable|numeric|min:0',
        'data_change_size' => 'nullable|numeric|min:0',
        'replication_frequency' => 'nullable|integer|min:0',
        'num_replication' => 'nullable|numeric|min:0',
        'amount_data_change' => 'nullable|numeric|min:0',
        'replication_bandwidth' => 'nullable|numeric|min:0',
        'rpo_achieved' => 'nullable|numeric|min:0',
    ]);

    // Pastikan parent row MPDRaaS wujud
    $mp = MPDRaaS::firstOrCreate(
        ['version_id' => $version->id],
        [
            'project_id'  => $version->project_id,
            'customer_id' => $version->project->customer_id,
            'presale_id'  => $version->project->presale_id,
            'mpdraas_location' => 'None',
            'm_sql' => 'None',
            'operating_system' => 'Linux',
            'solution_type' => 'None',
            'ddos_requirement' => 'No',
        ]
    );

    // Derive fields (server fallback)
    $sd = (float)($data['system_disk'] ?? 0);
    $dd = (float)($data['data_disk'] ?? 0);
    $usedSystem = $sd;
    $usedData   = $dd;

    $ddPct = (float)($data['dd_change'] ?? 0);
    $dataChange = ($sd + $dd) * ($ddPct / 100.0);
    $dataChangeSize = $dataChange * 1024 * 8; // MB -> Mbits

    $freq = (int)($data['replication_frequency'] ?? 0);
    $numReplication = $freq > 0 ? (int)ceil(1440 / $freq) : 0;
    $amountPerSync  = $numReplication > 0 ? (int)ceil($dataChangeSize / $numReplication) : 0;
    $bandwidth      = $freq > 0 ? ceil(($amountPerSync / ($freq * 60)) * 10) / 10 : 0;
    $rpo            = $bandwidth > 0 ? ($amountPerSync / $bandwidth) / 60 : 0;

    $vcpu = (int)($data['vcpu'] ?? 0);
    $vram = (int)($data['vram'] ?? 0);
    $flavour = $data['flavour_mapping'] ?? null;
    if (!$flavour || $flavour === 'No match' || $flavour === 'No suitable flavour found') {
        $flavour = $this->flavourMap($vcpu, $vram);
    }

    $payload = array_merge($data, [
        'version_id' => $version->id,
        'mpdraas_id' => $mp->id,
        'used_system_disk' => $usedSystem,
        'used_data_disk' => $usedData,
        'data_change' => round($dataChange, 2),
        'data_change_size' => (int)round($dataChangeSize),
        'num_replication' => $numReplication,
        'amount_data_change' => $amountPerSync,
        'replication_bandwidth' => $bandwidth,
        'rpo_achieved' => round($rpo, 2),
        'flavour_mapping' => $flavour,
    ]);

    if (!empty($data['id'])) {
        $vm = MPDRaaSVM::findOrFail($data['id']);
        $vm->update($payload);
    } else {
        $vm = MPDRaaSVM::create($payload);
    }

    return response()->json(['ok'=>true, 'vm'=>$vm]);
}

public function destroyVm($versionId, MPDRaaSVM $vm)
{
    $vm->delete();
    return response()->json(['ok'=>true]);
}

}





