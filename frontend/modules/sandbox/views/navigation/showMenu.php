<?php $this->widget('bootstrap.widgets.BootMenu', array(
    'type'=>'list',
    'items'=>array(
        array('label'=>'WIDGETS'),
        array('label'=>'Test expired widget', 'url'=>array('/sandbox/widgetTest/testExpiredNotification')),
        array('label'=>'Test API', 'url'=>array('/sandbox/testApi/default')),
    ),
)); ?>