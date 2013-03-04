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
            ->setBcc(array("orders@voyanga.com"))
            ->setSubject('Заказ номер '.$params['orderBookingId'].' готов');
        $msg->setBody($params, 'text/html');
        foreach($pdfFileNames as $key=>$pdfInfo){
            $attachment = Swift_Attachment::fromPath($pdfInfo['filename']);
            $attachment->setFilename($pdfInfo['visibleName']);
            $msg->attach($attachment);
        }

        Yii::app()->mail->send($msg);
        foreach($pdfFileNames as $key=>$pdfInfo){
            if(file_exists($pdfInfo['filename']))
            {
                unlink($pdfInfo['filename']);
            }
        }

    }

    static public function sendOrderCanceled($params)
    {
        $msg = new YiiMailMessage();
        $msg->view = 'orderCanceled';
        $msg
            ->setFrom(appParams('adminEmail'), appParams('adminEmailName'))
            ->setTo($params['email'])
            ->setSubject('Заказ номер '.$params['orderBookingId'].' отменен');
        $msg->setBody(array(
        ), 'text/html');
        Yii::app()->mail->send($msg);
    }



}
