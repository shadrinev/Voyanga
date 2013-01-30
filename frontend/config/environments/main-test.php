<?php
/* main-private*/ 

/**
 * This is the configuration used during development.
 * This file should only contain settings that are specific to your
 * development environment. Any settings that would be used for production
 * should be specified in config/main.php.
 */
return array(
    'components'=>array(
        'RSentryException'=> array(
            'dsn'=> 'http://0a8a5a8f752047b4817d033007109c46:dcc2ccf28f654f9da5f151178b6886b6@mihan007.ru/2',
            'class' => 'common.extensions.yii-sentry-log.RSentryComponent',
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error',
                    'filter'=>'CLogFilter',
                    'logFile' => 'applicationError.log',
                ),
                array(
                    'class' => 'CEmailLogRoute',
                    'levels' => 'error, warning',
                    'filter' => 'CLogFilter',
                    'emails' => 'reports-frontend@voyanga.com,shadrin@voyanga.com,maximov@voyanga.com'
                ),
            ),
        ),
    ),
);
