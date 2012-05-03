<?php

//todo: create db table
class HotelSearch extends CActiveRecord
{
    public $id;
    public $timestamp;
    public $request_id;
    public $status;
    public $adult_count;
    public $child_count;
    public $key;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'hotel_search';
    }

    public function sendRequest(FlightSearchParams $oFlightSearchParams)
    {

    }
}