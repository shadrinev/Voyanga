<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 17.07.12
 * Time: 10:05
 */
class AviaSearchWidget extends CWidget
{

    public $model;

    public $attribute;

    public $assetsUrl;

    public $form;

    public function init()
    {
        if($this->assetsUrl===null)
            $this->assetsUrl = Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/assets',false,-1,YII_DEBUG);
        Yii::app()->getClientScript()->registerScriptFile($this->assetsUrl.'/'.'attachedRoutes.js');
    }

    public function run()
    {
        $this->render('template', array('links'=>$this->model->{$this->attribute}, 'form'=>$this->form));
    }

}
