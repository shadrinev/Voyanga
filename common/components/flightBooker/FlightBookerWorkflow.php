<?php
return array(
    'initial' => 'enterCredentials',
    'node' => array(
        array('id'=>'enterCredentials',         'transition'=> 'booking'),
        array('id'=>'booking',                  'transition'=>'waitingForPayment,bookingError'),
        array('id'=>'bookingError',             'transition'=>'error'),
        array('id'=>'waitingForPayment',        'transition'=>'paymentInProgress, paymentError'),
        array('id'=>'paymentInProgress',        'transition'=>'paid,paymentCanceledError,paymentError,startPayment,bookingTimeLimitError'),
        array('id'=>'paymentError',             'transition'=>'error'),
        //! payment canceled due to other segment failure
        array('id'=>'paymentCanceledError',      'transition'=>'error'),
        array('id'=>'paid',                     'transition'=>'ticketing,refundedError'),
        //! refunded duo to error in process not a user request
        array('id'=>'refundedError',            'transition'=>'error'),
        array('id'=>'bookingTimeLimitError',    'transition'=>'bookingTimeLimitError,error'),
        array('id'=>'ticketing',                'transition'=>'ticketReady,ticketingRepeat'),
        array('id'=>'ticketReady',              'transition'=>'confirmMoney, done'),
        array('id'=>'ticketingRepeat',          'transition'=>'ticketingRepeat,ticketingError,ticketReady,manualProcessing'),
        array('id'=>'manualProcessing',         'transition'=>'ticketingError,manualTicketing'),
        array('id'=>'manualTicketing',          'transition'=>'manualSuccess,manualError'),
        array('id'=>'ticketingError',           'transition'=>'moneyReturn'),
        array('id'=>'manualError',              'transition'=>'moneyReturn'),
        array('id'=>'moneyReturn',              'transition'=>'error'),
        array('id'=>'manualSuccess',            'transition'=>'done'),
        array('id'=>'confirmMoney',             'transition'=>'done'),
        array('id'=>'done'),
        array('id'=>'error')
    )
);
