<?php

class BaseAjaxController extends Controller
{
    private $_statusCode = 200;
    private $_statusText;
    private $_contentType = 'application/json';


    public $data = array();

    /**
     * @return array combine all GET and POST params to one array to give ability to write
     * function actionName($param1, param2), where e.g. $param1 from GET and param2 from POST
     */
    public function getActionParams()
    {
        return array_merge($_GET, $_POST);
    }

    /**
     * @param int $val custom status code
     */
    public function setStatusCode($val)
    {
        $this->_statusCode = $val;
        if ($this->_statusText == null)
            $this->_statusText = $this->_getStatusCodeMessage($this->_statusCode);
    }

    /**
     * @param string $val custom text
     */
    public function setStatusText($val)
    {
        $this->_statusText = $val;
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
        return (isset($codes[$status])) ? $codes[$status] : 'Unknown error';
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

    /**
     * Send normal response to client
     * @param mixed $data any data to send to client
     * @param bool $raw true = no json_ecode for $data | false = json_encode($data)
     */
    public function send($data, $raw=false)
    {
        $this->data = $data;
        $this->_sendResponse($raw);
    }

    /**
     * Sends error to client
     * @param $errorCode
     * @param bool $errorText
     */
    public function sendError($errorCode, $errorText=false)
    {
        $this->statusCode = $errorCode;
        if ($errorText)
        {
            $this->_statusText = $errorText;
        }
        else
        {
            $this->_statusText = $this->_getStatusCodeMessage($errorCode);
        }
        $this->_sendResponse();
    }

    /**
     * Send any file as binary string with correct mime-type. If file not found it sends 404 error.
     * @param $fileName
     */
    public function sendAsset($fileName)
    {
        if (!is_file($fileName))
        {
            $this->sendError(404);
            return;
        }
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