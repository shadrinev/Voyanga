<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 09.06.12
 * Time: 13:41
 */
class ConstructorController extends ABaseAdminController
{
    public function actionNew()
    {
        $flightForm = new FlightForm;
        $this->render('new', array('flightForm'=>$flightForm));
    }

    public function actionFlightSearch()
    {
        $flightForm = new FlightForm();
        if (isset($_GET['FlightForm']))
        {
            $flightForm->attributes = $_GET['FlightForm'];
            $result = MFlightSearch::getAllPricesAsJson($flightForm->departureCityId, $flightForm->arrivalCityId, $flightForm->departureDate);
            echo $result;
            Yii::app()->end();
        }
        else
            throw new CHttpException(404);
    }
}
