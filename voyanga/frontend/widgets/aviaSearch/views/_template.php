<div class="form-horizontal well" id="route<?php echo $i; ?>">
        <?php echo $form->datepickerRow(
            $model,
            "[$i]departureDate",
            array(
                'events'=> array(
                    'changeDate'=>'js:function(ev){$(this).datepicker("hide")}'
                ),
                'class'=>'datepicker'
            )
        );?>

        <?php echo $form->hiddenField($model, "[$i]departureCityId", array('class'=>'departureCity')); ?>

        <?php echo $form->labelEx($model, "[$i]departureCityId"); ?>
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
                'matcher'=>'js: function(){return true}',
                'sorter'=>'js:function(items){return items;}',
            ),

            'htmlOptions'=>array(
                'class'=>'span5 fromField',
                'value'=>'',
            )
        )); ?>

        <?php echo $form->hiddenField($model, "[$i]arrivalCityId", array('class'=>'arrivalCity')); ?>

        <?php echo $form->labelEx($model, "[$i]arrivalCityId"); ?>
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
            'matcher'=>'js: function(){return true}',
            'sorter'=>'js:function(items){return items;}',
        ),
        'htmlOptions'=>array(
            'class'=>'span5 toField',
            'value'=>'',
        )
    )); ?>

    <?php echo $form->checkboxRow($model, "[$i]isRoundTrip", array('class'=>'isRoundTrip')); ?>

    <span class="backdate">
        <?php echo $form->datepickerRow(
            $model,
            "[$i]backDate",
            array(
                'events'=> array(
                    'changeDate'=>'js:function(ev){$(this).datepicker("hide")}'
                ),
                'class'=>'datepicker',
            )
        );?>
    </span>

    <?php $this->widget('bootstrap.widgets.BootButton', array(
        'buttonType'=>'warning',
        'icon'=>'icon-minus',
        'size'=>'mini',
        'label'=>'Удалить перелёт',
        'htmlOptions'=>array(
            'data-del'=>'route'.$i,
            'class' => 'deleteRoute'
    ))); ?>
</div>