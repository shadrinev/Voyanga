<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 01.08.12
 * Time: 11:49
 */
class TourViewerWidget extends CWidget
{
    public $urlToBasket = '/admin/tour/basket/show';
    public $pathToAirlineImg = 'http://frontend.voyanga/img/airlines/';

    public function run()
    {
        $this->render('tourViewer', array('urlToBasket'=>$this->urlToBasket,'pathToAirlineImg'=>$this->pathToAirlineImg));
    }
}
