<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Reporter Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */
    'default' => env('REPORTER_CONNECTION', 'pgsql'),

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
    'connections' => [
        'pgsql' => [
            'driver' => 'postgres',
            'host' => env('REPORTER_HOST', '127.0.0.1'),
            'port' => env('REPORT_PORT', '5432'),
            'database' => env('REPORTER_DATABASE', 'forge'),
            'username' => env('REPORTER_USERNAME', 'forge'),
            'password' => env('REPORTER_PASSWORD', ''),
        ],
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('REPORTER_HOST', '127.0.0.1'),
            'port' => env('REPORT_PORT', '3306'),
            'database' => env('REPORTER_DATABASE', 'forge'),
            'username' => env('REPORTER_USERNAME', 'forge'),
            'password' => env('REPORTER_PASSWORD', ''),
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | JasperStarter Binary Path
    |--------------------------------------------------------------------------
    |
    | You must include the jasperstarter binary in-order for you to use this library
    | http://jasperstarter.cenote.de/
    |
    */
    'binary_path' => env('REPORTS_BINARY', base_path('vendor/bin/jasperstarter')),

    /*
    |--------------------------------------------------------------------------
    | JasperStarter JDBC Path
    |--------------------------------------------------------------------------
    |
    | You can override your own jdbc path, this is usefull if you're importing custom
    | fonts and libraries that your reports need
    |
    */
    'jdbc_path' => env('REPORTS_JDBC_DIR', base_path('vendor/rdpascua/jasperstarter/jdbc')),

    /*
    |--------------------------------------------------------------------------
    | Reporting Path
    |--------------------------------------------------------------------------
    |
    | Where is your *.jasper files storesd?
    |
    */
    'reports_path' => env('REPORTS_PATH', storage_path('app/reports'))
];
