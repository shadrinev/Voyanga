<?php
class Airport extends CActiveRecord
{
	public $id;
	public $code;
	public $position;
	public $city;
	public $city_id;
	public $local_ru;
	public $local_en;
	
	public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
	
	public function tableName()
    {
        return 'airport';
    }
}