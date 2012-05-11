<?php
/**
 * Params for application. Accesible via Yii::app->param['paramName'].
 *
 * This file contains frontend specific application parameters.
 */
// please notice the order of the merged arrays. It is important, and reflectes an ineritance hirarchy in a sense

$frontendParamsLocal = file_exists('frontend/config/params-local.php') ? require('frontend/config/params-local.php') : array();
$commonParams = require_once ('common/config/params.php');
return CMap::mergeArray(
    $commonParams,
    CMap::mergeArray(array(
        // this is used in contact page
        'adminEmail' => 'dev@voyanga.com',
        //Time in secontds for searching results from cache
        'flight_search_cache_time' => 3600 * 3,
        //Price factor for flight optimal
        'flight_price_factor' => 100,
        //Time factor for flight optimal
        'flight_time_factor' => 70,
        'aPassegerTypes' => array(1 => 'ADT', 2 => 'CNN', 3 => 'INN'),
        'GDSNemo' => array(
            'wsdlUri' => 'http://109.120.157.20:10002/Flights.asmx?wsdl',
            'uri' => 'http://109.120.157.20:10002/Flights.asmx',
            'trace'   => (int)(defined(YII_DEBUG)),
            'login' => 'webdev012',
            'password' => 'HHFJGYU3*^H',
            'userId' => 15
        )
    ), CMap::mergeArray(require_once (dirname(__FILE__) . '/environments/params-' . $commonParams['env.code'] . '.php'), $frontendParamsLocal)));