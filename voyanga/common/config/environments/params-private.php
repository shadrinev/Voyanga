<?php
/* params-private */

/**
 * This file contains private application parameters that may vary in different environment.
 * You may modify this file to fit for your environment.
 */
return array(
	"env.code" => "private",
    'db.name' => 'search',
    'db.connectionString'=>'mysql:host=localhost;dbname=search',
    'db.username'=>'oleg',
    'db.password'=>'q1w2e3r4',

    'log_db.name' => 'search',
    'log_db.connectionString'=>'mysql:host=localhost;dbname=logdb',
    'log_db.username'=>'oleg',
    'log_db.password'=>'q1w2e3r4',
);