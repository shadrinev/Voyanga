<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 19.07.12
 * Time: 12:18
 * To change this template use File | Settings | File Templates.
 */

Yii::import("common.extensions.payments.models.Bill");

class OrderBookingController extends Controller
{

    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('item',array(
            'data'=>$this->getOrderInfo($id),
        ));
    }

    /**
     * Lists all models.
     */
    public function actionIndex($search='',$all=false)
    {
        $criteria = array('order'=>'t.timestamp DESC');
        $navText = 'Показать без мусора';
        $navLink = $this->createUrl('index');

        if(!$all) {
            $criteria['with'] = array('hotelBookers', 'flightBookers');
            $criteria['together'] = true;
            $criteria['condition'] = "hotelBookers.status!='enterCredentials' and hotelBookers.status!='swHotelBooker/enterCredentials' and flightBookers.status!='enterCredentials' and flightBookers.status!='swFlightBooker/enterCredentials'";
            $navText = 'Показать все';
            $navLink = $this->createUrl('index', array('all'=>true));
        }

        if($search) {
            $s = $search;
            $criteria['with'] = array('hotelBookers', 'flightBookers');
            $criteria['together'] = true;
            $conds = Array();
            $conds[] = "flightBookers.nemoBookId = '$s'";
            $conds[] = "flightBookers.pnr = '$s'";
            $conds[] = "flightBookers.status = '$s'";
            $conds[] = "hotelBookers.status = '$s'";
            $conds[] = "email='$s'";
            $conds[] = "phone='$s'";
            $criteria['condition'] = implode(" OR ", $conds);

        }


        //$dataProvider=new EMongoDocumentDataProvider('GeoNames',array('criteria'=>array('conditions'=>array('iataCode'=>array('type'=>2)) )));
        //$dataProvider=new EMongoDocumentDataProvider('GeoNames',array('criteria'=>array('conditions'=>array('iataCode'=>array('type'=>2)) )));
        $dataProvider=new CActiveDataProvider('OrderBooking', array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>100,
            )
        ));
        $this->render('index',array(
            'dataProvider'=>$dataProvider,
            'navText' => $navText,
            'navLink' => $navLink,
        ));
    }

    public function actionGetInfo($id)
    {
        /** @var OrderBooking $model  */
        $model = $this->loadModel($id);

        $retArr = $model->attributes;
        $retArr['userDescription'] = $model->userDescription;
        $retArr['bookings'] = array();

        foreach($model->flightBookers as $flightBooker){
            $booking = array();
            $booking['status'] = $model->stateAdapter($flightBooker->status);
            $booking['type'] = 'Авиа';
            $booking['className'] = get_class($flightBooker);
            $booking['modelId'] = $flightBooker->id;
            $booking['description'] = $flightBooker->fullDescription;

            if($flightBooker->price){
                $booking['price'] = $flightBooker->price;
            }
            else
            {
                if($flightBooker->flightVoyage->price){
                    $booking['price'] = $flightBooker->flightVoyage->price;
                }
            }
            //! FIXME use YII urlgen
            $wfStates = WorkflowStates::model()->findByAttributes(array('className'=>'FlightBooker', 'objectId'=>$flightBooker->id));
            if(count($wfStates)==1)
            {
                $booking['wfUrl'] = '/admin/logging/workflowStates#'.$wfStates->_id;
            }

            $retArr['bookings'][] = $booking;
        }

        foreach($model->hotelBookers as $hotelBooker){
            $booking = array();
            $booking['status'] = $model->stateAdapter($hotelBooker->status);
            $booking['type'] = 'Отель';
            $booking['description'] = $hotelBooker->fullDescription;
            if($hotelBooker->price){
                $booking['price'] = $hotelBooker->price;
            }
            else
            {
                if(isset($hotelBooker->hotel)){
                    if(isset($hotelBooker->hotel->rubPrice)){
                        $booking['price'] = $hotelBooker->hotel->rubPrice;
                    }
                }
            }
            //! FIXME use YII urlgen
            $wfStates = WorkflowStates::model()->findByAttributes(array('className'=>'HotelBooker', 'objectId'=>$hotelBooker->id));
            if(count($wfStates)==1)
            {
                $booking['wfUrl'] = '/admin/logging/workflowStates#'.$wfStates->_id;
            }

            $retArr['bookings'][] = $booking;
        }

        echo json_encode($retArr);
    }

    private function getOrderInfo($id)
    {
        /** @var OrderBooking $model  */
        $model = $this->loadModel($id);

        $retArr = $model->attributes;
        $retArr['orderBooking'] = $model;
        $retArr['userDescription'] = $model->userDescription;
        $retArr['flightBookings'] = array();
        $retArr['hotelBookings'] = array();

        foreach($model->flightBookers as $flightBooker) {
            $retArr['flightBookings'][] = $flightBooker;
//            die(var_dump($flightBooker->flightVoyage->taxes));
        }

        foreach($model->hotelBookers as $hotelBooker){
            $retArr['hotelBookings'][] = $hotelBooker;
        }
        return $retArr;
    }


    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model=OrderBooking::model()->findByPk($id);

        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }


    public function actionResendEmail($id) {
        $res = Yii::app()->cron->add(time() + 75, 'orderemail', 'cron', array('orderId'=>$id));
        $this->redirect(Array('view', 'id'=>$id));
    }

    /**
    * Returns map {title=>url} of actions availiable for this orderBooking
    */
    protected function orderActions($orderBooking) {
        switch ($orderBooking->rawOrderStatus) {
            case 'done':
                $result = array();
                $result["Выслать письмо повторно"] = $this->createUrl('resendEmail', array('id'=>$orderBooking->id));
                $result["Отменить заказ"] = $this->createUrl('cancelOrder', array('id'=>$orderBooking->id));
                return $result;
            default:
                return array();
        }
    }

    public function actionCancelOrder($id) {
        $data = $this->getOrderInfo($id);
        if($data['orderBooking']->rawOrderStatus !== 'done') {
            return;
        }
        $billIds = Array();
        foreach ($data['flightBookings'] as $booker) {
            $bookerComponent = new FlightBookerComponent();
            $bookerComponent->setFlightBookerFromId($booker->id);
            $bookerComponent->status('canceled');
            $billIds[$booker->bill->transactionId]=1;
        }
        foreach ($data['hotelBookings'] as $booker) {
            $bookerComponent = new HotelBookerComponent();
            $bookerComponent->setHotelBookerFromId($booker->id);
            $bookerComponent->status('canceled');
            $booker->status('canceled');
            $billIds[$booker->bill->transactionId]=1;
        }
        $res = Yii::app()->cron->add(time() + 75, 'ordercanceledemail', 'cron', array('orderId'=>$id));
        echo "<h1>Заказ отменен<h1>";
        echo "Транзакции пейонлайна для отмены:";
        echo "<ul>";
        foreach(array_keys($billIds) as $billId) {
            echo "<li>" . $billId . "</li>";
        }
        echo "</ul>";
        echo "<a href='" . $this->createUrl('index', array('id'=>$data['orderBooking']->id)) . "'>Назад</a>";
   }


    /**
    * Returns map {title=>url} of actions availiable for this booker
    */
    protected function adminActions($booker) {
        if($booker instanceof FlightBooker)
            return $this->flightAdminActions($booker);
        if($booker instanceof HotelBooker)
            return $this->hotelAdminActions($booker);
    }

    private function flightAdminActions($booker) {
        switch ($booker->status) {
            case 'swFlightBooker/done':
                $result = array();
                if($booker->bill->status=='PRE') {
                    $result['подтвердить списание'] =  $this->createUrl('confirmBill', array('billId'=>$booker->bill->id));
                }
                return $result;
            case 'swFlightBooker/waitingForPayment':
                return array('Отметить как оплаченный' => $this->createUrl('markFlightPaid', array('bookingId'=>$booker->id)));
            case 'swFlightBooker/paid':
                 return array("Автовыписка(без письма)" => $this->createUrl('ticketFlight', array('bookingId'=>$booker->id)));
            case 'swFlightBooker/ticketingError':
                 return array("Автовыписка(без письма)" => $this->createUrl('ticketFlight', array('bookingId'=>$booker->id)));
            default:
                return array();

        }
    }


    private function hotelAdminActions($booker) {
        switch ($booker->status) {
            case 'swHotelBooker/waitingForPayment':
                # code...
                return array("Отметить как оплаченный" => $this->createUrl('markHotelPaid', array('bookingId'=>$booker->id)));
            case 'swHotelBooker/paid':
                 return array("Автовыписка(без письма)" => $this->createUrl('ticketHotel', array('bookingId'=>$booker->id)));
            default:
                return array();
        }
    }

    /*
    Booking actions
    */
    public function actionMarkHotelPaid($bookingId) {
        $booking = new HotelBookerComponent();
        $booking->setHotelBookerFromId($bookingId);
        $booking->status('paymentInProgress');
        $booking->status('paid');

        $this->redirect(Array('view', 'id'=>$booking->getCurrent()->orderBookingId));
    }

    public function actionMarkFlightPaid($bookingId) {
        $booking = new FlightBookerComponent();
        $booking->setFlightBookerFromId($bookingId);
        $booking->status('paymentInProgress');
        $booking->status('paid');

        $this->redirect(Array('view', 'id'=>$booking->getCurrent()->orderBookingId));
    }

    public function actionTicketFlight($bookingId) {
        $booking = new FlightBookerComponent();
        $booking->setFlightBookerFromId($bookingId);
        $payments = Yii::app()->payments;
        $payments->notifyNemo($booking);
        $booking->status('ticketing');
        $this->redirect(Array('view', 'id'=>$booking->getCurrent()->orderBookingId));
    }

    public function actionTicketHotel($bookingId) {
        $booking = new HotelBookerComponent();
        $booking->setHotelBookerFromId($bookingId);
        $booking->status('ticketing');
        $this->redirect(Array('view', 'id'=>$booking->getCurrent()->orderBookingId));
    }

    public function actionConfirmBill($billId) {
        $bill = Bill::model()->findByPk($billId);
        if(!$bill->getChannel()->confirm())
            die( "FIALED");
        $this->redirect(Array('view', 'id'=>$bill->getChannel()->getOrderBookingId()));
    }

    public function actionInjectTicketNumbers($bookingId) {
        $booking = new FlightBookerComponent();
        $booking->setFlightBookerFromId($bookingId);
        foreach($_POST['tickets'] as $passId => $ticket) {
            $pass = FlightBookingPassport::model()->findByPk($passId);
            $pass->ticketNumber = $ticket;
            $pass->save();
        }
        $flightVoyage = $booking->getCurrent()->flightVoyage;

        foreach($_POST['terminal'] as $fvkey=>$value) {
            foreach($value as $fkey=>$terminalCode) {
                if($terminalCode)
                    $flightVoyage->flights[$fvkey]->flightParts[$fkey]->departureTerminalCode = $terminalCode;
            }
        }
        //! force serialization, save will be called on later status change
        $booking->getCurrent()->flightVoyage = $flightVoyage;
        if($booking->getCurrent()->status == 'swFlightBooker/ticketing') {
            //! Does nothing but sets ticketingError status
            $booking->status('ticketingRepeat');
        }
        $booking->status('manualSuccess');
        $this->redirect(Array('view', 'id'=>$booking->getCurrent()->orderBookingId));
    }

}
