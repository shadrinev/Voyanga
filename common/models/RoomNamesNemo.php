<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 17.08.12
 * Time: 11:49
 * To change this template use File | Settings | File Templates.
 */
/**
 * This is the model class for table "room_names_nemo".
 *
 * The followings are the available columns in table 'room_names_nemo':
 * @property integer $id
 * @property integer $roomTypeId
 * @property integer $roomSizeId
 * @property string $roomNameCanonical
 * @property integer $roomNameRusId
 *
 * The followings are the available model relations:
 * @property RoomNamesRus $roomNameRus
 */
class RoomNamesNemo extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return RoomNamesNemo the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'room_names_nemo';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('roomTypeId, roomSizeId, roomNameRusId', 'numerical', 'integerOnly'=>true),
            array('roomNameCanonical', 'length', 'max'=>200),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, roomTypeId, roomSizeId, roomNameCanonical, roomNameRusId', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'roomNameRus' => array(self::BELONGS_TO, 'RoomNamesRus', 'roomNameRusId'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'roomTypeId' => 'Room Type',
            'roomSizeId' => 'Room Size',
            'roomNameCanonical' => 'Room Name Canonical',
            'roomNameRusId' => 'Room Name Rus',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('roomTypeId',$this->roomTypeId);
        $criteria->compare('roomSizeId',$this->roomSizeId);
        $criteria->compare('roomNameCanonical',$this->roomNameCanonical,true);
        $criteria->compare('roomNameRusId',$this->roomNameRusId);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}