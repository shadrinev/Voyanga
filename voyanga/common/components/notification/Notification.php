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
			Yii::setPathOfAlias('notification', realpath(dirname(__FILE__).'/..'));
        Yii::import('notification.type.*');
    }

    public function handle(Event $event)
    {
        $notificationType = $event->name;
        $user = $event->args[0];
        $notificationObject = $event->args[1];
        $time = $event->args[2];

        $manager = new NotificationManager;
        $manager->add($user, $notificationType, $notificationObject, $time);
    }
}