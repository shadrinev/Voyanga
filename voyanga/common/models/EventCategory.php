<?php

/**
 * This is the model class for table "event_category".
 *
 * The followings are the available columns in table 'event_category':
 * @property string $id
 * @property string $root
 * @property string $title
 * @property string $lft
 * @property string $rgt
 * @property integer $level
 */
class EventCategory extends FrontendActiveRecord
{
    public $parentId;

    public function behaviors()
    {
        return array(
            'nestedSetBehavior'=>array(
                'class'=>'site.common.extensions.yiiext.behaviors.model.trees.NestedSetBehavior',
                'leftAttribute'=>'lft',
                'rightAttribute'=>'rgt',
                'levelAttribute'=>'level',
                'hasManyRoots' => true
            ),
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return EventCategory the static model class
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
        return 'event_category';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('title', 'required'),
            array('title', 'length', 'max'=>255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, root, title, lft, rgt, level', 'safe', 'on'=>'search'),
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
            'root' => 'Root',
            'title' => 'Название категории',
            'lft' => 'Lft',
            'rgt' => 'Rgt',
            'level' => 'Level',
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

        $criteria->compare('id',$this->id,true);
        $criteria->compare('root',$this->root,true);
        $criteria->compare('title',$this->title,true);
        $criteria->compare('lft',$this->lft,true);
        $criteria->compare('rgt',$this->rgt,true);
        $criteria->compare('level',$this->level);
        $criteria->order = $this->hasManyRoots
            ?$this->rootAttribute . ', ' . $this->leftAttribute
            :$this->leftAttribute;

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }
}