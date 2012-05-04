<?php
/**
 * Log database connection settings
 */
return array(
    'class' => 'CDbConnection',
    'connectionString' => 'mysql:host=localhost;dbname=logdb',
    'emulatePrepare' => true,
    'username' => 'oleg',
    'password' => 'q1w2e3r4',
    'charset' => 'utf8',
    'schemaCachingDuration' => 3600 * 24, //1 day

    // comment on production
    'enableParamLogging' => false,
    'enableProfiling' => false,
);