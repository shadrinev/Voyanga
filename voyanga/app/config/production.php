<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 04.05.12
 * Time: 11:43
 *
 * Specific production config goes here
 *
 */
return CMap::mergeArray(
    require(dirname(__FILE__).'/main.php'),

    array(
        'components' => array(
            'db' => require(dirname(__FILE__) . '/parts/db_production.php'),

            'logdb' => require(dirname(__FILE__).'/parts/log_db_production.php'),
        )
    )
);