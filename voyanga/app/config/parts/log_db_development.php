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

    // comment on production
    'enableParamLogging' => YII_DEBUG,
    'enableProfiling' => YII_DEBUG,
);