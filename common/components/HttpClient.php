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
     * @param string $url where to send request
     * @return array [headers, response body]
     */
    public function get($url)
    {
        $curlHandler = $this->createHandler();
        curl_setopt($curlHandler, CURLOPT_URL, $url);

        $result = curl_exec($curlHandler);
        curl_close($curlHandler);

        return $this->parseResult($result);
    }

    /**
     * Perform post request
     *
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
       curl_close($curlHandler);

       return $this->parseResult($result);
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
        if ($result !== FALSE)
        {
            list($headers, $result) = explode("\r\n\r\n", $result, 2);
            if (strpos($headers, 'Continue') !== FALSE)
            {
                list($headers, $result) = explode("\r\n\r\n", $result, 2);
            }
            return array($headers, $result);
        }
        else
        {
            throw new HttpClientException(curl_error($curlHandler));
        }
    }
}


class HttpClientException extends Exception
{

}