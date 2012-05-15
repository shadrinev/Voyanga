<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
	'id'=>'event-form',
	'enableAjaxValidation'=>false,
    'focus'=>array($model,'startDate')
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->datepickerRow(
        $model,
        'startDate',
        array(
            'class'=>'span2',
            'options'=> array(
                'weekStart'=>1,
                'format'=>'dd.mm.yyyy'
            ),
            'events'=> array(
                'changeDate'=>'js:function(ev){$(this).datepicker("hide"); $("#Event_endDate").focus()}'
            )
        )
    );?>

    <?php echo $form->datepickerRow(
        $model,
        'endDate',
        array(
            'class'=>'span2',
            'options'=> array(
                'weekStart'=>1,
                'format'=>'dd.mm.yyyy'
            ),
            'events'=> array(
                'changeDate'=>'js:function(ev){$(this).datepicker("hide"); $("#Event_title").focus()}'
            )
        )
    );?>

    <?php echo $form->textFieldRow($model,'title',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'cityId',array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'address',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'contact',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'status',array('class'=>'span5')); ?>

	<?php echo $form->textAreaRow($model,'preview',array('rows'=>6, 'cols'=>50, 'class'=>'span8')); ?>



	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.BootButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Добавить' : 'Сохранить',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
