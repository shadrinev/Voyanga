<div class="form">
    <?php echo $form; ?>
</div>
<?php
if($flightStack)
{
    $stack=$this->beginWidget('ext.EFlightVoageStackWidget');
    //$stack_behavior = new FlightStackStarategyPrice();
    $stack->attachBehavior('price',array(
        'class'=>'ext.FlightStackStrategyPrice',
        'sortKey'=>'price',
        'flightSearchKey'=>$flightSearchKey
    ));
    $stack->setFlightStack($flightStack);
    $this->endWidget();
}?>