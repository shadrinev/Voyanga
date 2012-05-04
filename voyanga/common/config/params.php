<?php

$root=realpath(dirname(__FILE__).'/../..');
Yii::setPathOfAlias('site',$root);

/**
 * Parameters shared by all applications.
 * Please put environment-sensitive parameters in params-env.php
 */
$commonParamsLocal = require('common/config/params-local.php');
$commonParamsEnv = require('common/config/environments/params-'.$commonParamsLocal['env.code'].'.php');

return CMap::mergeArray(array(
	// DB connection configurations
	'db.name' => '',
	'db.connectionString'=>'mysql:host=127.0.0.1;dbname=',
	'db.username'=>'',
    'db.password'=>'',

    'cache.core'=>extension_loaded('apc') ?
		array(
			'class' => 'CApcCache',
		) :
		array(
			'class' => 'CDbCache',
			'connectionID' => 'db',
			'autoCreateCacheTable' => true,
			'cacheTableName' => 'cache',
		),
	'cache.content' => array(
		'class' => 'CDbCache',
		'connectionID' => 'db',
		'autoCreateCacheTable' => true,
		'cacheTableName' => 'cache',
	),

	'urlRules' => array( //URL rules needed by CUrlManager
		/*
		"rss.xml" => 'site/feed',
		"<action:($rootPages)>" => 'site/<action>',


		'<controller:\w+>' => '<controller>/index',
		'<controller:\w+>/<id:\d+>/<name>' => '<controller>/view',
		'<controller:\w+>/<id:\d+>' => '<controller>/view',
		'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
		*/
		
		//	an example:
		//'organizations/default/*' => 'companies/default',
		//'schools/default/*' => 'companies/default',
	),
	
	'php.exePath' => '/usr/bin/php'
), CMap::mergeArray( $commonParamsEnv, $commonParamsLocal));
