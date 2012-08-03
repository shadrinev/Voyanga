Expired Notification
====================
Displays modal window with any message in some time after user loads page.
Example of usage below.

Copy-paste coding
-----------------
<?php $this->widget('site.common.widgets.expiredNotification.expiredNotificationWidget', array(
    'time' => 2,
    'header' => false,
    'message' => 'Yep! I works! <a href="">Reload me</a>',
    'showCancel' => false,
    'modalOptions' => array()
)); ?>

With comments coding
-----------------
<?php $this->widget('site.common.widgets.expiredNotification.expiredNotificationWidget', array(
    'time' => 2, // time before show notification (in seconds)
    'header' => false, // header of modal to show. Optional.
    'message' => 'Yep! I works! <a href="">Reload me</a>', // message to show. Required
    'showCancel' => false, // ability to close window. Optional.
    'modalOptions' => array() // options for modal window (@link BootModal). Optional.
)); ?>
