<?php

namespace Nexterp\JasperReports;

use Illuminate\Support\ServiceProvider;

class JasperReportsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(JasperReportService::class, function ($app) {
            return new JasperReportService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish reports
        $this->publishes([
            __DIR__ . '/../resources/reports' => resource_path('reports/vendor/jasper-reports'),
        ], 'jasper-reports-templates');

        // Load Routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        // Load Views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'jasper-reports');

        // Publish Java 8 binaries if needed (optional, locally stored in .java8)
    }
}
