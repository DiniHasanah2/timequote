<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings; // <— ADD
use Maatwebsite\Excel\Events\AfterSheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class QuotationArrayExport implements FromArray, WithTitle, WithEvents, WithStyles, WithDrawings
{
    private const RM_FMT = '"RM" #,##0.00;[Red]-"RM" #,##0.00;"RM -"';

    protected array $rows;
    protected string $sheetTitle;
    protected ?string $logoSource;

    public function __construct(array $rows, string $sheetTitle = 'Quotation Summary', ?string $logoSource = null)
    {
        $this->rows       = $rows;
        $this->sheetTitle = $sheetTitle;
        $this->logoSource = $logoSource;
    }

    public function array(): array { return $this->rows; }
    public function title(): string { return $this->sheetTitle; }

    // ====== LETAK LOGO GUNA WithDrawings ======
    public function drawings(): array
    {
        if (!$this->logoSource) return [];

        // Pastikan absolute path
        $path = $this->logoSource;
        if (!is_file($path)) {
            $maybe = public_path(ltrim($path, '/'));
            if (is_file($maybe)) $path = $maybe;
        }
        if (!is_file($path)) return [];

        $d = new Drawing();
        $d->setName('Company Logo');
        $d->setDescription('Company Logo');
        $d->setPath($path); // absolute filesystem path
        $d->setHeight(24);  // adjust ikut banner
        $d->setCoordinates('A2'); // Banner row (A2 = "CLOUD SERVICES")
        $d->setOffsetX(6);
        $d->setOffsetY(2);

        return [$d];
    }

    // Font asas utk semua kolum
    public function styles(Worksheet $sheet)
    {
        return [
            'A:Z' => ['font' => ['name' => 'DejaVu Sans', 'size' => 10]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $ws     = $event->sheet->getDelegate();
                $maxRow = (int) $ws->getHighestRow();

                // Lebar kolum
                $ws->getColumnDimension('A')->setWidth(35);
                $ws->getColumnDimension('B')->setWidth(28);
                $ws->getColumnDimension('C')->setWidth(18);
                $ws->getColumnDimension('D')->setWidth(18);
                $ws->getColumnDimension('E')->setWidth(20);

                // ====== BARIS CONFIDENTIAL (A1:E1) ======
                $a1 = trim((string)$ws->getCell('A1')->getValue());
                if ($a1 !== '' && str_starts_with($a1, 'Confidential')) {
                    $ws->mergeCells('A1:E1');
                    $ws->getStyle('A1')->applyFromArray([
                        'font' => ['size' => 10],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical'   => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                    $ws->getRowDimension(1)->setRowHeight(16);
                }

                // ====== BANNER "CLOUD SERVICES" ======
                /*$bannerRow = $this->findRowByFirstCell($ws, 'CLOUD SERVICES') ?? 0;
                if ($bannerRow > 0) {
                    $ws->mergeCells("A{$bannerRow}:E{$bannerRow}");
                    $ws->getStyle("A{$bannerRow}:E{$bannerRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 14],
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FBC2E0'], // pink lembut
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $ws->getRowDimension($bannerRow)->setRowHeight(26);

                    // JANGAN panggil placeLogo lagi (logo dah melalui drawings())
                }*/

           // ====== BANNER "CLOUD SERVICES" + LOGO (center blok logo+teks) ======
$bannerRow = $this->findRowByFirstCell($ws, 'CLOUD SERVICES') ?? 0;
if ($bannerRow > 0) {
    $title = (string) $ws->getCell("A{$bannerRow}")->getValue();

    // 1) Kolum A utk logo
    $ws->getColumnDimension('A')->setWidth(13); // adjust ikut saiz logo

    // 2) Sediakan kolum F sebagai 'counterweight' (lebar F = lebar A) dan hide
    $ws->getColumnDimension('F')->setWidth($ws->getColumnDimension('A')->getWidth());
    if (method_exists($ws->getColumnDimension('F'), 'setVisible')) {
        $ws->getColumnDimension('F')->setVisible(false);
    }

    // 3) Pindahkan teks ke B, merge B:F dan center
    $ws->setCellValue("B{$bannerRow}", $title);
    $ws->setCellValue("A{$bannerRow}", null);
    $ws->mergeCells("B{$bannerRow}:F{$bannerRow}");
    $ws->getStyle("B{$bannerRow}:F{$bannerRow}")
       ->getAlignment()
       ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
       ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

    // 4) Gaya banner untuk A:F (bukan A:E lagi)
    $ws->getStyle("A{$bannerRow}:F{$bannerRow}")->applyFromArray([
        'font' => ['bold' => true, 'size' => 14],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FBC2E0']],
        'borders' => ['outline' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
    ]);
    $ws->getRowDimension($bannerRow)->setRowHeight(28);

    // 5) (optional) Kalau kau nak border keliling banner termasuk F, biar macam atas.

    // 6) Logo diletak via drawings() di A{$bannerRow}
}




                // ====== ATTENTION ======
                $attnRow = $this->findRowByFirstCell($ws, 'Attention:') ?? 0;
                if ($attnRow > 0) {
                    $ws->mergeCells("B{$attnRow}:E{$attnRow}");
                    $ws->getStyle("A{$attnRow}:E{$attnRow}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '939191']],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $ws->getRowDimension($attnRow)->setRowHeight(18);
                }

                // ====== CONTRACT DURATION + COMMITMENT BOX (2 ROWS) ======
                $cdRow  = $this->findRowByFirstCell($ws, 'Contract Duration:') ?? 0;
                $otcRow = $this->findRowByFirstCell($ws, 'One Time Charges (Exclude SST):') ?? 0;
                if ($cdRow > 0 && $otcRow > 0) {
                    $ws->mergeCells("C{$cdRow}:C{$otcRow}");
                    $ws->mergeCells("D{$cdRow}:D{$otcRow}");

                    $ws->getStyle("A{$cdRow}:D{$otcRow}")
                       ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                    $ws->getStyle("C{$cdRow}:C{$otcRow}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                    $ws->getStyle("D{$cdRow}:D{$otcRow}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

                    $ws->getStyle("D{$cdRow}:D{$otcRow}")
                       ->getNumberFormat()->setFormatCode(self::RM_FMT);
                    $ws->getStyle("B{$otcRow}:B{$otcRow}")
                       ->getNumberFormat()->setFormatCode(self::RM_FMT);

                    $ws->getRowDimension($cdRow)->setRowHeight(20);
                    $ws->getRowDimension($otcRow)->setRowHeight(20);
                }

                // ====== TOTAL CONTRACT VALUE BOX ======
                $boxTitleRow = $this->findRowByFirstCell($ws, 'TOTAL CONTRACT VALUE (WITH SST)') ?? 0;
                if ($boxTitleRow > 0) {
                    $boxValueRow = $boxTitleRow + 1;

                    $ws->mergeCells("A{$boxTitleRow}:E{$boxTitleRow}");
                    $ws->mergeCells("A{$boxValueRow}:E{$boxValueRow}");

                    $ws->getStyle("A{$boxTitleRow}:E{$boxTitleRow}")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F0F0']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);

                    $ws->getStyle("A{$boxValueRow}:E{$boxValueRow}")->applyFromArray([
                        'font' => ['size' => 12],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $ws->getStyle("A{$boxValueRow}")
                       ->getNumberFormat()->setFormatCode(self::RM_FMT);
                }

                // ====== "Summary of Quotation" (tajuk) ======
                $summaryRow = $this->findRowByFirstCell($ws, 'Summary of Quotation') ?? 0;
                if ($summaryRow > 0) {
                    $ws->mergeCells("A{$summaryRow}:E{$summaryRow}");
                    $ws->getStyle("A{$summaryRow}:E{$summaryRow}")->applyFromArray([
                        'font' => ['bold' => false, 'size' => 11],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $ws->getRowDimension($summaryRow)->setRowHeight(18);
                }

                // ====== Header jadual (Category..Total Monthly) ======
                $headerRow = $this->findRowByFirstCell($ws, 'Category') ?? 0;
                if ($headerRow > 0) {
                    $ws->getStyle("A{$headerRow}:E{$headerRow}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '939191']],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER,
                            'wrapText'   => true,
                        ],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $ws->getRowDimension($headerRow)->setRowHeight(20);

                    // Freeze header
                    $ws->freezePane('A' . ($headerRow + 1));

                    // Range isi data (hingga sebelum blok totals)
                    $totalsHead = $this->findRowByCell($ws, 'B', 'ONE TIME CHARGES TOTAL') ?? ($maxRow + 1);
                    $dataStart  = $headerRow + 1;
                    $dataEnd    = max($dataStart, $totalsHead - 2);

                    if ($dataStart <= $dataEnd) {
                        $ws->getStyle("A{$dataStart}:E{$dataEnd}")
                           ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                        $ws->getStyle("B{$dataStart}:E{$dataEnd}")
                           ->getNumberFormat()->setFormatCode(self::RM_FMT);

                        $ws->getStyle("B{$dataStart}:E{$dataEnd}")
                           ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $ws->getStyle("A{$dataStart}:A{$dataEnd}")
                           ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    }
                }

                // ====== Meta kiri atas ======
                if ($summaryRow > 0) {
                    $metaEnd = $summaryRow - 2;
                    if ($metaEnd >= 2) {
                        $ws->getStyle("A2:A{$metaEnd}")->applyFromArray(['font' => ['bold' => true]]);
                        $ws->getStyle("B2:B{$metaEnd}")->getAlignment()
                           ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    }
                }

                // ====== Blok TOTALS ======
                $totalKeys = [
                    'ONE TIME CHARGES TOTAL',
                    'MONTHLY TOTAL',
                    'CONTRACT TOTAL',
                    'SERVICE TAX (8%)',
                    'FINAL TOTAL (Include Tax)',
                ];

                foreach ($totalKeys as $k) {
                    $r = $this->findRowByCell($ws, 'B', $k);
                    if (!$r) continue;

                    // Pindah label ke kolum A (merge A:B), amount di C:E
                    $label = $ws->getCell("B{$r}")->getValue();
                    $ws->setCellValueExplicit("A{$r}", (string)$label, DataType::TYPE_STRING);
                    $ws->setCellValue("B{$r}", null);
                    $ws->mergeCells("A{$r}:B{$r}");
                    $ws->mergeCells("C{$r}:E{$r}");

                    $ws->getStyle("A{$r}:B{$r}")->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $ws->getStyle("C{$r}:E{$r}")->applyFromArray([
                        'font' => ['bold' => false],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $ws->getStyle("C{$r}")->getNumberFormat()->setFormatCode(self::RM_FMT);
                    $ws->getRowDimension($r)->setRowHeight(18);
                }

                // ====== TERMS & CONDITIONS ======
                $tncHeadRow = $this->findRowByFirstCell($ws, 'Terms and Conditions:') ?? 0;
                if ($tncHeadRow > 0) {
                    $tncBodyRow = $tncHeadRow + 1;

                    $ws->mergeCells("A{$tncHeadRow}:E{$tncHeadRow}");
                    $ws->getStyle("A{$tncHeadRow}:E{$tncHeadRow}")->applyFromArray([
                        'font' => ['bold' => true],
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                    ]);

                    $ws->mergeCells("A{$tncBodyRow}:E{$tncBodyRow}");
                    $ws->getStyle("A{$tncBodyRow}:E{$tncBodyRow}")->applyFromArray([
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
                    ]);
                    $ws->getRowDimension($tncBodyRow)->setRowHeight(220);
                }

                // Print layout
                $ws->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $ws->getPageSetup()->setFitToWidth(1);
                $ws->getPageMargins()->setTop(0.25)->setRight(0.25)->setLeft(0.25)->setBottom(0.25);
                $ws->getPageSetup()->setPrintArea("A1:E{$maxRow}");
            },
        ];
    }

    private function findRowByFirstCell(Worksheet $ws, string $needle): ?int
    {
        $max = $ws->getHighestRow();
        for ($r = 1; $r <= $max; $r++) {
            if (trim((string)$ws->getCell("A{$r}")->getValue()) === $needle) {
                return $r;
            }
        }
        return null;
    }

    private function findRowByCell(Worksheet $ws, string $col, string $needle): ?int
    {
        $max = $ws->getHighestRow();
        $col = strtoupper($col);
        for ($r = 1; $r <= $max; $r++) {
            if (trim((string)$ws->getCell("{$col}{$r}")->getValue()) === $needle) {
                return $r;
            }
        }
        return null;
    }
}


/*<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class QuotationArrayExport implements FromArray, WithTitle, WithEvents, WithStyles
{
    private const RM_FMT = '"RM" #,##0.00;[Red]-"RM" #,##0.00;"RM -"';

    protected array $rows;
    protected string $sheetTitle;
    protected ?string $logoSource;

    public function __construct(array $rows, string $sheetTitle = 'Quotation Summary', ?string $logoSource = null)
    {
        $this->rows       = $rows;
        $this->sheetTitle = $sheetTitle;
        $this->logoSource = $logoSource;
    }

    public function array(): array { return $this->rows; }
    public function title(): string { return $this->sheetTitle; }

    // Font asas utk semua kolum
    public function styles(Worksheet $sheet)
    {
        return [
            'A:Z' => ['font' => ['name' => 'DejaVu Sans', 'size' => 10]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $ws     = $event->sheet->getDelegate();
                $maxRow = (int) $ws->getHighestRow();

                // Lebar kolum
                $ws->getColumnDimension('A')->setWidth(35);
                $ws->getColumnDimension('B')->setWidth(28);
                $ws->getColumnDimension('C')->setWidth(18);
                $ws->getColumnDimension('D')->setWidth(18);
                $ws->getColumnDimension('E')->setWidth(20);

                // ====== BARIS CONFIDENTIAL (A1:E1) ======
                $a1 = trim((string)$ws->getCell('A1')->getValue());
                if ($a1 !== '' && str_starts_with($a1, 'Confidential')) {
                    $ws->mergeCells('A1:E1');
                    $ws->getStyle('A1')->applyFromArray([
                        'font' => ['size' => 10],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical'   => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                    $ws->getRowDimension(1)->setRowHeight(16);
                }

                // ====== BANNER "CLOUD SERVICES" + LOGO ======
                $bannerRow = $this->findRowByFirstCell($ws, 'CLOUD SERVICES') ?? 0;
                if ($bannerRow > 0) {
                    $ws->mergeCells("A{$bannerRow}:E{$bannerRow}");
                    $ws->getStyle("A{$bannerRow}:E{$bannerRow}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 14],
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FBC2E0'], // pink lembut
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $ws->getRowDimension($bannerRow)->setRowHeight(26);

                    // letak logo di kiri banner
                    $this->placeLogo($ws, "A{$bannerRow}");
                }

                // ====== ATTENTION ======
                $attnRow = $this->findRowByFirstCell($ws, 'Attention:') ?? 0;
                if ($attnRow > 0) {
                    $ws->mergeCells("B{$attnRow}:E{$attnRow}");
                    $ws->getStyle("A{$attnRow}:E{$attnRow}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '939191']],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $ws->getRowDimension($attnRow)->setRowHeight(18);
                }

                // ====== CONTRACT DURATION + COMMITMENT BOX (2 ROWS) ======
                $cdRow  = $this->findRowByFirstCell($ws, 'Contract Duration:') ?? 0;
                $otcRow = $this->findRowByFirstCell($ws, 'One Time Charges (Exclude SST):') ?? 0;
                if ($cdRow > 0 && $otcRow > 0) {
                    // merge Commitment label/value (col C & D) across 2 rows
                    $ws->mergeCells("C{$cdRow}:C{$otcRow}");
                    $ws->mergeCells("D{$cdRow}:D{$otcRow}");

                    // grid border
                    $ws->getStyle("A{$cdRow}:D{$otcRow}")
                       ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                    // alignment
                    $ws->getStyle("C{$cdRow}:C{$otcRow}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_CENTER);
                    $ws->getStyle("D{$cdRow}:D{$otcRow}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);

                    // number formats (money) untuk D (commitment) & B (OTC)
                    $ws->getStyle("D{$cdRow}:D{$otcRow}")
                       ->getNumberFormat()->setFormatCode(self::RM_FMT);
                    $ws->getStyle("B{$otcRow}:B{$otcRow}")
                       ->getNumberFormat()->setFormatCode(self::RM_FMT);

                    $ws->getRowDimension($cdRow)->setRowHeight(20);
                    $ws->getRowDimension($otcRow)->setRowHeight(20);
                }

                // ====== TOTAL CONTRACT VALUE BOX ======
                $boxTitleRow = $this->findRowByFirstCell($ws, 'TOTAL CONTRACT VALUE (WITH SST)') ?? 0;
                if ($boxTitleRow > 0) {
                    $boxValueRow = $boxTitleRow + 1;

                    $ws->mergeCells("A{$boxTitleRow}:E{$boxTitleRow}");
                    $ws->mergeCells("A{$boxValueRow}:E{$boxValueRow}");

                    $ws->getStyle("A{$boxTitleRow}:E{$boxTitleRow}")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F0F0']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);

                    $ws->getStyle("A{$boxValueRow}:E{$boxValueRow}")->applyFromArray([
                        'font' => ['size' => 12],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $ws->getStyle("A{$boxValueRow}")
                       ->getNumberFormat()->setFormatCode(self::RM_FMT);
                }

                // ====== "Summary of Quotation" (tajuk) ======
                $summaryRow = $this->findRowByFirstCell($ws, 'Summary of Quotation') ?? 0;
                if ($summaryRow > 0) {
                    $ws->mergeCells("A{$summaryRow}:E{$summaryRow}");
                    $ws->getStyle("A{$summaryRow}:E{$summaryRow}")->applyFromArray([
                        'font' => ['bold' => false, 'size' => 11],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER,
                        ],
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $ws->getRowDimension($summaryRow)->setRowHeight(18);
                }

                // ====== Header jadual (Category..Total Monthly) ======
                $headerRow = $this->findRowByFirstCell($ws, 'Category') ?? 0;
                if ($headerRow > 0) {
                    $ws->getStyle("A{$headerRow}:E{$headerRow}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '939191']],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical'   => Alignment::VERTICAL_CENTER,
                            'wrapText'   => true,
                        ],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $ws->getRowDimension($headerRow)->setRowHeight(20);

                    // Freeze header
                    $ws->freezePane('A' . ($headerRow + 1));

                    // Range isi data (hingga sebelum blok totals)
                    $totalsHead = $this->findRowByCell($ws, 'B', 'ONE TIME CHARGES TOTAL') ?? ($maxRow + 1);
                    $dataStart  = $headerRow + 1;
                    $dataEnd    = max($dataStart, $totalsHead - 2);

                    if ($dataStart <= $dataEnd) {
                        $ws->getStyle("A{$dataStart}:E{$dataEnd}")
                           ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                        $ws->getStyle("B{$dataStart}:E{$dataEnd}")
                           ->getNumberFormat()->setFormatCode(self::RM_FMT);

                        $ws->getStyle("B{$dataStart}:E{$dataEnd}")
                           ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $ws->getStyle("A{$dataStart}:A{$dataEnd}")
                           ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    }
                }

                // ====== Meta kiri atas (jika ada block ‘Generated Date’, dsb) ======
                if ($summaryRow > 0) {
                    $metaEnd = $summaryRow - 2;
                    if ($metaEnd >= 2) {
                        $ws->getStyle("A2:A{$metaEnd}")->applyFromArray(['font' => ['bold' => true]]);
                        $ws->getStyle("B2:B{$metaEnd}")->getAlignment()
                           ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    }
                }

                // ====== Blok TOTALS bawah jadual ======
                $totalKeys = [
                    'ONE TIME CHARGES TOTAL',
                    'MONTHLY TOTAL',
                    'CONTRACT TOTAL',
                    'SERVICE TAX (8%)',
                    'FINAL TOTAL (Include Tax)',
                ];

                foreach ($totalKeys as $k) {
                    $r = $this->findRowByCell($ws, 'B', $k);
                    if (!$r) continue;

                    // Pindah label ke kolum A (merge A:B), amount di C:E
                    $label = $ws->getCell("B{$r}")->getValue();
                    $ws->setCellValueExplicit("A{$r}", (string)$label, DataType::TYPE_STRING);
                    $ws->setCellValue("B{$r}", null);
                    $ws->mergeCells("A{$r}:B{$r}");
                    $ws->mergeCells("C{$r}:E{$r}");

                    $ws->getStyle("A{$r}:B{$r}")->applyFromArray([
                        'font' => ['bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $ws->getStyle("C{$r}:E{$r}")->applyFromArray([
                        'font' => ['bold' => false],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                    $ws->getStyle("C{$r}")->getNumberFormat()->setFormatCode(self::RM_FMT);
                    $ws->getRowDimension($r)->setRowHeight(18);
                }

                // ====== TERMS & CONDITIONS ======
                $tncHeadRow = $this->findRowByFirstCell($ws, 'Terms and Conditions:') ?? 0;
                if ($tncHeadRow > 0) {
                    $tncBodyRow = $tncHeadRow + 1;

                    // Heading
                    $ws->mergeCells("A{$tncHeadRow}:E{$tncHeadRow}");
                    $ws->getStyle("A{$tncHeadRow}:E{$tncHeadRow}")->applyFromArray([
                        'font' => ['bold' => true],
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                    ]);

                    // Body (wrap + border)
                    $ws->mergeCells("A{$tncBodyRow}:E{$tncBodyRow}");
                    $ws->getStyle("A{$tncBodyRow}:E{$tncBodyRow}")->applyFromArray([
                        'borders' => ['outline' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
                    ]);
                    // tinggi besar supaya muat teks
                    $ws->getRowDimension($tncBodyRow)->setRowHeight(220);
                }

                // Print layout
                $ws->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
                $ws->getPageSetup()->setFitToWidth(1);
                $ws->getPageMargins()->setTop(0.25)->setRight(0.25)->setLeft(0.25)->setBottom(0.25);
                $ws->getPageSetup()->setPrintArea("A1:E{$maxRow}");
            },
        ];
    }

    private function placeLogo(Worksheet $ws, string $cell): void
    {
        if (!$this->logoSource) return;

        try {
            if (str_starts_with($this->logoSource, 'data:image')) {
                // Base64 -> MemoryDrawing
                $chunks = explode(',', $this->logoSource, 2);
                $data   = $chunks[1] ?? '';
                $img    = imagecreatefromstring(base64_decode($data));
                if ($img !== false) {
                    $md = new MemoryDrawing();
                    $md->setImageResource($img);
                    $md->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
                    $md->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
                    $md->setHeight(18);
                    $md->setCoordinates($cell);
                    $md->setOffsetX(6);
                    $md->setWorksheet($ws);
                }
            } elseif (is_file($this->logoSource)) {
                $d = new Drawing();
                $d->setPath($this->logoSource);
                $d->setHeight(18);
                $d->setCoordinates($cell);
                $d->setOffsetX(6);
                $d->setWorksheet($ws);
            }
        } catch (\Throwable $e) {
            // biar senyap kalau gagal letak logo
        }
    }

    private function findRowByFirstCell(Worksheet $ws, string $needle): ?int
    {
        $max = $ws->getHighestRow();
        for ($r = 1; $r <= $max; $r++) {
            if (trim((string)$ws->getCell("A{$r}")->getValue()) === $needle) {
                return $r;
            }
        }
        return null;
    }

    private function findRowByCell(Worksheet $ws, string $col, string $needle): ?int
    {
        $max = $ws->getHighestRow();
        $col = strtoupper($col);
        for ($r = 1; $r <= $max; $r++) {
            if (trim((string)$ws->getCell("{$col}{$r}")->getValue()) === $needle) {
                return $r;
            }
        }
        return null;
    }
}*/


/*namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Cell\DataType; // <-- penting untuk setCellValueExplicit

class QuotationArrayExport implements FromArray, WithTitle, WithEvents, WithStyles
{
   
    private const RM_FMT = '"RM" #,##0.00;[Red]-"RM" #,##0.00;"RM -"';

    protected array $rows;
    protected string $sheetTitle;

    public function __construct(array $rows, string $sheetTitle = 'Quotation Summary')
    {
        $this->rows = $rows;
        $this->sheetTitle = $sheetTitle;
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }

    // Font asas utk semua kolum
    public function styles(Worksheet $sheet)
    {
        return [
            'A:Z' => ['font' => ['name' => 'DejaVu Sans', 'size' => 10]],
        ];
    }





    public function registerEvents(): array
{
    return [
        AfterSheet::class => function (AfterSheet $event) {
            $ws = $event->sheet->getDelegate();
            $maxRow = (int) $ws->getHighestRow();

            // Lebar kolum
            $ws->getColumnDimension('A')->setWidth(35);
            $ws->getColumnDimension('B')->setWidth(28);
            $ws->getColumnDimension('C')->setWidth(18);
            $ws->getColumnDimension('D')->setWidth(18);
            $ws->getColumnDimension('E')->setWidth(20);

            // Tajuk (A1:E1) — tiada warna, sederhana sahaja
            if (trim((string)$ws->getCell('A1')->getValue()) !== '') {
                $ws->mergeCells('A1:E1');
                $ws->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $ws->getRowDimension(1)->setRowHeight(22);
            }

            // "Summary of Quotation" — tiada beige, tak bold
            $summaryRow = $this->findRowByFirstCell($ws, 'Summary of Quotation') ?? 0;
            if ($summaryRow > 0) {
                $ws->mergeCells("A{$summaryRow}:E{$summaryRow}");
                $ws->getStyle("A{$summaryRow}:E{$summaryRow}")->applyFromArray([
                    'font' => ['bold' => false, 'size' => 11],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    // tiada fill, border nipis outline sahaja
                    'borders' => ['outline' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);
                $ws->getRowDimension($summaryRow)->setRowHeight(18);
            }

            // Header jadual (A..E) — boleh kekal grey gelap untuk kontras
            $headerRow = $this->findRowByFirstCell($ws, 'Category') ?? 0;
            if ($headerRow > 0) {
                $ws->getStyle("A{$headerRow}:E{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '939191'],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);
                $ws->getRowDimension($headerRow)->setRowHeight(20);

                // Freeze header
                $ws->freezePane('A'.($headerRow + 1));

                // Range isi data (hingga sebelum blok totals)
                $dataStart  = $headerRow + 1;
                $totalsHead = $this->findRowByCell($ws, 'B', 'ONE TIME CHARGES TOTAL') ?? ($maxRow + 1);
                $dataEnd    = max($dataStart, $totalsHead - 2);

                if ($dataStart <= $dataEnd) {
                    // Border nipis & konsisten
                    $ws->getStyle("A{$dataStart}:E{$dataEnd}")
                       ->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    // Format RM untuk nilai kewangan (B..E)
                    $ws->getStyle("B{$dataStart}:E{$dataEnd}")
                       ->getNumberFormat()->setFormatCode(self::RM_FMT);

                    // Perataan
                    $ws->getStyle("B{$dataStart}:E{$dataEnd}")
                       ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $ws->getStyle("A{$dataStart}:A{$dataEnd}")
                       ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                }
            }

            // Meta kiri (A2.. sebelum summary) — bold ringan pada label je
            if ($summaryRow > 0) {
                $metaEnd = $summaryRow - 2;
                if ($metaEnd >= 2) {
                    $ws->getStyle("A2:A{$metaEnd}")->applyFromArray(['font' => ['bold' => true]]);
                    $ws->getStyle("B2:B{$metaEnd}")->getAlignment()
                       ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                }
            }

            // ===== Blok TOTALS — tiada beige, amount tak bold, border nipis =====
            $totalKeys = [
                'ONE TIME CHARGES TOTAL',
                'MONTHLY TOTAL',
                'CONTRACT TOTAL',
                'SERVICE TAX (8%)',
                'FINAL TOTAL (Include Tax)',
            ];

            foreach ($totalKeys as $k) {
                $r = $this->findRowByCell($ws, 'B', $k);
                if (!$r) continue;

                // Pindah label ke kolum A (merge A:B)
                $label = $ws->getCell("B{$r}")->getValue();
                $ws->setCellValueExplicit("A{$r}", (string)$label, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $ws->setCellValue("B{$r}", null);
                $ws->mergeCells("A{$r}:B{$r}");

                // Amount di C, merge C:E
                $ws->mergeCells("C{$r}:E{$r}");

                // Label bold ringan, amount normal; tiada fill
                $ws->getStyle("A{$r}:B{$r}")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => ['outline' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);
                $ws->getStyle("C{$r}:E{$r}")->applyFromArray([
                    'font' => ['bold' => false],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => ['outline' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                ]);
                $ws->getStyle("C{$r}:C{$r}")
                   ->getNumberFormat()->setFormatCode(self::RM_FMT);

                $ws->getRowDimension($r)->setRowHeight(18);
            }

            // Print layout
            $ws->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $ws->getPageSetup()->setFitToWidth(1);
            $ws->getPageMargins()->setTop(0.25)->setRight(0.25)->setLeft(0.25)->setBottom(0.25);
            $ws->getPageSetup()->setPrintArea("A1:E{$maxRow}");
        },
    ];
}

    private function findRowByFirstCell(Worksheet $ws, string $needle): ?int
    {
        $max = $ws->getHighestRow();
        for ($r = 1; $r <= $max; $r++) {
            if (trim((string)$ws->getCell("A{$r}")->getValue()) === $needle) {
                return $r;
            }
        }
        return null;
    }

    private function findRowByCell(Worksheet $ws, string $col, string $needle): ?int
    {
        $max = $ws->getHighestRow();
        $col = strtoupper($col);
        for ($r = 1; $r <= $max; $r++) {
            if (trim((string)$ws->getCell("{$col}{$r}")->getValue()) === $needle) {
                return $r;
            }
        }
        return null;
    }
}*/


