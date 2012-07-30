<?php

$aParams = array(
                'Request' => array(
                        'SearchFlights' => array(
                                'LinkOnly' => false, 
                                'ODPairs' => array(
                                        'Type' => 'OW', 
                                        'Direct' => "false", 
                                        'AroundDates' => "0", 
                                        'ODPair' => array(
                                                'DepDate' => '2012-06-11T00:00:00', 
                                                'DepAirp' => array(
                                                        'CodeType' => 'IATA', 
                                                        '_'=>'MOW' ), 
                                                'ArrAirp' => array(
                                                        'CodeType' => 'IATA', 
                                                        '_'=>'PAR' ) )
                                 ),
                                 'Travellers'=>array('Traveller'=>array(array('Type'=>'ADT','Count'=>'1'),array('Type'=>'ADT','Count'=>'1'))),
                                 'Restrictions'=>array('ClassPref'=>'all','OnlyAvail'=>'true','AirVPrefs'=>'','IncludePrivateFare'=>'false','CurrencyCode'=>'RUB'),
         
         ) ),
         'Source'=>array('ClientId'=>102,
         'APIKey'=>'7F48365D42B73307C99C12A578E92B36',
         'Language'=>'UA',
         'Currency'=>'RUB'
         ) );

class GDSNemoSoapClient extends SoapClient
{
	public static $bTestBooking = TRUE;
	public static $sTestResponseFileName = NULL;
	public static $sLastRequest;
	public static $sLastResponse;
	public static $sLastHeaders;
	public static $sLastCurlError;
	
	public function __doRequest($request, $location, $action, $version)
	{
	    if($action == 'SearchFlights'){
	        $request = '<?xml version="1.0" encoding="UTF-8"?>
<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" xmlns:ns1="http://srt.mute-lab.com/nemoflights/?version%3D1.0%26for%3DSearchFlights">
  <env:Body>
    <ns1:search>
      <RequestBin>
        <Request>
          <SearchFlights>
            <ODPairs Type="OW" Direct="false" AroundDates="0">
              <ODPair>
                <DepDate>2011-07-13T00:00:00</DepDate>
                <DepAirp CodeType="IATA">MOW</DepAirp>
                <ArrAirp CodeType="IATA">PAR</ArrAirp>
              </ODPair>
            </ODPairs>
            <Travellers>
              <Traveller Type="ADT" Count="1"/>
              <Traveller Type="CNN" Count="1"/>
            </Travellers>
            <Restrictions>
              <ClassPref>all</ClassPref>
              <OnlyAvail>true</OnlyAvail>
              <AirVPrefs/>
              <IncludePrivateFare>false</IncludePrivateFare>
              <CurrencyCode>RUB</CurrencyCode>
            </Restrictions>
          </SearchFlights>
        </Request>
        <Source>
          <ClientId>110</ClientId>
          <APIKey>C59661F96B218FFA1523413C3B669D12</APIKey>
          <Language>UA</Language>
          <Currency>RUB</Currency>
        </Source>
      </RequestBin>
    </ns1:search>
  </env:Body>
</env:Envelope>';
	        $sXML = $this->makeSoapRequest($request, $location, $action, $version);
	    }else{
	        //echo htmlspecialchars($sRequest);
	        $sXML = $this->makeSoapRequest($request, $location, $action, $version);
	        //$sXML = '';
	    }
	    
	    return $sXML;
	}
    private function makeSoapRequest($sRequest, $sLocation, $sAction, $iVersion){
		$rCh = curl_init();
		
		curl_setopt($rCh,CURLOPT_POST,(true));
		curl_setopt($rCh,CURLOPT_HEADER,true);
		curl_setopt($rCh,CURLOPT_RETURNTRANSFER, true);
		curl_setopt($rCh,CURLOPT_POSTFIELDS,$sRequest);
		curl_setopt($rCh,CURLOPT_TIMEOUT,190);
		$aHeadersToSend = array();
		$aHeadersToSend[] = "Content-Length: ".strlen($sRequest);
		$aHeadersToSend[] = "Content-Type: text/xml; charset=utf-8";
		$aHeadersToSend[] = "SOAPAction: \"$sAction\"";
		
		curl_setopt($rCh,CURLOPT_HTTPHEADER,$aHeadersToSend);
		curl_setopt($rCh, CURLOPT_URL, $sLocation);
		$sData = curl_exec($rCh);
		//Biletoid_Utils::addLogMessage($sData, '/tmp/curl_response.txt');
		if($sData !== FALSE){
			list($sHeaders, $sData) = explode("\r\n\r\n", $sData, 2);
			if(strpos($sHeaders, 'Continue') !== FALSE){
				list($sHeaders, $sData) = explode("\r\n\r\n", $sData, 2);
			}
			//AlpOnline_SoapClient::$sLastHeaders = $sHeaders;
		}else{
			//AlpOnline_SoapClient::$sLastCurlError = curl_error ($rCh);
		}
		
		return $sData;
	}
}

$oClient = new GDSNemoSoapClient( 'http://srt.mute-lab.com/nemoflights/wsdl.php?for=SearchFlights', array(
                'trace' => 1, 
                'typemap' => array(
                        array(
                                'type_ns' => 'http://spos.ru/flights/2010-02-01', 
                                'type_name' => 'PersonFlightExtensions' ) ) ) );
print_r($oClient->search($aParams));
