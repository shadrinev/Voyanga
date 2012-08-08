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

    /**
     * Function to save reference to order at table
     *
     * @abstract
     * @return mixed
     */
    public function saveReference($order);

    /**
     * Return array of passports. If no passport needs so it should return false. If we need passports but they not provided return an empty array.
     * Array = array of classes derived from BasePassportForm (e.g. BaseFlightPassportForm)
     *
     * @abstract
     * @return mixed false
     */
    public function getPassports();

    /**
     * Return weight of element among others inside trip constructor. Important when dates are the same. Smaller - higher.
     *
     * @abstract
     * @return mixed
     */
    public function getWeight();
}
