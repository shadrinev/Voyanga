<?php
/**
 * This is the model class for table "airport".
 *
 * The followings are the available columns in table 'airport':
 * @property integer $id
 * @property integer $position
 * @property string $code
 * @property string $localRu
 * @property string $localEn
 * @property integer $cityId
 *
 * The followings are the available model relations:
 * @property City $city
 */
class Airport extends CActiveRecord
{
    private static $airports = array();
    
    public static function model($className = __CLASS__)
    {
        return parent::model( $className );
    }
    
    public static function getAirportByCode($sCode)
    {
        if (isset( Airport::$airports[$sCode] ))
        {
            return Airport::$airports[$sCode];
        }
        else
        {
            Yii::beginProfile( 'laodAirportFromDB' );
            $oAirport = Airport::model()->findByAttributes( array(
                    'code' => $sCode ) );
            if ($oAirport)
            {
                $city = $oAirport->city;
                Airport::$airports[$oAirport->code] = $oAirport;
                Yii::endProfile( 'laodAirportFromDB' );
                return Airport::$airports[$sCode];
            }
            else
            {
                //throw new CException( Yii::t( 'application', 'Airport with code {code} not found', array(
                //        '{code}' => $sCode ) ) );
                //todo: write to log info about not found airport
                $airport = new Airport();
                $city = City::model()->findByAttributes( array(
                'code' => $sCode ) );
                if($city){
                    $airport->cityId = $city->id;
                    $city1 = $airport->city;
                }
                Airport::$airports[$sCode] = $airport;
                Yii::endProfile( 'laodAirportFromDB' );
                return Airport::$airports[$sCode];
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
            array('code', 'length', 'max'=>5),
            array('localRu, localEn', 'length', 'max'=>45),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, position, code, localRu, localEn, cityId', 'safe', 'on'=>'search'),
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
}