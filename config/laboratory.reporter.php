<?php

return [

    /*
    |--------------------------------------------------------------------------
    | JasperStarter Binary Path
    |--------------------------------------------------------------------------
    |
    | You must include the jasperstarter binary in-order for you to use this library
    | http://jasperstarter.cenote.de/
    |
    */
    'binary_path' => env('REPORTS_BINARY'),

    /*
    |--------------------------------------------------------------------------
    | Reporting Path
    |--------------------------------------------------------------------------
    |
    | Where is your *.jasper files storesd?
    |
    */
    'reports_path' => env('REPORTS_PATH', resource_path('reports')),

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your reporter. It's advisable
    | to use a readonly user for the reporter
    |
    | Supported: "postgres", "mysql"
    |
    */
    'connection' => [
        'driver' => 'postgres',
        'host' => env('REPORTER_HOST', '127.0.0.1'),
        'port' => env('REPORT_PORT', '5432'),
        'database' => env('REPORTER_DATABASE', 'forge'),
        'username' => env('REPORTER_USERNAME', 'forge'),
        'password' => env('REPORTER_PASSWORD', ''),
    ],
];
