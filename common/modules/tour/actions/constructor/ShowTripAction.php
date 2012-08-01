<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 01.08.12
 * Time: 11:06
 */
class ShowTripAction extends CAction
{
    public function run()
    {
        $trip = Yii::app()->order->getPositions(false);
        //VarDumper::dump($trip);
        //die();
        $tabs = array();
        foreach ($trip['items'] as $item)
        {
            if ($item instanceof FlightTripElement)
            {
                /** @var $item FlightTripElement */
                $from = City::getCityByPk($item->departureCity);
                $to = City::getCityByPk($item->arrivalCity);
                $tab['label'] = '<b>Перелёт</b><br>'.$item->departureDate."<br>".$from->localRu." &mdash; ".$to->localRu;
                $tab['content'] = VarDumper::dumpAsString($item->getPassports(), 10, true);
                $tabs[] = $tab;
            }
            if ($item instanceof HotelTripElement)
            {
                /** @var $item HotelTripElement */
                $from = City::getCityByPk($item->city);
                $tab['label'] = '<b>Отель в городе '.$from->localRu.'</b><br>'.$item->checkIn." &mdash; ".$item->checkOut;
                $tab['content'] = VarDumper::dumpAsString($item->getPassports(), 10, true);
                $tabs[] = $tab;
            }
        }
        if (isset($tabs[0]))
            $tabs[0]['active'] = true;
        $this->controller->render('showTrip', array('tabs'=>$tabs));
    }
}
