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
                'changeDate'=>'js:function(ev){$(this).datepicker("hide")}'
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
            'url'=>'#popupInfo',
            'type'=>'primary',
            'label'=>'Поиск перелёта',
            'htmlOptions'=>array('id'=>'searchFlight', 'data-toggle'=>'modal')
        )); ?>
    </div>

<?php $this->endWidget(); ?>

<?php $this->beginWidget('bootstrap.widgets.BootModal', array('id'=>'popupInfo')); ?>

<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h3>Результат запроса</h3>
</div>

<div class="modal-body" id='flight-search-result'>
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
    $this->renderPartial('_flight_search_result', array('variable'=>$templateVariable)); ?>

<?php Yii::app()->clientScript->registerScript('flight-search', "
    $('#searchFlight,#repeatFlightSearch').live('click', function(){
        $('#flight-search-result').html('Поиск перелёта...');
        $.getJSON('/admin/tour/constructor/flightSearch', $('#flight-form').serialize())
        .done(function(data) {
            var html = {$templateVariable}(data);
            $('#flight-search-result').html(html);
            $('.chooseFlight').on('click',function(){
                var key1 = $('#searchId').data('searchid');
                var key2 = $(this).data('searchkey');
                $.getJSON('/admin/tour/basket/add/type/".FlightVoyage::TYPE."/key/'+key1+'/searchId/'+key2)
                    .done(function(data){
                        $.getJSON('/admin/tour/basket/show')
                            .done(function(data) {
                                var html = handlebarTour(data);
                                $('#tour-output').html(html);
                            })
                            .fail(function(data){
                                $('#tour-output').html(data);
                            });
                        $('#popupInfo').modal('hide');
                    });
            });
        })
        .fail(function(data){
            $('#flight-search-result').html('<div class=\"alert alert-error\">Произошла ошибка! Попробуйте <a id=\"repeatFlightSearch\" href=\"#\">повторить поиск</a>.</div>');
        });
    });
", CClientScript::POS_READY); ?>