<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
    //'type' =>'search',
    'id'=>'hotel-form',
    'enableAjaxValidation'=>false,
    'htmlOptions'=>array(
        'enctype' => 'multipart/form-data'
    )
)); ?>

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
            'onselect'=>'js:function(res){$("#HotelForm_cityId").val(res.id)}',
            'matcher'=>'js: function(){return true}',
        ),
        'htmlOptions'=>array(
            'class'=>'span5 fromField',
            'value'=>'',
        )
    )); ?>

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

    <?php echo $form->textFieldRow($model, 'duration'); ?>

    <?php $this->widget('common.widgets.rooms.Rooms', array('model' => $model, 'attribute'=>'rooms', 'form'=>$form)); ?>

    <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.BootButton', array(
            'url'=>'#popupInfo',
            'type'=>'primary',
            'label'=>'Поиск перелёта',
            'htmlOptions'=>array('id'=>'searchHotel', 'data-toggle'=>'modal')
        )); ?>
    </div>

<?php $this->endWidget(); ?>

<?php $this->beginWidget('bootstrap.widgets.BootModal', array('id'=>'popupInfo')); ?>

<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h3>Результат запроса</h3>
</div>

<div class="modal-body" id='modalText'>
    <p>Идет запрос данных...</p>
</div>

<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.BootButton', array(
    'label'=>'Close',
    'url'=>'#',
    'htmlOptions'=>array('data-dismiss'=>'modal'),
)); ?>
</div>

<?php $this->endWidget(); ?>

<?php $templateVariable = 'flightSearchResult';
    $this->renderPartial('_hotels', array('variable'=>$templateVariable)); ?>

<?php Yii::app()->clientScript->registerScript('flight-search', "
    $('#searchHotel,#repeatHotelSearch').live('click', function(){
        $('#modalText').html('Поиск отеля...');
        $.getJSON('/admin/tour/constructor/hotelSearch', $('#hotel-form').serialize())
        .done(function(data) {
            var html = {$templateVariable}(data);
            console.log(data);
            $('#hotel-search-result').html(html);
            $('#popupInfo').modal('hide');
        })
        .fail(function(data){
            $('#modalText').html('<div class=\"alert alert-error\">Произошла ошибка! Попробуйте <a id=\"repeatHotelSearch\" href=\"#\">повторить поиск</a>.</div>');
        });
    });
", CClientScript::POS_READY); ?>

<?php if (isset($autosearch) and ($autosearch))
{
    Yii::app()->clientScript->registerScript('hotel-search-autostart',
        '$("#searchHotel").trigger("click");
         $(".fromField").val("'.$cityName.'");
         ',
    CClientScript::POS_READY);
}
?>