<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 25.07.12
 * Time: 11:44
 */
abstract class TripElement extends CModel implements IECartPosition, IOrderElement
{
    const TYPE_FLIGHT = 1;
    const TYPE_HOTEL = 2;

    public $type;

    public function getId()
    {
        if (!$this->_id)
        {
            $counter = Yii::app()->user->getState('trip.'.__CLASS__.'.counter');
            if ($counter)
                $this->setId($counter+1);
            else
                $this->setId(1);
        }
        return $this->_id;
    }

    public function setId($val)
    {
        $this->_id = $val;
        Yii::app()->user->setState('trip.'.__CLASS__.'.counter', $this->_id);
    }
}
