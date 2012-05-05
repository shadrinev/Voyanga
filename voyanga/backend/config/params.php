<?php

/**
 * This file contains backend specific application parameters.
 */
// please notice the order of the merged arrays. It is important, and reflectes an ineritance hirarchy in a sense

$frontendParamsLocal = file_exists('backend/config/params-local.php') ? require('backend/config/params-local.php') : array ();
$commonParams = require_once ('common/config/params.php');

return CMap::mergeArray (
    $commonParams,
    CMap::mergeArray (array(
        'GDSNemo' => array(
            'wsdlUri' => 'http://109.120.157.20:10002/Flights.asmx?wsdl',
            'trace'   => (int)(defined(YII_DEBUG)),
            'login' => 'webdev012',
            'password' => 'HHFJGYU3*^H',
            'userId' => 15
        ),
    ), CMap::mergeArray (require_once (dirname(__FILE__).'/environments/params-'.$commonParams['env.code'].'.php'), $frontendParamsLocal)));
