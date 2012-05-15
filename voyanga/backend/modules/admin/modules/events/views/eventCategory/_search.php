<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
    'action'=>Yii::app()->createUrl($this->route),
    'method'=>'get',
)); ?>

<?php echo $form->textFieldRow($model,'id',array('class'=>'span5','maxlength'=>10)); ?>

<?php echo $form->textFieldRow($model,'root',array('class'=>'span5','maxlength'=>10)); ?>

<?php echo $form->textFieldRow($model,'title',array('class'=>'span5','maxlength'=>255)); ?>

<?php echo $form->textFieldRow($model,'lft',array('class'=>'span5','maxlength'=>10)); ?>

<?php echo $form->textFieldRow($model,'rgt',array('class'=>'span5','maxlength'=>10)); ?>

<?php echo $form->textFieldRow($model,'level',array('class'=>'span5')); ?>

<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.BootButton', array(
    'type'=>'primary',
    'label'=>'Search',
)); ?>
</div>

<?php $this->endWidget(); ?>