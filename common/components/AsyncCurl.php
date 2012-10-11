<?php
/**
 * Copyright (c) 2011 Nic Luciano <nic@getglue.com>
 *
 * Permission to use, copy, modify, and/or distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 */

/**

$ws = new AsyncCurl();

$ws->add("http://localhost/users", array("userId" => "test_add"), "POST");
$ws->add("http://localhost/users/friends", array("userId" => "test_add"));

$responses = $ws->send();

echo responses[0]->body;
 */

class AsyncRequest
{

    /**
     * The url of the request
     * @var string
     */
    public $url;

    /**
     * The POST or GET data to be sent to the server
     * @var array
     */
    public $data;

    /**
     * The HTTP method of the request
     * @var string
     */
    public $method;

    /**
     * The curl handle representing the request
     * @var curl handle
     */
    public $ch;

    /**
     * Generates a curl handle representing the HTTP request
     * and initializes the members of the Request object
     * @param url
     * @param data
     * @param method
     */
    function __construct($url, $data, $method)
    {

        // http://www.php.net/manual/en/function.http-build-query.php#102324
        $qs = http_build_query($data, "", "&");
        $ch = curl_init();

        if ($method == "GET")
        {
            if (strlen($qs) > 0)
                curl_setopt($ch, CURLOPT_URL, "{$url}?{$qs}");
            else
                curl_setopt($ch, CURLOPT_URL, "{$url}");
        }
        else if ($method == "POST")
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $qs);
            curl_setopt($ch, CURLOPT_URL, "{$url}");
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $this->url = $url;
        $this->data = $data;
        $this->method = $method;
        $this->ch = $ch;

    }

}

class AsyncResponse
{

    /**
     * The body of the HTTP response
     * @var string
     */
    public $body;

    /**
     * The HTTP headers of the response
     * @var string
     */
    public $headers;

    /**
     * Initialize the Response properties
     * @param body
     * @param headers
     */
    function __construct($body, $headers)
    {
        $this->body = $body;
        $this->headers = $headers;
    }

}

class AsyncCurl
{
    /**
     * A list of all Requests to be made
     * @var array
     * @access private
     */
    private $requests = array();
    private $orderRequests = array();

    /**
     * Adds a request to the list of Async request
     * @param url
     * @param data
     * @param method
     * @see Request
     */
    function add($url, $data = array(), $method = "GET")
    {
        $this->requests[] = new AsyncRequest($url, $data, $method);
    }

    /**
     * Executes all the calls added to it's stack
     * and returns the associated Responses
     * @return AsyncResponse
     * @see Response
     */
    function send()
    {
        $responses = array();
        $cmh = curl_multi_init();
        $still_running = null;

        foreach ($this->requests as $i => $request)
        {
            curl_multi_add_handle($cmh, $request->ch);
            $cmhAsIndex = intval($request->ch);
            $this->orderRequests[$cmhAsIndex] = $i;
        }

        do
        {
            $mrc = curl_multi_exec($cmh, $still_running);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);

        while ($still_running && $mrc == CURLM_OK)
        {
            if (curl_multi_select($cmh) != -1)
            {
                do
                {
                    $mrc = curl_multi_exec($cmh, $still_running);
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }

        foreach ($this->requests as $request)
        {
            $body = curl_multi_getcontent($request->ch);
            $cmhAsIndex = intval($request->ch);
            $index = $this->orderRequests[$cmhAsIndex];
            $responses[$index] = new AsyncResponse($body, array(
                "http_code" => curl_getinfo($request->ch, CURLINFO_HTTP_CODE)
            ));
            curl_multi_remove_handle($cmh, $request->ch);
        }

        curl_multi_close($cmh);
        return $responses;
    }
}