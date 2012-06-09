<?php
class HotelBookClient
{
    public $differenceTimestamp = 0;
    public $isSynchronized = false;
    public $lastHeaders;

    public function request($url, $getData = null, $postData = null)
    {
        $rCh = curl_init();

        if($postData)
        {
            curl_setopt($rCh, CURLOPT_POST, (true));
        }
        curl_setopt($rCh, CURLOPT_HEADER, true);
        curl_setopt($rCh, CURLOPT_RETURNTRANSFER, true);
        if($postData)
        {
            curl_setopt($rCh, CURLOPT_POSTFIELDS, $postData);
        }
        curl_setopt($rCh, CURLOPT_TIMEOUT, 190);
        //$aHeadersToSend = array();
        //$aHeadersToSend[] = "Content-Length: " . strlen($sRequest);
        //$aHeadersToSend[] = "Content-Type: text/xml; charset=utf-8";
        //$aHeadersToSend[] = "SOAPAction: \"$sAction\"";

        //curl_setopt($rCh, CURLOPT_HTTPHEADER, $aHeadersToSend);

        //evaluate get parametrs
        if($getData)
        {
            $pos = strpos($url,'?');
            if($pos !== false)
            {
                list($url,$args) = explode("?", $url, 2);
                parse_str($args,$params);
                $getData = array_merge($params,$getData);
            }

            $url = $url.'?'.http_build_query($getData);
        }


        curl_setopt($rCh, CURLOPT_URL, $url);
        $sData = curl_exec($rCh);
        //Biletoid_Utils::addLogMessage($sData, '/tmp/curl_response.txt');
        if ($sData !== FALSE)
        {
            list($sHeaders, $sData) = explode("\r\n\r\n", $sData, 2);
            if (strpos($sHeaders, 'Continue') !== FALSE)
            {
                list($sHeaders, $sData) = explode("\r\n\r\n", $sData, 2);
            }
            $this->lastHeaders = $sHeaders;
        }
        else
        {
            $this->lastCurlError = curl_error ($rCh);
        }

        return $sData;
    }

    private function getChecksum($time)
    {
        return md5(md5(Yii::app()->params['HotelBook']['password']).$time);
    }

    public function getCountries()
    {
        $this->synchronize();
        echo "iNN";
        $time = time() + $this->differenceTimestamp;
        $getData = array('login'=>Yii::app()->params['HotelBook']['login'],'time'=>$time,'checksum'=>$this->getChecksum($time));
        $countries = $this->request(Yii::app()->params['HotelBook']['uri'].'countries',$getData);
        $countriesObject = simplexml_load_string($countries);
        $return = array();
        foreach($countriesObject->Countries->Country as $country)
        {
            $id = intval($country['id']);
            $name = trim((string)$country);
            $return[$id] = array('id'=>$id,'nameRu'=>$name);
        }
        $getData['language']= 'en';
        $countries = $this->request(Yii::app()->params['HotelBook']['uri'].'countries',$getData);
        $countriesObject = simplexml_load_string($countries);
        foreach($countriesObject->Countries->Country as $country)
        {
            $id = intval($country['id']);
            $name = trim((string)$country);
            $return[$id]['nameEn'] = $name;
        }
        //print_r($return);
        return $return;
    }

    public function getCities($countryId = 0)
    {
        $this->synchronize();
        $time = time() + $this->differenceTimestamp;
        $getData = array('login'=>Yii::app()->params['HotelBook']['login'],'time'=>$time,'checksum'=>$this->getChecksum($time));
        if($countryId)
        {
            $getData['country_id'] = $countryId;
        }
        $cities = $this->request(Yii::app()->params['HotelBook']['uri'].'cities',$getData);
        $citiesObject = simplexml_load_string($cities);
        $return = array();
        foreach($citiesObject->Cities->City as $city)
        {
            $id = intval($city['id']);
            $name = trim((string)$city);
            $country_id = intval($city['country']);
            $return[$id] = array('id'=>$id,'nameEn'=>$name,'countryId'=>$country_id);
        }
       /* $getData['language']= 'en';
        $cities = $this->request(Yii::app()->params['HotelBook']['uri'].'cities',$getData);
        $citiesObject = simplexml_load_string($cities);
        foreach($citiesObject->Cities->City as $city)
        {
            $id = intval($city['id']);
            $name = trim((string)$city);
            $return[$id]['nameEn'] = $name;
        }*/
        //print_r($return);
        return $return;
    }

    public function hotelSearch($params)
    {
        $this->synchronize();
        $time = time() + $this->differenceTimestamp;
        $getData = array('login'=>Yii::app()->params['HotelBook']['login'],'time'=>$time,'checksum'=>$this->getChecksum($time));

        $xml = '<?xml version="1.0" encoding="utf-8"?>
<HotelSearchRequest>
    <Request
        cityId="6788"
        checkIn="2012-09-17"
        duration="7" confirmation="all"/>
    <Rooms>
        <Room roomSizeId="8" child="0" roomNumber="1" >
        </Room>
    </Rooms>
</HotelSearchRequest>';
        /*if($countryId)
        {
            $getData['country_id'] = $countryId;
        }*/
        $hotelsXml = $this->request(Yii::app()->params['HotelBook']['uri'].'hotel_search',$getData,array('request'=>$xml));
        echo $hotelsXml;
        $hotelsObject = simplexml_load_string($hotelsXml);
        VarDumper::dump($hotelsObject);
        /*$return = array();
        foreach($citiesObject->Cities->City as $city)
        {
            $id = intval($city['id']);
            $name = trim((string)$city);
            $country_id = intval($city['country']);
            $return[$id] = array('id'=>$id,'nameEn'=>$name,'countryId'=>$country_id);
        }*/
    }

    public function synchronize()
    {
        if(!$this->isSynchronized)
        {
            $unixtime = $this->request(Yii::app()->params['HotelBook']['uri'].'unix_time');

            $diff = Yii::app()->cache->get('hotelbookDifferenceTimestamp');
            if($diff === false){
                $this->differenceTimestamp = $unixtime - time();
                Yii::app()->cache->set('hotelbookDifferenceTimestamp',$this->differenceTimestamp);
            }else{
                $this->differenceTimestamp = $diff;
            }
            $this->isSynchronized = true;
        }

        //echo "ts:{$unixtime} NN:".date("Y-m-d H:i:s",$unixtime).' NFT:'.date("Y-m-d H:i:s").' NCC:'.date("Y-m-d H:i:s",(time() + $this->differenceTimestamp));

    }
}