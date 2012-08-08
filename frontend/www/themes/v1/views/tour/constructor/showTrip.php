<?php $this->widget('bootstrap.widgets.BootTabbable', array(
    'type'=>'tabs',
    'placement'=>'left', // 'above', 'right', 'below' or 'left'
    'tabs'=>$tabs,
     'encodeLabel' => false
    )
); ?>

<?php
//init script data
Yii::app()->clientScript->registerScript('flight-search', 'constructorViewer.tabsJson = '. json_encode($tabs).';', CClientScript::POS_READY);
?>

<?php $templateVariable = 'flightSearchResult';
$this->renderPartial('_flights', array('variable'=>$templateVariable,'showSaveTour'=>true, 'showDelete'=>false));
$this->renderPartial('_hotels', array('variable'=>'hotelSearchResult','showSaveTour'=>true, 'showDelete'=>false));
$this->renderPartial('_choosed_flight', array('variable'=>'choosedFlight','showSaveTour'=>true, 'showDelete'=>false));
?>

<?php echo CHtml::link('Назад в конструктор', array('create'), array('class'=>'btn')); ?>
<?php echo CHtml::link('Вперед в будущее', array('makeBooking'), array('class'=>'btn')); ?>