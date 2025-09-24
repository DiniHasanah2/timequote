<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Version;
use App\Models\Quotation;
use App\Models\InternalSummary;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use App\Services\CommercialLinkService;
use App\Support\JwtLink;




class QuotationController extends Controller
{
  public function preview($versionId)
{
    $version = Version::with(['project.customer', 'solution_type'])->findOrFail($versionId);

    $quotation = Quotation::firstOrCreate(
        ['version_id' => $versionId],
        [
            'project_id' => $version->project_id,
            'presale_id' => auth()->id(),
            'status'     => 'pending',
            'quote_code' => 'Q-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5)),
        ]
    );

  
    $internal = InternalSummary::where('version_id', $versionId)->first() ?? new InternalSummary();
    $pricing  = config('pricing');

   
    [$managedSummary, $totalManagedCharges]             = $this->computeManagedSummary($version);
    [$licenseRateCard, $totalLicenseCharges]            = $this->computeLicenseRateCard($internal, $pricing);
    [$ecsSummary, $klEcsTotal, $cjEcsTotal]             = $this->computeEcsSummary($version);
    [$klTotal, $cjTotal]                                = $this->computeNetworkTotals($internal, $pricing);
    [$psDays, $psUnit, $totalProfessionalCharges]       = $this->computeProfessionalTotals($internal);
    [$storageSummary, $totalStorageCharges]             = $this->computeStorageSummary($internal, $pricing);
    [$cloudSecuritySummary, $totalcloudSecurityCharges] = $this->computeCloudSecuritySummary($internal, $pricing);
    [$monitoringSummary, $totalMonitoringCharges]       = $this->computeMonitoringSummary($internal, $pricing);
    [$securitySummary, $totalSecurityCharges]           = $this->computeSecuritySummary($internal, $pricing);
    [$backupSummary, $totalBackupCharges]               = $this->computeBackupSummary($internal, $pricing);

   
    $licenseKL = collect($licenseRateCard)->sum('kl_price');
    $licenseCJ = collect($licenseRateCard)->sum('cj_price');

    
    $klEcsTotal = collect($ecsSummary)->sum('kl_price');
    $cjEcsTotal = collect($ecsSummary)->sum('cj_price');

    $monthlyTotal =
        ($totalManagedCharges ?? 0) +
        (($klTotal ?? 0) + ($cjTotal ?? 0)) +
        (($klEcsTotal ?? 0) + ($cjEcsTotal ?? 0)) +
        ($totalLicenseCharges ?? ($licenseKL + $licenseCJ)) +
        ($totalStorageCharges ?? 0) +
        ($totalBackupCharges ?? 0) +
        ($totalcloudSecurityCharges ?? 0) +
        ($totalMonitoringCharges ?? 0) +
        ($totalSecurityCharges ?? 0);

    
    $contractDuration = (int) session("quotation.$versionId.contract_duration", $quotation->contract_duration ?? 12);

   
    $contractTotal = ($monthlyTotal * $contractDuration) + ($totalProfessionalCharges ?? 0);
    $serviceTax    = $contractTotal * 0.08;
    $finalTotal    = $contractTotal + $serviceTax;

   
    $quotation->total_amount = round($finalTotal, 2);
    $quotation->save();

    $quotationId = $quotation->id;

    return view('projects.security_service.quotation', compact(
        'version','quotation','quotationId','totalProfessionalCharges',
        'klTotal','cjTotal','managedSummary','totalManagedCharges',
        'ecsSummary','licenseRateCard',
        'securitySummary','totalSecurityCharges',
        'monitoringSummary','totalMonitoringCharges',
        'cloudSecuritySummary','totalcloudSecurityCharges',
        'storageSummary','totalStorageCharges',
        'backupSummary','totalBackupCharges',
        'psDays','psUnit','totalLicenseCharges'
    ) + [
        'project'           => $version->project,
        'mode'              => 'monthly',
        'viewOnly'          => 0,

      
        'contractDuration'  => $contractDuration,
        'monthlyTotal'      => $monthlyTotal,
        'contractTotal'     => $contractTotal,
        'serviceTax'        => $serviceTax,
        'finalTotal'        => $finalTotal,
    ]);
}

public function annual($versionId)
{
    /** @var \Illuminate\View\View $view */
    $view = $this->preview($versionId); // Kira semua (bulanan) seperti biasa
    $data = $view->getData();

    $duration = 12;
    $data['mode'] = 'annual';


    foreach ([
        'managedSummary',
        'licenseRateCard',
        'ecsSummary',
        'storageSummary',
        'backupSummary',
        'cloudSecuritySummary',
        'monitoringSummary',
        'securitySummary',
    ] as $key) {
        if (!empty($data[$key]) && is_array($data[$key])) {
            $data[$key] = $this->scaleLineItems($data[$key], $duration);
        }
    }

    
    $data = $this->scaleRecurringTotals($data, $duration);

   
    $monthlyTotal = (float) ($view->getData()['monthlyTotal'] ?? 0);
    $totalProfessionalCharges = (float) ($view->getData()['totalProfessionalCharges'] ?? 0);

    $contractDuration = $duration;
    $contractTotal    = ($monthlyTotal * $contractDuration) + $totalProfessionalCharges;
    $serviceTax       = $contractTotal * 0.08;
    $finalTotal       = $contractTotal + $serviceTax;

    $data['contractDuration'] = $contractDuration;
    $data['contractTotal']    = $contractTotal;
    $data['serviceTax']       = $serviceTax;
    $data['finalTotal']       = $finalTotal;
    $data['annualRecurringTotal']= $monthlyTotal * $contractDuration;


$data['is_prescaled'] = true;

   
    

    return view('projects.security_service.annual_quotation', $data);
}



    public function generateQuotation($versionId)
    {
        return $this->preview($versionId);
    }

  

    public function autoSave(Request $request, $versionId)
{
    if ($request->field === 'contract_duration') {
        $val = (int) $request->value;

        
        session()->put("quotation.$versionId.contract_duration", $val);

      
        $version = \App\Models\Version::findOrFail($versionId);
        $quotation = \App\Models\Quotation::firstOrCreate(
            ['version_id' => $versionId],
            [
                'project_id' => $version->project_id,
                'presale_id' => auth()->id(),
                'status'     => 'pending',
                'quote_code' => 'Q-' . now()->format('Ymd') . '-' . \Illuminate\Support\Str::upper(\Illuminate\Support\Str::random(5)),
            ]
        );

        $quotation->contract_duration = $val;
        $quotation->save();

        return response()->json(['success' => true, 'contract_duration' => $val]);
    }

    return response()->json(['success' => true]);
}



