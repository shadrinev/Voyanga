<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
    //'type' =>'search',
    'id'=>'constructor-flight-form',
    'enableClientValidation'=>true,
    'htmlOptions'=>array(
        'enctype' => 'multipart/form-data'
    )
)); ?>

    <?php /*echo $form->datepickerRow(
        $model,
        'departureDate',
        array(
            'events'=> array(
                'changeDate'=>'js:function(ev){$(this).datepicker("hide")}'
            )
        )
    );*/?><!--

    <?php /*echo $form->hiddenField($model,'departureCityId', array('validateOnType'=>true, 'id'=>$form->getId().'-departureCityId')); */?>

    <?php /*echo $form->labelEx($model,'departureCityId'); */?>
    <?php /*$this->widget('bootstrap.widgets.BootTypeahead', array(
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
            'onselect'=>'js:function(res){$("#'.$form->getId().'-departureCityId'.'").val(res.id)}',
            'matcher'=>'js: function(){return true}',
        ),
        'htmlOptions'=>array(
            'class'=>'span5 fromField',
            'value'=>'',
            'id'=>$form->getId().'-departureCityField'
        )
    )); */?>
    <?php /*echo $form->error($model, 'departureCityId'); */?>

    <?php /*echo $form->hiddenField($model,'arrivalCityId', array('id'=>$form->getId().'-arrivalCityId')); */?>

    <?php /*echo $form->labelEx($model,'arrivalCityId'); */?>
    --><?php /*$this->widget('bootstrap.widgets.BootTypeahead', array(
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
            'onselect'=>'js:function(res){$("#'.$form->getId().'-arrivalCityId'.'").val(res.id)}',
            'matcher'=>'js: function(){return true}',
        ),
        'htmlOptions'=>array(
            'class'=>'span5 toField',
            'value'=>'',
        )
    )); */?>

    <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.BootButton', array(
            'url'=>'#popupInfo',
            'type'=>'primary',
            'label'=>'Поиск перелёта',
            'htmlOptions'=>array('id'=>'searchFlightConstructor')
        )); ?>
    </div>

<?php $this->endWidget(); ?>

<?php $this->beginWidget('bootstrap.widgets.BootModal', array('id'=>'constructor-popupInfo')); ?>

<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h3>Результат запроса</h3>
</div>

<div class="modal-body" id='constructor-flight-search-result'>
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

<?php $templateFlightSearchVariable = 'tourFlightsSearchResult';
    $this->renderPartial('_flight_search_result', array('variable'=>$templateFlightSearchVariable,'showSaveTour'=>false, 'showDelete'=>false)); ?>


<?php Yii::app()->clientScript->registerScript('constructor-flight-search', "
    $('#searchFlightConstructor,#constructor-repeatFlightSearch').live('click', function(){
        $('#constructor-modalText').html('Поиск перелёта...');
        $('#constructor-popupInfo').modal('show');
        $.ajax({
          url: '/tour/constructor/flightSearch',
          dataType: 'json',
          data: $('#constructor-flight-form').serialize(),
          timeout: 90000
        })
        .done(function(data) {
            var html = {$templateFlightSearchVariable}(data);
            console.log(data);
            $('#constructor-flight-search-result').html(html);
        })
        .fail(function(data){
            console.log(data);
            if (data.statusText=='timeout')
                data.responseText = 'Время ожидания запроса превышено.';
            $('#constructor-modalText').html('<div class=\"alert alert-error\">Произошла ошибка! Попробуйте <a id=\"constructor-repeatFlightSearch\" href=\"#\">повторить поиск</a>.<br>Текст ошибки:<br>'+data.responseText+'</div>');
        });
    });
", CClientScript::POS_READY); ?>

<?php if (isset($autosearch) and ($autosearch))
{
    Yii::app()->clientScript->registerScript('flight-search-autostart',
        '$("#searchFlight").trigger("click");
         $(".fromField").val("'.$fromCityName.'");
         $(".toField").val("'.$toCityName.'");',
    CClientScript::POS_READY);
}
?>