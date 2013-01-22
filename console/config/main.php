<?php

$root=dirname(__FILE__).'/../..';
$params = require('console/config/params.php');

// We need to set this path alias to be able to define the migrations directory
Yii::setPathOfAlias('root', $root);
Yii::setPathOfAlias('common', $root.'/common');
Yii::setPathOfAlias('frontend', $root.'/frontend');
Yii::setPathOfAlias('console', $root.'/console');
Yii::setPathOfAlias('uploads', $root.'/frontend/www/uploads');
require_once('common/packages/packages.php');

$consoleMainLocal = file_exists('console/config/main-local.php') ? require('console/config/main-local.php') : array ();

// please notice the order of the merged arrays. It is important, and reflectes an ineritance hirarchy in a sense
return CMap::mergeArray (
    require_once ('common/config/main.php'), //currently doesn't exist
    array(
        'id'=>'bootstrap.clevertech.com',
        'name'=>'bootstrap',
        'basePath'=>'console',
        'params'=>$params,
        'preload'=>array('log'),
        'language' => 'ru_RU',

        'import'=>array(
                'site.common.extensions.*',
                'site.common.components.*',
                'site.common.models.*',
                'site.common.components.cron.*',
                'application.components.*',
                'application.models.*',
                'site.frontend.models.*',
        ),

        'commandMap'=>array(
            'migrate' => array (
                'class' => 'system.cli.commands.MigrateCommand',
                'migrationPath' => 'site.common.migrations',
            ),
            'benchmark' => array(
                'class' => 'site.backend.modules.admin.modules.benchmark.commands.ABenchmarkCommand'
            )
        ),

        'components'=>array(
            'morphy' => array(
                'class' => 'application.extensions.phpmorphy.PhpMorphyComponent'
            ),
            'syncCacheExecuter' => array(
                'class'=>'application.components.SyncCacheExecuter',
                'frontends' => array(
                    array(
                        'url' => 'http://test.voyanga.com/sync/get',
                        'key' => '12345'
                    )
                )
            ),
            'log'=>array(
                'class'=>'CLogRouter',
                'routes'=>array(
                    'main' => array(
                        'class'=>'CFileLogRoute',
                        'levels'=>'error, warning',
                        'filter'=>'CLogFilter',
                    ),
                    'console' => array(
                        'class'=>'StdErrRoute',
                        'levels'=>'error, warning',
                        'categories'=>'console.*'
                    ),
                    array(
                        'class' => 'CEmailLogRoute',
                        'levels' => 'error, warning',
                        'emails' => 'kuklin@voyanga.com'
                    ),
                ),
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
            'gdsAdapter' => array(
                'class' => 'GDSAdapter'
            ),

            'cache' => $params['cache.core'],
            'contentCache' => $params['cache.content'],
        ),), CMap::mergeArray (require_once (dirname(__FILE__).'/environments/main-'.$params['env.code'].'.php'), $consoleMainLocal));
