<?php
/* params-private */

/**
 * This file contains private application parameters that may vary in different environment.
 * You may modify this file to fit for your environment.
 */
return array(
	"env.code" => "private",

    'db.name' => 'search',
    'db.connectionString'=>'mysql:host=192.168.0.55;dbname=search',
    'db.username'=>'oleg',
    'db.password'=>'q1w2e3r4',

    'backendDb.name' => 'search',
    'backendDb.connectionString'=>'mysql:host=192.168.0.55;dbname=backend',
    'backendDb.username'=>'oleg',
    'backendDb.password'=>'q1w2e3r4',

    'userDb.name' => 'search',
    'userDb.connectionString'=>'mysql:host=192.168.0.55;dbname=backend',
    'userDb.username'=>'oleg',
    'userDb.password'=>'q1w2e3r4',

    'log_db.name' => 'search',
    'log_db.connectionString'=>'mysql:host=192.168.0.55;dbname=logdb',
    'log_db.username'=>'oleg',
    'log_db.password'=>'q1w2e3r4',

    'mongo.connectionString' => 'mongodb://192.168.0.55',
    'mongo.dbName'=> 'voyanga',

    'email.sender' => 'robot@misha.voyanga (Voyanga dev-robot)'
);
