<?php

class SiteController extends FrontendController
{
    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        $this->assignTitle('error');
        $this->layout = 'static';
        if ($error = Yii::app()->errorHandler->error)
        {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', array('error' => $error));
        }
        Yii::app()->end();
    }

    public function actionDeploy($key)
    {
        $secretKey = 'kasdjnfkn24r2wrn2efk';
        if ($key != $secretKey)
        {
            throw new CHttpException(404);
        }
        // Load all tables of the application in the schema
        Yii::app()->db->schema->getTables();
        // clear the cache of all loaded tables
        Yii::app()->db->schema->refresh();
        Yii::app()->clientScript->buildingMode = true;
        $myModule = Yii::app()->getModule('v2');
        Yii::app()->runController($myModule->id . '/default/index');
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionNewPassword($key = false)
    {
        if ($key)
        {
            $user = User::model()->findByAttributes(array('recover_pwd_key' => $key));
            if (($user) && (time() < strtotime($user->recover_pwd_expiration)))
            {
                $model = new NewPasswordForm();
                if (isset($_POST['NewPasswordForm']))
                {
                    $model->attributes = $_POST['NewPasswordForm'];
                    if ($model->validate())
                    {
                        $user->password = $model->password;
                        $user->recover_pwd_key = '';
                        $user->recover_pwd_expiration = date('Y-m-d h:i:s', time() - 1);
                        if ($user->save())
                            Yii::app()->user->setFlash('success', 'You have successfully changed your password. You may now use it to login to Present Value.');
                        else
                            $model->addErrors($user->errors);
                    }
                }
                $this->render('newPassword', array('model' => $model));
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
                    $user = User::model()->findByAttributes(array('email' => $model->email));
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
            $this->render('recoveryPassword', array('model' => $model));
        }
    }

    public function actionIata()
    {
        $this->assignTitle('iata');
        $this->layout = 'static';
        $this->render('iata');
        Yii::app()->end();
    }

    public function actionAgreement_avia()
    {
        $this->assignTitle('agreementAvia');
        $this->layout = 'static';
        $this->render('agreement_avia');
        exit;
    }

    public function actionAgreement_hotel()
    {
        $this->assignTitle('agreementHotel');
        $this->layout = 'static';
        $this->render('agreement_hotel');
        Yii::app()->end();
    }

    public function actionAgreement()
    {
        $this->assignTitle('agreement');
        $this->layout = 'static';
        $this->render('agreement');
        Yii::app()->end();
    }

    public function actionMemcache()
    {
        $memcache = new Memcache;
        $memcache->connect('localhost', 11211) or die ("Could not connect to memcached server");
        $list = array();
        $allSlabs = $memcache->getExtendedStats('slabs');
        $items = $memcache->getExtendedStats('items');
        foreach ($allSlabs as $server => $slabs)
        {
            foreach ($slabs AS $slabId => $slabMeta)
            {
                $cdump = $memcache->getExtendedStats('cachedump', (int)$slabId);
                foreach ($cdump AS $keys => $arrVal)
                {
                    if ($arrVal)
                    {
                        foreach ($arrVal AS $k => $v)
                        {
                            $list[$k] = array(
                                'key' => $k,
                                //ключ
                                'server' => $server,
                                'slabId' => $slabId,
                                'detail' => $v,
                                'age' => $items[$server]['items'][$slabId]['age']
                            );
                        }
                    }
                }
            }
        }
        CVarDumper::dump($list);
    }
}
