<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 09.06.12
 * Time: 16:23
 */
class HandlebarsWidget extends CWidget
{
    public $id;

    public function init()
    {
        $assets = dirname(__FILE__) . '/assets';
        $baseUrl = Yii::app()->assetManager->publish($assets);
        Yii::app()->clientScript->registerScriptFile($baseUrl . '/handlebars-1.0.0.beta.6.js', CClientScript::POS_HEAD);
        if ($this->id==null)
            $this->id = $this->getId();
        ob_start();
        echo "<script id='{$this->id}' type='voyanga/handlebars-template'>";
    }

    public function run()
    {
        echo "</script>";
        ob_end_flush();
    }
}