    public function internalSummaryPdf($versionId)
    {
        $version = Version::with([
            'project.customer','project.presale','solution_type',
            'ecs_configuration','security_service','non_standard_items',
        ])->findOrFail($versionId);

        $internal = InternalSummary::where('version_id', $versionId)->first() ?? new InternalSummary();

        $logoPath   = public_path('assets/time_logo.png');
        $logoBase64 = is_file($logoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath)) : null;

        $data = [
            'version'   => $version,
            'project'   => $version->project,
            'internal'  => $internal,
            'logoBase64'=> $logoBase64,
            'logoPath'  => $logoPath,
        ];

        $fileName = sprintf(
            'internal_summary_%s_v%s.pdf',
            $version->project->project_code ?? Str::slug($version->project->name ?? 'project'),
            $version->version_number ?? '1'
        );

        $pdf = Pdf::loadView('pdf.internal-summary', $data)
            ->setPaper('a4', 'landscape')
            ->setOptions(['defaultFont' => 'sans-serif']);

        return $pdf->download($fileName);
    }

  public function downloadZip(Request $request, $versionId)
{
    $zipPath = null;
    try {
        [$tmpDir, $zipPath, $base] = $this->createZipBundleForVersion($versionId);

        // Validate ZIP bytes before sending
        $this->assertZipOkOrThrow($zipPath);

        // Matikan compression & buffer supaya header PK kekal di byte #0
        if (function_exists('apache_setenv')) { @apache_setenv('no-gzip', '1'); }
        @ini_set('zlib.output_compression', '0');
        while (ob_get_level() > 0) { @ob_end_clean(); }

        $filename = basename($zipPath);
        $size     = filesize($zipPath);

        return response()->stream(function () use ($zipPath) {
            $fp = fopen($zipPath, 'rb');
            while (!feof($fp)) {
                echo fread($fp, 1024 * 1024); // 1 MB chunks
                flush();
            }
            fclose($fp);
        }, 200, [
            'Content-Type'        => 'application/zip',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length'      => (string) $size,
            'X-Content-Type-Options' => 'nosniff',
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'error'   => 'Failed to generate ZIP',
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'line'    => $e->getLine(),
        ], 500);
    } finally {
        if ($zipPath) {
            // buang fail selepas request habis
            register_shutdown_function(function () use ($zipPath) {
                @unlink($zipPath);
            });
        }
    }
}



    /*public function downloadZip(Request $request, $versionId)
    {
        $tmpDir = null;
        try {
            [$tmpDir, $zipPath, $base] = $this->createZipBundleForVersion($versionId);

         
            $response = response()->download($zipPath, basename($zipPath))->deleteFileAfterSend(true);

        

if (!\is_file($zipPath)) {
    \Log::error('ZIP missing before download', ['zipPath' => $zipPath]);
    abort(500, 'ZIP file not found after generation.');
}

            return $response;

        } catch (\Throwable $e) {
            if ($tmpDir && is_dir($tmpDir)) {
                try { File::deleteDirectory($tmpDir); } catch (\Throwable $ex) {}
            }
            return response()->json([
                'error'   => 'Failed to generate ZIP',
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ], 500);
        }
    }*/



    

   private function assertZipOkOrThrow(string $path): void
{
    clearstatcache(true, $path);
    if (!is_file($path) || filesize($path) === 0) {
        throw new \RuntimeException("ZIP missing or empty: {$path}");
    }

    // Check signature (PK..)
    $fh = fopen($path, 'rb');
    $sig = fread($fh, 4);
    fclose($fh);
    $valid = in_array($sig, ["PK\x03\x04", "PK\x05\x06", "PK\x07\x08"], true);
    if (!$valid) {
        throw new \RuntimeException('Invalid ZIP signature: 0x' . bin2hex($sig));
    }

    // Try open with ZipArchive as extra sanity check
    $zip = new \ZipArchive();
    $rc = $zip->open($path);
    if ($rc !== true) {
        throw new \RuntimeException("ZipArchive cannot open zip (code {$rc})");
    }
    if ($zip->numFiles < 1) {
        $zip->close();
        throw new \RuntimeException('ZIP contains no files');
    }
    $zip->close();
}



    public function exportLink(Request $request, $versionId)
    {
        $tmpDir = null;
        try {
          
            [$tmpDir, $zipPath, $base] = $this->createZipBundleForVersion($versionId);

          
            $token     = (string) Str::ulid();
            $fileName  = basename($zipPath);
            $publicRel = "exports/{$token}/{$fileName}";

            //Storage::disk('public')->put($publicRel, file_get_contents($zipPath));

            //Storage::disk('obs')->put($publicRel, file_get_contents($zipPath));


$stream = fopen($zipPath, 'rb');
Storage::disk('obs')->writeStream($publicRel, $stream, [
    'visibility'   => 'private',
    'mimetype'     => 'application/zip', // pastikan mime betul
    'ContentType'  => 'application/zip', // sesetengah adapter guna key ni
]);
fclose($stream);









            if (!Storage::disk('obs')->exists($publicRel)) {
    throw new \RuntimeException("Upload to OBS failed or object not found: {$publicRel}");
}


           
            $signedUrl = URL::temporarySignedRoute(
                'exports.download',
                now()->addDays(14),
                ['token' => $token, 'file' => $fileName]
            );

         
            if ($tmpDir && is_dir($tmpDir)) {
                try { File::deleteDirectory($tmpDir); } catch (\Throwable $ex) {}
            }

          
            return back()->with('share_link', $signedUrl);

        } catch (\Throwable $e) {
            if ($tmpDir && is_dir($tmpDir)) {
                try { File::deleteDirectory($tmpDir); } catch (\Throwable $ex) {}
            }
            return back()->with('error', 'Failed to generate share link: '.$e->getMessage());
        }
    }

 
    /*public function shareDownload(Request $request, string $token, string $file)
{
    if (! $request->hasValidSignature()) abort(403, 'Link expired or invalid.');

    $path = "exports/{$token}/{$file}";                 // sekarang fail di OBS
    if (! Storage::disk('obs')->exists($path)) abort(404);

    $stream = Storage::disk('obs')->readStream($path);
    return response()->streamDownload(function() use ($stream) {
        fpassthru($stream);
    }, $file);
}*/


