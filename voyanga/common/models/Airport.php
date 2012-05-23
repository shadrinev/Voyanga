<?php
/**
 * This is the model class for table "airport".
 *
 * The followings are the available columns in table 'airport':
 * @property integer $id
 * @property integer $position
 * @property string $code
 * @property string $icaoCode
 * @property string $localRu
 * @property string $localEn
 * @property integer $cityId
 * @property real $latitude
 * @property real $longitude
 * @property string $site
 *
 * The followings are the available model relations:
 * @property City $city
 */
class Airport extends CActiveRecord
{
    private static $airports = array();
    private static $codeIdMap = array();
    
    public static function model($className = __CLASS__)
    {
        return parent::model( $className );
    }
    
    public static function getAirportByCode($code)
    {
        if (isset( Airport::$codeIdMap[$code] ))
        {
            return Airport::$airports[Airport::$codeIdMap[$code]];
        }
        else
        {
            $airport = Airport::model()->findByAttributes( array(
                    'code' => $code ) );
            if ($airport)
            {
                $city = $airport->city;
                Airport::$airports[$airport->id] = $airport;
                Airport::$codeIdMap[$airport->code] = $airport->id;
                return Airport::$airports[Airport::$codeIdMap[$code]];
            }
            else
            {
                throw new CException( Yii::t( 'application', 'City with code {code} not found', array(
                        '{code}' => $code ) ) );
            }
        }
    }

    public static function getAirportByPk($id)
    {
        if (isset( Airport::$airports[$id] ))
        {
            return Airport::$airports[$id];
        }
        else
        {
            $airport = Airport::model()->findByPk( $id );
            if ($airport)
            {
                $city = $airport->city;
                Airport::$airports[$airport->id] = $airport;
                Airport::$codeIdMap[$airport->code] = $airport->id;
                return Airport::$airports[$id];
            }
            else
            {
                throw new CException( Yii::t( 'application', 'City with id {id} not found', array(
                    '{id}' => $id ) ) );
            }
        }
    }
    
    public function tableName()
    {
        return 'airport';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('position, code, cityId', 'required'),
            array('position, cityId', 'numerical', 'integerOnly'=>true),
            array('code, icaoCode', 'length', 'max'=>5),
            array('localRu, localEn, site', 'length', 'max'=>45),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, position, code, localRu, localEn, cityId, latitude, longitude', 'safe', 'on'=>'search'),
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
            'city' => array(self::BELONGS_TO, 'City', 'cityId'),
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
            'code' => 'Code',
            'localRu' => 'Local Ru',
            'localEn' => 'Local En',
            'cityId' => 'City',
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
        $criteria->compare('code',$this->code,true);
        $criteria->compare('localRu',$this->localRu,true);
        $criteria->compare('localEn',$this->localEn,true);
        $criteria->compare('cityId',$this->cityId);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function getCity()
    {
        if (isset( $this->city ))
        {
            return $this->city;
        }else{
            $this->city = City::getCityByPk($this->cityId);
            return $this->city;
        }
    }
}