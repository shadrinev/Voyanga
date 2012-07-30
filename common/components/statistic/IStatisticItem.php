<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 28.05.12
 * Time: 16:16
 */
interface IStatisticItem
{
    /**
     * @abstract
     * @return array Data to save into statistic
     */
    public function getStatisticData();
}