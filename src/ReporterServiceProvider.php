<?php

namespace Reporter;

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
            __DIR__ . '/../config/reporter.php' => config_path('laboratory.reporter.php'),
        ], 'laboratory-reporter');
    }

    /**
     * Register the service provider.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function register()
    {
        $this->app->bind('reporter', function ($app) {
            $binaryPath = $app['config']->get('laboratory.reporter.binary_path');
            $jdbcPath = $app['config']->get('laboratory.reporter.jdbc_path');
            $connections = $app['config']->get('laboratory.reporter.connections');
            $default = $app['config']->get('laboratory.reporter.default');
            $resourcePath = $app['config']->get('laboratory.reporter.reports_path');

            $jasperStarter = new JasperStarter(
                $binaryPath,
                $jdbcPath,
                $resourcePath,
                $connections,
                $default
            );

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
            'reporter',
        ];
    }
}
