<?php
/* params-demo */


/**
 * This file contains private application parameters that may vary in different environment.
 * You may modify this file to fit for your environment.
 */
return array(
    "env.code" => "oleg",
    /*'HotelBook' => array(
        'uri' => 'http://hotelbook.ru/xml/',
        'login' => 'voyangaXML',
        'password' => 'BZEFODZoA1!5',
        'room' => array(
            'DBL' => 10,
            'TWIN' => 20,
            'STD' => array(10, 12900),
        ),
        'distanceFromCityCenter' => 5000,
    ),*/
    'HotelBook' => array(
        'uri' => 'http://test.hotelbook.vsespo.ru/xml/',
        'login' => 'voyanga',
        'password' => 'vLP1xe',
        'room' => array(
            'DBL' => 10,
            'TWIN' => 20,
            'STD' => array(10, 12900),
        ),
        'distanceFromCityCenter' => 5000,

    ),
);

