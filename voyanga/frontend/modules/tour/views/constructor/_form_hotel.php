<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
    //'type' =>'search',
    'id'=>'hotel-form',
    'clientOptions' => array(
        'validateOnSubmit' => true,
    ),
    'enableAjaxValidation'=> true,
    'htmlOptions'=>array(
        'enctype' => 'multipart/form-data'
    )
)); ?>

    <?php echo $form->labelEx($model,'cityId'); ?>
    <?php $this->widget('bootstrap.widgets.BootTypeahead', array(
        'options'=>array(
            'items'=>10,
            'ajax' => array(
                'url' => "/site/cityAutocompleteForHotel/withHotels/1",
                'timeout' => 500,
                'displayField' => "label",
                'triggerLength' => 2,
                'method' => "get",
                'loadingClass' => "loading-circle",
            ),
            'onselect'=>'js:function(res){$("#HotelForm_cityId").val(res.id)}',
            'matcher'=>'js: function(){return true}',
        ),
        'htmlOptions'=>array(
            'class'=>'span5 fromField',
            'value'=>$cityName,
        )
    )); ?>
    <?php echo $form->error($model, 'cityId'); ?>

    <?php echo $form->datepickerRow(
        $model,
        'fromDate',
        array(
            'events'=> array(
                'changeDate'=>'js:function(ev){$(this).datepicker("hide")}'
            )
        )
    );?>

    <?php echo $form->hiddenField($model,'cityId'); ?>

    <?php echo $form->dropDownListRow($model, 'duration', range(0,31)); ?>

    <?php $this->widget('common.widgets.rooms.Rooms', array('model' => $model, 'attribute'=>'rooms', 'form'=>$form)); ?>

    <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.BootButton', array(
            'buttonType'=>'submit',
            'type'=>'primary',
            'label'=>'Поиск отеля',
            'htmlOptions'=>array('id'=>'searchHotel')
        )); ?>
    </div>

<?php $this->endWidget(); ?>