<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 23.08.12
 * Time: 11:29
 */
class InternalMysqlDumper
{
    private $file;

    public function getDump($file)
    {
        $this->file = $file;
        foreach ($this->getDbs() as $db)
            $this->getDumpForDb($db);
    }

    private function getDumpForDb($db)
    {
        echo date('H:i:s Y-m-d').' Start backup '.$db.PHP_EOL;
        $file = $this->file.'_'.$db.'.sql';
        $command = 'mysqldump -uoleg -pq1w2e3r4 '.$db.' > '.$file;
        $results = array();
        exec($command, $results);
        if (!empty($results))
            CVarDumper::dump($results);
        echo date('H:i:s Y-m-d').' End backup '.$db.PHP_EOL;
    }

    public function getDbs()
    {
        return array(
            'backend',
            'logdb',
            'search',
        );
    }

}
