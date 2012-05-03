<?php
/**
 * Hotel Class
 * 
 * @author oleg
 *
 */
/**
 * This is the model class for table "hotel".
 *
 * The followings are the available columns in table 'hotel':
 * @property integer $id
 * @property integer $position
 * @property string $name
 * @property string $description
 * @property integer $stars
 * @property integer $cityId
 * @property integer $countryId
 */

//todo: add address to db
//todo: add contacts to db
//todo: add photos to db
//todo: add dateFrom to db
//todo: add dateTo to db
//todo: add price to db
//todo: add adultCount to db
//todo: add childCount to db
class Hotel
{
    public $address;
    public $contacts;
    public $photos;
    public $dateFrom;
    public $dateTo;
    public $price;
    public $adultCount;
    public $childCount;
}