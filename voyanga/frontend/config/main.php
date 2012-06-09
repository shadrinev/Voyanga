<?php

$root = dirname(__FILE__) . '/../..';
$params = require('frontend/config/params.php');
$routes = require('frontend/config/routes.php');

// We need to set this path alias to be able to use the path of alias
// some of this may not be nescessary now, as now the directory is changed to projects root in the bootstrap script
Yii::setPathOfAlias('root', $root);
Yii::setPathOfAlias('common', $root . '/common');
Yii::setPathOfAlias('backend', $root . '/common');
Yii::setPathOfAlias('www', $root . '/frontend/www');
Yii::setPathOfAlias('frontend', $root . '/frontend');
Yii::setPathOfAlias('uploads', $root . '/frontend/www/uploads');

$frontendMainLocal = file_exists('frontend/config/main-local.php') ? require('frontend/config/main-local.php') : array();

return CMap::mergeArray(
    require_once ('common/config/main.php'),
    array(
        'id' => 'frontend.voyanga.com',
        'name' => 'Voyanga',
        'basePath' => 'frontend',
        'params'  => $params,
        'language' => 'ru',
        'theme' => 'classic',
        'preload' => array(
            'log'
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
            'application.components.shoppingCart.*'
        ),

        'modules' => array(
            'gii' => array(
                'class' => 'system.gii.GiiModule',
                'password' => 'a1a2a3a4',
                // If removed, Gii defaults to localhost only. Edit carefully to taste.
                'ipFilters' => array(
                    '192.168.0.74',
                    '192.168.0.8',
                    '*',
                    '::1'
                )
            ),
            'gds' => array(
                'class' => 'site.common.modules.gds.GdsModule',
            )
        ),

        // application components
        'components' => array(
            'user' => array(
                // enable cookie-based authentication
                'allowAutoLogin' => true
            ),

            'urlManager' => array(
                'urlFormat' => 'path',
                'showScriptName' => false,
                'rules' => $routes,
            ),

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

            'sharedMemory' => array(
                'class' => 'application.components.SharedMemory',
                'maxSize' => 2*1024*1024,
            ),

            'log' => array(
                'class' => 'CLogRouter',
                'routes' => array(
                    array(
                        'class' => 'CWebLogRoute',
                        'categories' => 'application, nemo',
                        'levels' => 'error, warning, trace, profile, info'
                    ),

                    array(
                        'class' => 'CFileLogRoute',
                        'levels' => 'trace, info',
                        'categories' => 'application'
                    ),
                    array(
                        'class' => 'CFileLogRoute',
                        'levels' => 'trace, info',
                        'categories' => 'nemo',
                        'logFile' => 'nemo.log'
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
    )
);