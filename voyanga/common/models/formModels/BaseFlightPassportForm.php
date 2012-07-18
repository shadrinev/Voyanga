<?php
/**
 * PassportForm class
 * class for working with passort data in html forms
 * @author oleg
 *
 */
class BaseFlightPassportForm extends BasePassportForm
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
    public $documentTypeId = self::TYPE_RF;

    /** gender */
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    public $genderId;

    /** @var dd.MM.YYYY */
    public $birthday;

    /** @var expritation date for internation passport */
    public $expirationDate;

    public $countryId;

    public $series;
    public $number;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return CMap::mergeArray(parent::rules(), array(
            array('documentTypeId, series, number, birthday, countryId', 'required'),
            array('documentTypeId', 'in', 'range'=>array_keys(self::getPossibleTypes())),
            array('genderId', 'in', 'range'=>array_keys(self::getPossibleGenders())),
            array('birthday', 'date', 'format' => 'dd.MM.yyyy'),
            array('expirationDate', 'date', 'format' => 'dd.MM.yyyy', 'on'=>'type_'.self::TYPE_INTERNATIONAL),
        ));
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return CMap::mergeArray(parent::attributeLabels(), array(
            'type' => 'Тип документа',
            'gender' => 'Пол',
            'birthday' => 'Дата рождения (ДД.ММ.ГГГГ)',
            'expirationDate' => 'Дата истечения паспорта (ДД.ММ.ГГГГ)',
            'series' => 'Серия',
            'number' => 'Номер',
            'documentTypeId' => 'Документ',
            'genderId' => 'Пол',
            'countryId' => 'Страна',
        ));
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

    public function getPossibleGenders()
    {
        return array(
            self::GENDER_MALE => 'Муж',
            self::GENDER_FEMALE => 'Жен'
        );
    }
}