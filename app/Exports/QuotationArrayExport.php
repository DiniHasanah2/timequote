<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings; 
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

   
    public function drawings(): array
    {
        if (!$this->logoSource) return [];

        
        $path = $this->logoSource;
        if (!is_file($path)) {
            $maybe = public_path(ltrim($path, '/'));
            if (is_file($maybe)) $path = $maybe;
        }
        if (!is_file($path)) return [];

        $d = new Drawing();
        $d->setName('Company Logo');
        $d->setDescription('Company Logo');
        $d->setPath($path); 
        $d->setHeight(24);  
        $d->setCoordinates('A2'); 
        $d->setOffsetX(6);
        $d->setOffsetY(2);

        return [$d];
    }

   
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

               
                $ws->getColumnDimension('A')->setWidth(35);
                $ws->getColumnDimension('B')->setWidth(28);
                $ws->getColumnDimension('C')->setWidth(18);
                $ws->getColumnDimension('D')->setWidth(18);
                $ws->getColumnDimension('E')->setWidth(20);

               
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

               

        
$bannerRow = $this->findRowByFirstCell($ws, 'CLOUD SERVICES') ?? 0;
if ($bannerRow > 0) {
    $title = (string) $ws->getCell("A{$bannerRow}")->getValue();

   
    $ws->getColumnDimension('A')->setWidth(13); 

    
    $ws->getColumnDimension('F')->setWidth($ws->getColumnDimension('A')->getWidth());
    if (method_exists($ws->getColumnDimension('F'), 'setVisible')) {
        $ws->getColumnDimension('F')->setVisible(false);
    }

  
    $ws->setCellValue("B{$bannerRow}", $title);
    $ws->setCellValue("A{$bannerRow}", null);
    $ws->mergeCells("B{$bannerRow}:F{$bannerRow}");
    $ws->getStyle("B{$bannerRow}:F{$bannerRow}")
       ->getAlignment()
       ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
       ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

    
    $ws->getStyle("A{$bannerRow}:F{$bannerRow}")->applyFromArray([
        'font' => ['bold' => true, 'size' => 14],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FBC2E0']],
        'borders' => ['outline' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
    ]);
    $ws->getRowDimension($bannerRow)->setRowHeight(28);

   
}




             
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

                   
                    $ws->freezePane('A' . ($headerRow + 1));

                    $totalsHead = $this->findRowByCell($ws, 'B', 'ONE TIME CHARGES TOTAL') ?? ($maxRow + 1);
                    $dataStart  = $headerRow + 1;
                    //$dataEnd    = max($dataStart, $totalsHead - 2);
                    $dataEnd    = $totalsHead - 1;

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

               
                if ($summaryRow > 0) {
                    $metaEnd = $summaryRow - 2;
                    if ($metaEnd >= 2) {
                        $ws->getStyle("A2:A{$metaEnd}")->applyFromArray(['font' => ['bold' => true]]);
                        $ws->getStyle("B2:B{$metaEnd}")->getAlignment()
                           ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    }
                }

             
                $totalKeys = [
                    'ONE TIME CHARGES TOTAL',
                    'MONTHLY TOTAL',
                    'ANNUAL TOTAL', 
                    'CONTRACT TOTAL',
                    'SERVICE TAX (8%)',
                    'FINAL TOTAL (Include Tax)',
                ];

                foreach ($totalKeys as $k) {
                    $r = $this->findRowByCell($ws, 'B', $k);
                    if (!$r) continue;

                   
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
