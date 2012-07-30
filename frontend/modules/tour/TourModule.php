<?php
class TourModule extends CWebModule
{
    public $defaultController = 'Constructor';

    public function init()
    {
        Yii::import('site.frontend.modules.tour.models.*');
    }
}