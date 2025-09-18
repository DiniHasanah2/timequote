<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Version;
use App\Models\Quotation;
use App\Models\InternalSummary;
use Illuminate\Support\Str;

// CSV
use League\Csv\Writer;
use SplTempFileObject;

// Excel
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\QuotationArrayExport;

class QuotationCsvController extends Controller
{
    // ===== Excel (.xlsx) =====
    public function generateXlsx(Request $request, $versionId)
    {
        $version = Version::with(['project.customer', 'solution_type'])->findOrFail($versionId);

        // pastikan ada quotation row
        $quotation = Quotation::firstOrCreate(
            ['version_id' => $versionId],
            [
                'project_id' => $version->project_id,
                'presale_id' => auth()->id(),
                'status'     => 'pending',
                'quote_code' => 'Q-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5)),
            ]
        );

        $mode = $request->query('mode', 'monthly'); // 'monthly' | 'annual'
        $contractDuration = $mode === 'annual'
            ? 12
            : (int) (session("quotation.$versionId.contract_duration", $quotation->contract_duration ?? 12));

        $internal = InternalSummary::where('version_id', $versionId)->first() ?? new InternalSummary();
        $pricing = config('pricing');

        // ===== meta versi catalog (daripada pricing.php) =====
        $catalogMeta  = config('pricing._catalog') ?? [];
        $catalogLabel = is_array($catalogMeta)
            ? ($catalogMeta['version_name'] ?? ($catalogMeta['version_code'] ?? 'legacy'))
            : 'legacy';
        $catalogFrom  = is_array($catalogMeta) ? ($catalogMeta['effective_from'] ?? null) : null;
        $catalogTo    = is_array($catalogMeta) ? ($catalogMeta['effective_to'] ?? null) : null;

        // ringkasan/kiraan
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

        // monthly total (selaras PDF)
        $licenseKL    = collect($licenseRateCard)->sum('kl_price');
        $licenseCJ    = collect($licenseRateCard)->sum('cj_price');
        $licenseTotal = $totalLicenseCharges ?? ($licenseKL + $licenseCJ);

        $monthlyTotal =
            ($totalManagedCharges ?? 0) +
            (($klTotal ?? 0) + ($cjTotal ?? 0)) +
            (($klEcsTotal ?? 0) + ($cjEcsTotal ?? 0)) +
            $licenseTotal +
            ($totalStorageCharges ?? 0) +
            ($totalBackupCharges ?? 0) +
            ($totalcloudSecurityCharges ?? 0) +
            ($totalMonitoringCharges ?? 0) +
            ($totalSecurityCharges ?? 0);

        $contractTotal = ($monthlyTotal * $contractDuration) + ($totalProfessionalCharges ?? 0);
        $serviceTax    = $contractTotal * 0.08;
        $finalTotal    = $contractTotal + $serviceTax;


        // 0) meta untuk header atas
        /*$confLine = 'Confidential | ' . now()->format('d/m/Y')
            . ' | Quotation ID: ' . ($quotation->id ?? '-')
            . ($catalogLabel ? (' | Catalog Version: ' . $catalogLabel) : '');

        // label commitment ikut mode
        $isMonthly      = ($mode === 'monthly');
        $commitLabel    = $isMonthly ? 'Monthly Commitment (Exclude SST):' : 'Annual Commitment (Exclude SST):';
        $commitValueNum = $isMonthly ? (float)$monthlyTotal : (float)($monthlyTotal * 12);

        

        // logo path (cari yang wujud)
$logoPath = collect([
    public_path('assets/time_logo.png'),
    public_path('images/time-logo.png'),
    public_path('time_logo.png'),
])->first(fn($p) => is_file($p)) ?: null;


        // 1) HEADER + INFO ATAS
        $rows   = [];
        $rows[] = [$confLine]; // A1: Confidential line
        $rows[] = ['CLOUD SERVICES']; // banner pink + logo (styled in AfterSheet)
        $rows[] = []; // spacer
        $rows[] = ['Attention:', $version->project->customer->name ?? 'N/A'];

        // 2) CONTRACT DURATION + COMMITMENT (2 rows grid)
        $rows[] = ['Contract Duration:', $contractDuration . ' Months', $commitLabel, (float)$commitValueNum];
        $rows[] = ['One Time Charges (Exclude SST):', (float)($totalProfessionalCharges ?? 0), '', ''];

        // 3) BOX "TOTAL CONTRACT VALUE (WITH SST)"
        $rows[] = ['TOTAL CONTRACT VALUE (WITH SST)'];
        $rows[] = [(float)$finalTotal];

        // 4) spacer
        $rows[] = [];

        // 5) tajuk & header jadual summary
        $rows[] = ['Summary of Quotation'];
        $rows[] = ['Category', 'One Time Charges', 'KL Monthly', 'CJ Monthly', 'Total Monthly'];

     
        // Professional Services
        $rows[] = ['Professional Services', (float)$totalProfessionalCharges, null, null, null];

        // Managed
        $klManaged = (float) collect($managedSummary)->sum('kl_price');
        $cjManaged = (float) collect($managedSummary)->sum('cj_price');
        $rows[] = ['Managed Services', 0.0, $klManaged, $cjManaged, (float)$totalManagedCharges];

        // Network
        $rows[] = ['Network', 0.0, (float)$klTotal, (float)$cjTotal, (float)(($klTotal ?? 0) + ($cjTotal ?? 0))];

        // ECS
        $rows[] = ['Compute - ECS', 0.0, (float)$klEcsTotal, (float)$cjEcsTotal, (float)(($klEcsTotal ?? 0) + ($cjEcsTotal ?? 0))];

        // License
        $rows[] = ['Licenses', 0.0, (float)$licenseKL, (float)$licenseCJ, (float)$licenseTotal];

        // Storage
        $klStorage = (float) collect($storageSummary)->sum('kl_price');
        $cjStorage = (float) collect($storageSummary)->sum('cj_price');
        $rows[] = ['Storage', 0.0, $klStorage, $cjStorage, (float)$totalStorageCharges];

        // Backup
        $klBackup = (float) collect($backupSummary)->sum('kl_price');
        $cjBackup = (float) collect($backupSummary)->sum('cj_price');
        $rows[] = ['Backup', 0.0, $klBackup, $cjBackup, (float)$totalBackupCharges];

        // Cloud Security
        $klCloud = (float) collect($cloudSecuritySummary)->sum('kl_price');
        $cjCloud = (float) collect($cloudSecuritySummary)->sum('cj_price');
        $rows[] = ['Cloud Security', 0.0, $klCloud, $cjCloud, (float)$totalcloudSecurityCharges];

        // Monitoring
        $klMonitor = (float) collect($monitoringSummary)->sum('kl_price');
        $cjMonitor = (float) collect($monitoringSummary)->sum('cj_price');
        $rows[] = ['Monitoring', 0.0, $klMonitor, $cjMonitor, (float)$totalMonitoringCharges];

        // Security Services
        $klSecurity = (float) collect($securitySummary)->sum('kl_price');
        $cjSecurity = (float) collect($securitySummary)->sum('cj_price');
        $rows[] = ['Security Services', 0.0, $klSecurity, $cjSecurity, (float)$totalSecurityCharges];

        // 6) totals (label di B, amount di C — styled & merged dlm AfterSheet)
        $rows[] = [];
        $rows[] = ['', 'ONE TIME CHARGES TOTAL', (float)$totalProfessionalCharges];
        $rows[] = ['', 'MONTHLY TOTAL',           (float)$monthlyTotal];
        $rows[] = ['', 'CONTRACT TOTAL',          (float)$contractTotal];
        $rows[] = ['', 'SERVICE TAX (8%)',        (float)$serviceTax];
        $rows[] = ['', 'FINAL TOTAL (Include Tax)', (float)$finalTotal];

        // 7) TERMS & CONDITIONS
        $rows[] = [];
        $rows[] = ['Terms and Conditions:'];*/
        // ====== bina ROWS untuk .xlsx yang follow layout PDF ======

// 0) meta untuk header atas
$confLine = 'Confidential | ' . now()->format('d/m/Y')
    . ' | Quotation ID: ' . ($quotation->id ?? '-')
    . ($catalogLabel ? (' | Catalog Version: ' . $catalogLabel) : '');

// --- NEW: multiplier & label ikut mode ---
$isAnnual    = ($mode === 'annual');
$mult        = $isAnnual ? 12 : 1;
$periodLabel = $isAnnual ? 'Annual' : 'Monthly';

// label commitment ikut mode
$commitLabel    = $isAnnual ? 'Annual Commitment (Exclude SST):' : 'Monthly Commitment (Exclude SST):';
$commitValueNum = (float)($monthlyTotal * $mult);

// logo path (cari yang wujud)
$logoPath = collect([
    public_path('assets/time_logo.png'),
    public_path('images/time-logo.png'),
    public_path('time_logo.png'),
])->first(fn($p) => is_file($p)) ?: null;

// 1) HEADER + INFO ATAS
$rows   = [];
$rows[] = [$confLine]; // A1: Confidential line
$rows[] = ['CLOUD SERVICES']; // banner pink + logo (styled in AfterSheet)
$rows[] = []; // spacer
$rows[] = ['Attention:', $version->project->customer->name ?? 'N/A'];

// 2) CONTRACT DURATION + COMMITMENT (2 rows grid)
$rows[] = ['Contract Duration:', $contractDuration . ' Months', $commitLabel, $commitValueNum];
$rows[] = ['One Time Charges (Exclude SST):', (float)($totalProfessionalCharges ?? 0), '', ''];

// 3) BOX "TOTAL CONTRACT VALUE (WITH SST)"
$rows[] = ['TOTAL CONTRACT VALUE (WITH SST)'];
$rows[] = [(float)$finalTotal];

// 4) spacer
$rows[] = [];

// 5) tajuk & header jadual summary
$rows[] = ['Summary of Quotation'];
$rows[] = ['Category', 'One Time Charges', "KL {$periodLabel}", "CJ {$periodLabel}", "Total {$periodLabel}"];

// ===== baris kategori (nilai NOMBOR – formatting RM dibuat di AfterSheet) =====

// Professional Services (OTC tak darab)
$rows[] = ['Professional Services', (float)$totalProfessionalCharges, null, null, null];

// Managed
$klManaged = (float) collect($managedSummary)->sum('kl_price');
$cjManaged = (float) collect($managedSummary)->sum('cj_price');
$rows[] = ['Managed Services', 0.0, $klManaged * $mult, $cjManaged * $mult, (float)$totalManagedCharges * $mult];

// Network
$rows[] = ['Network', 0.0, (float)$klTotal * $mult, (float)$cjTotal * $mult, (float)(($klTotal ?? 0) + ($cjTotal ?? 0)) * $mult];

// ECS
$rows[] = ['Compute - ECS', 0.0, (float)$klEcsTotal * $mult, (float)$cjEcsTotal * $mult, (float)(($klEcsTotal ?? 0) + ($cjEcsTotal ?? 0)) * $mult];

// License
$licenseKL    = (float) collect($licenseRateCard)->sum('kl_price');
$licenseCJ    = (float) collect($licenseRateCard)->sum('cj_price');
$licenseTotal = (float) ($totalLicenseCharges ?? ($licenseKL + $licenseCJ));
$rows[] = ['Licenses', 0.0, $licenseKL * $mult, $licenseCJ * $mult, $licenseTotal * $mult];

// Storage
$klStorage = (float) collect($storageSummary)->sum('kl_price');
$cjStorage = (float) collect($storageSummary)->sum('cj_price');
$rows[] = ['Storage', 0.0, $klStorage * $mult, $cjStorage * $mult, (float)$totalStorageCharges * $mult];

// Backup
$klBackup = (float) collect($backupSummary)->sum('kl_price');
$cjBackup = (float) collect($backupSummary)->sum('cj_price');
$rows[] = ['Backup', 0.0, $klBackup * $mult, $cjBackup * $mult, (float)$totalBackupCharges * $mult];

// Cloud Security
$klCloud = (float) collect($cloudSecuritySummary)->sum('kl_price');
$cjCloud = (float) collect($cloudSecuritySummary)->sum('cj_price');
$rows[] = ['Cloud Security', 0.0, $klCloud * $mult, $cjCloud * $mult, (float)$totalcloudSecurityCharges * $mult];

// Monitoring
$klMonitor = (float) collect($monitoringSummary)->sum('kl_price');
$cjMonitor = (float) collect($monitoringSummary)->sum('cj_price');
$rows[] = ['Monitoring', 0.0, $klMonitor * $mult, $cjMonitor * $mult, (float)$totalMonitoringCharges * $mult];

// Security Services
$klSecurity = (float) collect($securitySummary)->sum('kl_price');
$cjSecurity = (float) collect($securitySummary)->sum('cj_price');
$rows[] = ['Security Services', 0.0, $klSecurity * $mult, $cjSecurity * $mult, (float)$totalSecurityCharges * $mult];

// 6) totals (label di B, amount di C — styled & merged dlm AfterSheet)
$rows[] = [];
$rows[] = ['', 'ONE TIME CHARGES TOTAL', (float)$totalProfessionalCharges];
// --- ubah label & nilai ikut mode ---
$rows[] = ['', $isAnnual ? 'ANNUAL TOTAL' : 'MONTHLY TOTAL', (float)$monthlyTotal * $mult];
$rows[] = ['', 'CONTRACT TOTAL', (float)$contractTotal];
$rows[] = ['', 'SERVICE TAX (8%)', (float)$serviceTax];
$rows[] = ['', 'FINAL TOTAL (Include Tax)', (float)$finalTotal];

// 7) TERMS & CONDITIONS (kekal sama)
$rows[] = [];
$rows[] = ['Terms and Conditions:'];

        $rows[] = [ 
            "1. The delivery lead time is subject to availability of our capacity, infrastructure and upon our acknowledgement of signed Service Order form.\n" .
            "2. Price quoted only valid for customer stated within this quotation for a duration of 60 days.\n" .
            "3. All prices quoted shall be subjected to\n" .
            "   • Other charges and expenses incurred due to additional services not covered in the above quotation shall be charged based on actual amount incurred\n" .
            "4. All agreements for the provision of the services are for a fixed period and in the event of termination prior to the completion of the fixed period, 100% of the rental or regular charges for the remaining contract period shall be imposed.\n" .
            "5. SLA is 99.95% Availability. No Performance SLA and Credit Guarantee is provided unless specifically mentioned.\n" .
            "6. TIME will only be providing Infrastructure as a service only (IaaS). Operating System and Application will be self-managed by customer unless relevant service is subscribed.\n" .
            "7. The price quoted does not include any Professional services and managed service beyond infrastructure level. If required, Scope of work and contract to be agreed before any work commence.\n" .
            "8. All sums due are exclusive of the taxes of any nature including but not limited to service tax, withholding taxes and any other taxes and all other government fees and charges assessed upon or with respect to the service(s)."
        ];

        // nama fail include slug versi
        $verSlug  = Str::slug($catalogLabel ?: 'legacy', '_');
        $filename = 'quotation_' . $version->id . '_' . $verSlug . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new QuotationArrayExport($rows, 'Quotation Summary', $logoPath), $filename);
    }

    // ===== CSV (.csv) kekalkan (kalau nak), tak perlu styling =====
    public function generateCsv(Request $request, $versionId)
    {
        $version = Version::with(['project.customer', 'solution_type'])->findOrFail($versionId);

        // Create a real quotation row if not exists
        $quotation = Quotation::firstOrCreate(
            ['version_id' => $versionId],
            [
                'project_id' => $version->project_id,
                'presale_id' => auth()->id(),
                'status'     => 'pending',
                'quote_code' => 'Q-' . now()->format('Ymd') . '-' . Str::upper(Str::random(5)),
            ]
        );

        $mode = $request->query('mode', 'monthly'); // 'monthly' | 'annual'
        $contractDuration = $mode === 'annual'
            ? 12
            : (int) (session("quotation.$versionId.contract_duration", $quotation->contract_duration ?? 12));

        $internal = InternalSummary::where('version_id', $versionId)->first() ?? new InternalSummary();
        $pricing  = config('pricing');

        // meta katalog
        $catalogMeta  = config('pricing._catalog') ?? [];
        $catalogLabel = is_array($catalogMeta)
            ? ($catalogMeta['version_name'] ?? ($catalogMeta['version_code'] ?? 'legacy'))
            : 'legacy';
        $catalogFrom  = is_array($catalogMeta) ? ($catalogMeta['effective_from'] ?? null) : null;
        $catalogTo    = is_array($catalogMeta) ? ($catalogMeta['effective_to'] ?? null) : null;

        // summary data
        [$managedSummary, $totalManagedCharges] = $this->computeManagedSummary($version);
        [$licenseRateCard, $totalLicenseCharges] = $this->computeLicenseRateCard($internal, $pricing);
        [$ecsSummary, $klEcsTotal, $cjEcsTotal] = $this->computeEcsSummary($version);
        [$klTotal, $cjTotal] = $this->computeNetworkTotals($internal, $pricing);
        [$psDays, $psUnit, $totalProfessionalCharges] = $this->computeProfessionalTotals($internal);
        [$storageSummary, $totalStorageCharges] = $this->computeStorageSummary($internal, $pricing);
        [$cloudSecuritySummary, $totalcloudSecurityCharges] = $this->computeCloudSecuritySummary($internal, $pricing);
        [$monitoringSummary, $totalMonitoringCharges] = $this->computeMonitoringSummary($internal, $pricing);
        [$securitySummary, $totalSecurityCharges] = $this->computeSecuritySummary($internal, $pricing);
        [$backupSummary, $totalBackupCharges] = $this->computeBackupSummary($internal, $pricing);

        $licenseKL = collect($licenseRateCard)->sum('kl_price');
        $licenseCJ = collect($licenseRateCard)->sum('cj_price');
        $licenseTotal = $totalLicenseCharges ?? ($licenseKL + $licenseCJ);

        $monthlyTotal =
            ($totalManagedCharges ?? 0) +
            (($klTotal ?? 0) + ($cjTotal ?? 0)) +
            (($klEcsTotal ?? 0) + ($cjEcsTotal ?? 0)) +
            $licenseTotal +
            ($totalStorageCharges ?? 0) +
            ($totalBackupCharges ?? 0) +
            ($totalcloudSecurityCharges ?? 0) +
            ($totalMonitoringCharges ?? 0) +
            ($totalSecurityCharges ?? 0);

        $contractTotal = ($monthlyTotal * $contractDuration) + ($totalProfessionalCharges ?? 0);
        $serviceTax    = $contractTotal * 0.08;
        $finalTotal    = $contractTotal + $serviceTax;

        // Create CSV in memory
        $csv = Writer::createFromFileObject(new SplTempFileObject());

        // Header
        $csv->insertOne(['Quotation Report']);
        $csv->insertOne(['Generated Date', now()->format('d/m/Y')]);
        $csv->insertOne(['Logged Version', $catalogLabel]);
        if ($catalogFrom || $catalogTo) {
            $csv->insertOne(['Catalog Effective', ($catalogFrom ?: '-') . ' – ' . ($catalogTo ?: '—')]);
        }
        $csv->insertOne(['Quotation ID', $quotation->id ?? 'N/A']);
        $csv->insertOne(['Customer', $version->project->customer->name ?? 'N/A']);
        $csv->insertOne(['Project', $version->project->name ?? 'N/A']);
        $csv->insertOne(['Contract Duration', $contractDuration . ' Months']);
        $csv->insertOne([]);

        $csv->insertOne(['Summary of Quotation']);
        $csv->insertOne(['Category', 'One Time Charges', 'KL Monthly', 'CJ Monthly', 'Total Monthly']);

        // PS
        $csv->insertOne(['Professional Services', $totalProfessionalCharges, '', '', '']);

        // Managed
        $klManaged = collect($managedSummary)->sum('kl_price');
        $cjManaged = collect($managedSummary)->sum('cj_price');
        $csv->insertOne(['Managed Services', 0, $klManaged, $cjManaged, $totalManagedCharges]);

        // Network
        $csv->insertOne(['Network', 0, $klTotal, $cjTotal, ($klTotal + $cjTotal)]);

        // ECS
        $csv->insertOne(['Compute - ECS', 0, $klEcsTotal, $cjEcsTotal, ($klEcsTotal + $cjEcsTotal)]);

        // Licenses
        $csv->insertOne(['Licenses', 0, $licenseKL, $licenseCJ, $licenseTotal]);

        // Storage
        $klStorage = collect($storageSummary)->sum('kl_price');
        $cjStorage = collect($storageSummary)->sum('cj_price');
        $csv->insertOne(['Storage', 0, $klStorage, $cjStorage, $totalStorageCharges]);

        // Backup
        $klBackup = collect($backupSummary)->sum('kl_price');
        $cjBackup = collect($backupSummary)->sum('cj_price');
        $csv->insertOne(['Backup', 0, $klBackup, $cjBackup, $totalBackupCharges]);

        // Cloud Security
        $klCloud = collect($cloudSecuritySummary)->sum('kl_price');
        $cjCloud = collect($cloudSecuritySummary)->sum('cj_price');
        $csv->insertOne(['Cloud Security', 0, $klCloud, $cjCloud, $totalcloudSecurityCharges]);

        // Monitoring
        $klMonitor = collect($monitoringSummary)->sum('kl_price');
        $cjMonitor = collect($monitoringSummary)->sum('cj_price');
        $csv->insertOne(['Monitoring', 0, $klMonitor, $cjMonitor, $totalMonitoringCharges]);

        // Security Services
        $klSecurity = collect($securitySummary)->sum('kl_price');
        $cjSecurity = collect($securitySummary)->sum('cj_price');
        $csv->insertOne(['Security Services', 0, $klSecurity, $cjSecurity, $totalSecurityCharges]);

        // Totals
        $csv->insertOne([]);
        $csv->insertOne(['', 'ONE TIME CHARGES TOTAL', $totalProfessionalCharges]);
        $csv->insertOne(['', 'MONTHLY TOTAL', $monthlyTotal]);
        $csv->insertOne(['', 'CONTRACT TOTAL', $contractTotal]);
        $csv->insertOne(['', 'SERVICE TAX (8%)', $serviceTax]);
        $csv->insertOne(['', 'FINAL TOTAL (Include Tax)', $finalTotal]);

        // download
        $verSlug = Str::slug($catalogLabel ?: 'legacy', '_');
        $csv->output('quotation_' . $version->id . '_' . $verSlug . '_' . now()->format('Ymd_His') . '.csv');
        die;
    }

    // ===== Helper methods (kekal sama) =====
    private function computeManagedSummary(\App\Models\Version $version): array
    {
        $svc = $version->security_service;
        if (!$svc) { return [[], 0.0]; }

        $services = [
            'Managed Operating System',
            'Managed Backup and Restore',
            'Managed Patching',
            'Managed DR',
        ];

        $counts = [];
        foreach ($services as $s) { $counts[$s] = ['kl_qty' => 0, 'cj_qty' => 0]; }

        foreach (range(1, 4) as $i) {
            $v = $svc->{'kl_managed_services_' . $i} ?? null;
            if ($v && in_array($v, $services, true)) $counts[$v]['kl_qty']++;
        }
        foreach (range(1, 4) as $i) {
            $v = $svc->{'cyber_managed_services_' . $i} ?? null;
            if ($v && in_array($v, $services, true)) $counts[$v]['cj_qty']++;
        }

        [$priceByName, $unitByName] = $this->lookupManagedServicePrices();

        $summary = [];
        $grand = 0.0;

        foreach ($services as $name) {
            $unit  = $unitByName[strtolower($name)] ?? 'VM';
            $price = (float) ($priceByName[strtolower($name)] ?? 0);

            $klPrice = $counts[$name]['kl_qty'] * $price;
            $cjPrice = $counts[$name]['cj_qty'] * $price;

            if (($counts[$name]['kl_qty'] + $counts[$name]['cj_qty']) > 0) {
                $summary[] = [
                    'name'            => $name,
                    'unit'            => $unit,
                    'price_per_unit'  => $price,
                    'kl_qty'          => $counts[$name]['kl_qty'],
                    'cj_qty'          => $counts[$name]['cj_qty'],
                    'kl_price'        => $klPrice,
                    'cj_price'        => $cjPrice,
                ];
            }
            $grand += $klPrice + $cjPrice;
        }

        return [$summary, round($grand, 2)];
    }

    private function lookupManagedServicePrices(): array
    {
        $pricing = config('pricing');
        $priceByName = [];
        $unitByName  = [];

        foreach ($pricing as $key => $item) {
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
            foreach ($pricing as $key => $item) {
                if (!is_array($item)) continue;
                $name = strtolower(trim((string)($item['name'] ?? '')));
                if (in_array($name, [
                    'managed operating system',
                    'managed backup and restore',
                    'managed patching',
                    'managed dr',
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
        $rows = [];
        $grand = 0.0;

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
                    'name'           => $label,
                    'unit'           => 'Unit',
                    'price_per_unit' => $price,
                    'kl_qty'         => $klQty,
                    'cj_qty'         => $cjQty,
                    'kl_price'       => $klQty * $price,
                    'cj_price'       => $cjQty * $price,
                ];
                $rows[] = $row;
                $grand += $row['kl_price'] + $row['cj_price'];
            }
        }

        return [$rows, round($grand, 2)];
    }

    private function computeStorageSummary($s, array $pricing): array
    {
        if (!$s) return [[], 0.0];

        $rows = [];
        $grand = 0.0;

        $map = [
            'Elastic Volume Service (EVS)' => [
                'kl'=>'kl_evs', 'cj'=>'cyber_evs', 'key'=>'CSTG-EVS-SHR-STD', 'unit'=>'GB',
            ],
            'Scalable File Service (SFS)' => [
                'kl'=>'kl_scalable_file_service', 'cj'=>'cyber_scalable_file_service', 'key'=>'CSTG-SFS-SHR-STD', 'unit'=>'GB',
            ],
            'Object Storage Service (OBS)' => [
                'kl'=>'kl_object_storage_service', 'cj'=>'cyber_object_storage_service', 'key'=>'CSTG-OBS-SHR-STD', 'unit'=>'GB',
            ],
            'Snapshot Storage' => [
                'kl'=>'kl_snapshot_storage', 'cj'=>'cyber_snapshot_storage', 'key'=>'CSTG-BCK-SHR-STD', 'unit'=>'GB',
            ],
            'Image Storage' => [
                'kl'=>'kl_image_storage', 'cj'=>'cyber_image_storage', 'key'=>'CSTG-OBS-SHR-IMG', 'unit'=>'GB',
            ],
        ];

        foreach ($map as $label => $def) {
            $unit  = $def['unit'];
            $price = (float)($pricing[$def['key']]['price_per_unit'] ?? 0);

            $klQty = (float)($s->{$def['kl']} ?? 0);
            $cjQty = (float)($s->{$def['cj']} ?? 0);

            if ($klQty > 0 || $cjQty > 0) {
                $row = [
                    'name'           => $label,
                    'unit'           => $unit,
                    'price_per_unit' => $price,
                    'kl_qty'         => $klQty,
                    'cj_qty'         => $cjQty,
                    'kl_price'       => $klQty * $price,
                    'cj_price'       => $cjQty * $price,
                ];
                $rows[] = $row;
                $grand += $row['kl_price'] + $row['cj_price'];
            }
        }

        return [$rows, round($grand, 2)];
    }

    private function computeCloudSecuritySummary($s, array $pricing): array
    {
        if (!$s) return [[], 0.0];

        $rows = [];
        $grand = 0.0;

        $map = [
            'Cloud Firewall (Fortigate)' => [
                'kl' => 'kl_firewall_fortigate', 'cj' => 'cyber_firewall_fortigate',
                'key' => 'CSEC-VFW-DDT-FG', 'unit' => 'Unit',
            ],
            'Cloud Firewall (OPNSense)' => [
                'kl' => 'kl_firewall_opnsense', 'cj' => 'cyber_firewall_opnsense',
                'key' => 'CSEC-VFW-DDT-OS', 'unit' => 'Unit',
            ],
            'Cloud Shared WAF (Mbps)' => [
                'kl' => 'kl_shared_waf', 'cj' => 'cyber_shared_waf',
                'key' => 'CSEC-WAF-SHR-HA', 'unit' => 'Mbps',
            ],
            'Anti-Virus (Panda)' => [
                'kl' => 'kl_antivirus', 'cj' => 'cyber_antivirus',
                'key' => 'CSEC-EDR-NOD-STD', 'unit' => 'Unit',
            ],
        ];

        foreach ($map as $label => $def) {
            $unit  = $def['unit'];
            $price = (float)($pricing[$def['key']]['price_per_unit'] ?? 0);

            $klQty = (float)($s->{$def['kl']} ?? 0);
            $cjQty = (float)($s->{$def['cj']} ?? 0);

            if ($klQty > 0 || $cjQty > 0) {
                $row = [
                    'name'           => $label,
                    'unit'           => $unit,
                    'price_per_unit' => $price,
                    'kl_qty'         => $klQty,
                    'cj_qty'         => $cjQty,
                    'kl_price'       => $klQty * $price,
                    'cj_price'       => $cjQty * $price,
                ];
                $rows[] = $row;
                $grand += $row['kl_price'] + $row['cj_price'];
            }
        }

        return [$rows, round($grand, 2)];
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
                'name'           => 'TCS inSight vMonitoring',
                'unit'           => 'Unit',
                'price_per_unit' => $price,
                'kl_qty'         => $klQty,
                'cj_qty'         => $cjQty,
                'kl_price'       => $klQty * $price,
                'cj_price'       => $cjQty * $price,
            ];
        }

        $grand = collect($rows)->sum(fn($r) => $r['kl_price'] + $r['cj_price']);
        return [$rows, round($grand, 2)];
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
                'name'           => 'Cloud Vulnerability Assessment (Per IP)',
                'unit'           => 'Unit',
                'price_per_unit' => $price,
                'kl_qty'         => $klQty,
                'cj_qty'         => $cjQty,
                'kl_price'       => $klQty * $price,
                'cj_price'       => $cjQty * $price,
            ];
        }

        $grand = 0.0;
        foreach ($rows as $r) { $grand += ($r['kl_price'] + $r['cj_price']); }

        return [$rows, round($grand, 2)];
    }

    private function computeBackupSummary($s, array $pricing): array
    {
        if (!$s) return [[], 0.0];

        $rows = [];
        $grand = 0.0;

        $map = [
            'Cloud Server Backup Service - Full Backup Capacity' => [
                'kl' => 'kl_full_backup_capacity',
                'cj' => 'cyber_full_backup_capacity',
                'key' => 'CSBS-STRG-BCK-CSBSF',
                'unit' => 'GB',
            ],
            'Cloud Server Backup Service - Incremental Backup Capacity' => [
                'kl' => 'kl_incremental_backup_capacity',
                'cj' => 'cyber_incremental_backup_capacity',
                'key' => 'CSBS-STRG-BCK-CSBSI',
                'unit' => 'GB',
            ],
            'Cloud Server Replication Service - Retention Capacity' => [
                'kl' => 'kl_replication_retention_capacity',
                'cj' => 'cyber_replication_retention_capacity',
                'key' => 'CSBS-STRG-BCK-REPS',
                'unit' => 'GB',
            ],
        ];

        foreach ($map as $label => $def) {
            $price = (float)($pricing[$def['key']]['price_per_unit'] ?? 0);
            $unit  = $def['unit'];

            $klQty = (float)($s->{$def['kl']} ?? 0);
            $cjQty = (float)($s->{$def['cj']} ?? 0);

            if ($klQty > 0 || $cjQty > 0) {
                $row = [
                    'name'           => $label,
                    'unit'           => $unit,
                    'price_per_unit' => $price,
                    'kl_qty'         => $klQty,
                    'cj_qty'         => $cjQty,
                    'kl_price'       => round($klQty * $price, 2),
                    'cj_price'       => round($cjQty * $price, 2),
                ];
                $rows[] = $row;
                $grand += $row['kl_price'] + $row['cj_price'];
            }
        }

        return [$rows, round($grand, 2)];
    }

    private function computeEcsSummary(\App\Models\Version $version): array
    {
        $rows = $version->ecs_configuration ?? collect();
        if (!($rows instanceof \Illuminate\Support\Collection)) {
            $rows = collect($rows);
        }

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
                    'flavour'   => $flavour,
                    'unit'      => $unit,
                    'unit_price'=> $price,
                    'kl_qty'    => 0,
                    'cj_qty'    => 0,
                    'kl_price'  => 0.0,
                    'cj_price'  => 0.0,
                ];
            }

            if (strcasecmp($region, 'Kuala Lumpur') === 0) {
                $summary[$flavour]['kl_qty']   += 1;
                $summary[$flavour]['kl_price']  = $summary[$flavour]['kl_qty'] * $price;
            } elseif (strcasecmp($region, 'Cyberjaya') === 0) {
                $summary[$flavour]['cj_qty']   += 1;
                $summary[$flavour]['cj_price']  = $summary[$flavour]['cj_qty'] * $price;
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
        $nameToPrice = [];
        $nameToUnit  = [];

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

    private function computeNetworkTotals($s, array $pricing): array
    {
        $kl = 0.0; $cj = 0.0;

        $getPrice = function($key) use ($pricing) {
            return (float) data_get($pricing, "$key.price_per_unit", 0);
        };

        if (!empty($s->kl_bandwidth)) {
            $kl += $s->kl_bandwidth * $getPrice($this->getBandwidthPriceKey($s->kl_bandwidth));
        }
        if (!empty($s->cyber_bandwidth)) {
            $cj += $s->cyber_bandwidth * $getPrice($this->getBandwidthPriceKey($s->cyber_bandwidth));
        }
        if (!empty($s->kl_bandwidth_with_antiddos)) {
            $kl += $s->kl_bandwidth_with_antiddos * $getPrice($this->getBandwidthPriceKey($s->kl_bandwidth_with_antiddos));
        }
        if (!empty($s->cyber_bandwidth_with_antiddos)) {
            $cj += $s->cyber_bandwidth_with_antiddos * $getPrice($this->getBandwidthPriceKey($s->cyber_bandwidth_with_antiddos));
        }

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

        if (!empty($s->kl_virtual_private_leased_line)) {
            $kl += $s->kl_virtual_private_leased_line * $getPrice($this->getVPLLPriceKey($s->kl_virtual_private_leased_line));
        }
        if (!empty($s->cyber_virtual_private_leased_line)) {
            $cj += $s->cyber_virtual_private_leased_line * $getPrice($this->getVPLLPriceKey($s->cyber_virtual_private_leased_line));
        }

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

    private function computeProfessionalTotals($s)
    {
        $unit = (float) data_get(config('pricing'), 'CPFS-PFS-MDY-5OTC.price_per_unit', 1200);
        $days = (int) ($s->mandays ?? 0);
        $total = $days * $unit;
        return [$days, $unit, $total];
    }
}


























