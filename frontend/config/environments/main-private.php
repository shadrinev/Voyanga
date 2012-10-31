<?php
/* main-private*/ 

/**
 * This is the configuration used during development.
 * This file should only contain settings that are specific to your
 * development environment. Any settings that would be used for production
 * should be specified in config/main.php.
 */
return array(
	'modules'=>array(
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>false,
	    ),
	),
	'components'=>array(
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
                    'class' => 'CWebLogRoute',
                ),
                array(
                    'class' => 'CWebLogRoute',
                    'categories' => 'application, nemo',
                    'levels' => 'error, warning, trace, profile, info'
                ),
                array(
                    'class' => 'CProfileLogRoute',
                    'levels' => 'profile',
                    'enabled' => true
                ),
                array( // configuration for the toolbar
                    'class'=>'XWebDebugRouter',
                    'config'=>'alignLeft, opaque, runInDebug, fixedPos, collapsed, yamlStyle',
                    'levels'=>'error, warning, trace, profile, info',
                    //'categories' => 'HotelBookerComponent.*, application.simpleWorkflow',
                    'allowedIPs'=>array('192.168.0.10','192.168.0.74'),
                ),
            ),
		),
	),

);
