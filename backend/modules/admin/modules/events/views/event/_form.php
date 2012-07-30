<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
	'id'=>'event-form',
	'enableAjaxValidation'=>false,
    'htmlOptions'=>array(
        'enctype' => 'multipart/form-data'
    )
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

    <?php echo $form->labelEx($model,'categories'); ?>
    <?php $this->widget('common.widgets.treeLeafSelector.TreeLeafSelector', array(
        'model'=>$model,
        'attribute'=>'categories',
        'form' => $form
    )); ?>

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

    <?php echo $form->error($model, 'cityId'); ?>

	<?php echo $form->textFieldRow($model,'address',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'contact',array('class'=>'span5','maxlength'=>255)); ?>

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

    <?php echo $form->fileFieldRow($model,'pictureSmall',array('class'=>'span5')); ?>

    <?php echo $form->fileFieldRow($model,'pictureBig',array('class'=>'span5')); ?>

    <?php echo $form->labelEx($model, 'pictures'); ?>

    <?php $this->widget('common.extensions.EAjaxUpload.EAjaxUpload',
        array(
            'id'=>'uploadFile',
            'config'=>array(
               'action'=>Yii::app()->createUrl('/admin/events/event/uploadToGallery', array('id'=>$model->id)),
               'allowedExtensions'=>array("jpg"),
               'sizeLimit'=>10*1024*1024,// maximum file size in bytes
               'minSizeLimit'=>100,// minimum file size in bytes
               //'onComplete'=>"js:function(id, fileName, responseJSON){ alert(fileName); }",
               'messages'=>array(
                    'typeError'=>"{file} has invalid extension. Only {extensions} are allowed.",
                    'sizeError'=>"{file} is too large, maximum file size is {sizeLimit}.",
                    'minSizeError'=>"{file} is too small, minimum file size is {minSizeLimit}.",
                    'emptyError'=>"{file} is empty, please select files again without it.",
                    'onLeave'=>"The files are being uploaded, if you leave now the upload will be cancelled."
               ),
               'showMessage'=>"js:function(message){ alert(message); }"
              )
    )); ?>

    <?php echo $form->textFieldRow($model,'tagsString',array('class'=>'span5')); ?>

    <?php $this->widget('common.widgets.attachedLinks.AttachedLinks', array('model' => $model, 'attribute'=>'links', 'form'=>$form)); ?>

    <?php echo $form->dropDownListRow($model,'status',$model->getPossibleStatus(),array('class'=>'span5')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.BootButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Добавить' : 'Сохранить',
		)); ?>
        <?php $this->widget('bootstrap.widgets.BootButton', array(
            'url'=>$model->isNewRecord ? array('admin') : array('view','id'=>$model->id),
            'label'=>'Отмена',
        )); ?>
        <br>
        <?php echo (!$model->isNewRecord) ? ('<br>Из Москвы: '.$model->priceMoscow." руб., из Питера: ".$model->pricePiter." руб.<br>") : '' ?>
        <br>
        <?php $this->widget('bootstrap.widgets.BootButton', array(
        'buttonType'=>'submit',
        'type'=>'warning',
        'label'=>($model->isNewRecord)?'Запросить цену для Москвы':'Уточнить цену для Москвы',
        'htmlOptions'=>array('id'=>'getPrice'),
        'loadingText'=>'Запрос цены...',
         )); ?>
        <br><br>
        <?php $this->widget('bootstrap.widgets.BootButton', array(
        'buttonType'=>'submit',
        'type'=>'warning',
        'label'=>($model->isNewRecord)?'Запросить цену для Питера':'Уточнить цену для Питера',
        'htmlOptions'=>array('id'=>'getPiterPrice'),
        'loadingText'=>'Запрос цены...',
    )); ?>
	</div>

<?php $this->endWidget(); ?>
<?php
    if ($model->isNewRecord)
        Yii::app()->clientScript->registerScript('focus','setTimeout(function(){$("#Event_startDate_date").focus();}, 300)', CClientScript::POS_READY);
    Yii::app()->clientScript->registerScript('getPrice','
    $("#getPrice").on("click",function(){
        var btn = $(this),
            from = 4466,
            to = $("#Event_cityId").val(),
            dateStart = $("#Event_startDate_date").val(),
            dateEnd = $("#Event_endDate_date").val();
            btn.button("loading");
            $.get("/ajax/GetOptimalPrice/from/"+from+"/to/"+to+"/dateStart/"+dateStart+"/dateEnd/"+dateEnd)
            .done(function(data){
                console.log(data);
                btn.button("reset");
                var two = data.priceTo + data.priceBack;
                btn.append("&nbsp; <b>Цена: </b>"+Math.min(data.priceTo + data.priceBack, data.priceToBack) + " руб. (2 билета = " + two + " руб., туда-обратно = " + data.priceToBack + " руб.)");
            })
            .fail(function(data){
                btn.button("reset");
                btn.html("Произошёл сбой");
                btn.addClass("disabled");
            });
        return false;
    });
    $("#getPiterPrice").on("click",function(e){
        var btn = $(this),
            from = 5185,
            to = $("#Event_cityId").val(),
            dateStart = $("#Event_startDate_date").val(),
            dateEnd = $("#Event_endDate_date").val();
            btn.button("loading");
        $.get("/ajax/GetOptimalPrice/from/"+from+"/to/"+to+"/dateStart/"+dateStart+"/dateEnd/"+dateEnd)
        .done(function(data){
            console.log(data);
            btn.button("reset");
            var two = data.priceTo + data.priceBack;
            btn.append("&nbsp; <b>Цена: </b>"+Math.min(data.priceTo + data.priceBack, data.priceToBack) + " руб. (2 билета = " + two + " руб., туда-обратно = " + data.priceToBack + " руб.)");
        })
        .fail(function(data){
            btn.button("reset");
            btn.html("Произошёл сбой");
            e.preventDefault();
        });
        return false;
    });
    ', CClientScript::POS_READY);
?>