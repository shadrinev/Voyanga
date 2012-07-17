<div class="form-horizontal" id="route<?php echo $i; ?>">
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
                    'url' => "/ajax/cityForFlight",
                    'timeout' => 500,
                    'displayField' => "label",
                    'triggerLength' => 2,
                    'method' => "get",
                    'loadingClass' => "loading-circle",
                ),
                'onselect'=>'js:function(res){$("#FlightForm_departureCityId").val(res.id)}',
                'matcher'=>'js: function(){return true}',
                'sorter'=>'js:function(items){return items;}',
            ),

            'htmlOptions'=>array(
                'class'=>'span5 fromField',
                'value'=>'',
            )
        )); ?>

        <?php echo $form->hiddenField($model,'arrivalCityId'); ?>

        <?php echo $form->labelEx($model,'arrivalCityId'); ?>
        <?php $this->widget('bootstrap.widgets.BootTypeahead', array(
        'options'=>array(
            'items'=>10,
            'ajax' => array(
                'url' => "/ajax/cityForFlight",
                'timeout' => 500,
                'displayField' => "label",
                'triggerLength' => 2,
                'method' => "get",
                'loadingClass' => "loading-circle",
            ),
            'onselect'=>'js:function(res){$("#FlightForm_arrivalCityId").val(res.id)}',
            'matcher'=>'js: function(){return true}',
            'sorter'=>'js:function(items){return items;}',
        ),
        'htmlOptions'=>array(
            'class'=>'span5 toField',
            'value'=>'',
        )
    )); ?>

    <?php $this->widget('bootstrap.widgets.BootButton', array(
        'buttonType'=>'warning',
        'icon'=>'icon-minus',
        'size'=>'mini',
        'label'=>'Удалить ссылку',
        'htmlOptions'=>array(
            'data-del'=>'route'.$i,
            'class' => 'deleteLink'
    ))); ?>
</div>