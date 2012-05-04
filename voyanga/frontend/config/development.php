<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 04.05.12
 * Time: 11:41
 *
 * Specific development config goes here
 *
 */
return CMap::mergeArray(
    require(dirname(__FILE__).'/main.php'),

    array(
        'components' => array(
            'db' => require(dirname(__FILE__) . '/parts/db_development.php'),

            'logdb' => require(dirname(__FILE__).'/parts/log_db_development.php'),
        )
    )
);