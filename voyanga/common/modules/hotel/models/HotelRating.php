<?php

/**
 * This is the model class for table "hotel_rating".
 *
 * The followings are the available columns in table 'hotel_rating':
 * @property integer $city_id
 * @property string $canonical_name
 * @property double $rating
 */
class HotelRating extends CActiveRecord
{
        /**
         * Returns the static model of the specified AR class.
         * @param string $className active record class name.
         * @return HotelRating the static model class
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
                return 'hotel_rating';
        }

        /**
         * @return array validation rules for model attributes.
         */
        public function rules()
        {
            // NOTE: you should only define rules for those attributes that
            // will receive user inputs.
            //! FIXME check if name is > than 255 characters

            return array(
                array('city_id, canonical_name, rating', 'required'),
                array('city_id', 'numerical', 'integerOnly'=>true),
                array('rating', 'numerical'),
                array('canonical_name', 'length', 'max'=>255),
                // The following rule is used by search().
                // Please remove those attributes that should not be searched.
                array('canonical_name', 'safe', 'on'=>'search'),
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
                    'city' => array(
                        self::BELONGS_TO,
                        'City',
                        'city_id' ),
                );
        }

        /**
         * @return array customized attribute labels (name=>label)
         */
        public function attributeLabels()
        {
                return array(
                        'city_id' => 'City',
                        'canonical_name' => 'Canonical Name',
                        'rating' => 'Rating',
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
               $criteria->compare('canonical_name',$this->canonical_name,true);

                return new CActiveDataProvider($this, array(
                        'criteria'=>$criteria,
                ));
        }
        /**
         * Method to find rating for given hotels in given city
         *
         * @param array $hotel_names Names of hotels we want to lookup rating for
         * @param City $city City model instance for given hotel
         * @return array HotelName => rating
         */
        public function findByNames($hotel_names, $city)
        {
            $canonized_names = Array();
            foreach ($hotel_names as $name) {
                $canonical_name = UtilsHelper::canonizeHotelName($name, $city->localEn);
                $canonized_names[$name] = $canonical_name;
            }
            $criteria = new CDbCriteria();
            $criteria->addInCondition("canonical_name", $canonized_names);
            $criteria->addColumnCondition(Array("city_id"=>$city->id));
            $rows = $this->findAll($criteria);
            $canonical_name_to_rating = Array();
            foreach($rows as $row) {
                $canonical_name_to_rating[$row->canonical_name] = $row->rating;
            }
            $name_to_rating = Array();
            foreach($canonized_names as $name=>$canonical_name){
                if(isset($canonical_name_to_rating[$canonical_name]))
                    $name_to_rating[$name]=$canonical_name_to_rating[$canonical_name];
            }
            return $name_to_rating;
        }
}
