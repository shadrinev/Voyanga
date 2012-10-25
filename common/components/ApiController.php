<?php
class ApiController extends Controller
{
    private $_statusCode = 200;
    private $_statusText = "OK";
    private $_contentType = 'application/json';
    public $data = array();

    public function setStatusCode($val)
    {
        $this->_statusCode = $val;
        $this->_statusText = $this->_getStatusCodeMessage($this->_statusCode);
    }

    public function getStatusCode()
    {
        return $this->_statusCode;
    }

    private function _getStatusCodeMessage($status)
    {
        $codes = Array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

    private function _sendResponse($raw=false, $dataType = false)
    {
        $status = $this->_statusCode;
        if ($dataType === false)
            $contentType = $this->_contentType;
        else
            $contentType = $dataType;

        // set the status
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_statusText;
        @header($status_header);
        // and the content type
        @header('Content-type: ' . $contentType);

        $response = $this->data;

        if ($raw)
            echo $response;
        else
        {
            //jsonp handling
            $json = CJSON::encode($response);
            if (isset($_GET['callback']))
            {
                echo $_GET['callback'] . ' (';
                echo $json;
                echo ');';
            }
            else
                echo $json;
        }
    }

    public function send($data, $raw=false)
    {
        $this->data = $data;
        $this->_sendResponse($raw);
    }

    public function sendJson($data, $raw=false)
    {
        $this->data = $data;
        $this->_sendResponse($raw, 'application/json');
    }

    public function sendXml($data, $rootName = 'data')
    {
        $xml = new ArrayToXml($rootName);
        $this->data = $xml->toXml($data);
        $this->_sendResponse(true, 'application/xml');
    }

    public function sendError($errorCode, $errorText='')
    {
        $this->statusCode = $errorCode;
        $this->data = array('error' => $errorText);
        $this->_sendResponse(false);
    }

    public function init()
    {
        return parent::init();
    }
}
?>