public function shareDownload(Request $request, string $token, string $file)
{
    if (! $request->hasValidSignature()) abort(403, 'Link expired or invalid.');

    $path = "exports/{$token}/{$file}";
    try {
        if (! Storage::disk('obs')->exists($path)) {
            abort(404, "File not found in OBS: {$path}");
        }
        $stream = Storage::disk('obs')->readStream($path);
    } catch (\Throwable $e) {
        \Log::error('OBS download error', ['path'=>$path, 'err'=>$e->getMessage()]);
        abort(502, 'Cannot reach OBS (DNS/endpoint).');
    }

    /*return response()->streamDownload(function() use ($stream) {
        fpassthru($stream);
    }, $file);*/
    return response()->streamDownload(function() use ($stream) {
    fpassthru($stream);
}, $file, [
    'Content-Type' => 'application/zip',
    'X-Content-Type-Options' => 'nosniff',
]);

}






    private function createZipBundleForVersion(int|string $versionId): array
    {
        $version = Version::with(['project.customer','solution_type','ecs_configuration','security_service'])
            ->findOrFail($versionId);

        $quotation = Quotation::firstOrCreate(
            ['version_id' => $versionId],
            [
                'project_id' => $version->project_id,
                'presale_id' => auth()->id(),
                'status'     => 'pending',
                'quote_code' => 'Q-'.now()->format('Ymd').'-'.Str::upper(Str::random(5)),
            ]
        );

        $internal = InternalSummary::where('version_id', $versionId)->first() ?? new InternalSummary();
        $pricing  = config('pricing');

      
        [$managedSummary, $totalManagedCharges]             = $this->computeManagedSummary($version);
        [$licenseRateCard, $totalLicenseCharges]            = $this->computeLicenseRateCard($internal, $pricing);
        [$ecsSummary, $klEcsTotal, $cjEcsTotal]             = $this->computeEcsSummary($version);
        [$klTotal, $cjTotal]                                = $this->computeNetworkTotals($internal, $pricing);
        [$psDays, $psUnit, $totalProfessionalCharges]       = $this->computeProfessionalTotals($internal);
        [$storageSummary, $totalStorageCharges]             = $this->computeStorageSummary($internal, $pricing);
        [$cloudSecuritySummary, $totalcloudSecurityCharges] = $this->computeCloudSecuritySummary($internal, $pricing);
        [$monitoringSummary, $totalMonitoringCharges]       = $this->computeMonitoringSummary($internal, $pricing);
        [$securitySummary, $totalSecurityCharges]           = $this->computeSecuritySummary($internal, $pricing);
        [$backupSummary, $totalBackupCharges]               = $this->computeBackupSummary($internal, $pricing);

        $durationMonthly = (int)(session("quotation.$versionId.contract_duration", $quotation->contract_duration ?? 12));
        $durationAnnual  = 12;

        $logoPath   = public_path('assets/time_logo.png');
        $logoBase64 = is_file($logoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath)) : null;

        $common = [
            'version'                   => $version,
            'quotation'                 => $quotation,
            'internal'                  => $internal,
            'project'                   => $version->project,
            'viewOnly'                  => 1,
            'isPdf'                     => 1,
            'managedSummary'            => $managedSummary,
            'totalManagedCharges'       => $totalManagedCharges,
            'licenseRateCard'           => $licenseRateCard,
            'totalLicenseCharges'       => $totalLicenseCharges,
            'ecsSummary'                => $ecsSummary,
            'klEcsTotal'                => $klEcsTotal,
            'cjEcsTotal'                => $cjEcsTotal,
            'klTotal'                   => $klTotal,
            'cjTotal'                   => $cjTotal,
            'psDays'                    => $psDays,
            'psUnit'                    => $psUnit,
            'totalProfessionalCharges'  => $totalProfessionalCharges,
            'storageSummary'            => $storageSummary,
            'totalStorageCharges'       => $totalStorageCharges,
            'cloudSecuritySummary'      => $cloudSecuritySummary,
            'totalcloudSecurityCharges' => $totalcloudSecurityCharges,
            'monitoringSummary'         => $monitoringSummary,
            'totalMonitoringCharges'    => $totalMonitoringCharges,
            'securitySummary'           => $securitySummary,
            'totalSecurityCharges'      => $totalSecurityCharges,
            'backupSummary'             => $backupSummary,
            'totalBackupCharges'        => $totalBackupCharges,
            'logoBase64'                => $logoBase64,
            'logoPath'                  => $logoPath,
        ];

       
        $monthlyData = $common + ['mode' => 'monthly', 'contractDuration' => $durationMonthly];
        $annualData  = $common + ['mode' => 'annual',  'contractDuration' => $durationAnnual];

        $pdfMonthly = Pdf::loadView('pdf.quotation-table', $monthlyData)
            ->setPaper('a4', 'landscape')->setOptions(['defaultFont' => 'sans-serif'])->output();

        $pdfAnnual = Pdf::loadView('pdf.quotation-table', $annualData)
            ->setPaper('a4', 'landscape')->setOptions(['defaultFont' => 'sans-serif'])->output();

        $internalData = [
            'version'   => $version,
            'project'   => $version->project,
            'internal'  => $internal,
            'logoBase64'=> $logoBase64,
            'logoPath'  => $logoPath,
        ];
        $pdfInternal = Pdf::loadView('pdf.internal-summary', $internalData)
            ->setPaper('a4', 'landscape')->setOptions(['defaultFont' => 'sans-serif'])->output();

     
        $tmpDir = storage_path('app/tmp/'.Str::uuid());
        File::ensureDirectoryExists($tmpDir);

        $projectCode = $version->project->project_code ?? Str::slug($version->project->name ?? 'project');
        $verNo       = $version->version_number ?? '1';
        $base        = "quotation_{$projectCode}_v{$verNo}";

        $monthlyName  = "{$base}_monthly_{$durationMonthly}m.pdf";
        $annualName   = "{$base}_annual_12m.pdf";
        $internalName = "{$base}_internal_summary.pdf";
        $skuCsvName   = "{$base}_sku_list.csv";

        file_put_contents("$tmpDir/$monthlyName",  $pdfMonthly);
        file_put_contents("$tmpDir/$annualName",   $pdfAnnual);
        file_put_contents("$tmpDir/$internalName", $pdfInternal);

       
        $rows = $this->collectSkuRowsForCommercial(
            $internal, $pricing, $managedSummary, $licenseRateCard, $ecsSummary,
            $storageSummary, $cloudSecuritySummary, $monitoringSummary, $securitySummary, $backupSummary
        );
        $fp = fopen("$tmpDir/$skuCsvName", 'w');
        fputcsv($fp, ['SKU','Item Name','Unit','KL Qty','CJ Qty','Unit Price','KL Amount','CJ Amount','Total Amount']);
        foreach ($rows as $r) {
            fputcsv($fp, [
                $r['sku'], $r['name'], $r['unit'],
                $r['kl_qty'], $r['cj_qty'],
                number_format((float)$r['unit_price'], 4, '.', ''),
                number_format((float)$r['kl_price'], 2, '.', ''),
                number_format((float)$r['cj_price'], 2, '.', ''),
                number_format((float)($r['kl_price'] + $r['cj_price']), 2, '.', ''),
            ]);
        }
        fclose($fp);

        //$zipPath = "$tmpDir/{$base}_bundle.zip";

        $zipPath = storage_path('app/tmp/'.Str::uuid()."_{$base}_bundle.zip");

        // make sure parent dir exists (important when using a new UUID filename)
