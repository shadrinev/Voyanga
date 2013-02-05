<?php
/* params-private */

/**
 * This file contains private application parameters that may vary in different environment.
 * You may modify this file to fit for your environment.
 */

return array(
    "env.code" => "home",

    'enableHotelLogging' => true,
    'enableFlightLogging' => true,

    "api.endPoint" => "http://api.test.voyanga.com/v1/",
    'app.api.flightSearchUrl' => 'http://api.test.voyanga.com/v1/flight/search/BE',
    'app.api.hotelSearchUrl' => 'http://api.test.voyanga.com/v1/hotel/search',

    "baseUrl" => 'http://frontend.voyanga.lcl',

    'db.name' => 'search',
    'db.connectionString'=>'mysql:host=109.236.87.227;dbname=test_search;port=3307',
    'db.username'=>'voyanga',
    'db.password'=>'srazunadogovoritblya',

    'backendDb.name' => 'search',
    'backendDb.connectionString'=>'mysql:host=109.236.87.227;dbname=test_backend;port=3307',
    'backendDb.username'=>'voyanga',
    'backendDb.password'=>'srazunadogovoritblya',

    'userDb.name' => 'search',
    'userDb.connectionString'=>'mysql:host=109.236.87.227;dbname=test_backend;port=3307',
    'userDb.username'=>'voyanga',
    'userDb.password'=>'srazunadogovoritblya',

    'log_db.name' => 'search',
    'log_db.connectionString'=>'mysql:host=109.236.87.227;dbname=test_logdb;port=3307',
    'log_db.username'=>'voyanga',
    'log_db.password'=>'srazunadogovoritblya',

    'mongo.connectionString' => 'mongodb://109.236.87.123',
    'mongo.dbName'=> 'test_voyanga',

    'email.sender' => 'robot@misha.voyanga (Voyanga dev-robot)',
    'enableMemcached' => true,

    'HotelBook' => array(
        'uri' => 'http://test.hotelbook.vsespo.ru/xml/',
        'login' => 'voyanga',
        'password' => 'vLP1xe',
        'room' => array(
            'DBL' => 10,
            'TWIN' => 20,
            'STD' => array(10, 12900),
        ),
        'distanceFromCityCenter' => 5000,
    ),
    'sentry.dsn' => 'http://cc514c559d7c452cbcce18ea6fad927d:7901456e89b3417498605c034c81665a@109.236.87.123:9000/3'
);
