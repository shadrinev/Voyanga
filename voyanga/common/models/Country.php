<?php
/**
 * This is the model class for table "country".
 *
 * The followings are the available columns in table 'country':
 * @property integer $id
 * @property integer $position
 * @property string $code
 * @property string $localRu
 * @property string $localEn
 *
 * The followings are the available model relations:
 * @property City[] $cities
 */
class Country extends CActiveRecord
{
    private static $counries = array();
    private static $codeIdMap = array();

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Country the static model class
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
        return 'country';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('position', 'numerical', 'integerOnly'=>true),
            array('code', 'length', 'max'=>5),
            array('localRu, localEn', 'length', 'max'=>45),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, position, code, localRu, localEn', 'safe', 'on'=>'search'),
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
            'cities' => array(self::HAS_MANY, 'City', 'countryId'),
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

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public static function getCountryByPk( $id )
    {
        if ( isset( Country::$counries[$id] ) )
        {
            return Country::$counries[$id];
        }
        else
        {
            $Country = Country::model()->findByPk($id);
            if ( $Country )
            {
                Country::$counries[$Country->id] = $Country;
                Country::$codeIdMap[$Country->code] = $Country->id;
                return Country::$counries[$id];
            }
            else
            {
                throw new CException( Yii::t( 'application', 'Country with id {id} not found', array(
                    '{id}' => $id ) ) );
            }
        }
    }

    public static function getCountryByCode( $code )
    {
        if ( isset( Country::$codeIdMap[$code] ) )
        {
            return Country::$counries[Country::$codeIdMap[$code]];
        }
        else
        {
            $Country = Country::model()->findByAttributes( array(
                'code' => $code ) );
            if ( $Country )
            {
                Country::$counries[$Country->id] = $Country;
                Country::$codeIdMap[$Country->code] = $Country->id;
                return Country::$counries[Country::$codeIdMap[$code]];
            }
            else
            {
                throw new CException( Yii::t( 'application', 'Country with code {code} not found', array(
                    '{code}' => $code ) ) );
            }
        }
    }
}