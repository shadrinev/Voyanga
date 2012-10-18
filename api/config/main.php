<?php

$root = dirname(__FILE__) . '/../..';
$params = require('api/config/params.php');
$routes = require('api/config/routes.php');

// We need to set this path alias to be able to use the path of alias
// some of this may not be nescessary now, as now the directory is changed to projects root in the bootstrap script
Yii::setPathOfAlias('root', $root);
Yii::setPathOfAlias('common', $root . '/common');
Yii::setPathOfAlias('www', $root . '/api/www');
Yii::setPathOfAlias('backend', $root . '/backend');
Yii::setPathOfAlias('frontend', $root . '/frontend');
Yii::setPathOfAlias('api', $root . '/api');


$backendMainLocal = file_exists('api/config/main-local.php') ? require('api/config/main-local.php') : array();

return CMap::mergeArray(
    require_once ('common/config/main.php'),
    array(
        'id' => 'api.voyanga.com',
        'name' => 'Voyanga',
        'basePath' => 'api',
        'params' => $params,
        'language' => 'en',

        'preload' => array(
            'log',
            'bootstrap'
        ),

        // autoloading model and component classes
        'import' => array(
            'site.common.extensions.*',
            'site.common.components.*',
            'site.common.models.*',
            'application.components.*',
            'application.controllers.*',
            'application.models.*',
            'application.helpers.*',
            'site.common.modules.hotel.models.*'
        ),

        'modules' => array(
            'v1' => array(
                'class' => 'application.modules.v1.VersionOneModule',
                'modules' => array(
                    'flight',
                    'hotel',
                    'tour',
                    'helper'
                )
            )
        ),

        // application components
        'components' => array(
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

            'sharedMemory' => array(
                'class' => 'site.frontend.components.SharedMemory',
                'maxSize' => 1 * 1024 * 1024, //2*1024*1024
            ),

            'errorHandler' => array(
                // use 'site/error' action to display errors
                'errorAction' => 'error/default'
            ),

            'log' => array(
                'class' => 'CLogRouter',
                'routes' => array(
                    array(
                        'class' => 'CFileLogRoute',
                        'levels' => 'trace, info',
                        'categories' => 'application.*'
                    ),

                    array(
                        'class' => 'CDbLogRoute',
                        'levels' => 'info',
                        'categories' => 'system.*, application.db.*',
                        'connectionID' => 'logdb',
                        'autoCreateLogTable' => true,
                        'logTableName' => 'log_table'
                    ),
                    array(
                        'class' => 'CProfileLogRoute',
                        'levels' => 'profile',
                        'enabled' => false
                    ),
                    array(
                        'class' => 'CEmailLogRoute',
                        'levels' => 'error, warning',
                        'filter' => 'CLogFilter',
                        'emails' => 'reports-backend@voyanga.com,kuklin@voyanga.com,shadrin@voyanga.com,maximov@voyanga.com,kudinov@voyanga.com'
                    ),
                )
            ),

            'urlManager' => array(
                'urlFormat' => 'path',
                'showScriptName' => false,
                'rules' => $routes,
            ),
        ),
    )
);