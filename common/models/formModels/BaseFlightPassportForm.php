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
    * 4 - Svidetelstvo
    * 5 - grazdanin Drugoi strani
    */
    const TYPE_RF = 1;
    const TYPE_OTHER = 2;
    const TYPE_INTERNATIONAL = 3;
    const TYPE_BIRTH_CERT = 4;
    const TYPE_OTHER_COUNTRY = 5;

    public $documentTypeId = self::TYPE_RF;

    /** gender */
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    public $genderId;

    private $countries = array();

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
    public $bonusCard = '';
    public $bonusCardAirlineCode = '';

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
            array('srok, expirationDay, expirationMonth, expirationYear, bonusCard, bonusCardAirlineCode', 'safe'),
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

    public function setCountries($countriesIds)
    {
        $this->countries = $countriesIds;
    }

    public function setDocType($departureDate = null)
    {
        $ruCountries = true;
        //определение возможен ли тип Пасспорта РФ и Св-ва о роджд.
        $sngCountries = array(23,84,92,199,212,174);
        //ID of Countries with Expire date in Document
        $aExpCountries = array(7, 25, 40, 53, 61, 64, 79, 80, 81, 106, 111, 113, 126, 145, 150, 171, 172, 191, 192, 217, 219, 225, 227, 228, 234);
        if($this->countries){
            foreach($this->countries as $countryId){
                if(!in_array($countryId,$sngCountries)){
                    $ruCountries = false;
                }
            }
        }
        $number = $this->seriesNumber;
        $numLen = strlen($number);
        if($this->countryId == 174){
            if(!$ruCountries){
                if($numLen == 10){
                    //alarm
                }elseif($numLen == 9){
                    $this->documentTypeId = self::TYPE_INTERNATIONAL;
                }else{
                    $this->documentTypeId = self::TYPE_OTHER;
                }
            }else{
                if($numLen == 10 && is_numeric($number)){
                    //rf
                    $this->documentTypeId = self::TYPE_RF;
                }elseif($numLen == 9 && is_numeric($number)){
                    $this->documentTypeId = self::TYPE_INTERNATIONAL;
                    //zagran
                }elseif((!is_numeric($number)) && ($this->getAge($departureDate) < 14)){
                    //svidetelstvo
                    $this->documentTypeId = self::TYPE_BIRTH_CERT;
                }else{
                    //other
                    $this->documentTypeId = self::TYPE_OTHER;
                }
            }
        }else{
            $this->documentTypeId = self::TYPE_OTHER_COUNTRY;
        }
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

    public function getAge($departureDate = null)
    {
        $ret = null;
        if($departureDate){
            $depDateTime = DateTime::createFromFormat('Y-m-d', $departureDate);
        }else{
            $depDateTime = new DateTime();
        }
        if($this->getBirthday()){
            $birthdayDateTime = DateTime::createFromFormat('d.m.Y', $this->getBirthday());
            $diff = $birthdayDateTime->diff($depDateTime);
            return $diff->y;
        }
        return $ret;
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