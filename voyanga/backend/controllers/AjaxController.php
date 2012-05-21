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
            $ch = curl_init();
            $headers["User-Agent"] = "Curl/1.0";

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch,CURLOPT_TIMEOUT,1000);
            $response = curl_exec($ch);
            curl_close($ch);
            echo $response;
        }
        catch (Exception $e)
        {
            throw new CException($e);
        }
    }
}
