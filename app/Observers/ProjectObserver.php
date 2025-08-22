<?php

namespace App\Observers;

use App\Models\Project;

class ProjectObserver
{
    /**
     * Handle the Project "created" event.
     */
    public function created(Project $project): void
    {
        //
    }

    /**
     * Handle the Project "updated" event.
     */
    public function updated(Project $project): void
    {
        //
    }

    /**
     * Handle the Project "deleted" event.
     */
    public function deleted(Project $project): void
    {
        //
    }

    /**
     * Handle the Project "restored" event.
     */
    public function restored(Project $project): void
    {
        //
    }

    /**
     * Handle the Project "force deleted" event.
     */
    public function forceDeleted(Project $project): void
    {
        //
    }
    
    //{]public function saving(Project $project)
        //$project->quotation_value = $project->total_quotation_value;\
        
        // Update quotation_value whenever project is saved
        /*$project->quotation_value = $project->versions->sum(function($version) {
            return $version->quotations->sum('total_amount');
        });
}*/



        public function saved(Project $project)
{
    $project->load('versions.quotations'); // reload untuk pastikan ada data

    $totalQuotation = $project->versions->sum(function($version) {
        return $version->quotations->sum('total_amount');
    });

    // Elak infinite loop
    if ($project->quotation_value != $totalQuotation) {
        $project->quotation_value = $totalQuotation;
        $project->saveQuietly(); // guna quietly untuk elak trigger observer lagi
    }
}

    }
