<?php
/**
 * Passport class
 * Class for saving and loading passport data
 * @author oleg
 *
 */

/**
 * This is the model class for table "passport".
 *
 * The followings are the available columns in table 'passport':
 * @property integer $id
 * @property string $firstName
 * @property string $lastName
 * @property string $number
 * @property string $birthday
 * @property string $series
 * @property integer $documentTypeId
 * @property integer $countryId
 */

//todo: alter birthday to date
//todo: add gender_id to db
//todo: add expiration to db
//todo: country_id as foreign key
class Passport extends CActiveRecord
{
    /*
    * documentTypeId values:
    * 1 - Passport RF
    * 2 - Passport other country
    * 3 - Zagran
    */
    const TYPE_RF = 1;
    const TYPE_OTHER = 2;
    const TYPE_INTERNATIONAL = 3;
    const TYPE_BIRTH_CERT = 4;

    const GENDER_M = 1;
    const GENDER_F = 2;

    //public $expiration;
    //public $gender_id;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function rules()
    {
        return array(
            // name, email, subject and body are required
            array(
                'first_name, last_name, number, birthday, document_type_id, gender_id',
                'required'));
    }

    public function tableName()
    {
        return 'passport';
    }

    public function checkValid()
    {
        return true;
    }

    public static  function getPossibleTypes()
    {
        return array(
            self::TYPE_RF => 'Паспорт РФ',
            self::TYPE_INTERNATIONAL => 'Загран. паспорт',
            self::TYPE_OTHER => 'Паспорт другой страны',
            self::TYPE_BIRTH_CERT => 'Свидетельство о рождении'
        );
    }

    public static  function getPossibleGenders()
    {
        return array(
            self::GENDER_M => 'Мужской',
            self::GENDER_F => 'Женский',
        );
    }
}