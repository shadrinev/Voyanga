<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
	'id'=>'event-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>

    <?php $this->widget('bootstrap.widgets.BootDatepicker', array('model'=>$model, 'attribute'=>'startDate')); ?>

	<?php echo $form->textFieldRow($model,'startDate',array('class'=>'span2')); ?>

	<?php echo $form->textFieldRow($model,'endDate',array('class'=>'span2')); ?>

    <?php echo $form->textFieldRow($model,'title',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'cityId',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'address',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'contact',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'status',array('class'=>'span5')); ?>

	<?php echo $form->textAreaRow($model,'preview',array('rows'=>6, 'cols'=>50, 'class'=>'span8')); ?>

    <?php $this->widget('site.common.widgets.imperaviRedactor.EImperaviRedactorWidget',array(
        // можно использовать как для поля модели
        'model'=>$model,
        'attribute'=>'description',
        'options'   => array(
            'toolbar' => 'main',
        ),
        'htmlOptions' => array('rows' => 20,'cols' => 4)
        ));
    ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.BootButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Добавить' : 'Сохранить',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
