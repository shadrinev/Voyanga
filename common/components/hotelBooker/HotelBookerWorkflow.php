<?php
return array(
    'initial' => 'enterCredentials',
    'node' => array(
        array('id'=>'enterCredentials',         'transition'=> 'analyzing'),
        array('id'=>'analyzing',                'transition'=>'analyzing,hardWaitingForPayment,booking'),
        array('id'=>'booking',                  'transition'=>'softWaitingForPayment,bookingError'),
        array('id'=>'bookingError',             'transition'=>'error'),
        array('id'=>'softWaitingForPayment',    'transition'=>'bookingTimeLimitError,softStartPayment'),
        array('id'=>'bookingTimeLimitError',    'transition'=>'error'),
        array('id'=>'softStartPayment',         'transition'=>'bookingTimeLimitError,softWaitingForPayment, paid'),
        array('id'=>'hardWaitingForPayment',    'transition'=>'checkingAvailability, paid'),
        array('id'=>'checkingAvailability',     'transition'=>'availabilityError,hardStartPayment'),
        array('id'=>'availabilityError',        'transition'=>'error'),
        array('id'=>'hardStartPayment',         'transition'=>'ticketing,hardWaitingForPayment'),
        array('id'=>'paid',                     'transition'=>'ticketing'),
        array('id'=>'ticketing',                'transition'=>'ticketReady,ticketingRepeat,ticketingError'),
        array('id'=>'ticketReady',              'transition'=>'moneyTransfer,done'),
        array('id'=>'ticketingRepeat',          'transition'=>'ticketingRepeat,ticketReady,manualProcessing,ticketingError'),
        array('id'=>'manualProcessing',         'transition'=>'ticketingError,manualTicketing'),
        array('id'=>'manualTicketing',          'transition'=>'manualSuccess,manualError'),
        array('id'=>'ticketingError',           'transition'=>'moneyReturn'),
        array('id'=>'manualError',              'transition'=>'moneyReturn'),
        array('id'=>'moneyReturn',              'transition'=>'error'),
        array('id'=>'manualSuccess',            'transition'=>'done'),
        array('id'=>'moneyTransfer',            'transition'=>'done'),
        array('id'=>'done'),
        array('id'=>'error')
    )
)
?>