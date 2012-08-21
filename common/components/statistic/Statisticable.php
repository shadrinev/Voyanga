<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 28.05.12
 * Time: 16:21
 */
class Statisticable extends CActiveRecordBehavior
{
    private $_modelName;
    private $_modelId;

    public function saveStatistic()
    {
        $row = $this->getRow();
        $attributes = $this->getOwner()->getStatisticData();
        if (isset($attributes[0]) and (is_array($attributes[0])))
        {
            foreach ($attributes as $attr)
            {
                $keys = array_keys($attr);
                $row->initSoftAttributes($keys);
                foreach ($attr as $key=>$value)
                {
                    $row->$key = $value;
                }
                $row->save();
                $row = $this->getRow();
            }
        }
        else
        {
            $attr = array_keys($attributes);
            $row->initSoftAttributes($attr);
            foreach ($attr as $key=>$value)
            {
                $row->$key = $value;
            }
            $row->save();
        }
        return true;
    }

    private function getRow()
    {
        $row = new Statistic;
        $row->modelName = $this->getModelName();
        $row->modelId = $this->getModelId();
        $row->dateCreate = date('Y-m-d h:i:s', time());
        return $row;
    }

    private function getModelName()
    {
        if ($this->_modelName==null)
            $this->_modelName=get_class($this->getOwner());
        return $this->_modelName;
    }

    private function getModelId()
    {
        if ($this->_modelId==null)
            $this->_modelId=$this->getOwner()->getPrimaryKey();
        return $this->_modelId;
    }
}
