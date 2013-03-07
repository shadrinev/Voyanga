<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mihan007
 * Date: 07.03.13
 * Time: 16:09
 * To change this template use File | Settings | File Templates.
 */

class StatisticsController extends ApiController
{
    public function actionIndex($date1, $date2, $partner, $password)
    {
        $this->checkCredentials($partner, $password);
        $from = strtotime($date1) + 4 * 3600;
        $from = date('Y-m-d H:i:s', $from);
        $to = strtotime($date2) + 4 * 3600 + (24 * 3600 - 1);
        $to = date('Y-m-d H:i:s', $to);
        $criteria = new CDbCriteria();
        $criteria->compare('partnerId', Partner::getCurrentPartner()->id);
        $criteria->addCondition('timestamp >= \''.$from.'\'');
        $criteria->addCondition('timestamp <= \''.$to.'\'');
        $orders = OrderBooking::model()->findAll($criteria)
        VarDumper::dump($orders); die();
        $results = array();
        foreach ($orders as $i=>$order)
        {
            $state = $order->status;
            $price = $order->fullPrice;
            $el = array(
                'id' => $order->readableId,
                'created_at' => date('Y-m-d H:i', strtotime($order->timestamp) - 4*3600),
                'marker' => $order->marker,
                'price' => $price,
                'profit' => 0,
                'currency' => 'RUB',
                'state' => $state
            );
            if ($price>0)
                $results['order'.$i] = $el;
        }
        $xml = new ArrayToXml('bookings');
        $prepared = $xml->toXml($results);
        $prepared = preg_replace('/order\d+/', 'booking', $prepared);
        $this->data = $prepared;
        $this->_sendResponse(true, 'application/xml');
    }

    private function checkCredentials($u, $p)
    {
        $partner = Partner::model()->findByAttributes(array('name'=>$u));
        if (($partner) && ($partner->verifyPassword($p)))
        {
            Partner::setPartnerByName($u);
            return;
        }
        $this->sendError(403, 'Permission denied');
        Yii::app()->end();
    }
}