\File::ensureDirectoryExists(\dirname($zipPath));

// if a stale file somehow exists, remove it
if (\is_file($zipPath)) { @\unlink($zipPath); }

// --- Tambah check sini ---
$srcs = [
    "$tmpDir/$monthlyName",
    "$tmpDir/$annualName",
    "$tmpDir/$internalName",
    "$tmpDir/$skuCsvName",
];

foreach ($srcs as $p) {
    clearstatcache(true, $p);
    if (!is_file($p)) {
        throw new \RuntimeException("Source file missing: $p");
    }
    if (filesize($p) === 0) {
        throw new \RuntimeException("Source file is empty: $p");
    }
}


        
        

        $zip = new \ZipArchive();
        $openResult = $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        if ($openResult !== true) {
            throw new \RuntimeException("Cannot create zip file. Code: {$openResult}");
        }
        $zip->addFile("$tmpDir/$monthlyName",  $monthlyName);
        $zip->addFile("$tmpDir/$annualName",   $annualName);
        $zip->addFile("$tmpDir/$internalName", $internalName);
        $zip->addFile("$tmpDir/$skuCsvName",   $skuCsvName);
        $zip->close();

        clearstatcache(true, $zipPath);
if (!is_file($zipPath) || filesize($zipPath) === 0) {
    throw new \RuntimeException("ZIP not created or empty: {$zipPath}");
}





// verify creation & non-zero size
\clearstatcache(true, $zipPath);
if (!\is_file($zipPath) || \filesize($zipPath) === 0) {
    throw new \RuntimeException("ZIP not created or empty: {$zipPath}");
}

        try { \Illuminate\Support\Facades\File::deleteDirectory($tmpDir); } catch (\Throwable $e) {}
$tmpDir = null; 


    
        return [$tmpDir, $zipPath, $base];
    }

    private function collectSkuRowsForCommercial(
        $internal, array $pricing,
        array $managedSummary, array $licenseRateCard, array $ecsSummary,
        array $storageSummary, array $cloudSecuritySummary, array $monitoringSummary,
        array $securitySummary, array $backupSummary
    ): array {
        $rows = [];

        $findSkuByName = function (string $categoryOrPrefix, string $name) use ($pricing) {
            if (str_ends_with($categoryOrPrefix, '-')) {
                foreach ($pricing as $code => $item) {
                    if (!is_array($item)) continue;
                    if (is_string($code) && str_starts_with($code, $categoryOrPrefix)) {
                        if (strcasecmp(trim((string)($item['name'] ?? '')), $name) === 0) {
                            return $code;
                        }
                    }
                }
                return null;
            }
            foreach ($pricing as $code => $item) {
                if (!is_array($item)) continue;
                $cat = strtolower((string)($item['category_name'] ?? ''));
                if ($cat === strtolower($categoryOrPrefix) &&
                    strcasecmp(trim((string)($item['name'] ?? '')), $name) === 0) {
                    return $code;
                }
            }
            return null;
        };

        foreach ($managedSummary as $r) {
            $sku = $findSkuByName('Managed Services', $r['name']) ?? 'N/A';
            $rows[] = [
                'sku'=>$sku,'name'=>$r['name'],'unit'=>$r['unit'] ?? 'Unit',
                'kl_qty'=>$r['kl_qty'] ?? 0,'cj_qty'=>$r['cj_qty'] ?? 0,
                'unit_price'=>$r['price_per_unit'] ?? 0,
                'kl_price'=>$r['kl_price'] ?? 0,'cj_price'=>$r['cj_price'] ?? 0,
            ];
        }

        $licenseKeyMap = [
            'Microsoft Windows Server (Core Pack) - Standard'    => 'CLIC-WIN-COR-SRVSTD',
            'Microsoft Windows Server (Core Pack) - Data Center' => 'CLIC-WIN-COR-SRVDC',
            'Microsoft Remote Desktop Services (SAL)'            => 'CLIC-WIN-USR-RDSSAL',
            'Microsoft SQL (Web) (Core Pack)'                    => 'CLIC-WIN-COR-SQLWEB',
            'Microsoft SQL (Standard) (Core Pack)'               => 'CLIC-WIN-COR-SQLSTD',
            'Microsoft SQL (Enterprise) (Core Pack)'             => 'CLIC-WIN-COR-SQLENT',
            'RHEL (1-8vCPU)'                                     => 'CLIC-RHL-COR-8',
            'RHEL (9-127vCPU)'                                   => 'CLIC-RHL-COR-127',
        ];
        foreach ($licenseRateCard as $r) {
            $sku = $licenseKeyMap[$r['name']] ?? 'N/A';
            $rows[] = [
                'sku'=>$sku,'name'=>$r['name'],'unit'=>$r['unit'] ?? 'Unit',
                'kl_qty'=>$r['kl_qty'] ?? 0,'cj_qty'=>$r['cj_qty'] ?? 0,
                'unit_price'=>$r['price_per_unit'] ?? 0,
                'kl_price'=>$r['kl_price'] ?? 0,'cj_price'=>$r['cj_price'] ?? 0,
            ];
        }

        foreach ($ecsSummary as $r) {
            $sku = $findSkuByName('CMPT-ECS-', $r['flavour']) ?? 'N/A';
            $rows[] = [
                'sku'=>$sku,'name'=>$r['flavour'],'unit'=>$r['unit'] ?? 'Unit',
                'kl_qty'=>$r['kl_qty'] ?? 0,'cj_qty'=>$r['cj_qty'] ?? 0,
                'unit_price'=>$r['unit_price'] ?? ($r['price_per_unit'] ?? 0),
                'kl_price'=>$r['kl_price'] ?? 0,'cj_price'=>$r['cj_price'] ?? 0,
            ];
        }

        $cloudSecKeyMap = [
            'Cloud Firewall (Fortigate)' => 'CSEC-VFW-DDT-FG',
            'Cloud Firewall (OPNSense)'  => 'CSEC-VFW-DDT-OS',
            'Cloud Shared WAF (Mbps)'    => 'CSEC-WAF-SHR-HA',
            'Anti-Virus (Panda)'         => 'CSEC-EDR-NOD-STD',
        ];
        foreach ($cloudSecuritySummary as $r) {
            $sku = $cloudSecKeyMap[$r['name']] ?? 'N/A';
            $rows[] = [
                'sku'=>$sku,'name'=>$r['name'],'unit'=>$r['unit'] ?? 'Unit',
                'kl_qty'=>$r['kl_qty'] ?? 0,'cj_qty'=>$r['cj_qty'] ?? 0,
                'unit_price'=>$r['price_per_unit'] ?? 0,
                'kl_price'=>$r['kl_price'] ?? 0,'cj_price'=>$r['cj_price'] ?? 0,
            ];
        }

        foreach ($monitoringSummary as $r) {
            $rows[] = [
                'sku'=>'CMON-TIS-NOD-STD','name'=>$r['name'],'unit'=>$r['unit'] ?? 'Unit',
                'kl_qty'=>$r['kl_qty'] ?? 0,'cj_qty'=>$r['cj_qty'] ?? 0,
                'unit_price'=>$r['price_per_unit'] ?? 0,
                'kl_price'=>$r['kl_price'] ?? 0,'cj_price'=>$r['cj_price'] ?? 0,
            ];
        }

        foreach ($securitySummary as $r) {
            $rows[] = [
                'sku'=>'SECT-VAS-EIP-STD','name'=>$r['name'],'unit'=>$r['unit'] ?? 'Unit',
                'kl_qty'=>$r['kl_qty'] ?? 0,'cj_qty'=>$r['cj_qty'] ?? 0,
                'unit_price'=>$r['price_per_unit'] ?? 0,
                'kl_price'=>$r['kl_price'] ?? 0,'cj_price'=>$r['cj_price'] ?? 0,
            ];
        }

        $storageKeyMap = [
            'Elastic Volume Service (EVS)' => 'CSTG-EVS-SHR-STD',
            'Scalable File Service (SFS)'  => 'CSTG-SFS-SHR-STD',
            'Object Storage Service (OBS)' => 'CSTG-OBS-SHR-STD',
            'Snapshot Storage'             => 'CSTG-BCK-SHR-STD',
            'Image Storage'                => 'CSTG-OBS-SHR-IMG',
        ];
        foreach ($storageSummary as $r) {
            $sku = $storageKeyMap[$r['name']] ?? 'N/A';
            $rows[] = [
                'sku'=>$sku,'name'=>$r['name'],'unit'=>$r['unit'] ?? 'GB',
                'kl_qty'=>$r['kl_qty'] ?? 0,'cj_qty'=>$r['cj_qty'] ?? 0,
                'unit_price'=>$r['price_per_unit'] ?? 0,
                'kl_price'=>$r['kl_price'] ?? 0,'cj_price'=>$r['cj_price'] ?? 0,
            ];
        }

        $backupKeyMap = [
            'Cloud Server Backup Service - Full Backup Capacity'        => 'CSBS-STRG-BCK-CSBSF',
            'Cloud Server Backup Service - Incremental Backup Capacity' => 'CSBS-STRG-BCK-CSBSI',
            'Cloud Server Replication Service - Retention Capacity'     => 'CSBS-STRG-BCK-REPS',
            'Cloud Server Replication Service - Bandwidth 5TB'          => 'CDRS-SVC-REP-5TBD',
            'Cloud Server Replication Service - Bandwidth 10TB'         => 'CDRS-SVC-REP-10TBD',
            'Cloud Server Replication Service - Bandwidth 20TB'         => 'CDRS-SVC-REP-20TBD',
            'Cloud Server Replication Service - Bandwidth 50TB'         => 'CDRS-SVC-REP-50TBD',
            'Cloud Server Replication Service - Bandwidth 100TB'        => 'CDRS-SVC-REP-100TB',
        ];
        foreach ($backupSummary as $r) {
            $sku = $backupKeyMap[$r['name']] ?? 'N/A';
            $rows[] = [
                'sku'=>$sku,'name'=>$r['name'],'unit'=>$r['unit'] ?? 'GB',
                'kl_qty'=>$r['kl_qty'] ?? 0,'cj_qty'=>$r['cj_qty'] ?? 0,
                'unit_price'=>$r['price_per_unit'] ?? 0,
                'kl_price'=>$r['kl_price'] ?? 0,'cj_price'=>$r['cj_price'] ?? 0,
            ];
        }

        return $rows;
    }

   
