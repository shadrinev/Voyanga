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

    public $countryId;

    public $series='';
    public $seriesNumber;

    public $birthdayDay;
    public $birthdayMonth;
    public $birthdayYear;

    public $expirationDay;
    public $expirationMonth;
    public $expirationYear;
    public $ticketNumber='';
    public $passengerType = Passenger::TYPE_ADULT;

    public $srok = false;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return CMap::mergeArray(parent::rules(), array(
            array('birthdayDay', 'required', 'message' => 'Введите день рождения'),
            array('birthdayMonth', 'required', 'message' => 'Введите месяц рождения'),
            array('birthdayYear', 'required', 'message' => 'Введите год рождения'),
            array('documentTypeId, genderId, seriesNumber, countryId', 'required'),
            array('documentTypeId', 'in', 'range'=>array_keys(self::getPossibleTypes())),
            array('genderId', 'in', 'range'=>array_keys(self::getPossibleGenders())),
            array('birthday', 'date', 'format' => 'dd.MM.yyyy'),
            array('srok, expirationDay, expirationMonth, expirationYear', 'safe'),
            array('expirationDate', 'validateExpirationDate'),
        ));
    }

    public function validateExpirationDate($attr)
    {
        if (!$this->srok)
            if (!checkdate(intval($this->expirationMonth), intval($this->expirationDay), intval($this->expirationYear)))
                $this->addError('expirationDate', 'Введите дату истечения документа');
            else
            {
                $date = strtotime($this->expirationMonth.'/'.$this->expirationDay.'/'.$this->expirationYear);
                $now = time();
                if ($date < $now)
                    $this->addError('expirationDate', 'Срок действия вашего документа истёк');
            }
    }
    
    public function getBirthday()
    {
        if ($this->birthdayDay<10)
            $this->birthdayDay = '0'.intval($this->birthdayDay);
        if ($this->birthdayMonth<10)
            $this->birthdayMonth = '0'.intval($this->birthdayMonth);
        return $this->birthdayDay.'.'.$this->birthdayMonth.'.'.$this->birthdayYear;
    }

    public function setBirthday($value)
    {
        $utime = strtotime($value);
        $this->birthdayDay = date('d', $utime);
        $this->birthdayMonth = date('m', $utime);
        $this->birthdayYear = date('Y', $utime);
    }

    public function getExpirationDate()
    {
        return $this->expirationDay.'.'.$this->expirationMonth.'.'.$this->expirationYear;
    }

    public function setExpirationDate($value)
    {
        $utime = strtotime($value);
        $this->expirationDay = date('d', $utime);
        $this->expirationMonth = date('m', $utime);
        $this->expirationYear = date('Y', $utime);
    }

    public function getNumber()
    {
        return $this->seriesNumber;
    }

    public function setNumber($value)
    {
        $this->seriesNumber = $value;
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
            'seriesNumber' => 'Серия и № документа',
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
            self::TYPE_OTHER => 'Другой',
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

    public function handleFields()
    {
        $oldValue = $this->seriesNumber;
        $newValue = preg_replace('/[^А-Яа-яёЁA-Za-z0-9]/', '', $oldValue);
        $newValue = str_replace('n', '', $newValue);
        $newValue = str_replace('N', '', $newValue);
        $this->seriesNumber = $newValue;
    }
}