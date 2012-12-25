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
    public static $showMealValue = array('Без питания'=>false,'Не известно'=>false,'Американский завтрак'=>'Завтрак','Английский завтрак'=>'Завтрак','Завтрак в номер'=>'Завтрак','Завтрак + обед'=>'Завтрак и обед','Завтрак + обед + ужин'=>'Завтрак и обед и ужин','Завтрак + обед + ужин + напитки'=>'Завтрак и обед и ужин и напитки','Завтрак + ужин'=>'Завтрак и ужин','Континентальный завтрак'=>'Завтрак','Завтрак Шведский стол'=>'Завтрак','Завтрак'=>'Завтрак');
    public $sizeId;
    public $sizeIdNemo;
    public $sizeName;
    public $typeId;
    public $typeName;
    public $viewId;
    public $viewName;
    public $mealId;
    public $mealName;
    public $mealBreakfastId;
    public $mealBreakfastName;
    public $roomName;
    public $sharingBedding;
    public $cotsCount;
    public $childCount;
    public $_showName;
    public $specialOffer;
    public $offerText;
    public $childAges = array();
    public $roomInfo;
    public $rusNameFound;
    public $providerId;
    public $roomNameNemo;

    public function getShowName()
    {
        if($this->_showName){
            return $this->_showName;
        }else{
            if($this->roomNameNemo){
                $this->roomNameNemo->fillValues();

                if($this->roomNameNemo->rusName){
                    $this->_showName = $this->roomNameNemo->rusName;
                }else{
                    $this->_showName = $this->roomName;
                }
                return $this->_showName;
            }else{
                return '';
            }
        }
    }

    public function __construct($params)
    {
        $attrs = get_object_vars($this);
        foreach($attrs as $attrName=>$attrVal){
            if(isset($params[$attrName])){
                $this->{$attrName} = $params[$attrName];
            }
        }

        $roomNameCanonical = null;
        if($this->roomName){
            //$this->showName = $this->roomName;
            $roomInfo = $this->parseRoomName($this->roomName);
            $this->roomInfo = $roomInfo;

            $roomNameCanonical = $roomInfo['roomNameCanonical'];
        }
        $this->rusNameFound = false;

        //$needAddToDb = false;


        $this->roomNameNemo =& RoomNamesNemo::getNamesByParams($roomNameCanonical,$this->sizeId,$this->typeId);

        /*if(!$roomNameNemo){
            $needAddToDb = true;
            if($roomNameCanonical){
                $roomNameNemo = RoomNamesNemo::getNamesByParams($roomNameCanonical);
            }
        }
        if(!$roomNameCanonical && !$this->sizeId && !$this->typeId)
        {
            $needAddToDb = false;
        }

        if($needAddToDb){
            $newRoomNameNemo = new RoomNamesNemo();
            $newRoomNameNemo->roomNameCanonical = $roomNameCanonical;
            $newRoomNameNemo->roomSizeId = $this->sizeId;
            $newRoomNameNemo->roomTypeId = $this->typeId;
        }
        if($roomNameNemo){

            /** @var RoomNamesNemo */
            /*if($roomNameNemo->roomNameRusId){

                $roomNameRus = RoomNamesRus::getRoomNameRusByPk($roomNameNemo->roomNameRusId);

                if($roomNameRus){
                    $this->showName = $roomNameRus->roomNameRus;
                    $this->rusNameFound = true;
                    if($needAddToDb){
                        $newRoomNameNemo->roomNameRusId = $roomNameNemo->roomNameRusId;
                    }
                }
            }
        }
        if($needAddToDb){
            //echo "Try saving!";
            if(!$newRoomNameNemo->save()){
                //VarDumper::dump($newRoomNameNemo->getErrors());
            }
        }*/
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
            'showName'=>$this->showName,
            'roomNemoName'=>$this->roomName,
            'meal'=>$this->mealName,
            'mealBreakfast' => $this->mealBreakfastName,
            'cotsCount' => $this->cotsCount,
            'childCount' => $this->childCount,
            'childAges' => $this->childAges,
            'offer' => $this->roomInfo['offer'],
            'smoke' => $this->roomInfo['smoke'],
            'roomNameCanonical' => $this->roomInfo['roomNameCanonical'],
            'refundable' => $this->roomInfo['refundable'],
            'viewC' => $this->roomInfo['view'],
            'offerText' => $this->offerText,
            'providerId'=>$this->providerId,
        );
        foreach($ret as $key=>$val){
            if(!$val){
                unset($ret[$key]);
            }
        }

        return $ret;
    }

    /**
     * @static
     * @param $str
     * @param $words
     * @return bool
     */
    public static function stripWords(&$str,$words){
        //$replaced = false;
        $startLen = mb_strlen($str);

        $str = str_replace($words,'',$str);

        $endLen = mb_strlen($str);

        return ($endLen < $startLen);
    }

    /**
     * @static
     * @param $str
     * @param $words
     * @return bool
     */
    public static function findWords($str,$words){
        $find = false;
        if(is_array($words))
        {
            foreach($words as $word){
                if(mb_strpos($str,$word) !== false){
                    $find = true;
                    break;
                }
            }
        }
        elseif(is_string($words))
        {
            if(mb_strpos($str,$words) !== false){
                $find = true;
            }
        }

        return $find;
    }

    public function parseRoomName($roomName){
        /*
         * $roomInfo = array(
            'sizeId'=>null,
            'typeId'=>null,
            'typeName'=>null,
            'view'=>null,
            'breakfast'=>null,
            'refundable'=>null,
            'roomNameCanonical'=>null,
            'offer'=>null,
            'smoke'=>null
        );
         */
        $roomInfo = self::parseRoomNameStatic($roomName);
        if($this->specialOffer){
            $roomInfo['offer'] = $this->specialOffer;
        }
        /*if($this->viewName){
            $roomInfo['view'] = $this->viewName;
        }
        if($this->mealBreakfastName){
            $roomInfo['breakfast'] = true;
        }

        if($this->sizeId){
            $roomInfo['sizeId'] = $this->sizeId;
        }
        if($this->typeId){
            $roomInfo['typeId'] = $this->typeId;
        }*/
        return $roomInfo;
    }

    public static function parseRoomNameStatic($roomName){

        $roomInfo = array(
            'size'=>null,
            'typeId'=>null,
            'typeName'=>null,
            'view'=>null,
            'breakfast'=>null,
            'refundable'=>null,
            'roomNameCanonical'=>null,
            'offer'=>null,
            'smoke'=>null
        );
        /* TODO: функцию можно сильно ускорить если разбить всю roomName на слова,
         * и вначале проверять на налчие того, или иного слова
         * */
        $roomName = mb_convert_case($roomName, MB_CASE_LOWER, "UTF-8");
        /*if(self::stripWords($roomName,array(' standard','standard'))){

        }*/
       /* if(self::stripWords($roomName,array(' 1 bedroom',' one bedroom'))){

        }*/
        $roomName = str_replace(array('01 b/r','1 b/r','2 b/r',' s ',';',',','=','+','-','(',')','/'),' ',$roomName);
        self::stripWords($roomName,array(' room',' with shower','with terrace','included','includ'));

        //self::stripWords($roomName,array(' classic','classic',' offer',' offer-','offer','offer-'));


        if(self::findWords($roomName,array(' 2 bedroom',' two bedroom',' 3 bedroom'))){
            $roomInfo['typeName'] = 'suite senior';
        }
        if(self::findWords($roomName,array(' 2 people',' capacity 2',' dbl', ' double','dbl','double'))){
            $roomInfo['size'] = 'dbl';
        }elseif(self::findWords($roomName,array('twin','2 beds'))){
            $roomInfo['size'] = 'twin';
        }
        $roomName = str_replace(array('single','sngl'),'sgl',$roomName);
        //$roomName = str_replace(array('double'),'dbl',$roomName);
        $roomName = str_replace(array('triple'),'tpl',$roomName);
        $roomName = str_replace(array('quadruple',),'quad',$roomName);

        if(self::stripWords($roomName,array(' non refundable',' non-refundable','non refundable','standard-non-refundable','non-refundable','not refundable','not-refundable'))){
            $roomInfo['refundable'] = false;
        }
        if(self::stripWords($roomName,array(' refundable','refundable'))){
            $roomInfo['refundable'] = true;
        }
        if(self::findWords($roomName,'smok')){
            if(self::stripWords($roomName,array(' non-smoking',' nonsmoking',' non smoking',' nonsmoke',' non-smoke',' non smoke','nonsmoke','non-smoke','non smoke','non-smoking','nonsmoking','non smoking'))){
                $roomInfo['smoke'] = false;
            }
            if(self::stripWords($roomName,array(' smoking',' smoke','smoking','smoke'))){
                $roomInfo['smoke'] = true;
            }
        }
        $roomName = str_replace('apartments','apartment',$roomName);
        if(self::findWords($roomName,array('deluxe','de luxe'))){
            $roomInfo['typeName'] = 'deluxe';
        }
        if(self::stripWords($roomName,array(' seaview',' sea view','seaview','sea view'))){
            $roomInfo['view'] = 'sea';
        }
        //if(self::findWords($roomName,'view')){
        $views = array(
            'ocean',
            'pyramid',
            'nile',
            'city',
            'canal',
            'park',
            'lagoon',
            'river',
            'garden',
            'castle',
            'tower',
            'pool',
            'acropolis',
            'panoramic',
            'inland',
            'walk',
            'street',
            'atrium',
            'water',
            'terrace',
            'courtyard',
            'marina',
            'rome',
        );
        foreach($views as $view){
            if(self::stripWords($roomName,array(' '.$view.'view',$view.' views',$view.' view','view '.$view,' '.$view))){
                $roomInfo['view'] = $view;
                break;
            }
        }
        $views = array(
            'with',
        );
        foreach($views as $view){
            if(self::stripWords($roomName,array(' '.$view.'view',$view.' views',$view.' view','view '.$view))){
                $roomInfo['view'] = $view;
                break;
            }
        }
        if(($roomInfo['view'] == 'with') || (!$roomInfo['view'] && self::findWords($roomName,'view')) ){
            self::stripWords($roomName,array('views','view'));
            $roomInfo['view'] = true;
        }

        //}
        if(self::findWords($roomName,'break')){
            if(self::stripWords($roomName,array(' without breakfast',' without break'))){
                $roomInfo['breakfast'] = false;
            }
            if(self::stripWords($roomName,array(' + breakfast',' +breakfast',' with breakfast','+breakfast','breakfast',' with break','breakfas'))){
                $roomInfo['breakfast'] = true;
            }
        }
        if(self::stripWords($roomName,array('special offer',' specialoffer','offer'))){
            $roomInfo['offer'] = true;
        }

        if(self::findWords($roomName,array('studio'))){
            $roomInfo['typeName'] = 'studio';
        }
        if(self::findWords($roomName,'junior suite')){
            $roomInfo['typeName'] = 'junior suite';
        }
        $roomName = str_replace(array('  ','   ','    '),' ',$roomName);

        $roomInfo['roomNameCanonical'] = trim($roomName);

        return $roomInfo;
    }
}
