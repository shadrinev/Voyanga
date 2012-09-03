<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 24.07.12
 * Time: 12:39
 */
class CityListWidget extends CWidget
{
    public $model;
    public $attribute;
    public $attributeId;
    public $attributeReadable;

    private $items;
    private $assetsUrl;

    public function init()
    {
        if($this->assetsUrl===null)
            $this->assetsUrl = Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/assets',false,-1,YII_DEBUG);
        Yii::app()->getClientScript()->registerScriptFile($this->assetsUrl.'/'.'attachedItems.js');
    }

    public function run()
    {
        $newModelClass = 'EventStartCityForm';
        $newModel = new $newModelClass;
        $this->render('template', array(
            'newItem'=>$newModel,
            'model'=>$this->model,
            'attribute'=>$this->attribute,
            'attributeId'=>$this->attributeId,
            'attributeReadable'=>$this->attributeReadable,
            'form'=>new BootActiveForm
        ));
    }
}
