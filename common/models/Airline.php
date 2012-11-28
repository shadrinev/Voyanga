<?php
/**
 * This is the model class for table "airline".
 *
 * The followings are the available columns in table 'airline':
 * @property integer $id
 * @property integer $position
 * @property string $code
 * @property string $localRu
 * @property string $localEn
 */
class Airline extends CActiveRecord
{

    private static $airlines = array();
    private static $codeIdMap = array();

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('code', 'required'),
            array('position', 'numerical', 'integerOnly' => true),
            array('code', 'length', 'max' => 5),
            array('localRu, localEn', 'length', 'max' => 45),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, position, code, localRu, localEn', 'safe', 'on' => 'search'),
        );
    }


    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('position', $this->position);
        $criteria->compare('code', $this->code, true);
        $criteria->compare('localRu', $this->localRu, true);
        $criteria->compare('localEn', $this->localEn, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    public static function getAirlineByPk($id)
    {
        if (isset(Airline::$airlines[$id]))
        {
            return Airline::$airlines[$id];
        }
        else
        {
            $airline = Airline::model()->findByPk($id);
            if ($airline)
            {
                Airline::$airlines[$airline->id] = $airline;
                Airline::$codeIdMap[$airline->code] = $airline->id;
                return Airline::$airlines[$id];
            }
            else
            {
                throw new CException(Yii::t('application', 'Airline with id {id} not found', array(
                    '{id}' => $id
                )));
            }
        }
    }

    public static function getAirlineByCode($code)
    {
        if (isset(Airline::$codeIdMap[$code]))
        {
            return Airline::$airlines[Airline::$codeIdMap[$code]];
        }
        else
        {
            $airline = Airline::model()->findByAttributes(array(
                'code' => $code
            ));
            if ($airline)
            {
                Airline::$airlines[$airline->id] = $airline;
                Airline::$codeIdMap[$airline->code] = $airline->id;
                return Airline::$airlines[Airline::$codeIdMap[$code]];
            }
            else
            {
                throw new CException(Yii::t('application', 'Airline with code '.CVarDumper::dumpAsString($code).' not found'));
            }
        }
    }

    public function tableName()
    {
        return 'airline';
    }
}