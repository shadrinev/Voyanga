<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 29.08.12
 * Time: 14:28
 * To change this template use File | Settings | File Templates.
 */
/**
 * This is the model class for table "partner".
 *
 * The followings are the available columns in table 'partner':
 * @property integer $id
 * @property string $name
 * @property string $password
 * @property string $salt
 * @property string $passwordStrategy
 * @property integer $requiresNewPassword
 * @property integer $cookieTime
 */
class Partner extends CActiveRecord
{
    private static $currentPartner = null;

    public function behaviors()
    {
        Yii::import('site.common.extensions.YiiPasswords.*');
        return array(
            "APasswordBehavior" => array(
                "class" => "common.extensions.YiiPasswords.APasswordBehavior",
                "defaultStrategyName" => "bcrypt",
                "strategies" => array(
                    "bcrypt" => array(
                        "class" => "common.extensions.YiiPasswords.ABcryptPasswordStrategy",
                        "workFactor" => 14
                    ),
                    "legacy" => array(
                        "class" => "common.extensions.YiiPasswords.ALegacyMd5PasswordStrategy",
                    )
                ),
            )
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Partner the static model class
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
        return 'partner';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, password, passwordStrategy, requiresNewPassword', 'required'),
            array('requiresNewPassword, cookieTime', 'numerical', 'integerOnly'=>true),
            array('name, password', 'length', 'max'=>45),
            array('salt', 'length', 'max'=>15),
            array('passwordStrategy', 'length', 'max'=>40),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, password, salt, passwordStrategy, requiresNewPassword, cookieTime', 'safe', 'on'=>'search'),
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
            'name' => 'Имя',
            'password' => 'Пароль',
            'salt' => 'Salt',
            'passwordStrategy' => 'Password Strategy',
            'requiresNewPassword' => 'Requires New Password',
            'cookieTime' => 'Время сохранения ключа(в днях)',
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
        $criteria->compare('name',$this->name,true);
        $criteria->compare('password',$this->password,true);
        $criteria->compare('salt',$this->salt,true);
        $criteria->compare('passwordStrategy',$this->passwordStrategy,true);
        $criteria->compare('requiresNewPassword',$this->requiresNewPassword);
        $criteria->compare('cookieTime',$this->cookieTime);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Функция зашифровки id в строку
     * @param integer $id
     * @return string
     */
    private static function encodeId($id)
    {
        $chars = 'a2bc3de4fg5hk6mn7pq8su9vxyz'; // Используем непохожие друг на друга символы
        $length = 27; //strlen($chars); // если изменяем набор символов, то число нужно изменить
        $hash = '';
        while ($id > $length - 1)
        {
            $hash = $chars[fmod($id, $length)] . $hash;
            $id = floor($id / $length);
        }
        return $chars[$id] . $hash;
    }

    /**
     * Функция расшифровки id из строки
     * @param string $string
     * @return integer
     */
    private static function decodeId($string)
    {
        $chars = 'a2bc3de4fg5hk6mn7pq8su9vxyz'; // Используем непохожие друг на друга символы
        $length = 27; //strlen($chars); // если изменяем набор символов, то число нужно изменить
        $size = strlen($string) - 1;
        $array = str_split($string);
        $id = strpos($chars, array_pop($array));
        foreach ($array as $i => $char)
        {
            $id += strpos($chars, $char) * pow($length, $size - $i);
        }
        return $id;
    }

    /**
     * Функция расшифровки id из строки
     * @param string $string
     * @return integer
     */
    public function generatePassword($length = 10)
    {
        $chars = 'a2bc3de4fg5hk6mn7pq8su9vxyz'; // Используем непохожие друг на друга символы
        $lenChars = strlen($chars); // если изменяем набор символов, то число нужно изменить
        $newPassword = '';
        for($i = 0; $i<$length; $i++)
        {
            $randomIndex = rand(0,$lenChars-1);
            $newPassword .= $chars[$randomIndex];
        }
        return $newPassword;
    }

    public function getPartnerKey(){
        return self::encodeId(($this->id + 10100));
    }

    public static function getPartnerByKey($key){
        $id = self::decodeId($key);
        $id = $id - 10100;
        $partner = Partner::model()->findByPk($id);
        if($partner) return $partner;
        return false;
    }

    public static function setPartnerByKey(){
        $partner = false;
        if(isset($_REQUEST['pid'])){
            if($partner = self::getPartnerByKey($_REQUEST['pid'])){
                setcookie('partnerKey',$_REQUEST['pid'],time()+3600*24*$partner->cookieTime);
                $_SESSION['partnerKey'] = $_REQUEST['pid'];
            }
        }
        if(isset($_COOKIE['partnerKey']) && !($partner)){
            if($partner = self::getPartnerByKey($_COOKIE['partnerKey'])){
                $_SESSION['partnerKey'] = $_COOKIE['partnerKey'];
            }
        }
        if(isset($_SESSION['partnerKey']) && !($partner)){
            $partner = self::getPartnerByKey($_SESSION['partnerKey']);
        }
        if($partner){
            self::$currentPartner = $partner;
        }
    }

    public static function getCurrentPartner(){
        return self::$currentPartner;
    }
}