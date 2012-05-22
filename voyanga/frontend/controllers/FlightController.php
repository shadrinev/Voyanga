<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 21.05.12
 * Time: 13:32
 */
class FlightController extends ApiController
{
    public function actionGetOptimalPrice($from, $to, $dateStart, $dateEnd, $forceUpdate)
    {
        try
        {
            $price = MFlightSearch::getOptimalPrice($from, $to, $dateStart, $dateEnd, $forceUpdate);
            $this->send($price);
            die();
        }
        catch (Exception $e)
        {
            $this->sendError(500, $e->getMessage());
        }
    }

    public function actionGetPrice($from, $to, $dateStart, $dateEnd, $forceUpdate)
    {
        try
        {
            Yii::app()->sharedMemory->erase();
            $cache = FlightCache::model()->findAll();
            foreach ($cache as $c)
                $c->save();
            //$price = MFlightSearch::getOptimalPrice($from, $to, $dateStart, $dateEnd, $forceUpdate);
            $result = Yii::app()->sharedMemory->read(true);
        }
        catch (Exception $e)
        {
            $this->sendError(500, $e->getMessage());
        }
    }
}
