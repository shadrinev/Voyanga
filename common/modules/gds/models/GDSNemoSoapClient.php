<?php
class GDSNemoSoapClient extends SoapClient
{
    public static $testBooking = TRUE;
    public static $testResponseFileName = NULL;
    public static $lastRequest;
    public static $lastResponse;
    public static $lastHeaders;
    public static $lastCurlError;
    public $gdsRequest;

    public function __doRequest($request, $location, $action, $version, $oneWay = 0)
    {
        //echo $action;

        if ( strpos($action,'Search11') !== FALSE )
        {
            //echo $action.'||||';
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
            //echo VarDumper::xmlDump($request);
            $sXML = $this->makeSoapRequest($request, $location, $action, $version);
            //$sXML = parent::__doRequest($request, $location, $action, $version);
            //echo VarDumper::xmlDump($sXML);
            //die();
            //$sXML = file_get_contents('/srv/www/oleg.voyanga/public_html/responseSearch.xml');

        }
        elseif( strpos($action,'bookFlight1') !== FALSE)
        {
            $this->gdsRequest->requestXml = UtilsHelper::formatXML($request);
            if (appParams('enableFlightLogging'))
                $this->gdsRequest->save();
            VarDumper::dump($request);
            return "";
        }
        else
        {
            $this->gdsRequest->requestXml = UtilsHelper::formatXML($request);
            if (appParams('enableFlightLogging'))
                $this->gdsRequest->save();
            $startTime = microtime(true);
            $sXML = $this->makeSoapRequest($request, $location, $action, $version);
            $endTime = microtime(true);
            $this->gdsRequest->executionTime = ($endTime - $startTime);
            $this->gdsRequest->responseXml = UtilsHelper::formatXML($sXML);
            if (appParams('enableFlightLogging'))
                $this->gdsRequest->save();
            if(!$sXML){
                $this->gdsRequest->errorDescription = Yii::t( 'application', 'Error on soap request. Curl description: {curl_desc}. Last headers: {last_headers}.', array('{curl_desc}'=>GDSNemoSoapClient::$lastCurlError,'{last_headers}'=>GDSNemoSoapClient::$lastHeaders));
                if (appParams('enableFlightLogging'))
                    $this->gdsRequest->save();
                return "";
//                throw new CException( Yii::t( 'application', 'Error on soap request. Curl description: {curl_desc}. Last headers: {last_headers}.', array('{curl_desc}'=>GDSNemoSoapClient::$lastCurlError,'{last_headers}'=>GDSNemoSoapClient::$lastHeaders)) );
            }
        }

        return $sXML;
    }

    //! FIXME $version is unused
    private function makeSoapRequest($request, $location, $action, $version)
    {

        $headersToSend = array();
        $headersToSend[] = "Content-Length: " . strlen($request);
        $headersToSend[] = "Content-Type: text/xml; charset=utf-8";
        $headersToSend[] = "SOAPAction: \"$action\"";

        $data = false;
        try {
            list($headers, $data) = Yii::app()
                ->httpClient->post($location,
                                   $request,
                                   $headersToSend,
                                   Array(CURLOPT_TIMEOUT=>100));
            GDSNemoSoapClient::$lastHeaders = $headers;
        } catch (HttpClientException $e) {
            GDSNemoSoapClient::$lastCurlError = $e->getMessage();
        }
       return $data;
    }
}