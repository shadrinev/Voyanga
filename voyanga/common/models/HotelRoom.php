<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 13.06.12
 * Time: 17:29
 * To change this template use File | Settings | File Templates.
 */
class HotelRoom extends CApplicationComponent
{
    /*
     * [Room] => SimpleXMLElement#37
                    (
                        [@attributes] => array
                        (
                            'roomSizeId' => '2'
                            'roomSizeName' => 'DBL'
                            'roomTypeId' => '1'
                            'roomTypeName' => 'STD'
                            'roomViewId' => '1'
                            'roomViewName' => 'ROH'
                            'roomNumber' => '1'
                            'mealId' => '2'
                            'mealName' => 'Завтрак'
                            'mealBreakfastId' => '25'
                            'mealBreakfastName' => ''
                            'child' => '1'
                            'cots' => '0'
                            'sharingBedding' => 'false'
                        )
                        [ChildAge] => '6'
                    )
     */
    public static $roomSizeRoomTypesMap = array(1 => array(1), 2 => array(2, 3), 3 => array(5), 4 => array(6));
    /** @var array mapping from Hotel Book size id to amount of adults into apt */
    public static $roomSizeIdCountMap = array(1=>1,2=>2,3=>2,4=>1,5=>3,6=>4,7=>1,8=>2);
    public $sizeId;
    public $sizeName;
    public $typeId;
    public $typeName;
    public $viewId;
    public $viewName;
    public $mealId;
    public $mealName;
    public $mealBreakfastId;
    public $mealBreakfastName;
    public $sharingBedding;
    public $cotsCount;
    public $childCount;
    public $childAges = array();

    public function __construct($params)
    {
        $attrs = get_object_vars($this);
        foreach($attrs as $attrName=>$attrVal){
            if(isset($params[$attrName])){
                $this->{$attrName} = $params[$attrName];
            }
        }
    }

    public function getAdults()
    {
        return self::$roomSizeIdCountMap[$this->sizeId];
    }

    public function getKey()
    {
        return $this->sizeId.'|'.$this->typeId.'|'.$this->viewId.'|'.$this->mealId.'|'.$this->mealBreakfastId.'|'.$this->sharingBedding.'|'.$this->childCount.'|'.$this->cotsCount;
    }

    public function getJsonObject()
    {
        /*
        public $sizeId;
        public $sizeName;
        public $typeId;
        public $typeName;
        public $viewId;
        public $viewName;
        public $mealId;
        public $mealName;
        public $mealBreakfastId;
        public $mealBreakfastName;
        public $sharingBedding;
        public $cotsCount;
        public $childCount;
        public $childAges = array();
       */
        $ret = array('size' => $this->sizeName,
            'type'=>$this->typeName,
            'view'=>$this->viewName,
            'meal'=>$this->mealName,
            'mealBreakfast' => $this->mealBreakfastName,
            'cotsCount' => $this->cotsCount,
            'childCount' => $this->childCount,
            'childAges' => $this->childAges
        );

        return $ret;
    }
}
