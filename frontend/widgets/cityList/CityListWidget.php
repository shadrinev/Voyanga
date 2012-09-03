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
    public $attributeReadable;

    private $items;
    private $assetsUrl;

    public function init()
    {
        if($this->assetsUrl===null)
            $this->assetsUrl = Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/assets',false,-1,YII_DEBUG);
        Yii::app()->getClientScript()->registerScriptFile($this->assetsUrl.'/'.'attachedItems.js');
        $this->normalize();
    }

    public function run()
    {
        $newModelClass = get_class($this->model);
        $newModel = new $newModelClass;
        $this->render('template', array(
            'newItem'=>$newModel,
            'model'=>$this->model,
            'items'=>$this->items,
            'attribute'=>$this->attribute,
            'attributeReadable'=>$this->attributeReadable,
            'readableItems'=>$this->model->{$this->attributeReadable},
            'form'=>new BootActiveForm
        ));
    }

    public function normalize()
    {
        if ($this->model->{$this->attribute}==null)
            $this->items = array();
        else
            $this->items = $this->model->{$this->attribute};
    }
}
