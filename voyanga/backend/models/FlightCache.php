<?php
class FlightCache extends CommonFlightCache
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return FlightCache the static model class
     */
    public static function model($className = __CLASS__)
    {
        echo $className;
        return parent::model($className);
    }


    public function beforeSave()
    {
        return parent::beforeSave();
    }



}