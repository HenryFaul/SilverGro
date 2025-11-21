<?php

namespace App\Providers;

use App\Models\TransportInvoice;
use App\Observers\TransportInvoiceObserver;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observer to ensure invoice customer_id always matches transaction
        TransportInvoice::observe(TransportInvoiceObserver::class);
    }
}
