<?php Yii::import('site.common.modules.tour.models.*'); ?>
<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 09.06.12
 * Time: 13:41
 */
class ConstructorController extends BaseExtController
{
    public $tab='tour';

    public $defaultAction = 'new';

    public function actionCreate()
    {
        if ($res = Yii::app()->user->getState('trip.tour.form'))
            $model = @unserialize($res);
        else
            $model = new TourBuilderForm();
        if (isset($_POST['TourBuilderForm']))
        {
            $model->attributes = $_POST['TourBuilderForm'];
            $model->trips = array();
            if (isset($_POST['TripForm']))
            {
                $validTrips = true;
                foreach ($_POST['TripForm'] as $i=>$attributes)
                {
                    $trip = new TripForm();
                    $trip->attributes = $attributes;
                    $validTrips = $validTrips and $trip->validate();
                    if ($validTrips)
                        $model->trips[] = $trip;
                }
                if ($validTrips and $model->validate())
                {
                    Yii::app()->user->setState('trip.tour.form', serialize($model));
                    Yii::app()->shoppingCart->clear();
                    ConstructorBuilder::build($model);
                    $this->redirect('showTrip');
                }
            }
        }
        $this->render('create', array('model'=>$model));
    }

    public function actionShowTrip()
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
        $this->render('showTrip', array('tabs'=>$tabs));
    }

    public function actionMakeBooking()
    {
        $trip = Yii::app()->order->getPositions(false);
        $valid = true;

        foreach($trip as $cartElement)
        {
            if($cartElement instanceof FlightTripElement)
            {
                if(!$cartElement->flightVoyage)
                {
                    $valid = false;
                }
            }

            if($cartElement instanceof HotelTripElement)
            {
                if(!$cartElement->hotel)
                {
                    $valid = false;
                }
            }
        }
        VarDumper::dump(Yii::app()->shoppingCart);
        if($valid)
        {
            Yii::app()->order->booking();
        }
        echo 123;die();
    }

    public function actionNew($clear=false, $isTab=false)
    {
        if ($clear)
            Yii::app()->shoppingCart->clear();
        $flightForm = new FlightForm;
        $hotelForm = new HotelForm;
        if ($isTab)
            $this->renderPartial('new', array('flightForm'=>$flightForm, 'hotelForm'=>$hotelForm,'autosearch'=>false, 'cityName'=>'', 'duration'=>1));
        else
            $this->render('new', array('flightForm'=>$flightForm, 'hotelForm'=>$hotelForm,'autosearch'=>false, 'cityName'=>'', 'duration'=>1));
    }

    public function actionFlightSearch()
    {
        $flightForm = new FlightForm();
        if (isset($_GET['FlightForm']))
        {
            $flightForm->attributes = $_GET['FlightForm'];
            if (isset($_GET['RouteForm']))
            {
                foreach ($_GET['RouteForm'] as $route)
                {
                    $newRoute = new RouteForm();
                    $newRoute->attributes = $route;
                    $flightForm->routes[] = $newRoute;
                }
            }
            if ($flightForm->validate())
            {
                //$this->storeSearches($flightForm->departureCityId, $flightForm->arrivalCityId, $flightForm->departureDate, $flightForm->adultCount, $flightForm->childCount, $flightForm->infantCount);
                $result = MFlightSearch::getAllPricesAsJson($flightForm);
                echo $result;
            }
            else
            {
                throw new CHttpException(500, CHtml::errorSummary($flightForm));
            }
            Yii::app()->end();
        }
        else
            throw new CHttpException(404);
    }

    function compareByTime($a, $b)
    {
        if ($a[3] == $b[3]) {
            return 0;
        }
        return ($a[3] > $b[3]) ? -1 : 1;
    }

    private function storeSearches($from, $to, $date, $adultCount, $childCount, $infantCount)
    {
        $hash = $from.$to.$date;
        $element = array($from, $to, $date, time(), $adultCount, $childCount, $infantCount);
        $elements = Yii::app()->user->getState('lastSearches');
        $elements[$hash] = $element;
        uasort($elements, array($this, 'compareByTime'));
        $last = array_splice($elements, 0, 10);
        Yii::app()->user->setState('lastSearches', $last);
    }
}
