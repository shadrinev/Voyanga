<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mihan007
 */
class PdfGenerator extends CApplicationComponent
{
    private $hotelClient;
    public $orderBookingId=false;
    private $controller;

    public function init()
    {
        Yii::import('site.common.modules.hotel.models.*');
        $this->hotelClient = new HotelBookClient();
        $this->controller = new Controller('pdf');
    }

    public function forHotelItem($item)
    {
        $hotelBooker = HotelBooker::model()->findByPk($item->hotelBookerId);
        $hotelPassports = HotelBookingPassport::model()->findAllByAttributes(array('hotelBookingId'=>$item->hotelBookerId));
        if ($hotelBooker)
        {
            $voucherInfo = $this->hotelClient->voucher($hotelBooker->orderId);
            $hotelInfo = $this->hotelClient->hotelDetail($hotelBooker->hotel->hotelId);
            $this->hotelClient->hotelSearchDetails($hotelBooker->hotel);
            $pnr = implode(', ',$voucherInfo->references);
            if($voucherInfo->suppliers){
                $pnr .= ' ('.implode(', ',$voucherInfo->suppliers).')';
            }
            if(!$this->orderBookingId)
                $this->orderBookingId = $hotelBooker->orderBookingId;
            $pdfFileName = $this->controller->renderPdf('ticketHotel',array(
                'type'=>'hotel',
                'ticket'=>$hotelBooker->hotel,
                'bookingId'=>$this->orderBookingId,
                'pnr'=>$pnr,
                'hotelPassports'=>$hotelPassports,
                'hotelInfo'=>$hotelInfo
            ));
            return $pdfFileName;
        }
    }

    public function forFlightItem($item)
    {
        $flightBooker = FlightBooker::model()->findByPk($item->flightBookerId);
        $flightPassports = FlightBookingPassport::model()->findAllByAttributes(array('flightBookingId'=>$item->flightBookerId));
        if ($flightBooker)
        {
            if(!$this->orderBookingId)
                $this->orderBookingId = $flightBooker->orderBookingId;
            $pdfFileName = $this->controller->renderPdf('ticketAvia', array(
                'type'=>'avia',
                'ticket'=>$flightBooker->flightVoyage,
                'bookingId'=>$flightBooker->orderBookingId,
                'pnr'=>$flightBooker->pnr,
                'flightPassports'=>$flightPassports,
            ));
            return $pdfFileName;
        }
    }
}
