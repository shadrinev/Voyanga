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

    private $category;

    public function add($time)
    {
        foreach($this->getNotificationType()->getNotifications() as $type=>$params)
            Yii::app()->cron->addTask($this->getCategory(), $this->getKey(), $type, $params, $time);
    }

    public function edit($user, $notificationType, $notificationObject, $time)
    {
        foreach($this->getNotificationType()->getNotifications() as $type=>$params)
            Yii::app()->cron->editTask($this->getCategory(), $this->getKey(), $type, $params, $time);
    }

    public function delete($user, $notificationType, $notificationObject)
    {
        foreach($this->getNotificationType()->getNotifications() as $type=>$params)
            Yii::app()->cron->deleteTask($this->getCategory(), $this->getKey());
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
        $this->category = $value;
        $this->notificationType = new $value;
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

    public function getCategory()
    {
        return $this->category;
    }
}
