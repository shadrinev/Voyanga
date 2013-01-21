<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 29.08.12
 * Time: 13:52
 *
 * The followings are the available columns in table 'event_price':
 * @property integer $id
 * @property integer $eventId
 * @property integer $cityId
 * @property double $bestPrice
 * @property double $bestTime
 * @property double $bestPriceTime
 * @property string $created
 * @property string $updated
 */
class EventPrice extends CActiveRecord
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'event_price';
    }

    public function relations()
    {
        return array(
            'city'=>array(self::BELONGS_TO, 'City', 'cityId')
        );
    }

    public function behaviors(){
        return array(
            'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'created',
                'updateAttribute' => 'updated',
            )
        );
    }

    public function getJsonObject()
    {
        return array(
            'city' => array(
                'title' => $this->city->localRu
            ),
            'price' => floor($this->bestPrice)
        );
    }
}
