<?php
/* main-int */

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
					'logFile' => 'commandsError.log',
				),
				/*array(
					'class'=>'CEmailLogRoute',
					'levels'=>'error',
					'filter'=>'CLogFilter',
					'emails' => isset($params['email.route']) ? $params['email.route'] : array('crm_dev@clevertech.biz'), // emails need to be written here
				),*/
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'warning, trace, info',
					'filter'=>'CLogFilter',
				),
			),
		),
	),

);
