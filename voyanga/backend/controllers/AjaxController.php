<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 21.05.12
 * Time: 10:15
 */
class AjaxController extends Controller
{
    public function actionGetOptimalPrice($from, $to, $dateStart, $dateEnd, $forceUpdate=true)
    {
        try
        {
            $dateStart = Event::getFlightFromDate($dateStart);
            $dateEnd = Event::getFlightToDate($dateEnd);

            $fromTo = FlightSearcher::getOptimalPrice($from, $to, $dateStart, false, $forceUpdate);
            $toFrom = FlightSearcher::getOptimalPrice($to, $from, $dateEnd, false, $forceUpdate);
            $fromBack = FlightSearcher::getOptimalPrice($from, $to, $dateStart, $dateEnd, $forceUpdate);
            $response = array(
                'priceTo' => (int)$fromTo,
                'priceBack' => (int)$toFrom,
                'priceToBack' => (int)$fromBack
            );
            header('Content-type: application/json');
            echo json_encode($response);
        }
        catch (Exception $e)
        {
            throw new CException($e);
        }
    }
}
