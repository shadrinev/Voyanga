<?php

        $rCh = curl_init();
        $url = 'http://ya.ru/';

        //if ($postData)
        //{
        //    curl_setopt($rCh, CURLOPT_POST, (true));
        //}
        curl_setopt($rCh, CURLOPT_HEADER, true);
        curl_setopt($rCh, CURLOPT_RETURNTRANSFER, true);
        //if ($postData)
        //{
        //    curl_setopt($rCh, CURLOPT_POSTFIELDS, $postData);
        //}
        curl_setopt($rCh, CURLOPT_TIMEOUT, 80);
        //$aHeadersToSend = array();
        //$aHeadersToSend[] = "Content-Length: " . strlen($sRequest);
        //$aHeadersToSend[] = "Content-Type: text/xml; charset=utf-8";
        //$aHeadersToSend[] = "SOAPAction: \"$sAction\"";

        //curl_setopt($rCh, CURLOPT_HTTPHEADER, $aHeadersToSend);

        //evaluate get parametrs
        /*if ($getData)
        {
            $pos = strpos($url, '?');
            if ($pos !== false)
            {
                list($url, $args) = explode("?", $url, 2);
                parse_str($args, $params);
                $getData = array_merge($params, $getData);
            }

            $url = $url . '?' . http_build_query($getData);
        }*/


        curl_setopt($rCh, CURLOPT_URL, $url);

                $sData = curl_exec($rCh);


                if ($sData !== FALSE)
                {
                    list($sHeaders, $sData) = explode("\r\n\r\n", $sData, 2);
                    if (strpos($sHeaders, 'Continue') !== FALSE)
                    {
                        list($sHeaders, $sData) = explode("\r\n\r\n", $sData, 2);
                    }
                    echo "headers: ".$sHeaders;
                    echo "response: ".$sData;


                }
                else
                {
                    echo "curlError:".curl_error($rCh);
                }
?>