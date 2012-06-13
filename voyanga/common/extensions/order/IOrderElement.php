<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 08.06.12
 * Time: 18:44
 */
interface IOrderElement
{
    /**
     * @abstract
     * @return boolean detects if element valid for current time
     */
    public function getIsValid();

    /**
     * @abstract
     * @return boolean is this element should be paid
     */
    public function getIsPayable();

    /**
     *
     * Function to save this item to persistent storage
     *
     * @abstract
     * @return boolean is saving ok
     */
    public function saveToOrderDb();

    /**
     * Function to get start time of event to sort it in chronological order
     *
     * @abstract
     * @return integer timestamp
     */
    public function getTime();
}
