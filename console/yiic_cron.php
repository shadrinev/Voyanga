<?php
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));

defined('YII_DEBUG') or define('YII_DEBUG', (@$argv == 'index')? false : true);

date_default_timezone_set('Europe/Moscow');
chdir(dirname(__FILE__).'/..');

$root=dirname(__FILE__).'/..';
$common=$root.'/common';
require_once('common/components/Yii.php');
$config='console/config/main.php'; 
require_once('common/lib/global.php');
require_once('console/components/ConsoleApplication.php');

if($argc > 2){
    $uniqKey = $argv[1];
    $executeTimestamp = time();
    unset($argv[1]);
    unset($_SERVER['argv'][1]);
    $argc--;
    $_SERVER['argc']--;
    $argv = array_merge($argv);
    $_SERVER['argv'] = array_merge($_SERVER['argv']);
}
//print_r($_SERVER);
//print_r($argv);
ob_start();
function __shutdown(){
    $outStr = ob_get_contents();
    ob_end_clean();
    if(CronTask::$uniqKey)
    {
        $cronTask = CronTask::model()->findByAttributes(array('uniqKey'=>CronTask::$uniqKey));
        if($cronTask)
        {
            $cronTask->executeTimestamp = date('Y-m-d H:i:s', CronTask::$executeTimestamp);
            $cronTask->executeOut = $outStr;
            $cronTask->save();
        }
    }

    echo 'all out string:'.$outStr;
}


register_shutdown_function('__shutdown');

if(isset($config))
{
	$app=Yii::createApplication('ConsoleApplication', $config);
	$app->commandRunner->addCommands(YII_PATH.'/cli/commands');
	$env=@getenv('YII_CONSOLE_COMMANDS');
	if(!empty($env))
		$app->commandRunner->addCommands($env);
}
else
	$app=Yii::createConsoleApplication(array('basePath'=>dirname(__FILE__).'/cli'));

require_once('common/components/shortcuts.php');
CronTask::$uniqKey = $uniqKey;
CronTask::$executeTimestamp = $executeTimestamp;
//die();
$app->run();
/* Below - the old version of this file*/
/*
defined('YII_DEBUG') or define('YII_DEBUG',true);

$root=dirname(__FILE__);
$config=$root.'/config/main.php';

require_once($root.'/../common/lib/global.php');
require_once($root.'/../common/lib/yii-1.1.8/yiic.php');
*/