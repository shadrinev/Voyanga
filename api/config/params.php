<?php

/**
 * This file contains backend specific application parameters.
 */
// please notice the order of the merged arrays. It is important, and reflectes an ineritance hirarchy in a sense

$frontendParamsLocal = file_exists('api/config/params-local.php') ? require('api/config/params-local.php') : array();
$commonParams = require_once ('common/config/params.php');
print_r($commonParams);
$environmentParams = require_once (dirname(__FILE__) . '/environments/params-' . $commonParams['env.code'] . '.php');
return CMap::mergeArray(
    $commonParams,
    CMap::mergeArray(array(), CMap::mergeArray($environmentParams, $frontendParamsLocal)));
