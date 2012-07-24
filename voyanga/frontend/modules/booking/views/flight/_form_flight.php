<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
    //'type' =>'search',
    'id'=>'flight-form',
    'enableAjaxValidation'=>true,
    'htmlOptions'=>array(
        'enctype' => 'multipart/form-data'
    )
)); ?>

    <?php $this->widget('frontend.widgets.aviaSearch.AviaSearchWidget', array('model' => $model, 'attribute'=>'routes')); ?>

    <?php echo $form->radioButtonListInlineRow($model, 'flightClass', $model->getPossibleFlightClasses()); ?>
    <?php echo $form->dropDownListRow($model, 'adultCount', FlightForm::getPossibleAdultCount()); ?>
    <?php echo $form->dropDownListRow($model, 'childCount', $model->getPossibleChildCount()); ?>
    <?php echo $form->dropDownListRow($model, 'infantCount', $model->getPossibleInfantCount()); ?>

    <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.BootButton', array(
            'url'=>'#popupInfo',
            'buttonType'=>'submit',
            'type'=>'primary',
            'label'=>'Поиск перелёта',
            'htmlOptions'=>array('id'=>'searchFlight')
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
    $this->renderPartial('_flights', array('variable'=>$templateVariable,'showSaveTour'=>false, 'showDelete'=>false)); ?>

<?php Yii::app()->clientScript->registerScript('flight-search', "
    $('#searchFlight,#repeatFlightSearch').live('click', function(){
        $('#popupInfo').modal('show');
        $('#modalText').html('Поиск перелёта...');
        $.ajax({
          url: '/tour/constructor/flightSearch',
          dataType: 'json',
          data: $('#flight-form').serialize(),
          timeout: 90000
        })
        .done(function(data) {
            var html = {$templateVariable}(data);
            console.log(data);
            $('#flight-search-result').html(html);
            $('#popupInfo').modal('hide');
        })
        .fail(function(data){
            console.log(data);
            if (data.statusText=='timeout')
                data.responseText = 'Время ожидания запроса превышено.';
            $('#modalText').html('<div class=\"alert alert-error\">Произошла ошибка! Попробуйте <a id=\"repeatFlightSearch\" href=\"#\">повторить поиск</a>.<br>Текст ошибки:<br>'+data.responseText+'</div>');
        });
        return false;
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