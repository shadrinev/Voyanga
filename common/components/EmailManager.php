<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 12.12.12
 * Time: 9:06
 */
Yii::import('common.extensions.yii-mail.*');
class EmailManager
{
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

    static public function sendUserInfo(FrontendUser $user, $password)
    {
        $msg = new YiiMailMessage();
        $msg->view = 'newUser';
        $msg
            ->setFrom(appParams('adminEmail'), appParams('adminEmailName'))
            ->setTo($user->email)
            ->setSubject('Ваши учётные данные');
        $msg->setBody(array(
            'email' => $user->email,
            'password'=> $password,
        ), 'text/html');
        Yii::app()->mail->send($msg);
    }

    static public function sendEmailOrderInfo($params,$pdfFileNames)
    {
        $msg = new YiiMailMessage();
        $msg->view = 'orderInfo';
        $msg
            ->setFrom(appParams('adminEmail'), appParams('adminEmailName'))
            ->setTo($params['email'])
            ->setSubject('Ваш заказ готов');
        $msg->setBody($params, 'text/html');
        foreach($pdfFileNames as $key=>$pdfInfo){
            $attachment = Swift_Attachment::fromPath($pdfInfo['filename']);
            $attachment->setFilename('ticket_'.$pdfInfo['type'].'_'.$key.'.pdf');
            $msg->attach($attachment);
        }

        Yii::app()->mail->send($msg);
    }
}
