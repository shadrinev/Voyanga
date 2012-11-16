<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 01.08.12
 * Time: 11:12
 */
class FlightSearchAction extends CAction
{
    public function run()
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
