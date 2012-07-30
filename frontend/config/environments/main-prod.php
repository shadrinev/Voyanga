<?php
/* main-prod*/ 

/**
 * This is the configuration used during development.
 * This file should only contain settings that are specific to your
 * development environment. Any settings that would be used for production
 * should be specified in config/main.php.
 */
return array(
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
				/*
				array(
					'class'=>'CEmailLogRoute',
					'levels'=>'error',
					'filter'=>'CLogFilter',
					'emails' => isset($params['email.route']) ? $params['email.route'] : $params['error.emails'], // emails need to be written here, // emails need to be written here, this is set in client params
				),*/
			),
		),
	),
);
