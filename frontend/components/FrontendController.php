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
    public $description;

    public function beforeAction($action)
    {
        $this->title = Yii::app()->params['title.default'];
        return parent::beforeAction($action);
    }

    public function assignTitle($titleName, $dictionary=array(), $enablePrefix=true, $enableSuffix=true)
    {
        $title = '';
        $description = '';

        if ($enablePrefix)
            $title .= Yii::app()->params['title.prefix'];
        if (isset(Yii::app()->params['title.'.$titleName]))
            $title .= Yii::app()->params['title.'.$titleName];
        if (isset(Yii::app()->params['description.'.$titleName]))
            $description .= Yii::app()->params['description.'.$titleName];
        if ($enableSuffix)
            $title .= Yii::app()->params['title.suffix'];
        $title = strtr($title, $dictionary);
        $description = strtr($description, $dictionary);
        if (strlen($title)==0)
            $title = Yii::app()->params['title.default'];
        if (strlen($description)==0)
            $description = Yii::app()->params['description.default'];
        $this->title = $title;
        $this->description = $description;
    }
}
