<?php
/**
 * Database connection settings
 */
return array(
    'class' => 'CDbConnection',
    'connectionString' => 'mysql:host=localhost;dbname=search',
    'emulatePrepare' => true,
    'username' => 'oleg',
    'password' => 'q1w2e3r4',
    'charset' => 'utf8',
    'schemaCachingDuration'=> 3600 * 24, //1 day

    // comment on production
    'enableParamLogging' => YII_DEBUG,
    'enableProfiling' => YII_DEBUG,
);