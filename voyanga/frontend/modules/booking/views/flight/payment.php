<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
    'id'=>'payment-form',
    'enableAjaxValidation'=>false,
)); ?>

enter payment details here

<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.BootButton', array(
    'buttonType'=>'submit',
    'type'=>'primary',
    'label'=>'Продолжить',
    'htmlOptions'=>array('name'=>'submit', 'value'=>'go')
)); ?>
</div>

<?php $this->endWidget(); ?>

