<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 22.05.12
 * Time: 16:42
 */
class DumpDbCommand extends CConsoleCommand
{
    public function run($args)
    {
        $this->dumpMySql();
    }

    public function dumpMySql()
    {
        Yii::import('site.console.extensions.yii-database-dumper.SDatabaseDumper');
        $dumper = new SDatabaseDumper;

        // Get path to new backup file
        $file = Yii::getPathOfAlias('site.db').DIRECTORY_SEPARATOR.'dump_'.date('Y-m-d_H_i_s').'.sql';

        // Gzip dump
        if(function_exists('gzencode'))
            file_put_contents($file.'.gz', gzencode($dumper->getDump()));
        else
            file_put_contents($file, $dumper->getDump());
    }
}
