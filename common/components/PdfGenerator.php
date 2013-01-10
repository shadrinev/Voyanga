<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mihan007
 */
class PdfGenerator extends CApplicationComponent
{
    private $hotelClient;
    public $orderBookingId=false;
    public $orderBooking=false;
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
            $loop = true;
            $count = 0;
            while($loop){
                $voucherInfo = $this->hotelClient->voucher($hotelBooker->orderId);
                $voucherAvailable = true;
                //VarDumper::dump($voucherInfo);
                //die();
                //UtilsHelper::soapObjectsArray($voucherInfo->voucherAvailable);
                foreach($voucherInfo->voucherAvailable as $avail)
                    $voucherAvailable = $voucherAvailable && ($avail ? ($avail !='0' ? true : false) : false);
                if($voucherAvailable){
                    $loop = false;
                    break;
                }else{
                    $count++;
                    if($count > 2){
                        $loop = false;
                        return false;
                        break;
                    }
                    sleep(10);
                }
            }
            $hotelInfo = $this->hotelClient->hotelDetail($hotelBooker->hotel->hotelId);
            $this->hotelClient->hotelSearchDetails($hotelBooker->hotel);
            $pnr = implode(', ',$voucherInfo->references);
            if($voucherInfo->suppliers){
                $pnr .= ' ('.implode(', ',$voucherInfo->suppliers).')';
            }
            if(!$this->orderBookingId){
                $this->orderBookingId = $hotelBooker->orderBookingId;
                $this->orderBooking = OrderBooking::model()->findByPk($this->orderBookingId);
            }
            $pdfFileName = $this->controller->renderPdf('ticketHotel',array(
                'type'=>'hotel',
                'ticket'=>$hotelBooker->hotel,
                'bookingId'=>$this->orderBooking->readableId,
                'pnr'=>$pnr,
                'hotelPassports'=>$hotelPassports,
                'hotelInfo'=>$hotelInfo
            ));
            return array('realName'=>$pdfFileName,'visibleName'=>"hotel_{$hotelBooker->hotel->city->code}_".date('Ymd',strtotime($hotelBooker->hotel->checkIn)).".pdf");
        }
    }

    public function forFlightItem($item)
    {
        $flightBooker = FlightBooker::model()->findByPk($item->flightBookerId);
        $flightPassports = FlightBookingPassport::model()->findAllByAttributes(array('flightBookingId'=>$item->flightBookerId));
        if ($flightBooker)
        {
            if(!$this->orderBookingId){
                $this->orderBookingId = $flightBooker->orderBookingId;
                $this->orderBooking = OrderBooking::model()->findByPk($this->orderBookingId);
            }
            $pdfFileName = $this->controller->renderPdf('ticketAvia', array(
                'type'=>'avia',
                'ticket'=>$flightBooker->flightVoyage,
                'bookingId'=>$this->orderBooking->readableId,
                'pnr'=>$flightBooker->pnr,
                'flightPassports'=>$flightPassports,
            ));
            return array('realName'=>$pdfFileName,'visibleName'=>"avia_{$flightBooker->flightVoyage->departureCity->code}_{$flightBooker->flightVoyage->arrivalCity->code}_".date('Ymd',strtotime($flightBooker->flightVoyage->departureDate)).".pdf");
        }
    }
}
