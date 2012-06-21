<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 31.05.12
 * Time: 13:22
 */
class Notification extends CApplicationComponent
{
    public function init()
    {
        // Register the bootstrap path alias.
		if (!Yii::getPathOfAlias('notification'))
			Yii::setPathOfAlias('notification', realpath(dirname(__FILE__)));
        Yii::import('notification.*');
        Yii::import('notification.type.*');
    }

    public function handle(Event $event)
    {
        $manager = new NotificationManager;
        $notificationType = $event->name;
        $manager->setNotificationType($notificationType);
        $manager->setUser($event->args[0]);
        if (isset($event->args[1]))
        {
            $notificationObject = $event->args[1];
            $manager->setNotificationObject($notificationObject);
        }
        if (isset($event->args[1]))
        {
            $time = $event->args[2];
            $manager->add($time);
        }
    }
}