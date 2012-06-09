<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
    //'type' =>'search',
    'id'=>'flight-form',
    'enableAjaxValidation'=>false,
    'htmlOptions'=>array(
        'enctype' => 'multipart/form-data'
    )
)); ?>

    <?php echo $form->datepickerRow(
        $model,
        'departureDate',
        array(
            'events'=> array(
                'changeDate'=>'js:function(ev){$(this).datepicker("hide"); $("#Event_endDate_date").focus()}'
            )
        )
    );?>

    <?php echo $form->hiddenField($model,'departureCityId'); ?>

    <?php echo $form->labelEx($model,'departureCityId'); ?>
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
            'onselect'=>'js:function(res){$("#FlightForm_departureCityId").val(res.id)}',
            'matcher'=>'js: function(){return true}',
        ),
        'htmlOptions'=>array(
            'class'=>'span5',
            'value'=>''
        )
    )); ?>

    <?php echo $form->hiddenField($model,'arrivalCityId'); ?>

    <?php echo $form->labelEx($model,'arrivalCityId'); ?>
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
            'onselect'=>'js:function(res){$("#FlightForm_arrivalCityId").val(res.id)}',
            'matcher'=>'js: function(){return true}',
        ),
        'htmlOptions'=>array(
            'class'=>'span5',
            'value'=>''
        )
    )); ?>

    <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.BootButton', array(
            'buttonType'=>'submit',
            'type'=>'primary',
            'label'=>'Поиск перелёта',
        )); ?>
    </div>

<?php $this->endWidget(); ?>