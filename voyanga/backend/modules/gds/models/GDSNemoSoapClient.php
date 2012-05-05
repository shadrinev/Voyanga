<?php
class GDSNemoSoapClient extends SoapClient
{
    public static $testBooking = TRUE;
    public static $testResponseFileName = NULL;
    public static $lastRequest;
    public static $lastResponse;
    public static $lastHeaders;
    public static $lastCurlError;

    public function __doRequest($request, $location, $action, $version, $oneWay = 0)
    {
        echo $action;

        if ( strpos($action,'Search') !== FALSE )
        {
            /*
              <?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <GetMyName xmlns="http://tempuri.org/">
      <name>string</name>
    </GetMyName>
  </soap:Body>
</soap:Envelope>
             */
            $request = '<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <Search xmlns="http://tempuri.org/">
        <Request>
          <Requisites>
            <Login>webdev012</Login>
            <Password>HHFJGYU3*^H</Password>
            </Requisites>
          <RequestType>U</RequestType>
          <UserID>15</UserID>
          <Search>
            <ODPairs Type="OW" Direct="false" AroundDates="0">
              <ODPair>
                <DepDate>2012-06-13T00:00:00</DepDate>
                <DepAirp CodeType="IATA">MOW</DepAirp>
                <ArrAirp CodeType="IATA">PAR</ArrAirp>
              </ODPair>
            </ODPairs>
            <Travellers>
              <Traveller Type="ADT" Count="1"/>
            </Travellers>
            <Restrictions>
              <ClassPref>All</ClassPref>
              <OnlyAvail>true</OnlyAvail>
              <AirVPrefs/>
              <IncludePrivateFare>false</IncludePrivateFare>
              <CurrencyCode>RUB</CurrencyCode>
            </Restrictions>
          </Search>
        </Request>
    </Search>
  </soap:Body>
</soap:Envelope>';
            //echo parent::__doRequest($request, $location, $action, $version); die();
            $xml = $this->makeSoapRequest($request, $location, $action, $version);
            echo $xml;
            die();
        }
        else
        {
            echo VarDumper::dump($request);

            //$sXML = $this->makeSoapRequest($sRequest, $sLocation, $sAction, $iVersion);
            $xml = '';
        }

        return $xml;
    }

    private function makeSoapRequest($request, $location, $action, $version)
    {
        $rCh = curl_init();

        curl_setopt($rCh, CURLOPT_POST, (true));
        curl_setopt($rCh, CURLOPT_HEADER, true);
        curl_setopt($rCh, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($rCh, CURLOPT_POSTFIELDS, $request);
        curl_setopt($rCh, CURLOPT_TIMEOUT, 360);
        $headersToSend = array();
        $headersToSend[] = "Content-Length: " . strlen($request);
        $headersToSend[] = "Content-Type: text/xml; charset=utf-8";
        //$headersToSend[] = "SOAPAction: \"$action\"";

        curl_setopt($rCh, CURLOPT_HTTPHEADER, $headersToSend);
        curl_setopt($rCh, CURLOPT_URL, $location);
        $data = curl_exec($rCh);
        //Biletoid_Utils::addLogMessage($sData, '/tmp/curl_response.txt');
        if ($data !== FALSE)
        {
            list($headers, $data) = explode("\r\n\r\n", $data, 2);
            if (strpos($headers, 'Continue') !== FALSE)
            {
                list($headers, $data) = explode("\r\n\r\n", $data, 2);
            }
            //AlpOnline_SoapClient::$sLastHeaders = $sHeaders;
        }
        else
        {
            //AlpOnline_SoapClient::$sLastCurlError = curl_error ($rCh);
        }

        return $data;
    }

    private function newSoapRequest($request, $location, $action, $version)
    {
        $soap_do = curl_init();
        curl_setopt($soap_do, CURLOPT_URL,            $location );
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT,        100);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_POST,           true );
        curl_setopt($soap_do, CURLOPT_POSTFIELDS,    $request);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER,     array('Content-Type: text/xml; charset=utf-8', 'Content-Length: '.strlen($request) ));
        //curl_setopt($soap_do, CURLOPT_USERPWD, $user . ":" . $password);

        $result = curl_exec($soap_do);
        $err = curl_error($soap_do);
        return $result;
    }
}