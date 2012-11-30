<?php

Yii::import("common.extensions.payments.models.Payments_Channel");

class Payments_Channel_Gds_Galileo extends Payments_Channel {
    protected $name = 'gds_galileo';

    public function formParams() {
        $params = parent::formParams();
        //! FIXME: implement commission split
        $params['Commission'] = '0.00';//sprintf("%.2f", $this->booker->flightVoyage->commission);
        $params['PNR'] = 'ABCDFE';//$this->booker->pnr;
        $params['SecurityKey'] = $this->getSignature($params);
        $params['IData'] = '0192224171UTVOKUF                     029861572262150080610VKOSALINA NATALIA      ';
        return $params;
    }
}