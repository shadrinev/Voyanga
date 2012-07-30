<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
    'id'=>'event-category-form',
    'enableAjaxValidation'=>false,
)); ?>

<?php echo $form->errorSummary($model); ?>

<?php echo $form->textFieldRow($model,'title',array('class'=>'span5','maxlength'=>255)); ?>

<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.BootButton', array(
    'buttonType'=>'submit',
    'type'=>'primary',
    'label'=>$model->isNewRecord ? 'Создать' : 'Сохранить',
)); ?>
</div>

<?php $this->endWidget(); ?>