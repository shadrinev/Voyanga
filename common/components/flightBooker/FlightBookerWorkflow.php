<?php
return array(
    'initial' => 'enterCredentials',
    'node' => array(
        array('id'=>'enterCredentials',         'transition'=> 'booking'),
        array('id'=>'booking',                  'transition'=>'waitingForPayment,bookingError'),
        array('id'=>'bookingError',             'transition'=>'error'),
        array('id'=>'waitingForPayment',        'transition'=>'paymentInProgress, paymentError,bookingTimeLimitError'),
        array('id'=>'paymentInProgress',        'transition'=>'paid,paymentCanceledError,paymentError,startPayment,bookingTimeLimitError'),
        array('id'=>'paymentError',             'transition'=>'error,waitingForPayment'),
        //! payment canceled due to other segment failure
        array('id'=>'paymentCanceledError',      'transition'=>'error'),
        array('id'=>'paid',                     'transition'=>'ticketing,refundedError'),
        //! refunded duo to error in process not a user request
        array('id'=>'refundedError',            'transition'=>'error'),
        array('id'=>'bookingTimeLimitError',    'transition'=>'bookingTimeLimitError,error'),
        array('id'=>'ticketing',                'transition'=>'done,ticketingRepeat,paid'),
        array('id'=>'ticketingRepeat',          'transition'=>'ticketingRepeat,ticketingError,done'),
        array('id'=>'ticketingError',           'transition'=>'error,manualSuccess,ticketing'),
        array('id'=>'manualSuccess',            'transition'=>'done'),
        array('id'=>'done',                     'transition'=>'canceled'),
        array('id'=>'error'),
        array('id'=>'canceled')
    )
);
