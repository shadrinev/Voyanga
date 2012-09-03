<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
    //'type' =>'search',
    'id'=>'tour-builder-form',
    'enableAjaxValidation'=>true,
    'htmlOptions'=>array(
        'enctype' => 'multipart/form-data'
    )
)); ?>

    <?php $this->widget('site.frontend.widgets.tourBuilder.TourBuilderWidget', array('model' => $model, 'attribute'=>'trips')); ?>

    <?php echo $form->dropDownListRow($model, 'adultCount', FlightForm::getPossibleAdultCount()); ?>

    <?php echo $form->dropDownListRow($model, 'eventId', Event::getPossibleEvents(), array('id'=>'eventId')); ?>

    <div class="startCityId">
        <?php echo $form->hiddenField($model, "startCityId", array('class'=>'startCityId')); ?>
        <?php echo $form->labelEx($model, "startCityId"); ?>
        <?php $this->widget('bootstrap.widgets.BootTypeahead', array(
            'options'=>array(
                'items'=>10,
                'ajax' => array(
                    'url' => "/ajax/cityForFlightOrHotel",
                    'timeout' => 500,
                    'displayField' => "label",
                    'triggerLength' => 2,
                    'method' => "get",
                    'loadingClass' => "loading-circle",
                ),
                'matcher'=>'js: function(){return true}',
                'sorter'=>'js:function(items){return items;}',
            ),

            'htmlOptions'=>array(
                'class'=>'span5 tourStartField',
                'value'=>$model->startCityName,
            )
        )); ?>
    </div>

    <div id='newEventName'>
        <?php echo $form->textFieldRow($model, 'newEventName'); ?>
    </div>

    <div class="eventStartCityIds">
        <?php $this->widget('site.frontend.widgets.cityList.CityListWidget', array(
            'model' => $model,
            'attribute'=>'eventStartCityIds',
            'attributeReadable'=>'eventStartCityNames'
        )); ?>
    </div>

    <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.BootButton', array(
        'buttonType'=>'submit',
        'type'=>'primary',
        'label'=>'Поехали!',
        'htmlOptions'=>array('id'=>'searchHotel')
    )); ?>

    <a class="btn btn-primary" href='/tour/constructor/showBasket'>Готовые</a>

    </div>

<?php $this->endWidget(); ?>

<?php Yii::app()->getClientScript()->registerScript('linkToEvent', "
    var withoutEvent = $('div.startCityId'),
        withEvent = $('div.eventStartCityIds'),
        eventSelect = $('#eventId'),
        newEventField = $('#newEventName');

    function toggleCitiesWidget()
    {
        if (eventSelect.val() == '".Event::NO_EVENT_ITEM."')
        {
            withoutEvent.show();
            withEvent.hide();
            newEventField.hide();
        }
        else
        {
            withoutEvent.hide();
            withEvent.show();
            if (eventSelect.val() == '".Event::NEW_EVENT_ITEM."')
            {
                newEventField.show();
            }
            else
            {
                newEventField.hide();
            }
        }
    }

    eventSelect.change(toggleCitiesWidget).trigger('change');
"); ?>