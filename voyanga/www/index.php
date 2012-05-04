<?php

// change the following paths if necessary
$yii=dirname(__FILE__).'/../framework/1.1.10/yii.php';

$productionDomain = 'voyanga.com';

if ($_SERVER['HTTP_HOST']=='voyanga.com')
{
    $config=dirname(__FILE__).'/../app/config/production.php';
}
else
{
    // remove the following lines when in production mode
    defined('YII_DEBUG') or define('YII_DEBUG',true);
    // specify how many levels of call stack should be shown in each log message
    defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
    $config=dirname(__FILE__).'/../app/config/development.php';
}

require_once($yii);
Yii::createWebApplication($config)->run();