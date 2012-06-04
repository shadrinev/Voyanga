<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 04.06.12
 * Time: 13:29
 */
class NotificationManager extends CComponent
{
    private $user;
    private $notificationType;
    private $notificationObject;
    private $key;

    public function add($user, $notificationType, $notificationObject, $time)
    {
        $notificatonType = new $notificationType;
        foreach($notificationType->getNotifications() as $type=>$params)
            Yii::app()->cron->addTask('notification.'.$notificationType, $this->getKey(), $type, $params, $time);
    }

    public function edit($user, $notificationType, $notificationObject, $time)
    {
        $notificatonType = new $notificationType;
        foreach($notificationType->getNotifications() as $type=>$params)
            Yii::app()->cron->editTask('notification.'.$notificationType, $this->getKey(), $type, $params, $time);
    }

    public function delete($user, $notificationType, $notificationObject)
    {
        $notificatonType = new $notificationType;
        foreach($notificationType->getNotifications() as $type=>$params)
            Yii::app()->cron->deleteTask('notification.'.$notificationType, $this->getKey());
    }

    public function getKey()
    {
        return $this->getUser()->id . $this->getNotificationType() . $this->getNotificationObject()->id;
    }

    public function setKey($value)
    {
        $this->key = $value;
    }

    public function getNotificationType()
    {
        return $this->notificationType;
    }

    public function setNotificationType($value)
    {
        $this->notificationType = $value;
    }

    public function getNotificationObject()
    {
        return $this->notificationType;
    }

    public function setNotificationObject($value)
    {
        $this->notificationObject = $value;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($value)
    {
        $this->user = $value;
    }
}
