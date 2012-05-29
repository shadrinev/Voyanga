<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 28.05.12
 * Time: 18:25
 * To change this template use File | Settings | File Templates.
 */
class MongoKeyValue extends EMongoDocument // Notice: We extend EMongoDocument class instead of CActiveRecord
{
    public $value;
    public $key;

    public function getCollectionName()
    {
        return 'KeyValue';
    }


    // the same with attribute names
    public function attributeNames()
    {
        return array(
            'value' => 'Value',
            'key'=>'Key'
        );
    }

    /**
     * This method have to be defined in every model, like with normal CActiveRecord
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

}
