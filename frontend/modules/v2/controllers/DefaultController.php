<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 27.08.12
 * Time: 13:59
 */
class DefaultController extends CController
{
	public $nosidebar = false;

    public function actionIndex()
    {
        $this->render('frontend.www.themes.v2.views.layouts.main');
    }

    public function actionSandbox()
    {
    	$this->nosidebar = true;
        $this->render('v2.views.sandbox');
    }
}
