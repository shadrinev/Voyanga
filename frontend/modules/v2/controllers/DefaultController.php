<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 27.08.12
 * Time: 13:59
 */
class DefaultController extends CController
{
    public function actionIndex()
    {
        $this->render('v2.views.search_results');
    }

    public function actionHotels()
    {
        $this->render('v2.views.hotel_results');
    }

    public function actionSandbox()
    {
        $this->render('v2.views.sandbox');
    }
}
