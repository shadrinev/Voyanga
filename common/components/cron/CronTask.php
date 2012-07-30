<?php
/**
 * This is the model class for table "cron_task".
 *
 * The followings are the available columns in table 'cron_task':
 * @property integer $id
 * @property string $ownerModel
 * @property integer $ownerId
 * @property string $taskName
 * @property integer $taskId
 * @property string $timeAdded
 */
class CronTask extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return CronTask the static model class
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
        return 'cron_task';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('timeAdded', 'required'),
            array('ownerId, taskId', 'numerical', 'integerOnly'=>true),
            array('ownerModel, taskName', 'length', 'max'=>255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, ownerModel, ownerId, taskName, taskId, timeAdded', 'safe', 'on'=>'search'),
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
            'ownerModel' => 'Owner Model',
            'ownerId' => 'Owner',
            'taskName' => 'Task Name',
            'taskId' => 'Task',
            'timeAdded' => 'Time Added',
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
        $criteria->compare('ownerModel',$this->ownerModel,true);
        $criteria->compare('ownerId',$this->ownerId);
        $criteria->compare('taskName',$this->taskName,true);
        $criteria->compare('taskId',$this->taskId);
        $criteria->compare('timeAdded',$this->timeAdded,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}
