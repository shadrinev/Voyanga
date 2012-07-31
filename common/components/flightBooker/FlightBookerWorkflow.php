<?php
return array(
    'initial' => 'enterCredentials',
    'node' => array(
        array('id'=>'enterCredentials',         'transition'=> 'booking'),
        array('id'=>'booking',                  'transition'=>'waitingForPayment,bookingError'),
        array('id'=>'bookingError',             'transition'=>'error'),
        array('id'=>'waitingForPayment',        'transition'=>'waitingForPayment,startPayment,bookingTimeLimitError'),
        array('id'=>'startPayment',             'transition'=>'ticketing,bookingTimeLimitError,waitingForPayment'),
        array('id'=>'bookingTimeLimitError',    'transition'=>'bookingTimeLimitError,error'),
        array('id'=>'ticketing',                'transition'=>'ticketReady,ticketingRepeat'),
        array('id'=>'ticketReady',              'transition'=>'bspTransfer, done'),
        array('id'=>'ticketingRepeat',          'transition'=>'ticketingRepeat,ticketingError,ticketReady,manualProcessing'),
        array('id'=>'manualProcessing',         'transition'=>'ticketingError,manualTicketing'),
        array('id'=>'manualTicketing',          'transition'=>'manualSuccess,manualError'),
        array('id'=>'ticketingError',           'transition'=>'moneyReturn'),
        array('id'=>'manualError',              'transition'=>'moneyReturn'),
        array('id'=>'moneyReturn',              'transition'=>'error'),
        array('id'=>'manualSuccess',            'transition'=>'done'),
        array('id'=>'bspTransfer',              'transition'=>'done'),
        array('id'=>'done'),
        array('id'=>'error')
    )
)
?>