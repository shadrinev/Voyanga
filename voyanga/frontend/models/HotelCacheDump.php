<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 22.05.12
 * Time: 15:03
 */
class HotelCacheDump extends Component
{
    public $cityId;
    public $dateFrom;
    public $dateTo;
    public $stars;
    public $price;
    public $hotelId;
    public $hotelName;
    public $createdAt;
    public $updatedAt;

    /**
     * @param $value HotelCache
     */
    public function setModel($value)
    {
        $this->cityId = $value->cityId;
        $this->dateFrom = $value->dateFrom;
        $this->dateTo = $value->dateTo;
        $this->stars = $value->stars;
        $this->createdAt = time();
        $this->attributes = serialize($value->attributes);
    }
}
