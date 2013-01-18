<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 12.11.12
 * Time: 16:14
 * To change this template use File | Settings | File Templates.
 */
class EventInfoController extends Controller
{
    public function actionIndex()
    {
        $this->layout = 'static';
        $this->render('info');
    }

    public function actionInfo($eventId)
    {
        $event = Event::model()->findByPk($eventId);
        $defaultCityId = 4466;

        $pricesData = array();
        $this->layout = 'static';
        foreach($event->prices as $price){
            $pricesData[$price->city->id] = array('price'=>floor($price->bestPrice),'cityName'=>$price->city->localRu,'cityId'=>$price->city->id,'updateTime'=>$price->updated);
        }


        $tours = array();
        $dataProvider = new TripDataProvider();
        $cities = array();
        foreach($event->tours as $tour){

            $tours[$tour->startCityId] = array();
            $dataProvider->restoreFromDb($tour->orderId);
            //echo $tour->orderId.'dsf';

            //print_r($dataProvider->getSortedCartItemsOnePerGroup(false));die();
            $items = $dataProvider->getWithAdditionalInfo($dataProvider->getSortedCartItemsOnePerGroup(false));
            //print_r($items);die();
            $tours[$tour->startCityId] = $items;
            $tours[$tour->startCityId]['city'] = City::getCityByPk($tour->startCityId)->getAttributes();
            $cities[$tour->startCityId] = City::getCityByPk($tour->startCityId)->getAttributes();
        }
        if(!isset($cities[$defaultCityId])){
            foreach($cities as $defaultCityId=>$city)
                break;
        }
        //need search params
        $twoCities = array();
        $twoCities[$defaultCityId] = $cities[$defaultCityId];
        foreach($cities as $cityId=>$city)
            if(!isset($twoCities[$cityId])){
                $twoCities[$cityId] = $city;
                break;
            }



        //$tArr = array(array('test'=>3),array('test'=>1),array('test'=>2));
        //UtilsHelper::sortBy($tArr,'test');
        $pictures = array();
        foreach($event->pictures as $picture){
            $pictures[] = array('url'=>$picture->getUrl());
        }

        $this->render('info',array('event'=>$event,'priceData'=>$pricesData,'defaultCity'=>$defaultCityId,'tours'=>$tours,'cities'=>$cities,'twoCities'=>$twoCities,'pictures'=>$pictures));
    }
}
