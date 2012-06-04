<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 04.06.12
 * Time: 16:20
 */
class Order extends EMongoSoftDocument
{
    public $userId;

    public function getCollectionName()
    {
        return 'order';
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
