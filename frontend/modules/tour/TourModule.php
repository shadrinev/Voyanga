<?php
class TourModule extends CWebModule
{
    public $defaultController = 'constructor';

    public function init()
    {
        Yii::import('site.frontend.modules.tour.models.*');
    }
}