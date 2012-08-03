<?php
/**
 * Params for application. Accesible via Yii::app->param['paramName'].
 *
 * This file contains frontend specific application parameters.
 */
// please notice the order of the merged arrays. It is important, and reflectes an ineritance hirarchy in a sense

$frontendParamsLocal = file_exists('frontend/config/params-local.php') ? require('frontend/config/params-local.php') : array();
$commonParams = require_once ('common/config/params.php');
$environmentParams = require_once (dirname(__FILE__) . '/environments/params-' . $commonParams['env.code'] . '.php');
return CMap::mergeArray(
    $commonParams,
    CMap::mergeArray(array(
        // this is used in contact page
        'adminEmail' => 'dev@voyanga.com',
        'sharedMemory' => array(
            'flushDirectory' => 'application.runtime.cache',
            'flushExtension' => 'dump',
        )
    ), CMap::mergeArray($environmentParams, $frontendParamsLocal)));