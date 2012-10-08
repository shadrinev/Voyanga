<?php
/**
 * This is the model class for table "event_link".
 *
 * The followings are the available columns in table 'event_link':
 * @property integer $id
 * @property integer $eventId
 * @property string $url
 * @property string $title
 *
 * The followings are the available model relations:
 * @property Event $event
 */
class EventLink extends FrontendActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return EventLink the static model class
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
        return 'event_link';
    }

    public function __toString()
    {
        $text = strlen($this->title)>0 ? $this->title : $this->url;
        return CHtml::link($text, $this->url);
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('url','url', 'allowEmpty'=>false),
            array('eventId', 'numerical', 'integerOnly'=>true),
            array('url, title', 'length', 'max'=>255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, eventId, url, title', 'safe', 'on'=>'search'),
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
            'event' => array(self::BELONGS_TO, 'Event', 'eventId'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'eventId' => 'Event',
            'url' => 'URL',
            'title' => 'Текст ссылки',
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
        $criteria->compare('eventId',$this->eventId);
        $criteria->compare('url',$this->url,true);
        $criteria->compare('title',$this->title,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function getJsonObject()
    {
        return array(
            'title' => $this->title,
            'url' => $this->url
        );
    }
}