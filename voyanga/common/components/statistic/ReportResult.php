<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 29.05.12
 * Time: 10:05
 */
abstract class ReportResult extends EMongoSoftDocument
{
    abstract function getReportName();

    // As always define the getCollectionName() and model() methods !
    public function getCollectionName()
    {
        return 'result_'.$this->getReportName();
    }
}
