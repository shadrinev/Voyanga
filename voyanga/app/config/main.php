<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');


// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Voyanga',

    'runtimePath' => Yii::getPathOfAlias('system') . '/../../runtime/',

    'preload' => array(
        'log'
    ),

    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.helpers.*'
    ),

    'modules' => array(
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => 'a1a2a3a4',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters' => array(
                '192.168.0.74',
                '192.168.0.8',
                '::1'
            )
        ),

        'gds'
    ),

    // application components
    'components' => array(
        'user' => array(
            // enable cookie-based authentication
            'allowAutoLogin' => true
        ),

        'urlManager'=>array(
            'urlFormat'=>'path',
            'showScriptName'=>false,
            'rules'=> require(dirname(__FILE__).'/parts/routes.php'),
        ),

        'db' => require(dirname(__FILE__).'/parts/db.php'),

        'logdb' => require(dirname(__FILE__).'/parts/log_db.php'),

        'cache' => array(
            'class' => 'CMemCache',
            'servers' => array(
                array(
                    'host' => 'localhost',
                    'port' => 11211,
                    'weight' => 60
                )
            )
        ),

        'gdsAdapter' => array(
            'class' => 'GDSAdapter'
        ),

        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => 'site/error'
        ),

        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CWebLogRoute',
                    'levels' => 'error, warning',
                    'categories' => 'application',
                    'levels' => 'error, warning, trace, profile, info'
                ),

                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'trace, info',
                    'categories' => 'application.*'
                ),

                array(
                    'class' => 'CDbLogRoute',
                    'levels' => 'info',
                    'categories' => 'system.*',
                    'connectionID' => 'logdb',
                    'autoCreateLogTable' => true,
                    'logTableName' => 'log_table'
                ),

                array(
                    'class' => 'CProfileLogRoute',
                    'levels' => 'profile',
                    'enabled' => true
                ),

                array(
                    'class' => 'CEmailLogRoute',
                    'levels' => 'error, warning',
                    'emails' => 'maximov@danechka.com'
                )
            )
        )
    ),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => require(dirname(__FILE__).'/parts/params.php')
);