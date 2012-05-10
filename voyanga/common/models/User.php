<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 10.05.12
 * Time: 12:08
 */
class User extends AUser
{
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
        );
    }
}
