<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 01.06.12
 * Time: 17:57
 */
class OnFlightBooking extends NotificationType
{
    public function getNotifications()
    {
        return array(
            'email' => array(
                'subject' => 'Raised OnFlightBooking event',
                'body' => "Yahoo!"
            ),
            'sms' => array(
                'text' => 'Sms of raised onFlightBooking sent'
            )
        );
    }
}
