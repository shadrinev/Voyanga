<div class="form-horizontal well link<?php echo $i; ?>">
    <?php echo $form->dropDownListRow($model, "[$i]adultCount", array(1=>1, 2=>2, 3=>3, 4=>4)); ?>
    <?php echo $form->radioButtonListInlineRow($model, "[$i]childCount", array(0=>0, 1=>1)); ?>
    <?php echo $form->dropDownListRow($model, "[$i]childAge", range(0,21)); ?>
    <?php echo $form->radioButtonListInlineRow($model, "[$i]cots", range(0,3)); ?>
    <br>
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