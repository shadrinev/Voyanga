<?php
/**
 * A dummy user controller class
 * @package packages.users.controllers
 */
class UserController extends AUserController
{
    public function actionIndex()
    {
        if (Yii::app()->user->isGuest)
        {
            $this->redirect(array('user/login'));
        }
        else
        {
            $this->redirect(array('user/account'));
        }
    }

    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(array('user/login'));
    }

}
