<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kuklin
 * Date: 04.05.12
 * Time: 11:20
 * To change this template use File | Settings | File Templates.
 */
return array(
    // this is used in contact page
    'adminEmail' => 'dev@voyanga.com',
    //Time in secontds for searching results from cache
    'flight_search_cache_time' => 3600 * 3,
    //Price factor for flight optimal
    'flight_price_factor' => 100,
    //Time factor for flight optimal
    'flight_time_factor' => 70,
    'aPassegerTypes' => array(1 => 'ADT', 2 => 'CNN', 3 => 'INN')
);