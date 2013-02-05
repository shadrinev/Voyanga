<?php
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL', 3);
// On dev display all errors
if (YII_DEBUG)
{
    error_reporting(-1);
    ini_set('display_errors', true);
    header("Access-Control-Allow-Origin: *");
}

date_default_timezone_set('Europe/Moscow');

chdir(dirname(__FILE__) . '/../..');

$root = dirname(__FILE__) . '/..';
$common = $root . '/../common';

$config = 'api/config/main.php';
require_once('common/components/Yii.php');
require_once('common/components/WebApplication.php');
require_once('common/lib/global.php');
require_once('common/packages/packages.php');
require_once('common/components/shortcuts.php');

$app = Yii::createApplication('WebApplication', $config);
var_dump($app);
