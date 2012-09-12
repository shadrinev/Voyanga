<?php
/**
 * This is the model class for table "hotel_cache".
 *
 * The followings are the available columns in table 'hotel_cache':
 * @property integer $cityId
 * @property string $dateFrom
 * @property string $dateTo
 * @property integer $stars
 * @property double $price
 * @property integer $hotelId
 * @property string $hotelName
 * @property string $createdAt
 * @property string $updatedAt
 */
class CommonHotelCache extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return HotelCache the static model class
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
        return 'hotel_cache';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('cityId, stars, hotelId', 'numerical', 'integerOnly'=>true),
            array('price', 'numerical'),
            array('hotelName', 'length', 'max'=>255),
            array('createdAt, updatedAt', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('cityId, dateFrom, dateTo, stars, price, hotelId, hotelName, createdAt, updatedAt', 'safe', 'on'=>'search'),
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
            'cityId' => 'City',
            'dateFrom' => 'Date From',
            'dateTo' => 'Date To',
            'stars' => 'Stars',
            'price' => 'Price',
            'hotelId' => 'Hotel',
            'hotelName' => 'Hotel Name',
            'createdAt' => 'Created At',
            'updatedAt' => 'Updated At',
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

        $criteria->compare('cityId',$this->cityId);
        $criteria->compare('dateFrom',$this->dateFrom,true);
        $criteria->compare('dateTo',$this->dateTo,true);
        $criteria->compare('stars',$this->stars);
        $criteria->compare('price',$this->price);
        $criteria->compare('hotelId',$this->hotelId);
        $criteria->compare('hotelName',$this->hotelName,true);
        $criteria->compare('createdAt',$this->createdAt,true);
        $criteria->compare('updatedAt',$this->updatedAt,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function buildRow()
    {
        $attributes = $this->attributes;
        $row = implode(',', $attributes)."\n";
        return $row;
    }

    public function buildQuery()
    {
        $currentSql = '';
        if ($this->isNewRecord)
            $currentSql = "`createdAt` = NOW(), ";

        $query = "INSERT INTO ".$this->tableName()." SET "
            ."`cityId` = '".$this->cityId."', "
            ."`dateFrom` = '".$this->dateFrom."', "
            ."`dateTo` = '".$this->dateTo."', "
            ."`stars` = '".$this->stars."', "
            ."`price` = '".$this->price."', "
            ."`hotelId` = '".$this->hotelId."', "
            ."`hotelName` = '".$this->hotelName."', "
            .$currentSql
            ."`updatedAt` = NOW() "
            ." ON DUPLICATE KEY UPDATE "
            ."`cityId` = '".$this->cityId."', "
            ."`dateFrom` = '".$this->dateFrom."', "
            ."`dateTo` = '".$this->dateTo."', "
            ."`stars` = '".$this->stars."', "
            ."`price` = '".$this->price."', "
            ."`hotelId` = '".$this->hotelId."', "
            ."`hotelName` = '".$this->hotelName."', "
            ."`updatedAt` = NOW() "
            .";";
        return $query;
    }

    /**
     * @param $hotelBook array
     */
    public function populateFromJsonObject($hotelBook)
    {
        $this->cityId = $hotelBook['cityId'];
        $this->dateFrom = $hotelBook['dateFrom'];
        $this->dateTo = $hotelBook['dateTo'];
        $this->stars = $hotelBook['categoryId'];
        $this->price = $hotelBook['rubPrice'];
        $this->hotelId = $hotelBook['hotelId'];
        $this->hotelName = $hotelBook['hotelName'];
    }

    public function forceSave()
    {
        $query = $this->buildQuery();
        $command = Yii::app()->db->createCommand($query);
        $command->execute();
    }
}