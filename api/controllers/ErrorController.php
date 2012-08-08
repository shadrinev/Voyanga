<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 07.08.12
 * Time: 12:06
 */
class ErrorController extends ApiController
{
    public function actionDefault()
    {
        if ($error = Yii::app()->errorHandler->error)
        {
            VarDumper::dump($error);
        }
    }
}
