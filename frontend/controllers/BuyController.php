<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 17.10.12
 * Time: 14:21
 */
class BuyController extends FrontendController
{
    private $keys;

    public function filters()
    {
        return array(
            'accessControl',
            // perform access control for CRUD operations
        );
    }

    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array(
                    'checkFlight',
                    'makeBooking',
                    'makeBookingForItem',
                    'startPayment',
                    'getPayment',
                    'new',
                    'flightSearch',
                    'hotelSearch',
                    'showBasket',
                    'index',
                    'done',
                    'waitpayment',
                    'status',
                    'paymentStatus',
                    'cancelBooking'
                )
            ),
            array(
                'allow',
                'actions' => array(
                    'order',
                    'pdf'
                ),
                'users' => array('@')
            ),
            array('deny'),
        );
    }

    public function actions()
    {
        return array(
            'makeBooking' => array('class' => 'site.common.modules.tour.actions.constructor.MakeBookingAction'),
            'makeBookingForItem' => array('class' => 'site.common.modules.tour.actions.constructor.MakeBookingForItemAction'),
            'startPayment' => array('class' => 'site.common.modules.tour.actions.constructor.StartPaymentAction'),
            'getPayment' => array('class' => 'site.common.modules.tour.actions.constructor.GetPaymentAction'),
            'new' => array('class' => 'site.common.modules.tour.actions.constructor.NewAction'),
            'flightSearch' => array('class' => 'site.common.modules.tour.actions.constructor.FlightSearchAction'),
            'hotelSearch' => array('class' => 'site.common.modules.tour.actions.constructor.HotelSearchAction'),
            'showBasket' => array('class' => 'site.common.modules.tour.actions.constructor.ShowBasketAction'),
        );
    }

    public function actionIndex()
    {
        Yii::app()->user->setState('blockedToBook', null);
        if (isset($_GET['pid']))
        {
            Yii::app()->user->setState('orderBookingId', null);
            Yii::app()->user->setState('todayOrderId', null);
        }
        $this->layout = 'main';
        $this->addItems();
        if (isset($_GET['item'][0]['module']))
            Yii::app()->user->setState('currentModule', $_GET['item'][0]['module']);
        else
            //todo: think what is default here
            Yii::app()->user->setState('currentModule', 'Avia');
        $marker = (isset($_GET['marker'])) ? '?marker=' . $_GET['marker'] : '';
        if (isset($_GET['pid'])) //если мы пришли от партнёра, то проверяем перелёт
        {
            Yii::app()->user->setState('fromPartner', 1);
        }
        else
        {
            Yii::app()->user->setState('fromPartner', 0);
        }
        $direct = isset($_GET['dir']);
        $orderBooking = $this->createNewOrderBooking($direct);
        $this->createBookers();
        $this->redirect('buy/makeBooking/secretKey/' . $orderBooking->secretKey . $marker);
    }

    public function actionCheckFlight()
    {
        Yii::app()->user->getState('directRequest', false);
        $result = true;
        $tdp = new TripDataProvider();
        $el = $tdp->getSortedCartItemsOnePerGroup();
        if ($el[0] instanceof FlightTripElement)
            $result = $el[0]->flightVoyage->getIsValid();
        echo json_encode(array('result' => $result));
    }

    public function actionCancelBooking()
    {
        $ids = $_POST['ids'];
        $ids = explode(',', $ids);
        foreach ($ids as $id)
        {
            $flightBookerComponent = new FlightBookerComponent();
            $flightBookerComponent->setFlightBookerFromId($id);
            $flightBookerComponent->status('canceledByUser');
        }
    }

    private function redirectToSearchResults()
    {
        $this->redirect('/');
    }

    public function actionOrder($id)
    {
        $secretKey = $id;
        $this->layout = 'static';
        $orderBooking = OrderBooking::model()->findByAttributes(array('secretKey' => $secretKey));
        if (!$orderBooking)
            throw new CHttpException(404, 'Page not found');
        if ($orderBooking->userId != Yii::app()->user->id)
            throw new CHttpException(403, 'Доступ запрещён');
        $tripStorage = new TripDataProvider($orderBooking->id);
        $trip = $tripStorage->getSortedCartItemsOnePerGroupAsJson();
        $orderId = $orderBooking->id;
        $readableOrderId = $orderBooking->readableId;
        list ($passports, $ambigous, $roomCounters) = $this->getPassports($tripStorage);
        list ($icon, $header) = $tripStorage->getIconAndTextForPassports();
        $this->render('complete', array(
                                       'trip' => $trip,
                                       'readableOrderId' => $readableOrderId,
                                       'orderId' => $orderId,
                                       'secretKey' => $secretKey,
                                       'ambigousPassports' => $ambigous,
                                       'passportForms' => $passports,
                                       'bookingForm' => $this->getBookingForm($orderBooking),
                                       'icon' => $icon,
                                       'header' => $header,
                                       'headersForAmbigous' => $tripStorage->getHeadersForPassportDataPage(),
                                       'roomCounters' => $roomCounters
                                  ));
    }

    public function actionStatus($id)
    {
        $secretKey = $id;
        $this->layout = 'static';
        $orderBooking = OrderBooking::model()->findByAttributes(array('secretKey' => $secretKey));
        if (!$orderBooking)
            throw new CHttpException(404, 'Page not found');
        $statuses = array();
        foreach ($orderBooking->flightBookers as $flightBooker)
        {
            $flightVoyage = $flightBooker->getFlightVoyage();
            $id = $flightVoyage->getId();
            $statuses[$id] = $this->normalizeStatus($flightBooker->status);
        }
        foreach ($orderBooking->hotelBookers as $hotelBooker)
        {
            $hotel = $hotelBooker->getHotel();
            $id = $hotel->getId();
            $statuses[$id] = $this->normalizeStatus($hotelBooker->status);
        }
        echo json_encode($statuses);
    }

    public function actionPdf($id)
    {
        $pdf = Yii::app()->pdfGenerator;
        $tsp = new TripDataProvider();
        $items = $tsp->getSortedCartItemsOnePerGroup();
        foreach ($items as $item)
        {
            if ($item instanceof HotelTripElement)
            {
                if ($item->getId() == $id)
                {
                    $fileNameInfo = $pdf->forHotelItem($item);
                    Yii::app()->request->sendFile($fileNameInfo['visibleName'], file_get_contents($fileNameInfo['realName']));
                    @unlink($fileNameInfo['realName']);
                    break;
                }
            }
            elseif ($item instanceof FlightTripElement)
            {
                if ($item->getId() == $id)
                {
                    $fileNameInfo = $pdf->forFlightItem($item);
                    Yii::app()->request->sendFile($fileNameInfo['visibleName'], file_get_contents($fileNameInfo['realName']));
                    @unlink($fileNameInfo['realName']);
                    break;
                }
            }
        }
    }

    private function normalizeStatus($status)
    {
        $pos = strpos($status, '/');
        if ($pos !== false)
            $status = substr($status, $pos + 1);
        return $status;
    }

    public function addItems($from = 'GET')
    {
        $items = ($from == 'GET') ? $_GET['item'] : $_POST['item'];
        Yii::app()->shoppingCart->clear();
        foreach ($items as $i => $item)
        {
            if (!isset($item['type']))
                continue;
            if ($item['type'] == 'avia')
                $this->addFlightToTrip($item['searchKey'], $item['searchId']);
            if ($item['type'] == 'hotel')
                $this->addHotelToTrip($item['searchKey'], $item['searchId']);
        }
    }

    public function addFlightToTrip($searchKey, $searchId)
    {
        //to override igbinary_unserialize_long: 64bit long on 32bit platform
        $flightSearchResult = Yii::app()->pCache->get('flightSearchResult' . $searchId);
        $flightSearchParams = Yii::app()->pCache->get('flightSearchParams' . $searchId);
        if (($flightSearchParams) and ($flightSearchResult))
        {
            foreach ($flightSearchResult->flightVoyages as $result)
            {
                if ($result->flightKey == $searchKey)
                    $this->addFlightTripElement($result, $flightSearchParams);
            }
        }
        else
            throw new CHttpException(500, 'Cache expired');
    }

    public function addHotelToTrip($searchKey, $searchId)
    {
        //to override igbinary_unserialize_long: 64bit long on 32bit platform
        $hotelSearchResult = @Yii::app()->pCache->get('hotelSearchResult' . $searchId);
        $hotelSearchParams = @Yii::app()->pCache->get('hotelSearchParams' . $searchId);
        if (($hotelSearchParams) and ($hotelSearchResult))
        {
            foreach ($hotelSearchResult->hotels as $result)
            {
                if ($result->resultId == $searchKey)
                    $this->addHotelTripElement($result, $hotelSearchParams);
            }
        }
        else
            throw new CHttpException(500, 'Cache expired');
    }

    public function addFlightTripElement($flight, FlightSearchParams $flightSearchParams)
    {
        $flightTripElement = new FlightTripElement();
        $key = md5(serialize($flightSearchParams));
        if (!isset($this->keys[$key]))
            $this->keys[$key] = 0;
        if ($this->keys[$key] == 1)
        {
            $flightTripElement->fillFromSearchParams($flightSearchParams, true);
        }
        else
            $flightTripElement->fillFromSearchParams($flightSearchParams, false);
        if ($flightSearchParams->isRoundTrip())
        {
            $this->keys[$key] = 1;
            $flightTripElement->setGroupId($key);
        }
        $flightTripElement->flightVoyage = $flight;
        Yii::app()->shoppingCart->put($flightTripElement);
    }

    public function addHotelTripElement($hotel, $hotelSearchParams)
    {
        $hotelTripElement = new HotelTripElement();
        $hotelTripElement->fillFromSearchParams($hotelSearchParams);
        Yii::import('site.common.modules.hotel.models.*');
        $hotelClient = new HotelBookClient();
        $hotelClient->hotelSearchDetails($hotel);
        $hotelTripElement->hotel = $hotel;
        Yii::app()->shoppingCart->put($hotelTripElement);
    }

    public function actionDone($ids)
    {
        $ids = explode(',', $ids);
        Yii::app()->order->setBookerIds($ids);
        Yii::app()->user->setState('blockedToBook', null);
    }

    public function actionWaitpayment()
    {
        $this->layout = false;
        $order = Yii::app()->order;
        $this->render('waiting', array(
                                      'order' => $order
                                 ));

    }

    public function actionPaymentstatus()
    {
        $order = Yii::app()->order;
        $payments = Yii::app()->payments;
        $bookers = $payments->preProcessBookers($order->getBookers());
        $paid = true;
        $error = false;
        foreach ($bookers as $booker)
        {
            if (!in_array($payments->getStatus($booker), Array(
                                                              'paid',
                                                              'ticketReady',
                                                              'done',
                                                              'ticketing',
                                                              'ticketingRepeat'
                                                         ))
            )
            {
                $paid = false;
            }
            if (strpos(strtolower($payments->getStatus($booker)), 'error') !== FALSE)
            {
                $error = true;
            }
        }
        header("Content-type: application/json");
        echo json_encode(Array(
                              "paid" => $paid,
                              "error" => $error
                         ));
    }

    private function getPassports($tripStorage)
    {
        $items = $tripStorage->getSortedCartItems();
        $passportManager = new PassportManager();
        $passportManager->tripItems = $items;
        $ambigousPassports = $passportManager->generatePassportForms();
        $passports = $this->restorePassportsFromDb($items, $ambigousPassports);
        $passportManager->fillFromArray($passports);
        $roomCounters = (sizeof($passportManager->roomCounters) > 0) ? $passportManager->roomCounters : false;
        return array(
            $passportManager->passportForms,
            $ambigousPassports,
            $roomCounters
        );
    }

    private function restorePassportsFromDb($items, $ambigous)
    {
        if (!$ambigous)
        {
            $passports = array();
            foreach ($items as $item)
            {
                $passports = array_merge($passports, $item->getPassportsFromDb());
            }
        }
        return $passports;
    }

    private function getBookingForm($model)
    {
        $bookingForm = new BookingForm();
        $bookingForm->contactEmail = $model->email;
        $bookingForm->contactPhone = $model->phone;
        return $bookingForm;
    }

    private function createNewOrderBooking($direct)
    {
        if ($direct)
            $directValue = 1;
        else
            $directValue = 0;
        if (is_numeric(Yii::app()->user->getState('todayOrderId')))
            return OrderBooking::model()->findByAttributes(array('readableId' => Yii::app()->user->getState('todayOrderId')));
        $orderBooking = new OrderBooking();
        $orderBooking->secretKey = md5(microtime() . time() . appParams('salt'));
        $orderBooking->readableId = ""; //to prevent warning about "string should be here" of EAdvancedArBehavior
        $orderBooking->direct = $directValue;
        $orderBooking->meta = serialize($_SERVER);
        $orderBooking->save();
        $todayOrderId = OrderBooking::model()->count(array('condition' => "DATE(`timestamp`) = CURDATE()"));
        $readableNumber = OrderBooking::buildReadableNumber($todayOrderId);
        $orderBooking->saveAttributes(array('readableId' => $readableNumber));
        Yii::app()->user->setState('orderBookingId', $orderBooking->id);
        Yii::app()->user->setState('todayOrderId', $readableNumber);
        Yii::app()->user->setState('secretKey', $orderBooking->secretKey);
        return $orderBooking;
    }

    private function createBookers()
    {
        $dataProvider = new TripDataProvider();
        $itemsOnePerGroup = $dataProvider->getSortedCartItemsOnePerGroup();
        foreach ($itemsOnePerGroup as $item)
        {
            $tripElementWorkflow = $item->createTripElementWorkflow();
            $tripElementWorkflow->createRecordsForItem();
            $tripElementWorkflow->updateBookingId();
        }
    }
}
