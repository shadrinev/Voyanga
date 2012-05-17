<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
	'id'=>'event-form',
	'enableAjaxValidation'=>false,
)); ?>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->dateTimepickerRow(
        $model,
        'startDate',
        array(
            'date'=>array(
                'events'=> array(
                    'changeDate'=>'js:function(ev){$(this).datepicker("hide"); $("#Event_endDate_date").focus()}'
                )
            ),
            'time'=>array(
                'htmlOptions'=>array(
                    'value'=>$model->isNewRecord?'00:00':false,
                    'tabindex'=>1
                ),
            )
        )
    );?>

    <?php echo $form->dateTimepickerRow(
        $model,
        'endDate',
        array(
            'date'=>array(
                'events'=> array(
                    'changeDate'=>'js:function(ev){$(this).datepicker("hide"); $("#Event_title").focus()}'
                )
            ),
            'time'=>array(
                'htmlOptions'=>array(
                    'value'=>$model->isNewRecord?'23:59':false,
                    'tabindex'=>2
                ),
            )
        )
    );?>

    <?php echo $form->textFieldRow($model,'title',array('class'=>'span5')); ?>

	<?php echo $form->hiddenField($model,'cityId'); ?>

    <?php echo $form->labelEx($model,'cityId'); ?>
    <?php $this->widget('bootstrap.widgets.BootTypeahead', array(
        'options'=>array(
            'items'=>10,
            'ajax' => array(
                'url' => "/site/cityAutocomplete",
                'timeout' => 500,
                'displayField' => "label",
                'triggerLength' => 2,
                'method' => "get",
                'loadingClass' => "loading-circle",
            ),
            'onselect'=>'js:function(res){$("#Event_cityId").val(res.id)}',
            'matcher'=>'js: function(){return true}',
        ),
        'htmlOptions'=>array(
            'class'=>'span5',
            'value'=>$model->isNewRecord?'':$model->city->localRu
        )
    )); ?>

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
            'focus' => false,
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
<?php Yii::app()->clientScript->registerScript('focus','setTimeout(function(){$("#Event_startDate_date").focus();$("#Event_startDate_date").focus()}, 300)', CClientScript::POS_READY); ?>