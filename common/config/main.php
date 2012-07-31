<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 04.05.12
 * Time: 18:05
 *
 * For all applications around Voyanga
 */
return array(
    'preload' => array(
        'notification'
    ),

    'import' => array(
        'site.common.extensions.YiiMongoDbSuite.*',
        'site.common.components.statistic.*',
        'site.common.components.shoppingCart.*',
        'site.common.components.hotelBooker.*',
        'site.common.components.flightBooker.*',
        'site.common.components.order.*',
        'site.common.components.flightBooker.*',
        'site.common.extensions.simpleWorkflow.*',
        'site.common.models.apiModels.*',
        'site.common.models.formModels.*',
    ),

    'components'=>array(

        'cron'=>array(
            'class'=>'site.common.components.cron.CronComponent'
        ),

        'session'=>array(
            'class'=>'site.common.extensions.EMongoDbHttpSession.EMongoDbHttpSession',
            'dbName' => 'voyanga',
            'collectionName' => 'session',
        ),

        'flightBooker' => array(
            'class'=>'site.common.components.flightBooker.FlightBookerComponent',
        ),

        'hotelBooker' => array(
            'class'=>'site.common.components.hotelBooker.HotelBookerComponent',
        ),

        'workflow'=> array(
                    'class'=>'site.common.extensions.simpleWorkflow.SWPhpWorkflowSource',
            ),

        'observer' => array(
            'class' => 'site.common.components.observer.ObserverComponent',
            'observers' => array(
                'onEnterCredentials' => array(
                ),
                'onBeforeFlightSearch'=>array(
                ),
                'onAfterFlightSearch'=>array(
                ),
                'onBeforeFlightBooking'=>array(
                ),
                'onAfterFlightBooking'=>array(
                ),
                'onBeforeBookingTimeLimitError'=>array(

                )
            )
        ),

        'shoppingCart' => array(
            'class' => 'site.common.components.shoppingCart.EShoppingCart',
            'orderComponent' => 'order'
        ),

        'order' => array(
            'class' => 'site.common.components.order.OrderComponent'
        ),

        'notification' => array(
            'class' => 'site.common.components.notification.Notification'
        ),

        'mongodb' => array(
            'class'             => 'EMongoDB',
            'connectionString'  => 'mongodb://192.168.0.55',
            'dbName'            => 'voyanga',
            'fsyncFlag'         => false,
            'safeFlag'          => false,
            'useCursor'         => false,
        ),

        'configManager' => array (
            'class' => 'ConfigurationManager',
        ),

        'format' => array(
            'numberFormat' => array('decimals'=>2, 'decimalSeparator'=>'.', 'thousandSeparator'=>' '),
            'datetimeFormat' => 'd.m.Y H:i'
        ),

        'db'=>array(
            'class' => 'CDbConnection',
            'pdoClass' => 'NestedPDO',
            'connectionString' => $params['db.connectionString'],
            'username' => $params['db.username'],
            'password' => $params['db.password'],
            'schemaCachingDuration' => YII_DEBUG ? 0 : 86400000,  // 1000 days
            'enableParamLogging' => YII_DEBUG,
            'charset' => 'utf8',
        ),

        'logdb'=>array(
            'class' => 'CDbConnection',
            'pdoClass' => 'NestedPDO',
            'connectionString' => $params['db.connectionString'],
            'username' => $params['db.username'],
            'password' => $params['db.password'],
            'schemaCachingDuration' => YII_DEBUG ? 0 : 86400000,  // 1000 days
            'enableParamLogging' => YII_DEBUG,
            'charset' => 'utf8',
        ),

        'backendDb'=>array(
            'class' => 'CDbConnection',
            'pdoClass' => 'NestedPDO',
            'connectionString' => $params['backendDb.connectionString'],
            'username' => $params['backendDb.username'],
            'password' => $params['backendDb.password'],
            'schemaCachingDuration' => YII_DEBUG ? 0 : 86400000,  // 1000 days
            'enableParamLogging' => YII_DEBUG,
            'charset' => 'utf8',
        ),

        'userDb'=>array(
            'class' => 'CDbConnection',
            'pdoClass' => 'NestedPDO',
            'connectionString' => $params['userDb.connectionString'],
            'username' => $params['userDb.username'],
            'password' => $params['userDb.password'],
            'schemaCachingDuration' => YII_DEBUG ? 0 : 86400000,  // 1000 days
            'enableParamLogging' => true,
            'charset' => 'utf8',
        ),

        'user'=>array(
            'class'=>'common.components.VUser',
            'behaviors'=>array(
                'AUserBehavior' => array(
                    'class' => 'packages.users.behaviors.AUserBehavior'
                )
            ),
            'allowAutoLogin'=>true
        ),

        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'notification',
                    'levels' => 'notification',
                    'categories' => 'application'
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'cron.at.log',
                    'levels' => 'at',
                    'categories' => 'cron'
                ),
            )
        ),

        'hotelsRating'=>array(
            'class'=>'HotelsRatingComponent'
        ),

        'httpClient'=>array(
            'class'=>'HttpClient'
        ),

        'payments' => array(
            'class' => 'common.extensions.payments.PaymentsComponent',
            'shopId' => '8234784606-1636',
            'testMode' => true,
            'login' => '821',
            'password' => '9UfgieYI6vuTit12NHp1w5Ld9MSIhIph7gf8pVqrmW9mXx24WLPnJAnW8FmS8YMq2bWaeMtTYvHdfsXM',
        ),

    ),

    'modules'=>array(
        'users' => array(
            'class' => 'packages.users.AUsersModule',
            'userModelClass' => 'User', // the name of your custom user class
        ),
        'email' => array(
            'class' => 'packages.email.AEmailModule',
        ),
        'resources' => array(
            'class' => 'packages.resources.AResourceModule',
            'resourceDir' => 'application.www.resources',
            'resourcePath' => 'resources'
        )
    )
);
