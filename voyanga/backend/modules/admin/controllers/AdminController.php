<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 11.05.12
 * Time: 11:28
 */
class AdminController extends Controller
{
    public function actionIndex()
    {
        VarDumper::dump(Yii::app()->mongodb); die();
        $this->render('index');
    }
}
