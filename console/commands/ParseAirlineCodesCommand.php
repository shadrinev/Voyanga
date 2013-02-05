<?php
class ParseAirlineCodesCommand extends CConsoleCommand
{

    public function getHelp()
    {
        return <<<EOD
parseairlinecodes path/to/nemo_airlines.json
EOD;
    }

    public function actionIndex($path)
    {
        $data = json_decode(file_get_contents($path), true);
        foreach ($data as $entry) {
            if(count($entry) == 1) {
                echo $entry['code'] . " нет в немо \n";
                continue;
            }
            if(!isset($entry['3-х цифровой код'])) {
                echo $entry['code'] . " нет 3-х цифрового кода \n";
                continue;
            }
            echo "\$this->execute(\"UPDATE airline SET ticketingplate='" . sprintf("%03d", $entry['3-х цифровой код']) . "' WHERE code='" . $entry['code'] . "';\"); \n";
        }

    }
}
