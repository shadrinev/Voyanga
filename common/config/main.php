<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 04.05.12
 * Time: 18:05
 *
 * For all applications around Voyanga
 */
Yii::setPathOfAlias('cacheStorage', $root . '/common/cache_storage');
Yii::setPathOfAlias('pdfOutDir', $root . '/common/pdf_compile');
Yii::setPathOfAlias('imageStorage', $root . '/frontend/www/image_storage');

return array(
    'preload' => array(
        'notification', 'RSentryException'
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
        'site.common.models.order.*',
    ),

    'behaviors' => array(
        'onBeginRequest' => array(
            'class' => 'common.extensions.yii-newrelic.behaviors.YiiNewRelicWebAppBehavior',
        ),
    ),

    'components'=>array(

        'assetManager' => array(
            'forceCopy' => YII_DEBUG,
            'baseUrl' => '/assets/',
        ),

        'cron'=>array(
            'class'=>'site.common.components.cron.CronComponent'
        ),

        'session' => array(
            'autoCreateSessionTable' => YII_DEBUG,
            'class'=>'CDbHttpSession',
            'connectionID'=>'db',
            'sessionTableName'=>'yii_session',
            'timeout' => 3600 //1hr to store items inside session
        ),

        'pCache' => array(
            'autoCreateCacheTable' => false,
            'class' => 'CDbCache',
            'keyPrefix' => 'voyanga-',
            'hashKey' => false,
            'serializer' => array('igbinary_serialize', 'igbinary_unserialize'),
            'cacheTableName' => 'tbl_cache',
            'connectionID' => 'db',
            'GCProbability' => 1000000
        ),

        //быстрый кэш доступный во всех приложениях по одному ключу
        'sharedCache' => array(
            'class' => 'CMemCache',
            'keyPrefix' => 'v',
            'hashKey' => false,
            'useMemcached' => $params['enableMemcached'],
            'servers' => array(
                array(
                    'host' => 'localhost',
                    'port' => 11211,
                    'weight' => 60
                )
            )
        ),

        'RSentryException'=> array(
            'dsn'=> $params['sentry.dsn'],
            'class' => 'common.extensions.yii-sentry-log.RSentryComponent',
        ),

        'flightBooker' => array(
            'class'=>'site.common.components.flightBooker.FlightBookerComponent',
        ),

        'newRelic' => array(
            'class' => 'common.extensions.yii-newrelic.YiiNewRelic',
        ),

        'hotelBooker' => array(
            'class'=>'site.common.components.hotelBooker.HotelBookerComponent',
        ),

        'workflow'=> array(
            'class'=>'site.common.extensions.simpleWorkflow.SWPhpWorkflowSource',
        ),

        'pdfGenerator' => array(
            'class'=>'site.common.components.PdfGenerator'
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
            'connectionString'  => $params['mongo.connectionString'],
            'dbName'            => $params['mongo.dbName'],
            'fsyncFlag'         => false,
            'safeFlag'          => false,
            'useCursor'         => false,
        ),

        'mail' => array(
            'class' => 'common.extensions.yii-mail.YiiMail',
            'transportType' => 'smtp', // change to 'php' when running in real domain.
            'viewPath' => 'frontend.www.themes.v2.views.mail',
            'logging' => true,
            'dryRun' => false,
            'transportOptions' => array(
                'host' => $params['smtp.host'],
                'username' => $params['smtp.username'],
                'password' => $params['smtp.password'],
                'port' => $params['smtp.port'],
                //'encryption' => 'tls',
            ),
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
            'schemaCachingDuration' => YII_DEBUG ? 0 : 864000,  // 1000 days
            'schemaCachingExclude' => array('order_booking', 'partner'),
            'enableParamLogging' => YII_DEBUG,
            'charset' => 'utf8',
        ),

        'logdb'=>array(
            'class' => 'CDbConnection',
            'pdoClass' => 'NestedPDO',
            'connectionString' => $params['log_db.connectionString'],
            'username' => $params['log_db.username'],
            'password' => $params['log_db.password'],
            'schemaCachingDuration' => YII_DEBUG ? 0 : 864000,  // 1000 days
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
                    'logFile' => 'cache',
                    'levels' => 'cache',
                    'categories' => 'application'
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'cron.at.log',
                    'levels' => 'at',
                    'categories' => 'cron'
                ),
                array(
                    'class' => 'CDbLogRoute',
                    'categories' => 'sharedMemory.*',
                    'connectionID' => 'logdb',
                    'autoCreateLogTable' => true,
                    'logTableName' => 'shared_memory'
                ),
                array(
                    'class' => 'common.extensions.yii-sentry-log.RSentryLog',
                    'filter' => 'VoyangaLogFilter',
                    'dsn'=> $params['sentry.dsn'],
                    'levels'=> 'error, warning',
                ),
            )
        ),

        'hotelsRating'=>array(
            'class'=>'common.components.HotelsRatingComponent'
        ),

        'httpClient'=>array(
            'class'=>'HttpClient'
        ),

        'payments' => array(
            'class' => 'common.extensions.payments.PaymentsComponent',
            'nemoCallbackSecret' => 'onetwotripnegovno',
            'credentials' => $params['payonline.credentials'],
            'testMode' => $params['payonline.testMode']
        ),

        'gdsAdapter' => array(
            'class' => 'GDSAdapter'
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
