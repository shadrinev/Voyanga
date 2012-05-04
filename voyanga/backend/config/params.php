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

        // here comes the dynamic contents of this file - fronted specific parameters

    ), CMap::mergeArray (require_once (dirname(__FILE__).'/environments/params-'.$commonParams['env.code'].'.php'), $frontendParamsLocal)));
