<?php
/* params-private */

/**
 * This file contains private application parameters that may vary in different environment.
 * You may modify this file to fit for your environment.
 */
return array(
	"env.code" => "prod",

    'enableHotelLogging' => true,
    'enableFlightLogging' => true,

    "api.endPoint" => "//api.voyanga.com/v1/",
    'app.api.flightSearchUrl' => 'https://api.voyanga.com/v1/flight/search/BE',
    'app.api.hotelSearchUrl' => 'https://api.voyanga.com/v1/hotel/search',

    "baseUrl" => 'https://voyanga.com',

    'db.name' => 'search',
    'db.connectionString'=>'mysql:host=109.236.87.227;dbname=search;port=3307',
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

    'HotelBook' => array(
        'uri' => 'http://hotelbook.ru/xml/',
        'login' => 'voyangaXML',
        'password' => 'BZEFODZoA1!5',
        'room' => array(
            'DBL' => 10,
            'TWIN' => 20,
            'STD' => array(10, 12900),
        ),
        'distanceFromCityCenter' => 5000,
    ),

    'sentry.dsn' => 'http://76578c6199c441a79e5eb1c7b09e8e6c:0560a80489a346b492a8b3a8cca0dfd1@109.236.87.123:9000/2'
);
