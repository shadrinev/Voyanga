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
    public $search = 'search';

    public $tests = array(
        'aviaSearchSimple',
        'aviaSearchComplex',
        'aviaSearchRoundTrip',
        'aviaSearchComplexRoundTrip',
    );

    public function actionDefault()
    {
        foreach ($this->tests as $test)
        {
            echo '<h2>Perform test <b>'.$test.'</b></h2>';
            $result = $this->$test();
            if ($result)
            {
                echo '<h3 style="color: green">Test ok</h2>';
                echo "Result : <pre>$result</pre>";
            }
            else
            {
                echo '<h3 style="color: red">Test failed</h2>';
            }
            echo '<hr>';
        }
    }

    private function aviaSearchSimple()
    {
        $url = $this->api . '/' . $this->avia . '/' . $this->search;
        $search = array(
            'destinations' => array(
                'departure' => 'MOW',
                'arrival' => 'LED',
                'date' => '01.10.2012',
            )
        );
        $fullUrl = $url . '?' . http_build_query($search);
        $result = file_get_contents($fullUrl);
        return $result;
    }

    private function aviaSearchComplex()
    {
        return false;
    }

    private function aviaSearchRoundTrip()
    {
        return false;
    }

    private function aviaSearchComplexRoundTrip()
    {
        return false;
    }
}