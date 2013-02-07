<?php
return array(
    'initial' => 'enterCredentials',
    'node' => array(
        array('id'=>'enterCredentials',         'transition'=>'waitingForPayment'),
        //! FIXME do we need timelimit state
        array('id'=>'waitingForPayment',        'transition'=>'bookingTimeLimitError,paymentInProgress,paymentError'),
        array('id'=>'paymentInProgress',        'transition'=>'paid,paymentCanceledError,paymentError,bookingTimeLimitError'),
        array('id'=>'bookingTimeLimitError',    'transition'=>'error'),
        array('id'=>'paymentError',             'transition'=>'error'),
        array('id'=>'paid',                     'transition'=>'ticketing'),
        array('id'=>'ticketing',                'transition'=>'ticketReady,ticketingRepeat,ticketingError'),
        array('id'=>'ticketReady',              'transition'=>'moneyTransfer,done'),
        array('id'=>'ticketingRepeat',          'transition'=>'ticketingRepeat,ticketReady,manualProcessing,ticketingError'),
        array('id'=>'manualProcessing',         'transition'=>'ticketingError,manualTicketing'),
        array('id'=>'manualTicketing',          'transition'=>'manualSuccess,manualError'),
        array('id'=>'ticketingError',           'transition'=>'manualSuccess,error'),
        array('id'=>'manualError',              'transition'=>'moneyReturn'),
        array('id'=>'moneyReturn',              'transition'=>'error'),
        array('id'=>'manualSuccess',            'transition'=>'done'),
        array('id'=>'moneyTransfer',            'transition'=>'done'),
        array('id'=>'done',                     'transition'=>'canceled'),
        array('id'=>'error'),
        array('id'=>'canceled')
    )
);