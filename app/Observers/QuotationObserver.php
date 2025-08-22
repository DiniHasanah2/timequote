<?php

namespace App\Observers;

use App\Models\Quotation;

class QuotationObserver
{
    public function saved(Quotation $quotation)
    {
        $this->recalculateProjectQuotationValue($quotation);
    }

    public function deleted(Quotation $quotation)
    {
        $this->recalculateProjectQuotationValue($quotation);
    }

    protected function recalculateProjectQuotationValue(Quotation $quotation)
    {
        $project = $quotation->version->project;

        $project->load('versions.quotations');

        $total = $project->versions->sum(fn($v) =>
            $v->quotations->sum('total_amount')
        );

        $project->quotation_value = $total;
        $project->saveQuietly(); // avoid observer loop
    }
}
