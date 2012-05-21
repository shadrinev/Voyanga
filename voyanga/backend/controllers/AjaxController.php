<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 21.05.12
 * Time: 10:15
 */
class AjaxController extends Controller
{
    public function actionGetOptimalPrice($from, $to, $dateStart, $dateEnd)
    {
        try
        {
            $url = "http://frontend.misha.voyanga/site/getOptimalPrice/from/$from/to/$to/dateStart/$dateStart/dateEnd/$dateEnd";
            header('Content-type: application/json');
            echo file_get_contents($url);
        }
        catch (Exception $e)
        {
            throw new CException($e);
        }
    }
}
