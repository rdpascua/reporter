<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Reporter Connection
    |--------------------------------------------------------------------------
    |
    | This is the database connection that will be used when generating reports
    | from the database. You may use any of the connections defined in the
    | database configuration file.
    |
    */
    'connection' => env('REPORTER_CONNECTION'),

    /*
    |--------------------------------------------------------------------------
    | JasperStarter Binary Path
    |--------------------------------------------------------------------------
    |
    | You must include the jasperstarter binary in-order for you to use this library
    | http://jasperstarter.cenote.de/
    |
    */
    'binary_path' => env('REPORTER_BINARY'),

    /*
    |--------------------------------------------------------------------------
    | JasperStarter JDBC Path
    |--------------------------------------------------------------------------
    |
    | You can override your own jdbc path, this is useful if you're importing custom
    | fonts and libraries that your reports need
    |
    */
    'jdbc_path' => env('REPORTER_JDBC_PATH'),
];
