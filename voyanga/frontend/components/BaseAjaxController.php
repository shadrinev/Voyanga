<?php

class BaseAjaxController extends Controller
{
    private $_statusCode = 200;
    private $_statusText = "OK";
    private $_contentType = 'application/json';
    public $data = array();

    /**
     * @return array combine all GET and POST params to one array to give ability to write
     * function action($param1, param2)
     */
    public function getActionParams()
    {
        return array_merge($_GET, $_POST);
    }

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

    private function _sendResponse($raw=false)
    {
        $status = $this->_statusCode;
        $contentType = $this->_contentType;
        // set the status
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_statusText;
        header($status_header);
        // and the content type
        header('Content-type: ' . $contentType);

        $response = $this->data;

        if ($raw)
            echo $response;
        else
            echo json_encode($response);
    }

    public function send($data, $raw=false)
    {
        $this->data = $data;
        $this->_sendResponse($raw);
    }

    public function sendError($errorCode)
    {
        $this->statusCode = $errorCode;
        $this->_sendResponse();
    }

    public function sendAsset($fileName)
    {
        $mime = CFileHelper::getMimeType($fileName);
        $status = $this->_statusCode;
        $contentType = $this->_contentType;
        // set the status
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_statusText;
        header($status_header);
        // and the content type
        header('Content-type: ' . $mime);

        $file = fopen($fileName, 'r');
        $chunkSize = 1.5 * 1024 * 1024;
        while (!feof($file))
        {
            $chunk = fread($file, $chunkSize);
            echo $chunk;
        }
        fclose($file);
    }

    public function init()
    {
        return parent::init();
    }
}
?>