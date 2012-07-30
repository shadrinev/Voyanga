<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 22.06.12
 * Time: 13:48
 * To change this template use File | Settings | File Templates.
 */
class HotelRequest extends EMongoDocument // Notice: We extend EMongoDocument class instead of CActiveRecord
{
    public $requestNum;
    public $groupId;
    public $methodName;
    public $requestDescription;
    public $requestUrl;
    public $requestXml;
    public $responseXml;
    public $timestamp;
    public $executionTime;
    public $errorDescription;

    /**
     * This method have to be defined in every Model
     * @return string MongoDB collection name, witch will be used to store documents of this model
     */
    public function getCollectionName()
    {
        return 'HotelRequest';
    }

    // We can define rules for fields, just like in normal CModel/CActiveRecord classes
    public function rules()
    {
        return array(
            array('requestUrl', 'required'),
            array('requestNum, methodName, timestamp', 'safe'),
            //array('requestNum', 'numeric', 'integerOnly' => true),
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
