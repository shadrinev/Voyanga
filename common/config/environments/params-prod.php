<?php
/* params-private */

/**
 * This file contains private application parameters that may vary in different environment.
 * You may modify this file to fit for your environment.
 */
return array(
	"env.code" => "private",

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
);