private function scaleLineItems(array $rows, int $duration): array
{
    if ($duration <= 1) return $rows;
    foreach ($rows as &$r) {
        // Gandakan amounts (KL/CJ) ikut bulan. Qty & unit_price kekal.
        $r['kl_price'] = round((float)($r['kl_price'] ?? 0) * $duration, 2);
        $r['cj_price'] = round((float)($r['cj_price'] ?? 0) * $duration, 2);
    }
    unset($r);
    return $rows;
}

private function scaleRecurringTotals(array $data, int $duration): array
{
    if ($duration <= 1) return $data;

    foreach ([
        'totalManagedCharges',
        'totalLicenseCharges',
        'klEcsTotal','cjEcsTotal',
        'klTotal','cjTotal',
        'totalStorageCharges',
        'totalBackupCharges',
        'totalcloudSecurityCharges',
        'totalMonitoringCharges',
        'totalSecurityCharges',
    ] as $k) {
        if (isset($data[$k])) {
            $data[$k] = round((float)$data[$k] * $duration, 2);
        }
    }

    if (isset($data['monthlyTotal'])) {
        $data['annualRecurringTotal'] = round((float)$data['monthlyTotal'] * $duration, 2);
    }
    return $data;
}


    /*private function computeProfessionalTotals($s)
    {
        $unit = (float) data_get(config('pricing'), 'CPFS-PFS-MDY-5OTC.price_per_unit', 1200);
        $days = (int) ($s->mandays ?? 0);
        $total = $days * $unit;
        return [$days, $unit, $total];
    }*/

    private function computeProfessionalTotals($s)
{
    $days = (int) ($s->mandays ?? 0);

    $pricing = config('pricing');

    $sku1 = 'CPFS-PFS-MDY-1OTC'; // <=4 days
    $sku5 = 'CPFS-PFS-MDY-5OTC'; // >=5 days (discounted)

    $price1 = (float) data_get($pricing, "$sku1.price_per_unit", 0);
    $price5 = (float) data_get($pricing, "$sku5.price_per_unit", $price1);

    // Pilih harga ikut hari
    $unitPrice = $days >= 5 ? $price5 : $price1;

    $total = round($days * $unitPrice, 2);

    // Return ikut signature sedia ada: [$psDays, $psUnit, $totalProfessionalCharges]
    return [$days, 'Day', $total];
}


    private function computeNetworkTotals($s, $pricing)
    {
        $kl = 0.0; $cj = 0.0;
        $getPrice = fn($key) => (float) data_get($pricing, "$key.price_per_unit", 0);

        if (!empty($s->kl_bandwidth)) $kl += $s->kl_bandwidth * $getPrice($this->getBandwidthPriceKey($s->kl_bandwidth));
        if (!empty($s->cyber_bandwidth)) $cj += $s->cyber_bandwidth * $getPrice($this->getBandwidthPriceKey($s->cyber_bandwidth));
        if (!empty($s->kl_bandwidth_with_antiddos)) $kl += $s->kl_bandwidth_with_antiddos * $getPrice($this->getBandwidthPriceKey($s->kl_bandwidth_with_antiddos));
        if (!empty($s->cyber_bandwidth_with_antiddos)) $cj += $s->cyber_bandwidth_with_antiddos * $getPrice($this->getBandwidthPriceKey($s->cyber_bandwidth_with_antiddos));

        $map = [
            'kl_included_elastic_ip'    => ['CNET-EIP-SHR-FOC','KL'],
            'cyber_included_elastic_ip' => ['CNET-EIP-SHR-FOC','CJ'],
            'kl_elastic_ip'             => ['CNET-EIP-SHR-STD','KL'],
            'cyber_elastic_ip'          => ['CNET-EIP-SHR-STD','CJ'],
            'kl_elastic_load_balancer'    => ['CNET-ELB-SHR-STD','KL'],
            'cyber_elastic_load_balancer' => ['CNET-ELB-SHR-STD','CJ'],
            'kl_direct_connect_virtual'    => ['CNET-DGW-SHR-EXT','KL'],
            'cyber_direct_connect_virtual' => ['CNET-DGW-SHR-EXT','CJ'],
            'kl_l2br_instance'    => ['CNET-L2BR-SHR-EXT','KL'],
            'cyber_l2br_instance' => ['CNET-L2BR-SHR-EXT','CJ'],
            'kl_vpll_l2br'        => ['CNET-L2BR-SHR-INT','KL'],
            'cyber_vpll_l2br'     => ['CNET-L2BR-SHR-INT','CJ'],
            'kl_nat_gateway_small'     => ['CNET-NAT-SHR-S','KL'],
            'kl_nat_gateway_medium'    => ['CNET-NAT-SHR-M','KL'],
            'kl_nat_gateway_large'     => ['CNET-NAT-SHR-L','KL'],
            'kl_nat_gateway_xlarge'    => ['CNET-NAT-SHR-XL','KL'],
            'cyber_nat_gateway_small'  => ['CNET-NAT-SHR-S','CJ'],
            'cyber_nat_gateway_medium' => ['CNET-NAT-SHR-M','CJ'],
            'cyber_nat_gateway_large'  => ['CNET-NAT-SHR-L','CJ'],
            'cyber_nat_gateway_xlarge' => ['CNET-NAT-SHR-XL','CJ'],
            'kl_gslb'    => ['CNET-GLB-SHR-DOMAIN','KL'],
            'cyber_gslb' => ['CNET-GLB-SHR-DOMAIN','CJ'],
        ];

        foreach ($map as $field => [$priceKey, $region]) {
            $qty = (float) ($s->$field ?? 0);
            if ($qty > 0) {
                $line = $qty * $getPrice($priceKey);
                if ($region === 'KL') $kl += $line; else $cj += $line;
            }
        }

        if (!empty($s->kl_virtual_private_leased_line)) $kl += $s->kl_virtual_private_leased_line * $getPrice($this->getVPLLPriceKey($s->kl_virtual_private_leased_line));
        if (!empty($s->cyber_virtual_private_leased_line)) $cj += $s->cyber_virtual_private_leased_line * $getPrice($this->getVPLLPriceKey($s->cyber_virtual_private_leased_line));

        return [round($kl, 2), round($cj, 2)];
    }

    private function getBandwidthPriceKey($mbps)
    {
        if ($mbps <= 10) return 'CNET-BWS-CIA-10';
        if ($mbps <= 30) return 'CNET-BWS-CIA-30';
        if ($mbps <= 50) return 'CNET-BWS-CIA-50';
        if ($mbps <= 80) return 'CNET-BWS-CIA-80';
        return 'CNET-BWS-CIA-100';
    }

    private function getVPLLPriceKey($mbps)
    {
        if ($mbps <= 10) return 'CNET-PLL-SHR-10';
        if ($mbps <= 30) return 'CNET-PLL-SHR-30';
        if ($mbps <= 50) return 'CNET-PLL-SHR-50';
        if ($mbps <= 80) return 'CNET-PLL-SHR-80';
        return 'CNET-PLL-SHR-100';
    }

    public function generatePDF() { /* optional */ }

    
    private function computeManagedSummary(\App\Models\Version $version): array
    {
        $svc = $version->security_service;
        if (!$svc) return [[], 0.0];

        $services = [
            'Managed Operating System',
            'Managed Backup and Restore',
            'Managed Patching',
            'Managed DR',
        ];

        $counts = [];
        foreach ($services as $s) { $counts[$s] = ['kl_qty'=>0,'cj_qty'=>0]; }

        foreach (range(1,4) as $i) {
            $v = $svc->{'kl_managed_services_' . $i} ?? null;
            if ($v && in_array($v, $services, true)) $counts[$v]['kl_qty']++;
        }
        foreach (range(1,4) as $i) {
            $v = $svc->{'cyber_managed_services_' . $i} ?? null;
            if ($v && in_array($v, $services, true)) $counts[$v]['cj_qty']++;
        }

        [$priceByName, $unitByName] = $this->lookupManagedServicePrices();

        $summary = []; $grand = 0.0;
        foreach ($services as $name) {
            $unit  = $unitByName[strtolower($name)] ?? 'VM';
            $price = (float) ($priceByName[strtolower($name)] ?? 0);

            $klPrice = $counts[$name]['kl_qty'] * $price;
            $cjPrice = $counts[$name]['cj_qty'] * $price;

            if (($counts[$name]['kl_qty'] + $counts[$name]['cj_qty']) > 0) {
                $summary[] = [
                    'name'=>$name,'unit'=>$unit,'price_per_unit'=>$price,
                    'kl_qty'=>$counts[$name]['kl_qty'],'cj_qty'=>$counts[$name]['cj_qty'],
                    'kl_price'=>$klPrice,'cj_price'=>$cjPrice,
                ];
            }
            $grand += $klPrice + $cjPrice;
        }

        return [$summary, round($grand,2)];
    }

    private function lookupManagedServicePrices(): array
    {
        $pricing = config('pricing');
        $priceByName = []; $unitByName  = [];

        foreach ($pricing as $code => $item) {
            if (!is_array($item)) continue;
            $cat  = strtolower((string)($item['category_name'] ?? ''));
            $name = strtolower(trim((string)($item['name'] ?? '')));
            if ($name === '') continue;

            if ($cat === 'managed services') {
                $priceByName[$name] = (float)($item['price_per_unit'] ?? 0);
                $unitByName[$name]  = (string)($item['measurement_unit'] ?? 'VM');
            }
        }

        if (empty($priceByName)) {
            foreach ($pricing as $code => $item) {
                if (!is_array($item)) continue;
                $name = strtolower(trim((string)($item['name'] ?? '')));
                if (in_array($name, [
                    'managed operating system','managed backup and restore',
                    'managed patching','managed dr',
                ], true)) {
                    $priceByName[$name] = (float)($item['price_per_unit'] ?? 0);
                    $unitByName[$name]  = (string)($item['measurement_unit'] ?? 'VM');
                }
            }
        }

        return [$priceByName, $unitByName];
    }

    private function computeLicenseRateCard($summary, array $pricing): array
    {
        $rows = []; $grand = 0.0;
        $map = [
            'Microsoft Windows Server (Core Pack) - Standard'    => ['kl'=>'kl_windows_std',  'cj'=>'cyber_windows_std',  'key'=>'CLIC-WIN-COR-SRVSTD'],
            'Microsoft Windows Server (Core Pack) - Data Center' => ['kl'=>'kl_windows_dc',   'cj'=>'cyber_windows_dc',   'key'=>'CLIC-WIN-COR-SRVDC'],
            'Microsoft Remote Desktop Services (SAL)'            => ['kl'=>'kl_rds',          'cj'=>'cyber_rds',          'key'=>'CLIC-WIN-USR-RDSSAL'],
            'Microsoft SQL (Web) (Core Pack)'                    => ['kl'=>'kl_sql_web',      'cj'=>'cyber_sql_web',      'key'=>'CLIC-WIN-COR-SQLWEB'],
            'Microsoft SQL (Standard) (Core Pack)'               => ['kl'=>'kl_sql_std',      'cj'=>'cyber_sql_std',      'key'=>'CLIC-WIN-COR-SQLSTD'],
            'Microsoft SQL (Enterprise) (Core Pack)'             => ['kl'=>'kl_sql_ent',      'cj'=>'cyber_sql_ent',      'key'=>'CLIC-WIN-COR-SQLENT'],
            'RHEL (1-8vCPU)'                                     => ['kl'=>'kl_rhel_1_8',     'cj'=>'cyber_rhel_1_8',     'key'=>'CLIC-RHL-COR-8'],
            'RHEL (9-127vCPU)'                                   => ['kl'=>'kl_rhel_9_127',   'cj'=>'cyber_rhel_9_127',   'key'=>'CLIC-RHL-COR-127'],
        ];

        foreach ($map as $label => $def) {
            $price = (float)($pricing[$def['key']]['price_per_unit'] ?? 0);
            $klQty = (int)($summary->{$def['kl']} ?? 0);
            $cjQty = (int)($summary->{$def['cj']} ?? 0);

            if ($klQty > 0 || $cjQty > 0) {
                $row = [
                    'name'=>$label,'unit'=>'Unit','price_per_unit'=>$price,
                    'kl_qty'=>$klQty,'cj_qty'=>$cjQty,
                    'kl_price'=>$klQty * $price,'cj_price'=>$cjQty * $price,
                ];
                $rows[] = $row; $grand += $row['kl_price'] + $row['cj_price'];
            }
        }
        return [$rows, round($grand,2)];
    }

    private function computeStorageSummary($s, array $pricing): array
    {
        if (!$s) return [[], 0.0];

        $rows = []; $grand = 0.0;
        $map = [
            'Elastic Volume Service (EVS)' => ['kl'=>'kl_evs','cj'=>'cyber_evs','key'=>'CSTG-EVS-SHR-STD','unit'=>'GB'],
            'Scalable File Service (SFS)'  => ['kl'=>'kl_scalable_file_service','cj'=>'cyber_scalable_file_service','key'=>'CSTG-SFS-SHR-STD','unit'=>'GB'],
            'Object Storage Service (OBS)' => ['kl'=>'kl_object_storage_service','cj'=>'cyber_object_storage_service','key'=>'CSTG-OBS-SHR-STD','unit'=>'GB'],
            'Snapshot Storage'             => ['kl'=>'kl_snapshot_storage','cj'=>'cyber_snapshot_storage','key'=>'CSTG-BCK-SHR-STD','unit'=>'GB'],
            'Image Storage'                => ['kl'=>'kl_image_storage','cj'=>'cyber_image_storage','key'=>'CSTG-OBS-SHR-IMG','unit'=>'GB'],
        ];

        foreach ($map as $label => $def) {
            $unit  = $def['unit'];
            $price = (float)($pricing[$def['key']]['price_per_unit'] ?? 0);

            $klQty = (float)($s->{$def['kl']} ?? 0);
            $cjQty = (float)($s->{$def['cj']} ?? 0);

            if ($klQty > 0 || $cjQty > 0) {
                $row = [
                    'name'=>$label,'unit'=>$unit,'price_per_unit'=>$price,
                    'kl_qty'=>$klQty,'cj_qty'=>$cjQty,
                    'kl_price'=>$klQty * $price,'cj_price'=>$cjQty * $price,
                ];
                $rows[] = $row; $grand += $row['kl_price'] + $row['cj_price'];
            }
        }

        return [$rows, round($grand,2)];
    }

    private function computeCloudSecuritySummary($s, array $pricing): array
    {
        if (!$s) return [[], 0.0];

        $rows = []; $grand = 0.0;
        $map = [
            'Cloud Firewall (Fortigate)' => ['kl'=>'kl_firewall_fortigate','cj'=>'cyber_firewall_fortigate','key'=>'CSEC-VFW-DDT-FG','unit'=>'Unit'],
            'Cloud Firewall (OPNSense)'  => ['kl'=>'kl_firewall_opnsense','cj'=>'cyber_firewall_opnsense','key'=>'CSEC-VFW-DDT-OS','unit'=>'Unit'],
            'Cloud Shared WAF (Mbps)'    => ['kl'=>'kl_shared_waf','cj'=>'cyber_shared_waf','key'=>'CSEC-WAF-SHR-HA','unit'=>'Mbps'],
            'Anti-Virus (Panda)'         => ['kl'=>'kl_antivirus','cj'=>'cyber_antivirus','key'=>'CSEC-EDR-NOD-STD','unit'=>'Unit'],
        ];

        foreach ($map as $label => $def) {
            $unit  = $def['unit'];
            $price = (float)($pricing[$def['key']]['price_per_unit'] ?? 0);

            $klQty = (float)($s->{$def['kl']} ?? 0);
            $cjQty = (float)($s->{$def['cj']} ?? 0);

            if ($klQty > 0 || $cjQty > 0) {
                $row = [
                    'name'=>$label,'unit'=>$unit,'price_per_unit'=>$price,
                    'kl_qty'=>$klQty,'cj_qty'=>$cjQty,
                    'kl_price'=>$klQty * $price,'cj_price'=>$cjQty * $price,
                ];
                $rows[] = $row; $grand += $row['kl_price'] + $row['cj_price'];
            }
        }

        return [$rows, round($grand,2)];
    }

    private function computeMonitoringSummary($s, array $pricing): array
    {
        if (!$s) return [[], 0.0];

        $price = (float)($pricing['CMON-TIS-NOD-STD']['price_per_unit'] ?? 0);
        $klQty = (int)($s->kl_insight_vmonitoring ?? 0);
        $cjQty = (int)($s->cyber_insight_vmonitoring ?? 0);

        $rows = [];
        if ($klQty > 0 || $cjQty > 0) {
            $rows[] = [
                'name'=>'TCS inSight vMonitoring','unit'=>'Unit','price_per_unit'=>$price,
                'kl_qty'=>$klQty,'cj_qty'=>$cjQty,
                'kl_price'=>$klQty * $price,'cj_price'=>$cjQty * $price,
            ];
        }

        $grand = collect($rows)->sum(fn($r)=>$r['kl_price'] + $r['cj_price']);
        return [$rows, round($grand,2)];
    }

    private function computeSecuritySummary($s, array $pricing): array
    {
        if (!$s) return [[], 0.0];

        $price = (float)($pricing['SECT-VAS-EIP-STD']['price_per_unit'] ?? 0);
        $klQty = (int)($s->kl_cloud_vulnerability ?? 0);
        $cjQty = (int)($s->cyber_cloud_vulnerability ?? 0);

        $rows = [];
        if ($klQty > 0 || $cjQty > 0) {
            $rows[] = [
                'name'=>'Cloud Vulnerability Assessment (Per IP)','unit'=>'Unit','price_per_unit'=>$price,
                'kl_qty'=>$klQty,'cj_qty'=>$cjQty,
                'kl_price'=>$klQty * $price,'cj_price'=>$cjQty * $price,
            ];
        }

        $grand = 0.0; foreach ($rows as $r) { $grand += ($r['kl_price'] + $r['cj_price']); }
        return [$rows, round($grand,2)];
    }

    private function computeBackupSummary($s, array $pricing): array
    {
        if (!$s) return [[], 0.0];

        $rows = []; $grand = 0.0;

        $map = [
            'Cloud Server Backup Service - Full Backup Capacity' => [
                'kl'=>'kl_full_backup_capacity','cj'=>'cyber_full_backup_capacity','key'=>'CSBS-STRG-BCK-CSBSF','unit'=>'GB',
            ],
            'Cloud Server Backup Service - Incremental Backup Capacity' => [
                'kl'=>'kl_incremental_backup_capacity','cj'=>'cyber_incremental_backup_capacity','key'=>'CSBS-STRG-BCK-CSBSI','unit'=>'GB',
            ],
            'Cloud Server Replication Service - Retention Capacity' => [
                'kl'=>'kl_replication_retention_capacity','cj'=>'cyber_replication_retention_capacity','key'=>'CSBS-STRG-BCK-REPS','unit'=>'GB',
            ],
        ];

        foreach ($map as $label => $def) {
            $price = (float)($pricing[$def['key']]['price_per_unit'] ?? 0);
            $unit  = $def['unit'];
            $klQty = (float)($s->{$def['kl']} ?? 0);
            $cjQty = (float)($s->{$def['cj']} ?? 0);

            if ($klQty > 0 || $cjQty > 0) {
                $row = [
                    'name'=>$label,'unit'=>$unit,'price_per_unit'=>$price,
                    'kl_qty'=>$klQty,'cj_qty'=>$cjQty,
                    'kl_price'=>round($klQty * $price, 2),'cj_price'=>round($cjQty * $price, 2),
                ];
                $rows[] = $row; $grand += $row['kl_price'] + $row['cj_price'];
            }
        }
        return [$rows, round($grand, 2)];
    }

    private function computeEcsSummary(\App\Models\Version $version): array
    {
        $rows = $version->ecs_configuration ?? collect();
        if (!($rows instanceof \Illuminate\Support\Collection)) $rows = collect($rows);

        [$nameToPrice, $nameToUnit] = $this->buildEcsPricingLookups();

        $summary = [];
        foreach ($rows as $r) {
            $flavour = trim((string) $r->ecs_flavour_mapping);
            if ($flavour === '') continue;

            $region  = trim((string) $r->region);
            $price   = (float) ($nameToPrice[strtolower($flavour)] ?? 0);
            $unit    = (string) ($nameToUnit[strtolower($flavour)] ?? 'Unit');

            if (!isset($summary[$flavour])) {
                $summary[$flavour] = [
                    'flavour'=>$flavour,'unit'=>$unit,'unit_price'=>$price,
                    'kl_qty'=>0,'cj_qty'=>0,'kl_price'=>0.0,'cj_price'=>0.0,
                ];
            }
            if (strcasecmp($region, 'Kuala Lumpur') === 0) {
                $summary[$flavour]['kl_qty'] += 1;
                $summary[$flavour]['kl_price'] = $summary[$flavour]['kl_qty'] * $price;
            } elseif (strcasecmp($region, 'Cyberjaya') === 0) {
                $summary[$flavour]['cj_qty'] += 1;
                $summary[$flavour]['cj_price'] = $summary[$flavour]['cj_qty'] * $price;
            }
        }

        $ecsSummary = array_values($summary);
        $klTotal = collect($ecsSummary)->sum('kl_price');
        $cjTotal = collect($ecsSummary)->sum('cj_price');

        return [$ecsSummary, $klTotal, $cjTotal];
    }

    private function buildEcsPricingLookups(): array
    {
        $pricing = config('pricing');
        $nameToPrice = []; $nameToUnit  = [];

        foreach ($pricing as $key => $item) {
            if (is_array($item) && is_string($key) && str_starts_with($key, 'CMPT-ECS-')) {
                $name = strtolower(trim((string) ($item['name'] ?? '')));
                if ($name !== '') {
                    $nameToPrice[$name] = (float) ($item['price_per_unit'] ?? 0);
                    $nameToUnit[$name]  = (string) ($item['measurement_unit'] ?? 'Unit');
                }
            }
        }
        return [$nameToPrice, $nameToUnit];
    }


    
}
