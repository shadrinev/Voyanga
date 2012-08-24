<?php
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));
defined('YII_DEBUG') or define('YII_DEBUG', (@$argv == 'index')? false : true);
date_default_timezone_set('Europe/Moscow');

chdir(dirname(__FILE__).'/../');
$root=dirname(__FILE__);
require_once('common/components/Yii.php');
$config='console/config/main.php'; 
require_once('common/lib/global.php');

if(isset($config))
{
	$app=Yii::createConsoleApplication($config);
	$app->commandRunner->addCommands(YII_PATH.'/cli/commands');
	$env=@getenv('YII_CONSOLE_COMMANDS');
	if(!empty($env))
		$app->commandRunner->addCommands($env);
}
else
	$app=Yii::createConsoleApplication(array('basePath'=>dirname(__FILE__).'/cli'));

require_once('common/components/shortcuts.php');
$app->run();