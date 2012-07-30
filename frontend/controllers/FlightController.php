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

    public function actionFullCache()
    {
        try
        {
            //Yii::app()->sharedMemory->erase();
            $cache = FlightCache::model()->findAll();
            for($i=0; $i<1000; $i++)
            {
                foreach ($cache as $c)
                    $c->save();
            }
            $result = Yii::app()->sharedMemory->read(true);
        }
        catch (Exception $e)
        {
            $this->sendError(500, $e->getMessage());
        }
    }

    public function actionFullCache2()
    {
        try
        {
            //Yii::app()->sharedMemory->erase();
            $cache = FlightCache::model()->findAll(array('limit'=>100));
            for($i=0; $i<20; $i++)
            {
                foreach ($cache as $c)
                {
                    $from = rand(1, 6000);
                    $to = rand(1, 6000);
                    $c->from = $from;
                    $c->to = $to;
                    $c->save();
                }
            }
            $result = Yii::app()->sharedMemory->read(true);
        }
        catch (Exception $e)
        {
            $this->sendError(500, $e->getMessage());
        }
    }
}
