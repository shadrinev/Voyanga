<?php

class NewRelicApplication extends CWebApplication
{
    public function beforeControllerAction($controller, $action)
    {
        Yii::app()->newRelic->setTransactionName($controller->id, $action->id);
        return parent::beforeControllerAction($controller, $action);
    }
}