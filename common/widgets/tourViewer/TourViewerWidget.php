<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 01.08.12
 * Time: 11:49
 */
class TourViewerWidget extends CWidget
{
    public $orderId;
    public $urlToBasket = '/admin/tour/basket/show';
    public $urlToConstructor = '/admin/tour/constructor/create';
    public $pathToAirlineImg = 'http://test.voyanga.com/img/airlines/';

    public function init()
    {

    }

    public function run()
    {
        $this->render('tourViewer', array(
            'urlToBasket'=>$this->urlToBasket,
            'pathToAirlineImg'=>$this->pathToAirlineImg,
            'urlToConstructor' => $this->urlToConstructor,
            'suffix' => $this->id,
            'orderId' => $this->orderId
        ));
    }
}
