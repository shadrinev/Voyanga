<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 27.08.12
 * Time: 13:52
 */
class ProductionModule extends CWebModule
{
    public $defaultController = 'default';

    public function init()
    {
        parent::init();
        Yii::app()->theme = 'v2';
    }
}
