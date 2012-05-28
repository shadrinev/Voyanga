<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 28.05.12
 * Time: 16:23
 */
class Statistic extends EMongoSoftDocument
{
    public $modelName;
    public $modelId;
    public $dateCreate;

    // As always define the getCollectionName() and model() methods !
    public function getCollectionName()
    {
        return 'statistic_collection';
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
