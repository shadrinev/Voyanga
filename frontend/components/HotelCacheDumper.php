<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 22.05.12
 * Time: 15:01
 */
class HotelCacheDumper
{
    /**
     * @var FlightCache
     */
    public $model;

    public function save()
    {
        $dump = new HotelCacheDump;
        $dump->model = $this->model;
        Yii::app()->sharedMemory->write($dump);
    }

}
