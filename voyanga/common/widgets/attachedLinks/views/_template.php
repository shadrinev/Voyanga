<div class="form-horizontal" id="link<?php echo $i; ?>">
    <?php echo $form->textFieldRow($model, "[$i]url", array('class'=>'span3')); ?>
    <?php echo $form->textFieldRow($model, "[$i]title", array('class'=>'span3')); ?>
    <?php $this->widget('bootstrap.widgets.BootButton', array(
        'buttonType'=>'warning',
        'icon'=>'icon-minus',
        'size'=>'mini',
        'label'=>'Удалить ссылку',
        'htmlOptions'=>array(
            'data-del'=>'link'.$i,
            'class' => 'deleteLink'
    ))); ?>
</div>