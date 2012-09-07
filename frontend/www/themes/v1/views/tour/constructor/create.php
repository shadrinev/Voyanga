<?php $form = $this->beginWidget('bootstrap.widgets.BootActiveForm', array(
    //'type' =>'search',
    'id' => 'tour-builder-form',
    'enableAjaxValidation' => true,
    'htmlOptions' => array(
        'enctype' => 'multipart/form-data'
    )
)); ?>

<?php $this->widget('site.frontend.widgets.tourBuilder.TourBuilderWidget', array('model' => $model, 'attribute' => 'trips')); ?>

<?php echo $form->dropDownListRow($model, 'adultCount', FlightForm::getPossibleAdultCount()); ?>

<?php echo $form->dropDownListRow($model, 'eventId', Event::getPossibleEvents(), array('id' => 'eventId')); ?>

<div class="startCityId">
    <?php echo $form->hiddenField($model, "startCityId", array('class' => 'startCityId')); ?>
    <?php echo $form->labelEx($model, "startCityId"); ?>
    <?php $this->widget('bootstrap.widgets.BootTypeahead', array(
    'options' => array(
        'items' => 10,
        'ajax' => array(
            'url' => "/ajax/cityForFlightOrHotel",
            'timeout' => 500,
            'displayField' => "label",
            'triggerLength' => 2,
            'method' => "get",
            'loadingClass' => "loading-circle",
        ),
        'matcher' => 'js: function(){return true}',
        'sorter' => 'js:function(items){return items;}',
    ),

    'htmlOptions' => array(
        'class' => 'span5 tourStartField',
        'value' => $model->startCityName,
    )
)); ?>
</div>

<div class="eventStartCityIds">
    <?php $this->widget('site.frontend.widgets.cityList.CityListWidget', array(
    'model' => $model,
    'attribute' => 'startCities',
    'attributeId' => 'id',
    'attributeReadable' => 'name'
)); ?>
</div>

<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.BootButton', array(
    'buttonType' => 'submit',
    'type' => 'primary',
    'label' => 'Поехали!',
    'htmlOptions' => array('id' => 'searchHotel')
)); ?>
    <a class="btn btn-primary" href='/tour/constructor/showBasket'>Готовые</a>
</div>
<?php $this->endWidget(); ?>

<?php $this->beginWidget('bootstrap.widgets.BootModal', array('id'=>'newEventModal')); ?>
    <?php $form = $this->beginWidget('bootstrap.widgets.BootActiveForm', array(
        'id' => 'new-event-form',
        'enableAjaxValidation' => false,
    )); ?>
        <div class="modal-header">
            <a class="close" data-dismiss="modal">&times;</a>
            <h4>Новое событие</h4>
        </div>

        <div class="modal-body">
            <div id='newEventName'>
                <?php echo $form->textFieldRow($model, 'newEventName', array('name'=>'title')); ?>
            </div>
        </div>
        <div class="alert alert-error errors hide"></div>

        <div class="modal-footer">
        <?php $this->widget('bootstrap.widgets.BootButton', array(
            'buttonType' => 'submit',
            'type'=>'primary',
            'label'=>'Создать',
            'url'=>'#'
        )); ?>
        <?php $this->widget('bootstrap.widgets.BootButton', array(
            'label'=>'Отмена',
            'url'=>'#',
            'htmlOptions'=>array('data-dismiss'=>'modal'),
        )); ?>
        </div>
    <?php $this->endWidget(); ?>
<?php $this->endWidget(); ?>

<?php Yii::app()->getClientScript()->registerScript('linkToEvent', "
    var withoutEvent = $('div.startCityId'),
        withEvent = $('div.eventStartCityIds'),
        eventSelect = $('#eventId'),
        newEventField = $('#newEventName input'),
        newEventModal = $('#newEventModal'),
        newEventForm = $('#new-event-form'),
        newEventFormErrors = newEventForm.find('.errors'),
        eventsInformation = false,
        veryFirstDate = $('.startDate').eq(0);

    newEventModal.on('hidden', function () {
         if (eventSelect.val() == '" . Event::NEW_EVENT_ITEM . "')
         {
            eventSelect.val('" . Event::NO_EVENT_ITEM . "');
         }
    });

    newEventForm.on('submit', function(e) {
        var data = newEventForm.serialize();
        e.preventDefault();
        $.ajax({
            url: '/event/event/add',
            type: 'POST',
            dataType: 'json',
            data: data
            })
        .done(function(response) {
            newEventFormErrors.addClass('hide');
            refreshEventsInformation(response.id);
            newEventModal.modal('hide');
        })
        .error(function(response) {
            newEventFormErrors.html(response.statusText).removeClass('hide');
        });
    });

    function toggleCitiesWidget()
    {
        if (eventSelect.val() == '" . Event::NO_EVENT_ITEM . "')
        {
            withoutEvent.show();
            withEvent.hide();
        }
        else
        {
            withoutEvent.hide();
            withEvent.show();
            if (eventSelect.val() == '" . Event::NEW_EVENT_ITEM . "')
            {
                newEventModal.modal('show');
                newEventField.focus();
            }
            else
            {
                newEventField.hide();
            }
        }
    }

    function refreshEventsInformation(newEventId)
    {
        $.ajax({
            url: '/event/event/getAllEvents',
            dataType: 'json',
            })
        .done(function(response) {
            eventsInformation = response;
            refreshEventSelector();
            eventSelect.val(newEventId);
            changeStartDate();
        })
        .error(function(response) {
            console.log(response);
        });
    }

    function refreshEventSelector()
    {
        eventSelect.find('option:gt(1)').remove(); // remove old options
        $.each(eventsInformation, function(key, el) {
            eventSelect.append($('<option></option>')
                .attr('value', el.id).data('startdate',el.startDate).text(el.title));
        });
    }

    function changeStartDate()
    {
        var selectedStartDate = eventSelect.find(':selected').data('startdate');
        if (veryFirstDate.data('changed')!=1)
            veryFirstDate.val(selectedStartDate);
    }

    function handleEventChange()
    {
        toggleCitiesWidget();
        changeStartDate();
    }

    refreshEventsInformation(" . Event::NO_EVENT_ITEM . ");
    eventSelect.change(handleEventChange).trigger('change');
"); ?>