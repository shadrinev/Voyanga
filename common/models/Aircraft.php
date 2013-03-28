<?php
/**
 * This is the model class for table "airline".
 *
 * The followings are the available columns in table 'airline':
 * @property integer $id
 * @property integer $code
 * @property string $fullTitle
 */
class Aircraft extends CActiveRecord
{

    private static $aircrafts;

    public static function model( $className = __CLASS__ )
    {
        return parent::model( $className );
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

    public function tableName()
    {
        return 'aircraft';
    }

    static public function getFullTitleByNiataCode($nIataCode)
    {
        if (self::$aircrafts == null)
           self::loadAircrafts();
        $code = strtolower($nIataCode);
        if (isset(self::$aircrafts[$code]))
           return self::$aircrafts[$code];
        return '';
    }

    static private function loadAircrafts()
    {
        $criteria = new CDbCriteria();
        $criteria->select = 'fullTitle, nIataCode';
        $criteria->index = 'nIataCode';

        $tmp = Aircraft::model()->findAll($criteria);

        foreach ($tmp as $ind => $info)
        {
            self::$aircrafts[$ind] = $info->fullTitle;
        }
    }
}