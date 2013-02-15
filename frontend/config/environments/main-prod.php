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
					'filter'=>'VoyangaLogFilter',
					'logFile' => 'applicationError.log',
				),
                array(
                    'class' => 'CEmailLogRoute',
                    'levels' => 'error, warning',
                    'filter' => 'VoyangaLogFilter',
                    'emails' => 'reports-frontend@voyanga.com,shadrin@voyanga.com,maximov@voyanga.com'
                ),
			),
		),
	),
);
