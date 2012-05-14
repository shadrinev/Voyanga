<?php

$root=dirname(__FILE__).'/../..';
$params = require('console/config/params.php');

// We need to set this path alias to be able to define the migrations directory
Yii::setPathOfAlias('root', $root);
Yii::setPathOfAlias('common', $root.'/common');
Yii::setPathOfAlias('frontend', $root.'/frontend');
Yii::setPathOfAlias('uploads', $root.'/frontend/www/uploads');
require_once('common/packages/packages.php');

$consoleMainLocal = file_exists('console/config/main-local.php') ? require('console/config/main-local.php') : array ();

// please notice the order of the merged arrays. It is important, and reflectes an ineritance hirarchy in a sense
return CMap::mergeArray (
	require_once ('common/config/main.php'), //currently doesn't exist
	array(
	'id'=>'bootstrap.clevertech.com',
	'name'=>'bootstrap',
	'basePath'=>'console',
	'params'=>$params,
	'preload'=>array('log'),

	'import'=>array(
		'site.common.extensions.*',
		'site.common.components.*',
		'site.common.models.*',
		'application.components.*',
		'application.models.*',
		'site.frontend.models.*',
	),

	'commandMap'=>array(
		'migrate' => array (
			'class' => 'system.cli.commands.MigrateCommand',
			'migrationPath' => 'site.common.migrations',
		),
        'benchmark' => array(
            'class' => 'site.backend.modules.admin.modules.benchmark.commands.ABenchmarkCommand'
        )
     ),

	'components'=>array(


		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				'main' => array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
					'filter'=>'CLogFilter',
				),
			),
		),
		'db'=>array(
			'pdoClass' => 'NestedPDO',
			'connectionString' => $params['db.connectionString'],
			'username' => $params['db.username'],
			'password' => $params['db.password'],
			'charset' => 'utf8',
			'enableParamLogging' => YII_DEBUG,
			'emulatePrepare'=>true,
        ),
		'urlManager' => array(
			'urlFormat' => 'path',
			'showScriptName' => false,
            'rules' => $params['urlRules'],
            'baseUrl' => '',
		),

		'cache' => $params['cache.core'],
		'contentCache' => $params['cache.content'],
	),), CMap::mergeArray (	require_once (dirname(__FILE__).'/environments/main-'.$params['env.code'].'.php'), $consoleMainLocal));
