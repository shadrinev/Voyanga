<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 29.05.12
 * Time: 10:50
 */
class PopularityController extends Controller
{
    public function actionFlights()
    {
        $report = new PopularityOfDepartureCitySearch();
        $model = ReportExecuter::run($report);

        $this->render('flights', array('model'=>$model));
    }
}
