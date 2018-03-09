<?php

namespace Laboratory\Reporter;

use Exception;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;

class ReporterServiceProvider extends IlluminateServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/laboratory.reporter.php' => config_path('laboratory.reporter.php'),
        ], 'laboratory-reporter');
    }

    /**
     * Register the service provider.
     *
     * @throws \Exception
     * @return void
     */
    public function register()
    {
        $this->app->bind('reporter', function($app) {
            $binaryPath = $app['config']->get('laboratory.reporter.binary_path');
            $connection = $app['config']->get('laboratory.reporter.connection');
            $resourcePath = $app['config']->get('laboratory.reporter.reports_path');

            $jasperStarter = new JasperStarter($binaryPath, $resourcePath, [
                'driver' => $connection['driver'],
                'host' => $connection['host'],
                'port' => $connection['port'],
                'database' => $connection['database'],
                'username' => $connection['username'],
                'password' => $connection['password'],
            ]);

            return new Reporter($jasperStarter);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'reporter'
        ];
    }

}
