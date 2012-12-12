<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 12.12.12
 * Time: 9:06
 */
class EmailManager
{
    static public function sendOrderInfo()
    {

    }

    static public function sendRecoveryPassword($user)
    {
        $msg = new YiiMailMessage();
        $msg->view = 'recoverPassword';
        $msg
            ->setFrom(appParams('adminEmail'), appParams('adminEmailName'))
            ->setTo($user->email)
            ->setSubject('Восстановление пароля');
        $msg->setBody(array(
            'key' => $user->generateKey(),
            'date'=> date('d/m/Y', strtotime($user->recover_pwd_expiration))
        ), 'text/html');
        Yii::app()->mail->send($msg);
    }
}
