<div class="form">
    <?php echo $form; ?>
</div>
<?php if($bestCaches): ?>
    Лучшие предложения:
    <ul>
        <?php foreach($bestCaches as $bestCache):?>
            <li><?php echo $bestCache->dateFrom;?> - <?php echo $bestCache->priceBestPriceTime;?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
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