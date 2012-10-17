<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 17.10.12
 * Time: 14:21
 */
class BuyController extends Controller
{
    public function actionIndex()
    {
        $this->layout = 'static';
        $this->render('index');
    }
}
