<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 05.07.12
 * Time: 13:26
 */
class FrontendController extends Controller
{
    public $title;

    public function beforeAction($action)
    {
        $this->title = Yii::app()->params['title.default'];
        return parent::beforeAction($action);
    }

    public function assignTitle($titleName, $dictionary=array(), $enablePrefix=true, $enableSuffix=true)
    {
        $title = '';
        if ($enablePrefix)
            $title .= Yii::app()->params['title.prefix'];
        if (isset(Yii::app()->params['title.'.$titleName]))
            $title .= Yii::app()->params['title.'.$titleName];
        if ($enableSuffix)
            $title .= Yii::app()->params['title.suffix'];
        $title = strtr($title, $dictionary);
        if (strlen($title)==0)
            $title = Yii::app()->params['title.default'];
        $this->title = $title;
    }
}
