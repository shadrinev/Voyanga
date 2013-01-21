<?php

Yii::import("common.extensions.payments.models.Payments_Channel");

class Payments_Channel_Gds_Galileo extends Payments_Channel {
    protected $name = 'gds_galileo';

    public function formParams() {
        $params = parent::formParams();
        //! FIXME MOVE TO BILL
        $charges = $this->baseBooker->flightVoyage->charges;
        if($charges<0)
            $charges = 0;
        //! FIXME: implement commission split
        $params['Commission'] = sprintf("%.2f", $charges);
        $params['PNR'] = $this->baseBooker->pnr;
        $params['SecurityKey'] = $this->getSignature($params);
        return $params;
    }
}