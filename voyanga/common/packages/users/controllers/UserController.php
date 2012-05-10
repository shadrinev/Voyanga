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
            $this->redirect("user/login");
        }
        else
        {
            $this->redirect("user/account");
        }
    }

}
