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
        $this->render('frontend.www.themes.v2.views.layouts.main');
    }
}
