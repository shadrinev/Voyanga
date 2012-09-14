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
        'theme' => $params['app.theme'],
        'defaultController' => $params['app.defaultController'],
        'preload' => array(
            'log',
            'bootstrap'
        ),
        'onBeginRequest' => function($event){
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
            'booking' => array(
            ),
            'tour' => array(
                'controllerMap' => array(
                    'basket' => 'site.common.modules.tour.controllers.BasketController',
                    'constructor' => 'site.common.modules.tour.controllers.FrontendConstructorController',
                    'viewer' => 'site.common.modules.tour.controllers.FrontendViewerController',
                )
            ),
            'event' => array(
                'class'=>'site.frontend.modules.event.EventModule'
            ),
            'sandbox' => array(

            ),
            'v2' => array(
                'class'=>'site.frontend.modules.v2.ProductionModule'
            )
        ),

        // application components
        'components' => array(
            'bootstrap' => array(
                'class' => 'site.backend.extensions.bootstrap.components.Bootstrap', // assuming you extracted bootstrap under extensions
                'responsiveCss' => true,
                'debug' => true
            ),

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
                'class' => 'site.frontend.components.SharedMemory',
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
                        'filter' => 'CLogFilter',
                        'emails' => 'reports-frontend@voyanga.com,kuklin@voyanga.com,shadrin@voyanga.com,maximov@voyanga.com,kudinov@voyanga.com'
                    ),

                   array( // configuration for the toolbar
                        'class'=>'XWebDebugRouter',
                        'config'=>'alignLeft, opaque, runInDebug, fixedPos, collapsed, yamlStyle',
                        'levels'=>'error, warning, trace, profile, info',
                        //'categories' => 'HotelBookerComponent.*, application.simpleWorkflow',
                        'allowedIPs'=>array('192.168.0.10','192.168.0.74'),
                    ),
                )
            ),
            'clientScript' => array(
                'packages' => array(
                    'everything' => array(
                        'basePath' => 'frontend.www.themes.v2.assets',
                        'js' => array(
                            //! App supporting vendor modules
                            'js/vendor/jquery.js', 'js/vendor/knockout-2.1.0.js',
                            'js/vendor/knockout-repeat.js', 'js/vendor/knockout-deferred-updates.js',
                            'js/vendor/underscore.js',
                            'js/vendor/backbone.js', 'js/jquery-ui-1.8.22.custom.min.js',
                            'js/jquery.easing.1.3.js',
                            //! Markup related scripts and modules
                            'js/jquery.dotdotdot-1.5.1.js', 'js/resize-new.js',
                            'js/jquery.color.js', 'js/popup.js',
                            'js/tickets.js','js/panel-new.js', 'js/script.js',
                            'js/voyanga-calendar.js', 'js/jquery.select.slider.js',
                            'js/jquery.slider.min.js','js/jquery.slider.js',
                            //! Our application logic
                            'js/avia.filters.js', 'js/app/common/module.slider.js',
                            'js/app/common/utils.js', 'js/app/common/ko.extenders.js',
                            'js/app/common/filters.js', 'js/app/avia/panel.js',
                            'js/app/hotels/panel.js', 'js/app/hotels/models.js',
                            'js/app/hotels/controllers.js', 'js/app/hotels/module.js',
                            'js/app/avia/models.js', 'js/app/avia/controllers.js',
                            'js/app/avia/module.js', 'js/app/app.js'),
                        'css' => array('css/reset.style.css', 'css/style.css', 'css/popup.css','css/jslider.css','css/jslider.round.voyanga.css','css/jsslidecheck.css','css/panel.css', 'css/voyanga-calendar.css'),
                    ),
                ),
            ),
        ),
        'controllerMap' => array(
            'payments'=>array(
                'class'=>'common.extensions.payments.PaymentsController'
            )
        ),
    ),
    $frontendMainLocal
);
