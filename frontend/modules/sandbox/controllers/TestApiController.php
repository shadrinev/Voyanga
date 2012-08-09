<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 07.08.12
 * Time: 12:46
 */
class TestApiController extends FrontendController
{
    public $api = 'http://api.misha.voyanga/v1';
    public $avia = 'avia';
    public $hotel = 'hotel';
    public $search = 'search';

    public $tests = array(
/*        'aviaSearchSimple',
        'aviaSearchComplex',
        'aviaSearchRoundTrip',
        'aviaSearchComplexRoundTrip',*/
        'hotelSearchSimple',
    );

    public function actionDefault()
    {
        echo "<html><head><title>Testing API</title></head><body>";
        foreach ($this->tests as $test)
        {
            echo '<h2>Perform test <b>'.$test.'</b></h2>';
            $result = $this->$test();
            if ($result)
            {
                echo '<h3 style="color: green">Test ok</h3>';
                echo "Result : <pre>".$result."</pre>";
            }
            else
            {
                echo '<h3 style="color: red">Test failed</h3>';
            }
            echo '<hr>';
        }
        echo "</body></html>";
    }

    private function buildAviaApiUrl($params)
    {
        $url = $this->api . '/' . $this->avia . '/' . $this->search;
        $fullUrl = $url . '?' . http_build_query($params);
        return $fullUrl;
    }

    private function buildHotelApiUrl($params)
    {
        $url = $this->api . '/' . $this->hotel . '/' . $this->search;
        $fullUrl = $url . '?' . http_build_query($params);
        return $fullUrl;
    }

    private function aviaSearchSimple()
    {
        $search = array(
            'destinations' => array(
                array(
                'departure' => 'MOW',
                'arrival' => 'LED',
                'date' => '01.10.2012',
            ))
        );
        VarDumper::dump($search);
        $fullUrl = $this->buildAviaApiUrl($search);
        $result = file_get_contents($fullUrl);
        return $result;
    }

    private function aviaSearchComplex()
    {
        $search = array(
            'destinations' => array(
                array(
                    'departure' => 'MOW',
                    'arrival' => 'LED',
                    'date' => '01.10.2012',
                ),
                array(
                    'departure' => 'LED',
                    'arrival' => 'PAR',
                    'date' => '15.10.2012',
                ),
                array(
                    'departure' => 'PAR',
                    'arrival' => 'LON',
                    'date' => '20.10.2012',
                ),
            ));
        VarDumper::dump($search);
        $fullUrl = $this->buildAviaApiUrl($search);
        $result = file_get_contents($fullUrl);
        return $result;
    }

    private function aviaSearchRoundTrip()
    {
        $search = array(
            'destinations' => array(
                array(
                    'departure' => 'MOW',
                    'arrival' => 'LED',
                    'date' => '01.10.2012',
                ),
                array(
                    'departure' => 'LED',
                    'arrival' => 'MOW',
                    'date' => '15.10.2012',
                ),
            ));
        VarDumper::dump($search);
        $fullUrl = $this->buildAviaApiUrl($search);
        $result = file_get_contents($fullUrl);
        return $result;
        return false;
    }

    private function aviaSearchComplexRoundTrip()
    {
        $search = array(
            'destinations' => array(
                array(
                    'departure' => 'MOW',
                    'arrival' => 'LED',
                    'date' => '01.10.2012',
                ),
                array(
                    'departure' => 'LED',
                    'arrival' => 'PAR',
                    'date' => '15.10.2012',
                ),
                array(
                    'departure' => 'PAR',
                    'arrival' => 'MOW',
                    'date' => '20.10.2012',
                ),
            ));
        VarDumper::dump($search);
        $fullUrl = $this->buildAviaApiUrl($search);
        $result = file_get_contents($fullUrl);
        return $result;
    }

    private function hotelSearchSimple()
    {
        $search = array(
            'city' => 'MOW',
            'checkIn' => '2012-10-01',
            'duration' => 10,
            'rooms' => array(
                array(
                    'adt' => '2',
                    'chd' => '0',
                    'chdAge' => '0',
                    'cots' => '0',
                ))
        );
        VarDumper::dump($search);
        $fullUrl = $this->buildHotelApiUrl($search);
        $result = file_get_contents($fullUrl);
        return $result;
    }
}