<?php
class Country extends CActiveRecord
{
	public $id;
	public $code;
	public $position;
	public $local_ru;
	public $local_en;
	
	
	public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
	
	public function tableName()
    {
        return 'country';
    }
}