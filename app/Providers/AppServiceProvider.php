<?php

namespace App\Providers;


use App\Models\Project;
use App\Observers\ProjectObserver;
use Illuminate\Support\ServiceProvider;
use App\Models\Quotation;
use App\Observers\QuotationObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Project::observe(ProjectObserver::class);
        Quotation::observe(QuotationObserver::class);
    }

    
}
