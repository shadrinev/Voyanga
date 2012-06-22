<div class="form-horizontal" id="link<?php echo $i; ?>">
    <?php echo $form->textFieldRow($model, "[$i]adultCount", array('class'=>'span3')); ?>
    <?php echo $form->textFieldRow($model, "[$i]childCount", array('class'=>'span3')); ?>
    <?php echo $form->textFieldRow($model, "[$i]cots", array('class'=>'span3')); ?>
    <?php echo $form->textFieldRow($model, "[$i]childAge", array('class'=>'span3')); ?>
    <?php $this->widget('bootstrap.widgets.BootButton', array(
        'buttonType'=>'warning',
        'icon'=>'icon-minus',
        'size'=>'mini',
        'label'=>'Удалить комнату',
        'htmlOptions'=>array(
            'data-del'=>'link'.$i,
            'class' => 'deleteRoom'
    ))); ?>
</div>