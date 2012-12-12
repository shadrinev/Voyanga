<?php
/**
 * User: Kuklin Mikhail (mikhail@clevertech.biz)
 * Company: Clevertech LLC.
 * Date: 21.09.12 20:46
 */
class WebUser extends CWebUser
{
    private $_model;
    private $_id;

    public function getModel()
    {
        if (!$this->isGuest)
        {
            if (!$this->_model)
            {
                $this->_model = FrontendUser::model()->findByAttributes(array('username'=>$this->name));
            }
        }
        return $this->_model;
    }

    public function getId()
    {
        if (!$this->isGuest)
        {
            $model = $this->getModel();
            if ($model)
                $this->_id = $model->id;
        }
        return $this->_id;
    }

    public function getUserWithEmail($email)
    {
        $user = $this->checkIfExists($email);
        if (!$user)
            $user = $this->createNew($email);
        return $user;
    }

    public function checkIfExists($email)
    {
        $user = FrontendUser::model()->findByAttributes(array('email'=>$email));
        return $user;
    }
    
    public function createNew($email)
    {
        $newUser = new FrontendUser();
        $newUser->username = $email;
        $newUser->email = $email;
        $password = PasswordGenerator::createSimple();
        $newUser->password = $password;
        $newUser->save();
        if ($newUser->save())
        {
            EmailManager::sendUserInfo($newUser, $password);
        }
        return $newUser;
    }
}
