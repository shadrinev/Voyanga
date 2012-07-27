<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 25.07.12
 * Time: 13:20
 * To change this template use File | Settings | File Templates.
 */
class TimelineCalendarWidget extends CWidget
{
    public $eventsTimeline;

    private $assetsUrl;

    public function init()
    {
        if($this->assetsUrl===null)
            $this->assetsUrl = Yii::app()->getAssetManager()->publish(dirname(__FILE__).'/assets',false,-1,YII_DEBUG);
        Yii::app()->getClientScript()->registerScriptFile($this->assetsUrl.'/'.'jquery-ui-1.8.22.custom.min.js');
        Yii::app()->getClientScript()->registerScriptFile($this->assetsUrl.'/'.'jquery.easing.1.3.js');
        Yii::app()->getClientScript()->registerScriptFile($this->assetsUrl.'/'.'timelineCalendar.js');
        Yii::app()->getClientScript()->registerCssFile($this->assetsUrl.'/'.'timelineCalendar.css');
    }

    public function run()
    {
        $this->render('template', array('routes'=>$this->eventsTimeline));
    }
}
