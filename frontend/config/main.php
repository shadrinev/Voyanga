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
$packagesJs = require('frontend/assets/v2/coffee/app/packagesJs.php');
$packagesCss = require('frontend/assets/v2/css/packagesCss.php');

return CMap::mergeArray(
    require_once ('common/config/main.php'),
    array(
        'id' => 'frontend.voyanga.com',
        'name' => 'Voyanga',
        'basePath' => 'frontend',
        'params' => $params,
        'language' => 'ru',
        'theme' => $params['app.theme'],
        'defaultController' => $params['app.defaultController'],
        'preload' => array(
            'log',
            'RSentryException'
        ),
        'onBeginRequest' => function ($event)
        {
            Partner::setPartnerByKey();
        },

        // autoloading model and component classes
        'import' => array(
            'site.common.extensions.*',
            'site.common.components.*',
            'site.common.models.*',
            'application.components.*',
            'application.controllers.*',
            'application.models.*',
            'application.helpers.*',
            'site.common.components.shoppingCart.*',
            'site.common.extensions.order.*',
            'site.backend.extensions.bootstrap.widgets.*',
            'site.common.extensions.yiidebugtb.*', //our extension
            'site.frontend.extensions.EScriptBoost.*',
            'site.frontend.models.*',
            'site.frontend.components.*'
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
            ),
            'booking' => array(),
            'tour' => array(
                'controllerMap' => array(
                    'basket' => 'site.common.modules.tour.controllers.BasketController',
                    'constructor' => 'site.common.modules.tour.controllers.FrontendConstructorController',
                    'viewer' => 'site.common.modules.tour.controllers.FrontendViewerController',
                )
            ),
            'event' => array(
                'class' => 'site.frontend.modules.event.EventModule'
            ),
            'sandbox' => array(),
            'v2' => array(
                'class' => 'site.frontend.modules.v2.ProductionModule'
            )
        ),

        // application components
        'components' => array(
            'assetManager' => array(
                'class' => 'VAssetManager',
		'baseUrl' => '/assets/'
            ),
            /*            'clientScript' => array(
                            'class'=> YII_DEBUG ? 'EClientScriptBoost' : 'EClientScriptBoost',
                            'cacheDuration'=>30,
                        ),*/
            'bootstrap' => array(
                'class' => 'site.backend.extensions.bootstrap.components.Bootstrap', // assuming you extracted bootstrap under extensions
                'responsiveCss' => true,
                'debug' => true
            ),
            'user' => array(
                'allowAutoLogin' => true,
                'class' => 'frontend.components.WebUser',
                'loginUrl' => '/user/login',
                'returnUrl' => '/user/orders'
            ),
            'urlManager' => array(
                'urlFormat' => 'path',
                'showScriptName' => false,
                'rules' => $routes,
            ),
            'cache' => array(
                'class' => 'CMemCache',
                'useMemcached' => $params['enableMemcached'],
                'servers' => array(
                    array(
                        'host' => '127.0.0.1',
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
                'class' => 'site.frontend.components.SharedMemory',
                'maxSize' => 2 * 1024 * 1024,
            ),

            'log' => array(
                'class' => 'CLogRouter',
                'routes' => array(
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
                )
            ),
            'clientScript' => array(
                'packages' => CMap::mergeArray($packagesJs, $packagesCss)
            ),
        ),
        'controllerMap' => array(
            'payments' => array(
                'class' => 'common.extensions.payments.PaymentsController'
            )
        ),
    ),
    $frontendMainLocal
);
