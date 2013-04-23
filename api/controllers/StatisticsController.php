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
    private $flights = array();

    public function actionIndex($date1, $date2, $partner, $password)
    {
        $this->checkCredentials($partner, $password);
        $from = strtotime($date1) + 4 * 3600;
        $from = date('Y-m-d H:i:s', $from);
        $to = strtotime($date2) + 4 * 3600 + (24 * 3600 - 1);
        $to = date('Y-m-d H:i:s', $to);
        $criteria = new CDbCriteria();
        $criteria->select = 'direct, readableId, partnerId, timestamp, marker, hash, partner_status, full_partner_price';
        $criteria->compare('partnerId', Partner::getCurrentPartner()->id);
        $criteria->addCondition('t.timestamp >= \'' . $from . '\'');
        $criteria->addCondition('t.timestamp <= \'' . $to . '\'');
        $orders = OrderBooking::model()->with(
            array('flightBookers'=> array(
                'select' => 'id, status, flightVoyageInfo'
            )))->findAll($criteria);
        $results = array();
        $ordersReady = array();
        foreach ($orders as $i => $order)
        {
            $state = $order->partner_status;
            if (!$this->isUniqueOrder($order, $state))
                continue;
            $ordersReady[] = $order;
        }
        unset($orders);
        foreach ($ordersReady as $i => $order)
        {
            $price = $order->full_partner_price;
            $state = $this->flights[$order->hash];
            $el = array(
                'id' => $order->readableId,
                'created_at' => date('Y-m-d H:i', strtotime($order->timestamp) - 4 * 3600),
                'marker' => $order->marker,
                'price' => $price,
                'profit' => 0,
                'currency' => 'RUB',
                'state' => $state
            );
            if (($state=='PAID') || ($order->direct==1))
            {
                if ($price > 0)
                    $results['order' . $i] = $el;
            }
        }
        unset($ordersReady);
        $xml = new ArrayToXml('bookings');
        $prepared = $xml->toXml($results);
        $prepared = preg_replace('/order\d+/', 'booking', $prepared);
        $this->data = $prepared;
        $this->_sendResponse(true, 'application/xml');
    }

    /**
     * Служит для склеивания похожих заказов одного юзера в статистике для метапоиска
     *
     * @param $order Заказ
     * @param $state Его статус
     * @return bool Нужно ли показывать этот заказ в статистике
     */
    private function isUniqueOrder($order, $state)
    {
        $hash = $order->hash;
        if ($state == 'PAID')
        {
            $this->flights[$hash] = $state;
            return true;
        }
        if (!isset($this->flights[$hash]))
        {
            $this->flights[$hash] = $state;
            return true;
        }
        else
        {
            if (($state == 'PROCESSING') and ($this->flights[$hash] == 'CANCELLED'))
            {
                $this->flights[$hash] = 'PROCESSING';
            }
            return false;
        }
    }

    private function checkCredentials($u, $p)
    {
        $partner = Partner::model()->findByAttributes(array('name' => $u));
        if (($partner) && ($partner->verifyPassword($p)))
        {
            Partner::setPartnerByName($u);
            return;
        }
        $this->sendError(403, 'Permission denied');
        Yii::app()->end();
    }
}