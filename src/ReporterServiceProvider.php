<?php

namespace Rdpascua\Reporter;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ReporterServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('reporter')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Reporter::class);
        $this->app->alias(Reporter::class, 'reporter');
    }

    public function bootingPackage()
    {
        $this->app->bind(Reporter::class, function () {
            $binaryPath = config('reporter.binary_path');
            $jdbcPath = config('reporter.jdbc_path');
            $connections = config('database.connections');

            $jasperStarter = new JasperStarter(
                $binaryPath,
                $jdbcPath,
                $connections
            );

            return new Reporter($jasperStarter);
        });
    }
}
