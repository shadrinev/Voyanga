<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 10.01.13
 * Time: 17:01
 * To change this template use File | Settings | File Templates.
 */
class SmsManager
{
    private static function sendSMS($host, $port, $login, $password, $phone, $text, $sender = false, $wapurl = false)
    {
        try {
            $fp = fsockopen($host, $port, $errno, $errstr);
            if (!$fp) {
                return "errno: $errno \nerrstr: $errstr\n";
            }
            fwrite($fp, "GET /send/" .
                "?phone=" . rawurlencode($phone) .
                "&text=" . rawurlencode($text) .
                ($sender ? "&sender=" . rawurlencode($sender) : "") .
                ($wapurl ? "&wapurl=" . rawurlencode($wapurl) : "") .
                "  HTTP/1.0\n");
            fwrite($fp, "Host: " . $host . "\r\n");
            if ($login != "") {
                fwrite($fp, "Authorization: Basic " .
                    base64_encode($login. ":" . $password) . "\n");
            }
            fwrite($fp, "\n");
            $response = "";
            while(!feof($fp)) {
                $response .= fread($fp, 1);
            }
            fclose($fp);
            list($other, $responseBody) = explode("\r\n\r\n", $response, 2);
            return $responseBody;
        } catch (Exception $e) {
            return "exception";
        }
    }

    private static function send($phone, $text)
    {
        $phone = preg_replace('|([^\d]+)|','',$phone);
        if(strlen($phone) == 11 && $phone[0] == '8'){
            $phone[0] = '7';
        }
        self::sendSMS( Yii::app()->params['SMS']['server'], Yii::app()->params['SMS']['port'], Yii::app()->params['SMS']['login'], Yii::app()->params['SMS']['password'], $phone, $text, Yii::app()->params['SMS']['sender']);
    }

    public function init()
    {
        $this->controller = new Controller('sms');
    }

    public static function sendSmsOrderInfo($phone,$params){
        $controller = new Controller('sms');
        $text = $controller->renderPartial('allTicketsReady',$params,true);
        self::send($phone,$text);
    }
}
