<div class="form-horizontal well tourBuilder" id="trip<?php echo $i; ?>">
        <?php echo $form->hiddenField($model, "[$i]cityId", array('class'=>'tripCity')); ?>

        <?php echo $form->labelEx($model, "[$i]cityId"); ?>
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

    <?php echo $form->datepickerRow(
        $model,
        "[$i]startDate",
        array(
            'events'=> array(
                'changeDate'=>'js:function(ev){$(this).datepicker("hide")}'
            ),
            'class'=>'datepicker'
        )
    );?>

     <?php echo $form->datepickerRow(
            $model,
            "[$i]endDate",
            array(
                'events'=> array(
                    'changeDate'=>'js:function(ev){$(this).datepicker("hide")}'
                ),
                'class'=>'datepicker',
            )
     );?>

    <?php $this->widget('bootstrap.widgets.BootButton', array(
        'buttonType'=>'warning',
        'icon'=>'icon-minus',
        'size'=>'mini',
        'label'=>'Удалить',
        'htmlOptions'=>array(
            'data-del'=>'trip'.$i,
            'class' => 'deletetrip'
    ))); ?>
</div>