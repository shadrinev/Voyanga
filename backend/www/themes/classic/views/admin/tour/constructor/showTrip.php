<?php $this->widget('bootstrap.widgets.BootTabbable', array(
    'type'=>'tabs',
    'placement'=>'left', // 'above', 'right', 'below' or 'left'
    'tabs'=>$tabs,
     'encodeLabel' => false
    )
); ?>

<?php echo CHtml::link('Назад в конструктор', array('create'), array('class'=>'btn')); ?>
<?php echo CHtml::link('Забронировать тур', array('makeBooking'), array('class'=>'btn')); ?>