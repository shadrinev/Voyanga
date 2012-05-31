<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 31.05.12
 * Time: 13:25
 */
abstract class Notification extends CModel
{
    private $_to;
    private $_text;
    private $_time;
    private $_key;

    public function rules()
    {
        return array(
            array('time', 'type', 'type'=>'datetime', 'datetimeFormat'=>'yyyy-MM-dd hh:mm:ss', 'message' => '{attribute} is not a date and time!'),
            array('text', 'min'=>1),
            array('to, key', 'required')
        );
    }

    public function attributeNames()
    {
        return array('to','text','time');
    }

    public function setTo($value)
    {
        $this->_to = $value;
    }

    public function setText($value)
    {
        $this->_text = $value;
    }

    public function setTime($value)
    {
        $this->_time = strtotime($value);
    }

    public function getTo()
    {
        return $this->_to;
    }

    public function getText()
    {
        return $this->_text;
    }

    public function getTime()
    {
        $time = Yii::app()->format->formatDateTime($this->_time);
        return $time;
    }

    public function getKey()
    {
        return $this->_key;
    }

    public function setKey($value)
    {
        $this->_key = $value;
    }
}
