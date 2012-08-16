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

    public function run($args)
    {
        $this->backupFolder = $this->createBackupFolder();
        //todo: rewrite with mysqldump
        $this->dumpMySql();
        $this->dumpMongo();
        $this->createArchive();
        //todo: implement this
        //https://github.com/BenTheDesigner/Dropbox.git
        $this->uploadToDropbox();
    }

    public function dumpMySql()
    {
        echo date('H:i:s Y-m-d').' Start backup mysql'.PHP_EOL;
        Yii::import('site.console.extensions.yii-database-dumper.SDatabaseDumper');
        $dumper = new SDatabaseDumper;

        // Get path to new backup file
        $file = $this->backupFolder.DIRECTORY_SEPARATOR.'dump_mysql_'.date('Y-m-d_H_i_s').'.sql';

        file_put_contents($file, $dumper->getDump());
        echo date('H:i:s Y-m-d').' End backup mysql'.PHP_EOL.PHP_EOL;
    }

    public function dumpMongo()
    {
        echo date('H:i:s Y-m-d').' Start backup mongodb'.PHP_EOL;
        $path = $this->backupFolder.DIRECTORY_SEPARATOR;
        $command = 'mongodump --db voyanga --out '.$path .' > /dev/null';
        $results = array();
        exec($command, $results);
        CVarDumper::dump($results);
        echo date('H:i:s Y-m-d').' End backup mongodb'.PHP_EOL.PHP_EOL;
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
        echo 'Start packing'.PHP_EOL;
        $command = 'tar -zcvf '.realpath($this->backupFolder.'/..').DIRECTORY_SEPARATOR.'dump_'.date('Y-m-d_H_i_s').'.tar.gz '.$this->backupFolder;
        echo $command.' performing'.PHP_EOL;
        $results = array();
        exec($command, $results);
        CVarDumper::dump($results);
        echo date('H:i:s Y-m-d').' End packing packing'.PHP_EOL;
    }

    public function uploadToDropbox()
    {

    }
}
