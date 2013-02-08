<?php
/**
 * This is the model class for table "geoip".
 *
 * The followings are the available columns in table 'geoip':
 * @property integer $id
 * @property string $beginIp
 * @property string $endIp
 * @property integer $cityId
 * @property integer $countryId
 * @property integer $position
 */
class Geoip extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Geoip the static model class
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
        return 'geoip';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('beginIp, endIp, countryId, position', 'required'),
            array('cityId, countryId, position', 'numerical', 'integerOnly'=>true),
            array('beginIp, endIp', 'length', 'max'=>20),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, beginIp, endIp, cityId, countryId, position', 'safe', 'on'=>'search'),
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
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'beginIp' => 'Begin Ip',
            'endIp' => 'End Ip',
            'cityId' => 'City',
            'countryId' => 'Country',
            'position' => 'Position',
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
        $criteria->compare('beginIp',$this->beginIp,true);
        $criteria->compare('endIp',$this->endIp,true);
        $criteria->compare('cityId',$this->cityId);
        $criteria->compare('countryId',$this->countryId);
        $criteria->compare('position',$this->position);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}