<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 12.12.12
 * Time: 9:17
 */
class UserController extends CController
{
    public function actionNewPassword($key=false)
    {
        if ($key)
        {
            $user = FrontendUser::model()->findByAttributes(array('recover_pwd_key'=>$key));
            if (($user) && (time()<strtotime($user->recover_pwd_expiration)))
            {
                $model = new NewPasswordForm();
                if (isset($_POST['NewPasswordForm']))
                {
                    $model->attributes = $_POST['NewPasswordForm'];
                    if ($model->validate())
                    {
                        $user->password = $model->password;
                        $user->recover_pwd_key = '';
                        $user->recover_pwd_expiration = date('Y-m-d h:i:s', time()-1);
                        if ($user->save())
                            Yii::app()->user->setFlash('success', 'You have successfully changed your password. You may now use it to login to Present Value.');
                        else
                            $model->addErrors($user->errors);
                    }
                }
                $this->render('newPassword', array('model'=>$model));
            }
            else
            {
                throw new CHttpException(404, 'Not found or this link already expired');
            }
        }
        else
        {
            $model = new ForgotPasswordForm();
            if (isset($_POST['ForgotPasswordForm']))
            {
                $model->attributes = $_POST['ForgotPasswordForm'];
                if ($model->validate())
                {
                    $user = FrontendUser::model()->findByAttributes(array('email'=>$model->email));
                    if ($user)
                    {
                        Yii::app()->user->setFlash('success', 'You will receive an email shortly with instructions to create a new password.');
                        EmailManager::sendRecoveryPassword($user);
                        $this->refresh();
                    }
                    else
                        $model->addError('email', 'Email address not found');
                }
            }
            $this->render('recoveryPassword', array('model'=>$model));
        }
    }
}
