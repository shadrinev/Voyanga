<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 04.06.12
 * Time: 13:48
 */
class ObserverComponent extends CApplicationComponent
{
    public $defaultMethod = 'handle';

    /**
     * Must be in this format:
     *
     * array(
     *   '<event-name>' => array(
     *     array('<class-name>', '<method-name>'),
     *     array('<class-name>', '<method-name>'),
     *     '<function-name>',
     *   )
     *   '<another-event-name>' => array(
     *     array('<class-name>', '<method-name>'),
     *     array('<class-name>', '<method-name>')
     *   )
     * )
     *
     * Observer methods must have the signature `methodName(SNotification $notification)`
     *
     * @var array
     */
    public $observers;

    public function init()
    {
        Yii::import('site.common.components.observer.*');
    }

    /**
     *
     * @var SNotifications
     */
    protected $_default;

    /**
     * @return SNotifications
     */
    public function getDefault()
    {
        if (!$this->_default)
        {
            $this->_default = new Dispatcher;
            //looking through events
            foreach ($this->observers as $event => $values)
            {
                //looking through attached handlers
                foreach ($values as $value)
                {
                    if (!isset($value[2]))
                        $value[2] = array();
                    if (is_array($value))
                        $this->_default->attach($event, $value[0], $value[1], $value[2]);
                    else
                        $this->_default->attach($event, $value, $this->defaultMethod );
                }
            }
        }

        return $this->_default;
    }

    public function notify($event, $args = null)
    {
        $this->getDefault()->notify($event, $args);
    }
}
