<?php

class HotelCache extends CommonHotelCache
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return FlightCache the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function beforeSave()
    {
        parent::beforeSave();
        $dumper = new HotelCacheDumper();
        $dumper->model = $this;
        $dumper->save();
        return false;
    }
}