<?php

/**
 * This is the model class for table "hotel".
 *
 * The followings are the available columns in table 'hotel':
 * @property integer $id
 * @property integer $position
 * @property string $name
 * @property integer $stars
 * @property integer $cityId
 * @property integer $countryId
 * @property integer $raiting
 * @property integer $minPrice
 */
class HotelDb extends CActiveRecord
{

    /** @var HotelDb[] $hotelDbs */
    private static $hotelDbs = array();
    private static $needSave = false;
    private static $lazySaveIndex = 1;
    private static $lazySaveObjectsIds = array();

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HotelDb the static model class
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
        return 'hotel';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('id', 'required'),
            array('id, position, stars, cityId, countryId, raiting, minPrice', 'numerical', 'integerOnly'=>true),
            array('name', 'length', 'max'=>45),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, position, name, stars, cityId, countryId, raiting, minPrice', 'safe', 'on'=>'search'),
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
            'position' => 'Position',
            'name' => 'Name',
            'stars' => 'Stars',
            'cityId' => 'City',
            'countryId' => 'Country',
            'raiting' => 'Raiting',
            'minPrice' => 'Min Price',
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
        $criteria->compare('position',$this->position);
        $criteria->compare('name',$this->name,true);
        $criteria->compare('stars',$this->stars);
        $criteria->compare('cityId',$this->cityId);
        $criteria->compare('countryId',$this->countryId);
        $criteria->compare('raiting',$this->raiting);
        $criteria->compare('minPrice',$this->minPrice);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public static function &lazySaveHotelDb($params)
    {
        $hotelDb = new HotelDb();
        $attrs = $hotelDb->getAttributes();
        foreach($attrs as $attrName=>$attrVal){
            if(isset($params[$attrName])){
                $hotelDb->setAttribute($attrName,$params[$attrName]);
            }
        }
        self::$lazySaveIndex++;
        self::$needSave = true;
        self::$hotelDbs[self::$lazySaveIndex] = $hotelDb;
        return self::$hotelDbs[self::$lazySaveIndex];
    }

    public static function lazySave(){
        if(self::$needSave){
            $connection=Yii::app()->db;
            $values = array();
            $attrs = array('id','name','stars','cityId','countryId','rating','minPrice');
            foreach(self::$hotelDbs as $hotelDb){
                if($hotelDb->id){
                    $vals = array();
                    foreach($attrs as $attrName){
                        $attrVal = $hotelDb->getAttribute($attrName);
                        if($attrVal){
                            $vals[] = "'".addslashes($attrVal)."'";
                        }else{
                            $vals[] = 'NULL';
                        }
                    }
                    $values[] = "(".implode(',',$vals).")";
                }
            }
            $sql = 'INSERT INTO hotel ('.implode(',',$attrs).') VALUES '.implode(',',$values);
            //$sql .= " (".implode(',',$in).")";
            $sql .= " ON DUPLICATE KEY UPDATE rating=VALUES(rating),minPrice=VALUES(minPrice)";
            $command=$connection->createCommand($sql);
            $command->execute();
            self::$needSave = false;
        }
    }
}