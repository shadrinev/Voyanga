<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 12.12.12
 * Time: 9:17
 */
class UserController extends CController
{
    public $layout = 'static';

    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
        );
    }

    public function accessRules()
    {
        return array(
            array('allow', 'actions' => array('test','createTestUser', 'newPassword', 'login', 'validate', 'validateForgetPassword', 'signup')),
            array('allow', 'actions' => array('orders', 'logout'), 'users' => array('@')),
            array('deny'),
        );
    }

    public function actionSignup()
    {
        $model = new SignUpForm();
        if (isset($_POST['SignUpForm']))
        {
            $model->attributes = $_POST['SignUpForm'];
            if ($model->validate())
            {
                $user = new FrontendUser();
                $user->username = $model->email;
                $user->email = $model->email;
                $user->password = $model->password;
                $isExist = FrontendUser::model()->find(array(
                    'condition' => 'email = :email OR username = :username',
                    'params' => array(
                        ':email' => $user->email,
                        ':username' => $user->email
                    )
                ));
                if ($isExist)
                {
                    echo json_encode(array(
                        'status' => 'error',
                        'error' => 'Пользователь с указанным e-mail уже зарегистрирован на сайте'
                    ));
                }
                elseif ($user->save())
                {
                    EmailManager::sendUserInfo($user, $model->password);
                    echo '{"status" : "ok"}';
                }
                else
                {
                    echo json_encode(array(
                        'status' => 'error',
                        'error' => CHtml::errorSummary($user)
                    ));
                }
            }
            else
            {
                echo json_encode(array(
                    'status' => 'error',
                    'error' => CHtml::errorSummary($model)
                ));
            }
        }
    }

    public function actionCreateTestUser($email)
    {
        /* add demo users */
        $demoUser = new FrontendUser();
        $demoUser->username = "mihan007";
        $demoUser->email = $email;
        $password = $email . '123';
        $demoUser->password = $password;
        $demoUser->save();
        echo 'Ошибки:';
        VarDumper::dump($demoUser->errors);
        if (sizeof($demoUser->errors) == 0)
        {
            echo '<h1>Новый пользователь успешно создан</h1>';
            echo '<h2>Логин:' . $email . '</h2>';
            echo '<h2>Пароль:' . $password . '</h2>';
        }
    }

    public function actionNewPassword($key = false)
    {
        if ($key)
        {
            $user = FrontendUser::model()->findByAttributes(array('recover_pwd_key' => $key));
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
                            echo '{"status": "ok"}';
                        else
                            echo json_encode(array(
                                'status' => 'fail',
                                'errors' => CHtml::errorSummary($user)
                            ));
                    }
                    else
                        echo json_encode(array(
                            'status' => 'fail',
                            'errors' => CHtml::errorSummary($model)
                        ));
                }
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
                    $user = FrontendUser::model()->findByAttributes(array('email' => $model->email));
                    if ($user)
                    {
                        Yii::app()->user->setFlash('success', 'Вы получите письмо с инструкциями как восстановить ваш пароль.');
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

    public function actionOrders()
    {
        $criteria = new CDbCriteria();
        $criteria->order = 'timestamp desc';
        $criteria->addColumnCondition(array('userId'=>Yii::app()->user->id));

        $dataProvider = new CActiveDataProvider('OrderBooking', array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 100
            )
        ));
        $this->render('orders', array(
            'model' => $dataProvider
        ));
    }

    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        $model = new LoginForm;

        // collect user input data

        if (isset($_POST['LoginForm']))
        {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login())
            {
                $url = Yii::app()->user->returnUrl;
                if ($url == '/index.php')
                    $this->redirect('/user/orders');
                else
                    $this->redirect($url);
            }
        }

        $this->redirect('/');
    }

    public function actionValidate()
    {
        $model = new LoginForm();
        if (isset($_POST['LoginForm']))
        {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate())
            {
                echo '{"status" : "ok"}';
                Yii::app()->end();
            }
            else
            {
                echo json_encode(array(
                    'status' => "fail",
                    'errors' => CHtml::errorSummary($model)
                ));
                Yii::app()->end();
            }
        }
        echo '{status: "fail", errors: "Необходимо указать логин и пароль"}';
    }

    public function actionValidateForgetPassword()
    {
        $model = new ForgotPasswordForm();
        if (isset($_POST['ForgotPasswordForm']))
        {
            $model->attributes = $_POST['ForgotPasswordForm'];
            $email = $model->email;
            $user = FrontendUser::model()->findByAttributes(array('email'=>$email));
            if (!$model->validate())
            {
                echo json_encode(array(
                    'status' => "fail",
                    'errors' => CHtml::error($model, 'email')
                ));
                Yii::app()->end();
            }
            elseif (!$user)
            {
                echo json_encode(array(
                    'status' => "fail",
                    'errors' => 'Пользователь с таким e-mail не зарегистрирован'
                ));
                Yii::app()->end();
            }
            else
            {
                echo '{"status" : "ok"}';
                Yii::app()->end();
            }
        }
        echo '{status: "fail", errors: "Ошибка восстановления пароля"}';
    }


    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect('/');
    }
}
