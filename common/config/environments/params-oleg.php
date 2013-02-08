<?php
/* params-private */

/**
 * This file contains private application parameters that may vary in different environment.
 * You may modify this file to fit for your environment.
 */

return array(
    "env.code" => "oleg",

    'enableHotelLogging' => true,
    'enableFlightLogging' => true,

    "api.endPoint" => "http://api.oleg.voyanga/v1/",
    'app.api.flightSearchUrl' => 'http://api.oleg.voyanga/v1/flight/search/BE',
    'app.api.hotelSearchUrl' => 'http://api.oleg.voyanga/v1/hotel/search',

    "baseUrl" => 'http://frontend.oleg.voyanga',

    'db.name' => 'search',
    'db.connectionString'=>'mysql:host=109.236.87.227;dbname=test_search;port=3307',
    'db.username'=>'voyanga',
    'db.password'=>'srazunadogovoritblya',

    'backendDb.name' => 'search',
    'backendDb.connectionString'=>'mysql:host=109.236.87.227;dbname=backend;port=3307',
    'backendDb.username'=>'voyanga',
    'backendDb.password'=>'srazunadogovoritblya',

    'userDb.name' => 'search',
    'userDb.connectionString'=>'mysql:host=109.236.87.227;dbname=backend;port=3307',
    'userDb.username'=>'voyanga',
    'userDb.password'=>'srazunadogovoritblya',

    'log_db.name' => 'search',
    'log_db.connectionString'=>'mysql:host=109.236.87.227;dbname=logdb;port=3307',
    'log_db.username'=>'voyanga',
    'log_db.password'=>'srazunadogovoritblya',

    'mongo.connectionString' => 'mongodb://109.236.87.123',
    'mongo.dbName'=> 'voyanga',

    'email.sender' => 'robot@misha.voyanga (Voyanga dev-robot)',
    'enableMemcached' => false,
    'pdfConverterPath' => '/usr/bin/wkhtmltopdf',

    'sentry.dsn' => 'http://cc514c559d7c452cbcce18ea6fad927d:7901456e89b3417498605c034c81665a@109.236.87.123:9000/3'
);
