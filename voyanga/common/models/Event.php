<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 15.05.12
 * Time: 14:03
 */

/**
 * This is the model class for table "event".
 *
 * The followings are the available columns in table 'event':
 * @property integer $id
 * @property string $startDate
 * @property string $endDate
 * @property integer $cityId
 * @property string $address
 * @property string $contact
 * @property integer $status
 * @property string $preview
 * @property string $description
 */
class Event extends FrontendActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * The behaviors associated with the user model.
     * @see CActiveRecord::behaviors()
     */
    public function behaviors()
    {
        $behaviors = array();
        $behaviors['AResourceful'] = array(
            "class" => "packages.resources.components.AResourceful",
            "attributes" => array(
                "pictureSmall" => array(
                    "fileTypes" => array("png", "jpg"),
                ),
                'pictureBig' => array(
                    "fileTypes" => array("png", "jpg"),
                ),
                'pictures' => array(
                    "fileTypes" => array("png", "jpg"),
                    'multiple' => true
                ),
            )
        );
        $behaviors['EAdvancedArBehavior'] = array(
            'class' => 'common.components.EAdvancedArBehavior'
        );
        $behaviors['tags'] = array(
            'class' => 'common.extensions.yiiext.behaviors.model.taggable.ETaggableBehavior',
            // Table where tags are stored
            'tagTable' => 'event_tag',
            // Cross-table that stores tag-model connections.
            // By default it's your_model_tableTag
            'tagBindingTable' => 'event_has_tag',
            // Foreign key in cross-table.
            // By default it's your_model_tableId
            'modelTableFk' => 'eventId',
            // Tag table PK field
            'tagTablePk' => 'id',
            // Tag name field
            'tagTableName' => 'name',
            // Tag counter field
            // if null (default) does not write tag counts to DB
            'tagTableCount' => null,
            // Tag binding table tag ID
            'tagBindingTableTagId' => 'eventTagId',
            // Caching component ID. If false don't use cache.
            // Defaults to false.
            'cacheID' => 'cache',

            // Save nonexisting tags.
            // When false, throws exception when saving nonexisting tag.
            'createTagsAutomatically' => true,
        );

        return $behaviors;
    }

    public function getTagsString()
    {
        return $this->tags->toString();
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Event the static model class
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
        return 'event';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('links','checkLinks'),
            array('startDate','checkDates'),
            array('title, cityId, startDate, endDate, status', 'required'),
            array('cityId, status', 'numerical', 'integerOnly'=>true),
            array('title, address, contact', 'length', 'max'=>255),
            array('startDate, endDate, preview, description', 'safe'),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, startDate, endDate, cityId, address, contact, status, preview, description', 'safe', 'on'=>'search'),
        );
    }

    public function checkDates()
    {
        if (strtotime($this->startDate)>=strtotime($this->endDate))
            $this->addError('startDate', 'Дата начала события позже чем дата его завершения. Проверьте даты события.');
    }

    public function checkLinks($attribute)
    {
        $valid = true;
        foreach ($this->$attribute as $link)
        {
            $valid = $link->validate() && $valid;
        }
        if (!$valid)
        {
            $this->addError('links','Неверный формат ссылки');
        }
        return $valid;
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
            'categories' => array(self::MANY_MANY, 'EventCategory', 'event_has_category(eventId, eventCategoryId)'),
            'links' => array(self::HAS_MANY, 'EventLink', 'eventId'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'startDate' => 'Дата начала',
            'endDate' => 'Дата завершения',
            'cityId' => 'Город',
            'address' => 'Адрес',
            'contact' => 'Контакты',
            'status' => 'Статус',
            'statusName' => 'Статус',
            'preview' => 'Анонс',
            'description' => 'Описание',
            'title' => 'Название',
            'categories' => 'Категории',
            'pictureSmall' => 'Картинка-превью',
            'pictureBig' => 'Картинка полноразмерная',
            'pictures' => 'Галерея',
            'tagsString' => 'Теги'
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
        $criteria->compare('startDate',$this->startDate,true);
        $criteria->compare('endDate',$this->endDate,true);
        $criteria->compare('cityId',$this->cityId);
        $criteria->compare('address',$this->address,true);
        $criteria->compare('contact',$this->contact,true);
        $criteria->compare('status',$this->status);
        $criteria->compare('preview',$this->preview,true);
        $criteria->compare('description',$this->description,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    public function getPossibleStatus()
    {
        return array(
            self::STATUS_INACTIVE => 'Не активно',
            self::STATUS_ACTIVE => 'Активно',
        );
    }

    public function getStatusName()
    {
        $statuses = $this->getPossibleStatus();
        if (isset($statuses[$this->status]))
            return $statuses[$this->status];
        throw new Exception('No such status for event');
    }
}
