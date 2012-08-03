<?php
/**
 * Helper class for http requests sending.
 *
 * @author Anatoly Kudinov <kudinov@voyanga.com>
 * @copyright Copyright (c) 2012, EasyTrip LLC
 * @package utils
 */
class HttpClient extends CApplicationComponent
{
    /**
     * Perform get request
     *
     * @throws HttpClientException
     * @param string $url where to send request
     * @return array [headers, response body]
     */
    public function get($url)
    {
        $curlHandler = $this->createHandler();
        curl_setopt($curlHandler, CURLOPT_URL, $url);

        $result = curl_exec($curlHandler);
        if ($result !== FALSE)
        {
            curl_close($curlHandler);
            return $this->parseResult($result);
        }
        else
        {
            $error = curl_error($curlHandler);
            curl_close($curlHandler);
            throw new HttpClientException($error);
        }
    }

    /**
     * Perform post request
     *
     * @throws HttpClientException 
     * @param string $url where to send request
     * @param mixed $post post data
     * @param array $header headers to send with request
     * @param array $curlopts any specific curl options to set
     * @return array [headers, response body]
     */
    public function post($url, $post, $headers=false, $curlopts=false)
    {
        $curlHandler = $this->createHandler();
        curl_setopt($curlHandler, CURLOPT_URL, $url);
        curl_setopt($curlHandler, CURLOPT_POST, (true));
        curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $post);

        if($headers)
            curl_setopt($curlHandler, CURLOPT_HTTPHEADER, $headers);

        foreach($curlopts as $key=>$value)
        {
            curl_setopt($curlHandler, $key, $value);
        }

       $result = curl_exec($curlHandler);
       if ($result !== FALSE)
        {
            curl_close($curlHandler);
            return $this->parseResult($result);
        }
        else
        {
            $error = curl_error($curlHandler);
            curl_close($curlHandler);
            throw new HttpClientException($error);
        }
    }


    private function createHandler()
    {
        $curlHandler = curl_init();
        curl_setopt($curlHandler, CURLOPT_HEADER, true);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        return $curlHandler;
    }

    private function parseResult($result)
    {
        list($headers, $result) = explode("\r\n\r\n", $result, 2);
        if (strpos($headers, 'Continue') !== FALSE)
        {
            list($headers, $result) = explode("\r\n\r\n", $result, 2);
        }
        return array($headers, $result);
    }
}


class HttpClientException extends Exception
{

}