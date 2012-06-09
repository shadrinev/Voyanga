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
    ),
    'components'=>array(

        'observer' => array(
            'class' => 'site.common.extensions.observer.ObserverComponent',
            'observers' => array(
                'onBeforeFlightSearch'=>array(
                    'Notification',
                    array('Notification', 'handleBest')
                ),
                'onAfterFlightSearch'=>array(
                    'Notification'
                ),
                'onBeforeFlightBooking'=>array(
                    'Notification'
                ),
                'onAfterFlightBooking'=>array(
                    'Notification'
                )
            )
        ),

        'shoppingCart' => array(
            'class' => 'site.common.components.shoppingCart.EShoppingCart',
            'orderComponent' => 'order'
        ),

        'order' => array(
            'class' => 'site.common.extensions.order.OrderComponent'
        ),

        'notification' => array(
            'class' => 'site.common.extensions.notification.Notification'
        ),

        'mongodb' => array(
            'class'             => 'EMongoDB',
            'connectionString'  => 'mongodb://localhost',
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
            )
        )
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
