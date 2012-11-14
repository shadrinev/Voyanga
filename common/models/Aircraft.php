<?php
/**
 * This is the model class for table "airline".
 *
 * The followings are the available columns in table 'airline':
 * @property integer $id
 * @property integer $iataCode
 * @property integer $manufacturer
 * @property integer $manufacturerRu
 * @property string $model
 * @property string $type
 */
class Aircraft extends CActiveRecord
{

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

    public function getFullTitle()
    {
        $manufacturer = strlen($this->manufacturerRu) > 0 ? $this->manufacturerRu : $this->manufacturer;
        $model = $this->model;
        return $manufacturer." ".$model;
    }
}