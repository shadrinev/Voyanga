<?php
/**
 * User: Kuklin Mikhail (mikhail@clevertech.biz)
 * Company: Clevertech LLC.
 * Date: 19.08.12 22:35
 */
class FlightTripElementFrontendProcessor
{
    public static function prepareInfoForTab(FlightTripElement $flight)
    {
        $from = City::getCityByPk($flight->departureCity);
        $to = City::getCityByPk($flight->arrivalCity);

        $tab = array();
        $tab['info'] = array('type'=>'flight','flights'=>array());
        $tab['id'] = $flight->id.'_tab';
        $tab['groupId'] = $flight->getGroupId();
        $tab['label'] = '<b>Перелёт</b>';

        if($flight->flightVoyage)
        {
            $controller = Yii::app()->controller;
            $tab['content'] = $controller->renderPartial('//tour/constructor/_chosen_flight_precompiled', array('flight'=>$flight->flightVoyage->getJsonObject()), true);
            $tab['fill'] = true;
            $tab['itemOptions']['class'] = 'flight fill';
        }
        else
        {
            $tab['content'] = 'loading...';//VarDumper::dumpAsString($flight->getPassports(), 10, true);
            $tab['itemOptions']['class'] = 'flight unfill';
            $tab['fill'] = false;
        }

        return $tab;
    }

    public static function addGroupedInfoToTab($preparedFlight, $flight)
    {
        $from = City::getCityByPk($flight->departureCity);
        $to = City::getCityByPk($flight->arrivalCity);
        $preparedFlight['info']['flights'][] = array(
            'label' => '<br>'.$flight->departureDate."<br>".$from->localRu." &mdash; ".$to->localRu,
            'departureDate'=>$flight->departureDate,
            'cityFromId'=>$flight->departureCity,
            'cityToId'=>$flight->arrivalCity,
            'adultCount'=>$flight->adultCount,
            'childCount'=>$flight->childCount,
            'infantCount'=>$flight->infantCount
        );
        return $preparedFlight;
    }

    static public function buildTabLabel($current, $previous)
    {
        if (!empty($previous))
            $current['label'] .= $current['info']['flights'][0]['label'] . $current['info']['flights'][1]['label'];
        return $current;
    }
}
