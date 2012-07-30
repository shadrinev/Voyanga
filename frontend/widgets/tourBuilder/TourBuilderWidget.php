<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 24.07.12
 * Time: 12:39
 */
class TourBuilderWidget extends CWidget
{
    public $model;
    public $attribute;

    private $assetsUrl;

    public function init()
    {
        if($this->assetsUrl===null)
            $this->assetsUrl = Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/assets',false,-1,YII_DEBUG);
        Yii::app()->getClientScript()->registerScriptFile($this->assetsUrl.'/'.'attachedTrips.js');
    }

    public function run()
    {
        $this->render('template', array('trips'=>$this->model->{$this->attribute}, 'form'=>new BootActiveForm));
    }
}
