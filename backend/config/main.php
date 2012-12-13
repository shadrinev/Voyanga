<?php

$root = dirname(__FILE__) . '/../..';
$params = require('backend/config/params.php');
$routes = require('backend/config/routes.php');

// We need to set this path alias to be able to use the path of alias
// some of this may not be nescessary now, as now the directory is changed to projects root in the bootstrap script
Yii::setPathOfAlias('root', $root);
Yii::setPathOfAlias('common', $root . '/common');
Yii::setPathOfAlias('backend', $root . '/common');
Yii::setPathOfAlias('www', $root . '/backend/www');
Yii::setPathOfAlias('backend', $root . '/backend');
Yii::setPathOfAlias('uploads', $root . '/backend/www/uploads');

$backendMainLocal = file_exists('backend/config/main-local.php') ? require('backend/config/main-local.php') : array();

return CMap::mergeArray(
    require_once ('common/config/main.php'),
    array(
        'id' => 'backend.voyanga.com',
        'name' => 'Voyanga',
        'basePath' => 'backend',
        'params' => $params,
        'language' => 'ru',
        'theme' => 'classic',

        'preload' => array(
            'log',
            'bootstrap',
            'RSentryException'
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
            'application.lib.Dklab.*',
            'ext.bootstrap.widgets.*',
            'site.backend.models.*',
            'site.backend.components.*'
        ),

        'modules' => array(
            'gii' => array(
                'class' => 'system.gii.GiiModule',
                'password' => false,
                // If removed, Gii defaults to localhost only. Edit carefully to taste.
                'ipFilters' => array(
                    '192.168.0.74',
                    '192.168.0.8',
                    '::1',
                ),
                'generatorPaths' => array(
                    'bootstrap.gii', // since 0.9.1
                ),
            ),

            'admin' => array(
                'class' => 'application.modules.admin.AAdminModule',
                'modules' => array(
                    'users' => array(
                        'class' => 'application.modules.admin.modules.users.AUserAdminModule'
                    ),
                    'events' => array(
                        'class' => 'application.modules.admin.modules.events.EventAdminModule'
                    ),
                   'tour' => array(
                        'class' => 'application.modules.admin.modules.tour.TourAdminModule',
                        'controllerMap' => array(
                            'basket' => 'site.common.modules.tour.controllers.BasketController',
                            'constructor' => 'site.common.modules.tour.controllers.BackendConstructorController',
                            'viewer' => 'site.common.modules.tour.controllers.BackendViewerController',
                        )
                    ),
                    'statistic' => array(
                        'class' => 'application.modules.admin.modules.statistic.StatisticAdminModule'
                    ),
                    'benchmark' => array(
                        'class' => 'application.modules.admin.modules.benchmark.ABenchmarkModule',
                        'enabled' => false
                    ),
                    'rbac' => array(
                        'class' => 'packages.rbac.ARbacModule',
                        'enabled' => false
                    ),
                    'logging' => array(
                        'class' => 'application.modules.admin.modules.logging.LoggingAdminModule'
                    ),
                    'orders' => array(
                        'class' => 'application.modules.admin.modules.orders.OrdersAdminModule'
                    ),
                    'hotels' => array(
                        'class' => 'application.modules.admin.modules.hotels.HotelAdminModule'
                    ),
                    'partners' => array(
                        'class' => 'application.modules.admin.modules.partners.PartnersAdminModule'
                    ),
                )
            )
        ),

        // application components
        'components' => array(

            'bootstrap' => array(
                'class' => 'ext.bootstrap.components.Bootstrap', // assuming you extracted bootstrap under extensions
                'responsiveCss' => true,
                'debug' => true
            ),

            'cache' => array(
                'class' => 'CMemCache',
                'useMemcached' => $params['enableMemcached'],
                'servers' => array(
                    array(
                        'host' => 'localhost',
                        'port' => 11211,
                        'weight' => 60
                    )
                )
            ),

            'errorHandler' => array(
                // use 'site/error' action to display errors
                'errorAction' => 'site/error'
            ),
            'RSentryException'=> array(
                'dsn'=> 'http://0a8a5a8f752047b4817d033007109c46:dcc2ccf28f654f9da5f151178b6886b6@mihan007.ru/2',
                'class' => 'common.extensions.yii-sentry-log.RSentryComponent',
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
                        'filter' => 'CLogFilter',
                        'emails' => 'reports-backend@voyanga.com,shadrin@voyanga.com,maximov@voyanga.com'
                    ),
                )
            ),

            'sysinfo' => array(
                'class' => 'packages.sysinfo.ASystemInformation'
            ),

            'user'=>array(
                'class'=>'common.components.VUser',
                'behaviors'=>array(
                    'AUserBehavior' => array(
                        'class' => 'packages.users.behaviors.AUserBehavior'
                    )
                ),
                'loginUrl' => '/users/user/login',
                'allowAutoLogin'=>true
            ),

            'urlManager' => array(
                'urlFormat' => 'path',
                'showScriptName' => false,
                'rules' => $routes,
            ),
        ),

        'controllerMap' => array(
            'ajax' => 'site.frontend.controllers.AjaxController',
        )
    )
);