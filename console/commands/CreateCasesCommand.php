<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 22.05.12
 * Time: 16:42
 */
class CreateCasesCommand extends CConsoleCommand
{
    /** @var phpMorphy */
    private $morphy;

    public function run($args)
    {
        $this->morphy = Yii::app()->morphy;

        $cities = City::model()->findAll(array('select' => 'id, localRu'));
        foreach ($cities as $city)
        {
            $prepare = mb_strtoupper($city->localRu, 'utf-8');
            $cases = $this->analyzeCity($prepare);
            $city->caseNom = $city->localRu;
            $city->caseGen = $cases[0];
            $city->caseDat = $cases[1];
            $city->caseAcc = $cases[2];
            $city->caseIns = $cases[3];
            $city->casePre = $cases[4];
            if ($city->save())
                echo $city->localRu.' done.'.PHP_EOL;
            else
                echo $city->localRu.' error.'.PHP_EOL;
        }
    }

    private function analyzeCity($word)
    {
        return array(
            $this->getCase($word, 'РД'),
            $this->getCase($word, 'ДТ'),
            $this->getCase($word, 'ВН'),
            $this->getCase($word, 'ТВ'),
            $this->getCase($word, 'ПР'),
        );
    }

    private function getCase($word, $case)
    {
        $info = $this->morphy->castFormByGramInfo($word, 'С', array($case, 'ЕД'), false);
        if (isset($info[0]))
            return $this->mb_ucwords($info[0]['form']);
        return $this->mb_ucwords($word);
    }

    function mb_ucwords($str)
    {
        $str = mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
        return ($str);
    }
}
