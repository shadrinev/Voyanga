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
 * @property real $latitude
 * @property real $longitude
 * @property integer $hotelbookId
 * @property string $metaphoneRu
 * @property string $stateCode
 * The followings are the available model relations:
 * @property Airport[] $airports
 * @property Country $country
 * @property Route[] $routes
 * @property Route[] $routes1
 */
class City extends CActiveRecord
{
    private static $cities = array();
    private static $codeIdMap = array();
    private static $idHotelbookIdMap = array();

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
                        'position, countryId, countAirports',
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
                        'id, position, countryId, code, localRu, localEn, countAirports, latitude, longitude, hotelbookId, metaphoneRu, stateCode',
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
                'localEn' => 'Local En',
                'countAirports' => 'Count Airports');
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
        $criteria->compare( 'countAirports', $this->countAirports);
        $criteria->compare( 'metaphoneRu', $this->metaphoneRu, true );
        $criteria->compare( 'metaphoneRu', $this->stateCode, true );
        
        return new CActiveDataProvider( $this, array(
                'criteria' => $criteria ) );
    }

    public static function getCityByPk( $id )
    {
        if ( isset( City::$cities[$id] ) )
        {
            return City::$cities[$id];
        }
        else
        {
            $city = City::model()->findByPk($id);
            if ( $city )
            {
                City::$cities[$city->id] = $city;
                if($city->code)
                {
                    City::$codeIdMap[$city->code] = $city->id;
                }
                if($city->hotelbookId)
                {
                    City::$idHotelbookIdMap[$city->hotelbookId] = $city->id;
                }
                return City::$cities[$id];
            }
            else
            {
                throw new CException( Yii::t( 'application', 'City with id {id} not found', array(
                    '{id}' => $id ) ) );
            }
        }
    }

    public static function getCityByCode( $code )
    {
        if ( isset( City::$codeIdMap[$code] ) )
        {
            return City::$cities[City::$codeIdMap[$code]];
        }
        else
        {
            $city = City::model()->findByAttributes( array(
                'code' => $code ) );
            if ( $city )
            {
                City::$cities[$city->id] = $city;
                City::$codeIdMap[$city->code] = $city->id;
                if($city->hotelbookId)
                {
                    City::$idHotelbookIdMap[$city->hotelbookId] = $city->id;
                }
                return City::$cities[City::$codeIdMap[$code]];
            }
            else
            {
                throw new CException( Yii::t( 'application', 'City with code {code} not found', array(
                    '{code}' => $code ) ) );
            }
        }
    }

    public static function getCityByHotelbookId( $hotelbookId )
    {
        if ( isset( City::$idHotelbookIdMap[$hotelbookId] ) )
        {
            return City::$cities[City::$idHotelbookIdMap[$hotelbookId]];
        }
        else
        {
            $city = City::model()->findByAttributes( array(
                'hotelbookId' => $hotelbookId ) );
            if ( $city )
            {
                City::$cities[$city->id] = $city;
                if($city->code)
                {
                    City::$codeIdMap[$city->code] = $city->id;
                }
                if($city->hotelbookId)
                {
                    City::$idHotelbookIdMap[$city->hotelbookId] = $city->id;
                }
                return City::$cities[City::$idHotelbookIdMap[$hotelbookId]];
            }
            else
            {
                throw new CException( Yii::t( 'application', 'City with hoteldookId {code} not found', array(
                    '{code}' => $hotelbookId ) ) );
            }
        }
    }
}