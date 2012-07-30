<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 10.05.12
 * Time: 12:08
 */
class User extends AUser
{
    /**
     * @return array validation rules for model attributes.
     */
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return CMap::mergeArray(parent::rules(), array(
            array('requireNewPassword', 'numerical', 'integerOnly'=>true),
            array('name, salt, password, email', 'length', 'max'=>255),
            // The following rule is used by search().
            // Please remove those attributes that should not be searched.
            array('id, name, salt, password, email, requireNewPassword', 'safe', 'on'=>'search'),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Route the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'user';
    }

    public function attributeLabels()
    {
        return array(
            'name' => Yii::t('admin', 'Имя'),
            'password' => Yii::t('admin', 'Пароль'),
            'email' => Yii::t('admin', 'e-mail'),
            'thumbnail' => Yii::t('admin', 'Аватар')
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
        $criteria->compare('salt',$this->salt,true);
        $criteria->compare('password',$this->password,true);
        $criteria->compare('email',$this->email,true);
        $criteria->compare('requireNewPassword',$this->requireNewPassword);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination' => array(
                'pageSize' => 10
            )
        ));
    }
}
