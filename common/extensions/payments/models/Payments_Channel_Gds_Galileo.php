<?php

Yii::import("common.extensions.payments.models.Payments_Channel");

class Payments_Channel_Gds_Galileo extends Payments_Channel {
    protected $name = 'gds_galileo';

    public function formParams($booker) {
        $params = parent::formParams($booker);
        //! FIXME: implement commission split
        $params['Commission'] = sprintf("%.2f", $booker->flightVoyage->commission);
        $params['PNR'] = $booker->pnr;
        return $params;
    }
}