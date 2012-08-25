<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 22.05.12
 * Time: 16:42
 */
class DumpDbCommand extends CConsoleCommand
{
    private $backupFolder;
    private $backupFile;

    public function run($args)
    {
        $startTime = time();
        echo date('H:i:s Y-m-d').' Start of backup'.PHP_EOL;
        $this->backupFolder = $this->createBackupFolder();
        $this->dumpMySql();
        $this->dumpMongo();
        $this->createArchive();
        $this->uploadToYandexDisk();
        $this->deleteBackupSources();
        $endTime = time();
        $totalTime = $endTime - $startTime;
        echo date('H:i:s Y-m-d').' End of backup'.PHP_EOL;
        echo date('H:i:s Y-m-d').' Total time: '.$totalTime.' seconds'.PHP_EOL;
    }

    public function dumpMySql()
    {
        echo date('H:i:s Y-m-d').' Start backup mysql'.PHP_EOL;
        Yii::import('site.console.extensions.yii-database-dumper.SDatabaseDumper');
        $dumper = new InternalMysqlDumper();
        $file = $this->backupFolder.DIRECTORY_SEPARATOR.'dump_mysql_'.date('Y-m-d_H_i_s');
        $dumper->getDump($file);
        echo date('H:i:s Y-m-d').' End backup mysql'.PHP_EOL;
    }

    public function dumpMongo()
    {
        echo date('H:i:s Y-m-d').' Start backup mongodb'.PHP_EOL;
        $path = $this->backupFolder.DIRECTORY_SEPARATOR;
        $command = 'mongodump --db voyanga --out '.$path .' > /dev/null';
        $results = array();
        exec($command, $results);
        echo date('H:i:s Y-m-d').' End backup mongodb'.PHP_EOL;
    }

    public function createBackupFolder()
    {
        $path = Yii::getPathOfAlias('site.db').DIRECTORY_SEPARATOR.date('Y-m-d_H_i_s');
        if ((!is_dir($path)) and (!is_file($path)))
        {
            mkdir($path);
        }
        else
        {
            $path = $$path.'_'.time();
            mkdir($path);
        }
        return $path;
    }

    public function createArchive()
    {
        echo date('H:i:s Y-m-d').' Start packing'.PHP_EOL;
        $results = array();
        $this->backupFile = realpath($this->backupFolder.'/..').DIRECTORY_SEPARATOR.'dump_'.date('Y-m-d_H_i_s').'.tar.gz';
        $command = 'cd '.$this->backupFolder.DIRECTORY_SEPARATOR.' && tar -zcvf '.$this->backupFile.' `ls`';
        echo $command.' performing'.PHP_EOL;
        exec($command, $results);
        echo date('H:i:s Y-m-d').' End packing packing'.PHP_EOL;
    }

    public function uploadToYandexDisk()
    {
        echo date('H:i:s Y-m-d').' Uploading to yandex disk'.PHP_EOL;
        $yandexNarod = new YandexNarod();
        $yandexNarod->uploadFile('great-dbs', 'b4q0DNYN6NePdeVELgWQ', $this->backupFile);
        echo date('H:i:s Y-m-d').' End of uploading to yandex disk'.PHP_EOL;
    }

    public function deleteBackupSources()
    {
        echo date('H:i:s Y-m-d').' Removing temporary files'.PHP_EOL;
        $command = 'rm -rf '.$this->backupFolder;
        echo $command.' performing'.PHP_EOL;
        $results = array();
        exec($command, $results);
        echo date('H:i:s Y-m-d').' End removing temporary files'.PHP_EOL;
    }
}
