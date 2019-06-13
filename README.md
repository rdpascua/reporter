# Reporter

Tired of writing html reports? Worry not **Reporter** is here to solve your problems. Reporter uses
**jasperstarter** library to generate reports.

By default `rdpascua/jasperstarter` is baked already in the package.

## Installation

Add this to your `composer.json` as dependency

    "repositories": [{
        "url": "git@gitlab.revlv.net:laboratory/reporter.git",
        "type": "git"
    }],
    "require": {
        "laboratory/reporter": "~0.3",
    }

Add this to your service provider

    Laboratory\Reporter\ReporterServiceProvider::class,

You can optionally use the facade

    'Reporter' => Laboratory\Reporter\Facades\Reporter::class,

## Configuration

    php artisan vendor:publish --tag=laboratory-reporter

## Usage

    $reporter = Reporter::load('Basic', [
        'start_date' => '2017-01-01',
        'end_date' => '2018-01-01'
    ]);

    // You can download the pdf
    $reporter->download('sample-report.pdf');

    // Or stream the pdf directly to the browser
    $reporter->inline('sample-report.pdf');

    // Or save the pdf
    $reporter->save(storage_path() . '/app/files/sample-report.pdf');

## Testing

    $ composer test



