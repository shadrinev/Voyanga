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
 * @property string $title
 * @property string $address
 * @property string $contact
 * @property integer $status
 * @property string $preview
 * @property string $description

 * @property string $pictureSmall
 * @property string $pictureBig
 * @property array $pictures
 *
 * @property EventCategory[] $categories
 * @property EventLink[] $links
 * @property EventPrice[] $prices
 * @property EventTour[] $tours
 */
class Event extends FrontendActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const NEW_EVENT_ITEM = -1;
    const NO_EVENT_ITEM = 0;

    public $defaultThumbImageUrl = '/img/events/defaultSmall.jpg';
    public $defaultBigImageUrl = '/img/events/defaultBig.jpg';
    public $imgSrc = 'http://backend.voyanga.com';

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
            //for filling out from frontend
            array('title', 'required', 'on'=>'frontend'),

            array('links','checkLinks', 'on'=>'backend'),
            array('startDate','checkDates', 'on'=>'backend'),
            array('title, startDate, endDate, status', 'required', 'on'=>'backend'),
            array('status', 'numerical', 'integerOnly'=>true, 'on'=>'backend'),
            array('title, address, contact', 'length', 'max'=>255, 'on'=>'backend'),
            array('startDate, endDate, preview, description', 'safe', 'on'=>'backend'),
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
            'prices' => array(self::HAS_MANY, 'EventPrice', 'eventId'),
            'categories' => array(self::MANY_MANY, 'EventCategory', 'event_has_category(eventId, eventCategoryId)'),
            'links' => array(self::HAS_MANY, 'EventLink', 'eventId'),
            'startCities' => array(self::HAS_MANY, 'City', array('cityId'=>'id'), 'through'=>'prices'),
            'tours' => array(self::HAS_MANY, 'EventOrder', 'eventId')
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

    public static function getFlightFromDate($startDate)
    {
        return date('d.m.Y', strtotime($startDate)-24*3600);
    }

    public static function getFlightToDate($endDate)
    {
        return date('d.m.Y', strtotime($endDate)+24*3600);
    }

    private function getPriceForCity($cityCode, $forceUpdate)
    {
        try
        {
            $from = City::getCityByCode($cityCode)->id;
            $to = $this->cityId;

            $dateStart = self::getFlightFromDate($this->startDate);
            $priceTo = FlightSearcher::getOptimalPrice($from, $to, $dateStart, false, $forceUpdate);

            $dateEnd =self::getFlightToDate($this->endDate);
            $priceBack = FlightSearcher::getOptimalPrice($to, $from, $dateEnd, false, $forceUpdate);

            $priceToBack = FlightSearcher::getOptimalPrice($from, $to, $dateStart, $dateEnd, $forceUpdate);

            return min($priceTo+$priceBack, $priceToBack);
        }
        catch (Exception $e)
        {
            return 'N/A';
        }
    }

    public static function getPossibleEvents()
    {
        $allEvents = Event::model()->findAll();
        $existingEvents = CHtml::listData($allEvents, 'id', 'title');
        $newEvent = array(self::NEW_EVENT_ITEM => '..Создать новое событие..');
        $noEvent = array(self::NO_EVENT_ITEM => 'Не привязывать событие');
        return CMap::mergeArray(
            $noEvent,
            $newEvent,
            $existingEvents
        );
    }

    public function getPricesReadable()
    {
        $ret = array();
        foreach ($this->prices as $price)
        {
            $city = $price->city;
            $ret[] = 'Из '.$city->caseGen.': '.$price->bestPrice." руб.";
        }
        return implode(', ', $ret);
    }

    public function getJsonObject()
    {
        $data = array(
            'active' => false,
            "startDate" => DateTimeHelper::formatForJs($this->startDate),
            "endDate" => DateTimeHelper::formatForJs($this->endDate),
            "address" => $this->address,
            "contact" => $this->contact,
            "thumb" => isset($this->pictureSmall) ? $this->imgSrc.$this->pictureSmall->url : $this->defaultThumbImageUrl,
            "preview" => $this->preview,
            "description" => $this->description,
            "image" => isset($this->pictureBig) ? $this->imgSrc.$this->pictureBig->url : $this->defaultBigImageUrl,
            "title" => $this->title,
            "categories" => $this->getCategoriesJsonObject(),
            "links" => $this->getLinksJsonObject(),
            "tags" => $this->getTagsJsonObject(),
            "prices" => $this->getPricesJsonObject(),
            "tours" => $this->getToursJsonObject(),
        );
        return $data;
    }

    static public function getRandomEvents($amount = 8)
    {
        $condition = new CDbCriteria();
        $condition->limit = $amount;
        $condition->order = new CDbExpression('RAND()');
        $condition->compare('status', Event::STATUS_ACTIVE);
        $events = self::model()->findAll($condition);
        return $events;
    }

    public function getCategoriesJsonObject()
    {
        $arr = array();
        foreach ($this->categories as $category)
        {
            $arr[] = $category->getJsonObject();
        }
        return $arr;
    }

    public function getLinksJsonObject()
    {
        $arr = array();
        foreach ($this->links as $link)
        {
            $arr[] = $link->getJsonObject();
        }
        return $arr;
    }

    public function getTagsJsonObject()
    {
        $arr = array();
        $tags = array_map('trim', explode(',', $this->getTagsString()));
        foreach ($tags as $tag)
        {
            if (strlen($tag)>0)
                $arr[] = array('name' => $tag);
        }
        return $arr;
    }

    public function getPricesJsonObject()
    {
        $arr = array();
        foreach ($this->prices as $price)
        {
            $arr[] = $price->getJsonObject();
        }
        return $arr;
    }

    public function getToursJsonObject()
    {
        $arr = array();
        foreach ($this->tours as $tour)
        {
            $arr[] = $tour->getJsonObject();
        }
        return $arr;
    }
}
