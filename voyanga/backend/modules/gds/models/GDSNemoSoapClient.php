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

        if ( strpos($action,'Search11') !== FALSE )
        {
            echo $action.'||||';
            $request = '<?xml version="1.0" encoding="UTF-8"?>
<env:Envelope xmlns:env="http://schemas.xmlsoap.org/soap/envelope/">
  <env:Body>
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
                <DepAirp>MOW</DepAirp>
                <ArrAirp>IJK</ArrAirp>
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
  </env:Body>
</env:Envelope>';
            echo VarDumper::xmlDump($request);
            $sXML = $this->makeSoapRequest($request, $location, $action, $version);
            //$sXML = parent::__doRequest($request, $location, $action, $version);
            echo VarDumper::xmlDump($sXML);
            die();
        }
        else
        {
            echo VarDumper::xmlDump($request);
            //die();

            //$sXML = $this->makeSoapRequest($request, $location, $action, $version);
            $sXML = parent::__doRequest($request, $location, $action, $version);
            echo VarDumper::xmlDump($sXML);
            die();
        }

        return $sXML;
    }

    private function makeSoapRequest($sRequest, $sLocation, $sAction, $iVersion)
    {
        $rCh = curl_init();

        curl_setopt($rCh, CURLOPT_POST, (true));
        curl_setopt($rCh, CURLOPT_HEADER, true);
        curl_setopt($rCh, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($rCh, CURLOPT_POSTFIELDS, $sRequest);
        curl_setopt($rCh, CURLOPT_TIMEOUT, 190);
        $aHeadersToSend = array();
        $aHeadersToSend[] = "Content-Length: " . strlen($sRequest);
        $aHeadersToSend[] = "Content-Type: text/xml; charset=utf-8";
        $aHeadersToSend[] = "SOAPAction: \"$sAction\"";

        curl_setopt($rCh, CURLOPT_HTTPHEADER, $aHeadersToSend);
        curl_setopt($rCh, CURLOPT_URL, $sLocation);
        $sData = curl_exec($rCh);
        //Biletoid_Utils::addLogMessage($sData, '/tmp/curl_response.txt');
        if ($sData !== FALSE)
        {
            list($sHeaders, $sData) = explode("\r\n\r\n", $sData, 2);
            if (strpos($sHeaders, 'Continue') !== FALSE)
            {
                list($sHeaders, $sData) = explode("\r\n\r\n", $sData, 2);
            }
            //AlpOnline_SoapClient::$sLastHeaders = $sHeaders;
        }
        else
        {
            //AlpOnline_SoapClient::$sLastCurlError = curl_error ($rCh);
        }

        return $sData;
    }
}