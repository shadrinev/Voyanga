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
Yii::setPathOfAlias('imageStorage', $root . '/frontend/www/image_storage');

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
        'site.common.models.order.*',
    ),

    'components'=>array(

        'assetManager' => array(
            'forceCopy' => YII_DEBUG
        ),

        'cron'=>array(
            'class'=>'site.common.components.cron.CronComponent'
        ),

        'session' => array(
            'class'=>'site.common.extensions.EMongoDbHttpSession.EMongoDbHttpSession',
            'connectionString' => $params['mongo.connectionString'],
            'dbName'         => $params['mongo.dbName'],
            'collectionName' => 'session',
        ),

        'pCache' => array(
            'class' => 'CDbCache',
            'keyPrefix' => 'voyanga-',
            'hashKey' => false,
            'serializer' => array('igbinary_serialize', 'igbinary_unserialize'),
            'cacheTableName' => 'tbl_cache',
            'connectionID' => 'db'
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
            'connectionString'  => $params['mongo.connectionString'],
            'dbName'            => $params['mongo.dbName'],
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
            'connectionString' => $params['log_db.connectionString'],
            'username' => $params['log_db.username'],
            'password' => $params['log_db.password'],
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
            'credentials' => array(
                'gds_galileo'=> array(
                    'id' => 9377,
                    'key' => 'a51dfb8d-6c57-4ad4-a018-27593cfabddb'
                ),
                'gds_sabre' => array(
                    'id' => 9378,
                    'key' => '117869db-6a67-4f89-9753-a2fb7ee9bfc3'
                ),
                'lts' => array(
                    'id' => 9739,
                    'key' => 'dbaf1d76-3540-40aa-af13-4e05c4a776d2'
                ),
                'ecommerce' => array(
                    'id' => 9387,
                    'key' => '71eedb90-01d6-4ba9-b058-d965d98ecc64'
                )
/*
                'ltr'=> array(
                    'id' => 9377,
                    'key' => 'a51dfb8d-6c57-4ad4-a018-27593cfabddb'
                ),
                'gds_sabre' => array(
                    'id' => 9378,
                    'key' => '117869db-6a67-4f89-9753-a2fb7ee9bfc3'
                ),
                'gds_galileo' => array(
                    'id' => 9739,
                    'key' => 'dbaf1d76-3540-40aa-af13-4e05c4a776d2'
                ),
                'ecommerce' => array(
                    'id' => 9387,
                    'key' => '71eedb90-01d6-4ba9-b058-d965d98ecc64'
                )
*/
            )
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
