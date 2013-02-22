<?php

/**
 * This is the model class for table "frontend_user".
 *
 * The followings are the available columns in table 'frontend_user':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $salt
 * @property string $password_strategy
 * @property integer $requires_new_password
 * @property string $email
 * @property integer $login_attempts
 * @property integer $login_time
 * @property string $login_ip
 * @property string $validation_key
 * @property integer $create_id
 * @property integer $create_time
 * @property integer $update_id
 * @property integer $update_time
 * @property string $recover_pwd_key
 * @property string $recover_pwd_expiration
 */
class FrontendUser extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return FrontendUser the static model class
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
		return 'frontend_user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('requires_new_password, login_attempts, login_time, create_id, create_time, update_id, update_time', 'numerical', 'integerOnly'=>true),
			array('username', 'length', 'max'=>45),
            array('username', 'unique'),
            array('email', 'unique'),
			array('password, salt, email, validation_key', 'length', 'max'=>255),
			array('password_strategy', 'length', 'max'=>50),
			array('login_ip, recover_pwd_key', 'length', 'max'=>32),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, username, password, salt, password_strategy, requires_new_password, email, login_attempts, login_time, login_ip, validation_key, create_id, create_time, update_id, update_time, recover_pwd_key, recover_pwd_expiration', 'safe', 'on'=>'search'),
		);
	}

    /**
     * Behaviors
     * @return array
     */
    public function behaviors()
    {
        Yii::import('common.extensions.YiiPasswords.*');
        return array(
            // Password behavior strategy
            "APasswordBehavior" => array(
                "class" => "APasswordBehavior",
                "strategyAttribute" => "password_strategy",
                "defaultStrategyName" => "bcrypt",
                "strategies" => array(
                    "bcrypt" => array(
                        "class" => "ABcryptPasswordStrategy",
                        "workFactor" => 14,
                        "minLength" => 6
                    ),
                    "legacy" => array(
                        "class" => "ALegacyMd5PasswordStrategy",
                        'minLength' => 6
                    )
                ),
            ),
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
			'username' => 'Username',
			'password' => 'Password',
			'salt' => 'Salt',
			'password_strategy' => 'Password Strategy',
			'requires_new_password' => 'Requires New Password',
			'email' => 'Email',
			'login_attempts' => 'Login Attempts',
			'login_time' => 'Login Time',
			'login_ip' => 'Login Ip',
			'validation_key' => 'Validation Key',
			'create_id' => 'Create',
			'create_time' => 'Create Time',
			'update_id' => 'Update',
			'update_time' => 'Update Time',
			'recover_pwd_key' => 'Recover Pwd Key',
			'recover_pwd_expiration' => 'Recover Pwd Expiration',
		);
	}

    /**
     * Generates a new validation key (additional security for cookie)
     */
    public function regenerateValidationKey()
    {
        $this->saveAttributes(array(
            'validation_key' => md5(mt_rand() . mt_rand() . mt_rand()),
        ));
    }

    public function generateKey()
    {
        $key = md5($this->password.$this->salt);
        $this->recover_pwd_key = $key;
        $this->recover_pwd_expiration = date('Y-m-d H:i:s', time() + 2 * 7 * 3600);
        $this->update(array('recover_pwd_key', 'recover_pwd_expiration'));
        return $key;
    }
}