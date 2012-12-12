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
                $this->_model = User::model()->findByAttributes(array('username'=>$this->name));
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
}
