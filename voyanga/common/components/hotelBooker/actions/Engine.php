<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 02.07.12
 * Time: 11:14
 * To change this template use File | Settings | File Templates.
 */
class Engine extends CAction
{
    public function run($key)
    {
        //echo "INNN runnn";
        $parts = explode('_', $key);
        $cacheId = $parts[0];
        $searchId = $parts[1];
        $resultId = $parts[2];
        $resultSearch = Yii::app()->cache->get('hotelResult'.$cacheId);
        //TODO: need working without cache, if state more then enterCredentials

        if (!$resultSearch)
            throw new CHttpException(500, 'You request expired');

        $foundedHotel = null;
        foreach ($resultSearch['hotels'] as $hotel)
        {
            if ($hotel->resultId == $resultId)
            {
                $foundedHotel = $hotel;
                $foundedHotel->cacheId = $cacheId;
                break;
            }
        }

        if(!$foundedHotel)
        {
            $hotelBooker = HotelBooker::model()->findByAttributes(array('hotelResultKey'=>'hotel_key_'.$key));
            if($hotelBooker)
            {
                $foundedHotel = unserialize($hotelBooker->hotelInfo);
                $foundedHotel->cacheId = $cacheId;
            }
        }

        //$newHotel = (Yii::app()->user->getState('hotelResultKey') != $foundedHotel->getId());

        Yii::app()->hotelBooker->hotel = $foundedHotel;
        Yii::app()->hotelBooker->book();

        $status = Yii::app()->hotelBooker->current->swGetStatus()->getId();

        $actionName = 'stage'.ucfirst($status);
        VarDumper::dump($actionName);

        if ($action = $this->getController()->createAction($actionName))
        {
            $action->execute();
        }
        else
            Yii::app()->hotelBooker->$actionName();
    }
}
