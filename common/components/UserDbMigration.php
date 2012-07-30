<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 10.05.12
 * Time: 12:20
 */
class UserDbMigration extends CDbMigration
{
    private $_db;

    public function getDbConnection()
    {
        if($this->_db===null)
        {
            $this->_db=Yii::app()->getComponent('userDb');
            if(!$this->_db instanceof CDbConnection)
                throw new CException(Yii::t('yii', 'The "userDb" application component must be configured to be a CDbConnection object.'));
        }
        return $this->_db;
    }
}
