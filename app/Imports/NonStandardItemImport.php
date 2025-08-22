<?php

namespace App\Imports;

use App\Models\NonStandardItem;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;



class NonStandardItemImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas

{
    protected $version;
    protected $project;

    public function __construct($version)
    {
        $this->version = $version;
        $this->project = $version->project;
    }

    public function collection(Collection $rows)
    {
        \Log::info('IMPORT CLASS TRIGGERED ✅');

        foreach ($rows as $row) {
            \Log::info('IMPORT ROW: ' . json_encode($row));

            // Fix formula string issue

            $selling_price = $row['selling_price'] ?? null;
            /*if (is_string($selling_price) && str_contains($selling_price, '=')) {
                \Log::info('❌ SKIP: selling_price formula not evaluated');
                continue;
            }*/

            // Skip if incomplete
            if (
                empty($row['item_name']) || empty($row['unit']) ||
                empty($row['quantity']) || empty($row['cost']) ||
                empty($row['mark_up']) || empty($selling_price)
            ) {
                \Log::info('❌ SKIP: row incomplete');
                continue;
            }

            NonStandardItem::create([
                'id' => (string) Str::uuid(),
                'project_id' => $this->project->id,
                'version_id' => $this->version->id,
                'customer_id' => $this->project->customer_id,
                'presale_id' => $this->project->presale_id,
                'item_name' => $row['item_name'],
                'unit' => $row['unit'],
                'quantity' => (int) $row['quantity'],
                'cost' => (float) $row['cost'],
                'mark_up' => (float) $row['mark_up'],
                'selling_price' => (float) $selling_price,
            ]);
        }
    }
}
