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
        if(isset($trip['items']))
        {
            $tabItems = array();
            foreach ($trip['items'] as $item)
            {
                if ($item instanceof FlightTripElement)
                {
                    if(isset($tabItems[$item->getGroupId()]))
                    {
                        $tabItems[$item->getGroupId()][] = $item;
                    }
                    else
                    {
                        $tabItems[$item->getGroupId()] = array($item);
                    }
                }
                elseif($item instanceof HotelTripElement)
                {
                    $tabItems[] = $item;
                }
            }
            foreach ($tabItems as $tabItem)
            {
                if ($tabItem instanceof HotelTripElement)
                {
                    /** @var $tabItem HotelTripElement */
                    $from = City::getCityByPk($tabItem->city);
                    $tab = array();
                    $tab['label'] = '<b>Отель в городе '.$from->localRu.'</b><br>'.$tabItem->checkIn." &mdash; ".$tabItem->checkOut;
                    $tab['id'] = $tabItem->id.'_tab';
                    $tab['info'] = array(
                        'type'=>'hotel',
                        'cityId'=>$tabItem->city,
                        'checkIn'=>$tabItem->checkIn,
                        'checkOut'=>$tabItem->checkOut,
                        'adultCount'=>$tabItem->adultCount,
                        'childCount'=>$tabItem->childCount,
                        'infantCount'=>$tabItem->infantCount,

                    );
                    if($tabItem->hotel)
                    {
                        $tab['content'] = '123';//VarDumper::dumpAsString($tabItem->hotel, 10, true);
                        $tab['itemOptions']['class'] = 'hotel fill';
                        //$tab['fill'] = true;
                    }
                    else
                    {
                        $tab['content'] = '123';//VarDumper::dumpAsString($tabItem->getPassports(), 10, true);
                        $tab['itemOptions']['class'] = 'hotel unfill';
                        //$tab['fill'] = false;
                    }

                    $tabs[] = $tab;
                }
                else
                {
                    $tab = array();
                    $tab['label'] = '<b>Перелёт</b>';
                    $tab['info'] = array('type'=>'flight','flights'=>array());

                    /** @var $tabItem FlightTripElement[] */
                    foreach($tabItem as $item)
                    {
                        if(!isset($tab['id']))
                        {
                            $tab['id'] = $item->id.'_tab';
                        }
                        if(!isset($tab['groupId']))
                        {
                            $tab['groupId'] = $item->getGroupId();
                        }
                        $from = City::getCityByPk($item->departureCity);
                        $to = City::getCityByPk($item->arrivalCity);
                        $tab['label'] .='<br>'.$item->departureDate."<br>".$from->localRu." &mdash; ".$to->localRu;
                        $tab['info']['flights'][] = array(
                            'departureDate'=>$item->departureDate,
                            'cityFromId'=>$item->departureCity,
                            'cityToId'=>$item->arrivalCity,
                            'adultCount'=>$item->adultCount,
                            'childCount'=>$item->childCount,
                            'infantCount'=>$item->infantCount);
                    }

                    if($item->flightVoyage)
                    {
                        $tab['content'] = $this->controller->renderPartial('_choosed_flight_precompiled', array('flight'=>json_decode(json_encode($item->flightVoyage->getJsonObject()))), true);//VarDumper::dumpAsString($item->flightVoyage, 10, true);
                        $tab['fill'] = true;
                        $tab['itemOptions']['class'] = 'flight fill';

                    }
                    else
                    {
                        $tab['content'] = 'loading...';//VarDumper::dumpAsString($item->getPassports(), 10, true);
                        $tab['itemOptions']['class'] = 'flight unfill';
                        $tab['fill'] = false;
                    }

                    $tabs[] = $tab;
                }
            }
        }
        if (isset($tabs[0]))
            $tabs[0]['active'] = true;
        Yii::app()->getClientScript()->registerScriptFile('/js/constructorViewer.js');
        $this->controller->render('showTrip', array('tabs'=>$tabs));
    }
}
