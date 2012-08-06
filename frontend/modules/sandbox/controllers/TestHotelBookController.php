<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 06.08.12
 * Time: 12:57
 */
class TestHotelBookController extends Controller
{
    public function actionTimeoutError()
    {
        $error = "<head><title>504 Gateway timeout</title><body>504 Gateway timeout</body></head>";
        header("HTTP/1.1 504 Gateway timeout");
        echo $error;
        die();
    }

    public function actionOk()
    {
        header("HTTP/1.1 200 OK");
        die();
    }
}
