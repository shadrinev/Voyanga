<?php
/**
 * This is the model class for table "city".
 *
 * The followings are the available columns in table 'city':
 * @property integer $id
 * @property integer $position
 * @property integer $countryId
 * @property string $code
 * @property string $localRu
 * @property string $localEn
 *
 * The followings are the available model relations:
 * @property Airport[] $airports
 * @property Country $country
 * @property Route[] $routes
 * @property Route[] $routes1
 */
class City extends CActiveRecord
{
    
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return City the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model( $className );
    }
    
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'city';
    }
    
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
                array(
                        'position, countryId', 
                        'numerical', 
                        'integerOnly' => true ), 
                array(
                        'code', 
                        'length', 
                        'max' => 5 ), 
                array(
                        'localRu, localEn', 
                        'length', 
                        'max' => 45 ), 
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array(
                        'id, position, countryId, code, localRu, localEn', 
                        'safe', 
                        'on' => 'search' ) );
    }
    
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
                'airports' => array(
                        self::HAS_MANY, 
                        'Airport', 
                        'cityId' ), 
                'country' => array(
                        self::BELONGS_TO, 
                        'Country', 
                        'countryId' ), 
                'departureRoutes' => array(
                        self::HAS_MANY, 
                        'Route', 
                        'departureCityId' ), 
                'arrivalRoutes' => array(
                        self::HAS_MANY, 
                        'Route', 
                        'arrivalCityId' ) );
    }
    
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
                'id' => 'ID', 
                'position' => 'Position', 
                'countryId' => 'Country', 
                'code' => 'Code', 
                'localRu' => 'Local Ru', 
                'localEn' => 'Local En' );
    }
    
    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.
        

        $criteria = new CDbCriteria();
        
        $criteria->compare( 'id', $this->id );
        $criteria->compare( 'position', $this->position );
        $criteria->compare( 'countryId', $this->countryId );
        $criteria->compare( 'code', $this->code, true );
        $criteria->compare( 'localRu', $this->localRu, true );
        $criteria->compare( 'localEn', $this->localEn, true );
        
        return new CActiveDataProvider( $this, array(
                'criteria' => $criteria ) );
    }
}