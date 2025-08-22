<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use App\Models\User;
use App\Models\Customer;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        parent::boot();

        // Custom route‐model binding by user_code instead of id:
        Route::model('user', User::class, function ($value) {
            return User::where('user_code', $value)->firstOrFail();
        });

        Route::bind('customer', function ($value) {
            return Customer::where('id', $value)->firstOrFail();
        });
    }

    // … your map() or other methods …
}
