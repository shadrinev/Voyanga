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
        $report = new PopularityOfFlightsSearch();
        $model = ReportExecuter::run($report);
        $model->scenario = 'search';
        if(isset($_GET['PopularityOfFlightsSearchResult']))
            $model->attributes=$_GET['PopularityOfFlightsSearchResult'];
        $this->render('flights', array('model'=>$model));
    }

    public function actionDirections($cityId)
    {
        $report = new PopularityDirectionsCity($cityId);
        $model = ReportExecuter::run($report);
        $model->scenario = 'search';
        if(isset($_GET['PopularityDirectionsCityResult']))
            $model->attributes=$_GET['PopularityDirectionsCityResult'];
        $this->render('directions', array('model'=>$model, 'report'=>$report));
    }

    public function actionDirectionsTo($cityId)
    {
        $report = new PopularityDirectionsCity($cityId, false);
        $model = ReportExecuter::run($report);
        $model->scenario = 'search';
        if(isset($_GET['PopularityDirectionsCityResult']))
            $model->attributes=$_GET['PopularityDirectionsCityResult'];
        $this->render('directionsTo', array('model'=>$model, 'report'=>$report));
    }
}
