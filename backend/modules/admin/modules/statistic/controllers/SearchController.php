<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 29.05.12
 * Time: 10:50
 */
class SearchController extends Controller
{
    const PAGE_SIZE = 100;

    public function actionFlight()
    {
        $report = new AmountOfFlightSearch;
        $model = ReportExecuter::run($report);
        $dataProvider = new EMongoDocumentDataProvider($model, array(
            'keyField' => 'primaryKey',
            'pagination' => array(
                'pageSize' => self::PAGE_SIZE
            )
        ));
        $this->render('flight', array('dataProvider'=>$dataProvider));
    }
}
