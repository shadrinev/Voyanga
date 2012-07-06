<?php
class TourModule extends CWebModule
{
    public function init()
    {
        Yii::import('site.frontend.modules.tour.models.*');
    }
}