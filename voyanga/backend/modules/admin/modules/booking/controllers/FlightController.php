<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 20.06.12
 * Time: 13:00
 */
class FlightController extends ABaseAdminController
{
    public function actionIndex()
    {
        $flightForm = new FlightForm;
        $this->render('index', array('items'=>$this->generateItems(), 'flightForm'=>$flightForm, 'autosearch'=>false, 'fromCityName'=>'', 'toCityName'=>''));
    }

    public function actionSearch($from, $to, $date)
    {
        $flightForm = new FlightForm;
        $flightForm->departureCityId = $from;
        $flightForm->arrivalCityId = $to;
        $flightForm->departureDate = $date;
        $fromCityName = City::model()->findByPk($from)->localRu;
        $toCityName = City::model()->findByPk($to)->localRu;
        $this->render('index', array(
            'items'=>$this->generateItems(),
            'flightForm'=>$flightForm,
            'autosearch'=>true,
            'fromCityName'=>$fromCityName,
            'toCityName'=>$toCityName
        ));
    }

    public function generateItems()
    {
        $elements = Yii::app()->user->getState('lastSearches');
        $items = array();
        if (!is_array($elements))
            return $items;
        foreach ($elements as $element)
        {
            $item = array(
                'label' => City::model()->getCityByPk($element[0])->localRu . '&nbsp;&rarr;&nbsp;' . City::model()->getCityByPk($element[1])->localRu . '<br>(' . $element[2] .')',
                'url' => '/admin/booking/flight/search/from/'.$element[0].'/to/'.$element[1].'/date/'.$element[2],
                'encodeLabel' => false
            );
            $items[] = $item;
        }
        return $items;
    }
}
