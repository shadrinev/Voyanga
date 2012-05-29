<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 29.05.12
 * Time: 10:13
 */
class ReportExecuter
{
    public static function run(Report $report)
    {
        $report->getMongoCommand();
    }
}
