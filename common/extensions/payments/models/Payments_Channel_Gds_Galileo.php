<?php

Yii::import("common.extensions.payments.models.Payments_Channel");

class Payments_Channel_Gds_Galileo extends Payments_Channel {
    protected $name = 'gds_galileo';

    public function formParams() {
        $params = parent::formParams();
        //! FIXME: implement commission split
        $params['Commission'] = sprintf("%.2f", $this->booker->flightVoyage->commission);
        $params['PNR'] = $this->booker->pnr;
        return $params;
    }
}