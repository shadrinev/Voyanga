<?php Yii::import('site.common.modules.tour.models.*'); ?>
<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 09.06.12
 * Time: 13:41
 */
class FrontendConstructorController extends FrontendController
{
    public $tab='tour';

    public $defaultAction = 'create';

    public function actions()
    {
        return array(
            'create' => array('class'=>'site.common.modules.tour.actions.constructor.CreateAction'),
            'showEventTrip' => array('class'=>'site.common.modules.tour.actions.constructor.ShowEventTripAction'),
            'saveTour' => array('class'=>'site.common.modules.tour.actions.constructor.SaveTourAction'),
            'showTrip' => array('class'=>'site.common.modules.tour.actions.constructor.ShowTripAction'),
            'makeBooking' => array('class'=>'site.common.modules.tour.actions.constructor.MakeBookingAction'),
            'startPayment' => array('class'=>'site.common.modules.tour.actions.constructor.StartPaymentAction'),
            'getPayment' => array('class'=>'site.common.modules.tour.actions.constructor.GetPaymentAction'),
            'new' => array('class'=>'site.common.modules.tour.actions.constructor.NewAction'),
            'flightSearch' => array('class'=>'site.common.modules.tour.actions.constructor.FlightSearchAction'),
            'hotelSearch' => array('class'=>'site.common.modules.tour.actions.constructor.HotelSearchAction'),
            'showBasket' => array('class'=>'site.common.modules.tour.actions.constructor.ShowBasketAction'),
        );
    }
}
