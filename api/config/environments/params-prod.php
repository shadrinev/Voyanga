<?php
/* params-prod */

/**
 * This file contains private application parameters that may vary in different environment.
 * You may modify this file to fit for your environment.
 */
return array(
    "env.code" => "prod",
    'HotelBook' => array(
        'uri' => 'http://hotelbook.ru/xml/',
        'login' => 'voyangaXML',
        'password' => 'BZEFODZoA1!5',
        'room' => array(
            'DBL' => 10,
            'TWIN' => 20,
            'STD' => array(10, 12900),
        ),
        'distanceFromCityCenter' => 5000,
    ),
);