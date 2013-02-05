<?php
class GrabAirlineCodesCommand extends CConsoleCommand
{

    public function getHelp()
    {
        return <<<EOD
grabairlinecodes
EOD;
    }

    public function actionIndex()
    {
        $overall_results = array();
        foreach (Airline::model()->findall() as $airline) {
            $code = $airline->code;
            $data = file_get_contents("http://easytrip.nemo-ibe.com/guide_popup__carrier?iata=$code&KeepThis=true&TB_iframe=true&height=500&width=700");
            $dom = new DOMDocument();
            @$dom->loadHTML($data);

            $xpath = new DOMXPath($dom);
            $data = $xpath->query('//*[@id="table"]/tr');
            $rowResult = array();
            foreach($data as $row) {
                $result= $xpath->query('td/text()', $row);
                $key = $result->item(0)->textContent;
                $value = $result->item(1)->textContent;
                $rowResult[trim($key)] = trim($value);
            }
            $overall_results[] = $rowResult;
        }
        json_encode($overall_results);
    }
}
