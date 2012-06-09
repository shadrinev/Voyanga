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

    //Time in secontds for searching results from cache
    'flight_search_cache_time' => 3600 * 3,
    //Price factor for flight optimal
    'flight_price_factor' => 100,
    //Time factor for flight optimal
    'flight_time_factor' => 70,
    'aPassegerTypes' => array(1 => 'ADT', 2 => 'CNN', 3 => 'INN'),
    'GDSNemo' => array(
        'wsdlUri' => 'http://109.120.157.20:10002/Flights.asmx?wsdl',
        'uri' => 'http://109.120.157.20:10002/Flights.asmx',
        'trace'   => (int)(defined(YII_DEBUG)),
        'login' => 'webdev012',
        'password' => 'HHFJGYU3*^H',
        'userId' => 15,
        'agencyWsdlUri' => 'http://sys.nemo-ibe.com/nemoflights/wsdl.php?for=SearchFlights',
        'agencyId' => '120',
        'agencyApiKey' => '85C46C441F08204652F2DFADC3DE05CD'
    ),
    'HotelBook' => array(
        'uri' => 'http://test.hotelbook.vsespo.ru/xml/',
        'login' => 'test',
        'password' => 'test',
    ),

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
