<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 20.06.12
 * Time: 13:00
 */
class FlightController extends FrontendController
{
    public $tab = 'avia';

    private $flightBooker;

    public function actions()
    {
        return array(
            'buy' => array(
                'class' => 'common.components.flightBooker.actions.FlightEngineAction',
            ),
            'stageEnterCredentials' => array(
                'class' => 'common.components.flightBooker.actions.FlightEnterCredentialsAction',
            ),
        );
    }


    public function actionIndex($isTab=false)
    {
        $flightForm = new FlightForm;
        if ($isTab)
            $this->renderPartial('index', array('items'=>$this->generateItems(), 'flightForm'=>$flightForm, 'autosearch'=>false, 'fromCityName'=>'', 'toCityName'=>''));
        else
            $this->render('index', array('items'=>$this->generateItems(), 'flightForm'=>$flightForm, 'autosearch'=>false, 'fromCityName'=>'', 'toCityName'=>''));
    }

    public function actionSearch($from, $to, $date)
    {
        $flightForm = new FlightForm;
        $flightForm->departureCityId = $from;
        $flightForm->arrivalCityId = $to;
        $flightForm->departureDate = $date;
        $fromCityName = City::getCityByPk($from)->localRu;
        $toCityName = City::getCityByPk($to)->localRu;
        $this->render('index', array(
            'items'=>$this->generateItems(),
            'flightForm'=>$flightForm,
            'autosearch'=>true,
            'fromCityName'=>$fromCityName,
            'toCityName'=>$toCityName
        ));
    }

    public function stagePayment()
    {
        if (isset($_POST['submit']))
            Yii::app()->flightBooker->status('ticketing');
        else
            $this->render('payment');
    }

    public function generateItems()
    {
        Yii::app()->user->setState('lastSearches', null);
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
