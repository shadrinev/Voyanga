<?php
$debug = true;
$prodServers = array('test.voyanga.com', 'voyanga.com');
if ((isset($_SERVER['HTTP_HOST'])) && (in_array($_SERVER['HTTP_HOST'], $prodServers)))
  $debug = true;
defined('YII_DEBUG') or define('YII_DEBUG', $debug);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
defined('BUILDING') or define('BUILDING', false);
header("Content-type:text/html;charset=utf-8");
// On dev display all errors
if(YII_DEBUG) {
    error_reporting(-1);
    ini_set('display_errors', true);
}

date_default_timezone_set('Europe/Moscow');

chdir(dirname(__FILE__).'/../../');

$root=dirname(__FILE__).'/..';
$common=$root.'/../common';

$config='frontend/config/main.php';
require_once('common/components/Yii.php');
require_once('common/components/WebApplication.php');
require_once('common/lib/global.php');
require_once('common/packages/packages.php');
require_once('common/components/shortcuts.php');

$app = Yii::createApplication('WebApplication',$config);
$app->run();
