<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');


// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
        'basePath' => dirname( __FILE__ ) . DIRECTORY_SEPARATOR . '..', 
        'name' => 'My Web Application', 
        
        // preloading 'log' component
        'preload' => array(
                'log' ), 
        
        // autoloading model and component classes
        'import' => array(
                'application.models.*', 
                'application.components.*' ), 
        
        'modules' => array()// uncomment the following to enable the Gii tool
        /*
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'Enter Your Password Here',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		*/
        , 
        
        // application components
        'components' => array(
                'user' => array(
                        // enable cookie-based authentication
                        'allowAutoLogin' => true ), 
                // uncomment the following to enable URLs in path-format
                /*
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		*/
                'db' => array(
                        'connectionString' => 'mysql:host=localhost;dbname=search', 
                        'username' => 'oleg', 
                        'password' => 'q1w2e3r4', 
                        'charset' => 'utf8', 
                        'emulatePrepare' => true )// необходимо для некоторых версий инсталляций MySQL
, 
                'logdb' => array(
                        'class' => 'CDbConnection', 
                        'connectionString' => 'mysql:host=localhost;dbname=logdb', 
                        'username' => 'oleg', 
                        'password' => 'q1w2e3r4', 
                        'charset' => 'utf8', 
                        'emulatePrepare' => true )// необходимо для некоторых версий инсталляций MySQL
, 
                'cache' => array(
                        'class' => 'CMemCache', 
                        'servers' => array(
                                array(
                                        'host' => 'localhost', 
                                        'port' => 11211, 
                                        'weight' => 60 ) ) ),
                'gdsAdapter' => array(
                        'class' => 'GDSAdapter', 
                         ),
                // uncomment the following to use a MySQL database
                /*
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=testdrive',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
		),
		*/
                'errorHandler' => array(
                        // use 'site/error' action to display errors
                        'errorAction' => 'site/error' ), 
                'log' => array(
                        'class' => 'CLogRouter', 
                        'routes' => array(
                                array(
                                        'class' => 'CWebLogRoute', 
                                        'levels' => 'error, warning', 
                                        'categories' => 'application', 
                                        'levels' => 'error, warning, trace, profile, info' ), 
                                array(
                                        'class' => 'CFileLogRoute', 
                                        'levels' => 'trace, info', 
                                        'categories' => 'application.*' ), 
                                array(
                                        'class' => 'CDbLogRoute', 
                                        'levels' => 'info', 
                                        'categories' => 'system.*', 
                                        'connectionID' => 'logdb', 
                                        'autoCreateLogTable' => ture, 
                                        'logTableName' => 'log_table' ), 
                                array(
                                        'class' => 'CEmailLogRoute', 
                                        'levels' => 'error, warning', 
                                        'emails' => 'maximov@danechka.com' ) )// uncomment the following to show log messages on web pages
                        /*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
                         ) ), 
        
        // application-level parameters that can be accessed
        // using Yii::app()->params['paramName']
        'params' => array(
                // this is used in contact page
                'adminEmail' => 'webmaster@example.com' ) );