<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 28.05.12
 * Time: 16:21
 */
class Statisticable extends CActiveRecordBehavior
{
    public function afterSave($event)
    {
        $isMultiple = $this->getOwner()->isMultiple();
        $attributes = $this->getOwner()->getStatisticData();
        if ($isMultiple)
        {
            foreach ($attributes as $i=>$row)
                foreach ($row as $attr)
                {
                    $row = new Statistic;
                    $row->modelName = get_class($this->getOwner());
                    $row->modelId = $this->getOwner()->getPrimaryKey();
                    $row->initSoftAttributes(array_keys($attr));
                    foreach ($attributes as $key=>$value)
                    {
                        $row->$key = $value;
                    }
                    $row->save();
                }
        }
        else
        {
            $row = new Statistic;
            $row->modelName = get_class($this->getOwner());
            $row->modelId = $this->getOwner()->getPrimaryKey();
            $attributes = $this->getOwner()->getStatisticData();
            $row->initSoftAttributes(array_keys($attributes));
            foreach ($attributes as $key=>$value)
            {
                $row->$key = $value;
            }
            $row->save();
        }
    }
}
