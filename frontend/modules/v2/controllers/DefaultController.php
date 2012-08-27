<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 27.08.12
 * Time: 13:59
 */
class DefaultController extends CController
{
    public function actionIndex()
    {
        echo "Please use only CHtml::link(), Yii:app()->createAbsoluteLink() and other which uses CUrlManager to create ___ALL__ links. So moving controllers out here bring us less pain.";
    }
}
