<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 21.05.12
 * Time: 13:41
 */
class FlightSearcher extends Component
{
    public static $apiUrl = 'http://frontend.misha.voyanga/';
    public static $getOptimalPriceUrl = "flight/getOptimalPrice";

    public static function request($url, $params=array())
    {
        $url = self::$apiUrl.$url;

        $ch = curl_init();
        $headers["User-Agent"] = "Curl/1.0";

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT,1000);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        //http://frontend.misha.voyanga/flight/getOptimalPrice?from=4466&to=4466&dateStart=13.06.2012&dateEnd=0&forceUpdate=1
        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($httpStatus != 200)
        {
            throw new CException('error:' . urldecode($response));
        }
        else
            return $response;
    }

    public static function getOptimalPrice($from, $to, $dateStart, $dateEnd, $forceUpdate)
    {
        $params = array(
            'from' => $from,
            'to' => $to,
            'dateStart' => $dateStart,
            'dateEnd'=> $dateEnd,
            'forceUpdate'=>$forceUpdate
        );
        return self::request(self::$getOptimalPriceUrl, $params);
    }

}
