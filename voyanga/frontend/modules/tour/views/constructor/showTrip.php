<?php $this->widget('bootstrap.widgets.BootTabbable', array(
    'type'=>'tabs',
    'placement'=>'left', // 'above', 'right', 'below' or 'left'
    'tabs'=>$tabs,
     'encodeLabel' => false
    )
); ?>

<?php echo CHtml::link('Назад в конструктор', array('create'), array('class'=>'btn')); ?>