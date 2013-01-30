<?php
/**
 * Params for application. Accesible via Yii::app->param['paramName'].
 *
 * This file contains frontend specific application parameters.
 */
// please notice the order of the merged arrays. It is important, and reflectes an ineritance hirarchy in a sense

$frontendParamsLocal = file_exists('frontend/config/params-local.php') ? require('frontend/config/params-local.php') : array();
$commonParams = require_once ('common/config/params.php');
$titleParams = require_once ('titles.php');
$environmentParams = require_once (dirname(__FILE__) . '/environments/params-' . $commonParams['env.code'] . '.php');
$templates = require_once('frontend/assets/v2/coffee/app/templates.php');

return CMap::mergeArray(
    $commonParams,
    $titleParams,
    CMap::mergeArray(array(
        // this is used in contact page
        'sharedMemory' => array(
            'flushDirectory' => 'site.api.runtime.cache',
            'flushExtension' => 'dump',
        ),
        'frontend.app.templates' => $templates
    ),
    CMap::mergeArray($environmentParams, $frontendParamsLocal
)));