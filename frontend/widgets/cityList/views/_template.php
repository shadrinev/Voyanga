<div class="form-horizontal well" id="item-<?php echo $i; ?>">
        <?php echo $form->hiddenField($model, "[$i]$attributeId", array('class'=>'city')); ?>

        <?php echo $form->labelEx($model, "[$i]$attributeReadable"); ?>
        <?php $this->widget('bootstrap.widgets.BootTypeahead', array(
            'options'=>array(
                'items'=>10,
                'ajax' => array(
                    'url' => "/ajax/cityForFlightOrHotel",
                    'timeout' => 5,
                    'displayField' => "label",
                    'triggerLength' => 2,
                    'method' => "get",
                    'loadingClass' => "loading-circle",
                ),
                'onselect'=>'js:function(res){this.$element.siblings("input.city").val(res.id)}',
                'matcher'=>'js: function(){return true}',
                'sorter'=>'js:function(items){return items;}',
            ),

            'htmlOptions'=>array(
                'class'=>'span5 cityId',
                'value'=> ($newItem) ? '' : $model->$attributeReadable
            )
        )); ?>

    <?php $this->widget('bootstrap.widgets.BootButton', array(
        'buttonType'=>'warning',
        'icon'=>'icon-minus',
        'size'=>'mini',
        'label'=>'Удалить',
        'htmlOptions'=>array(
            'data-del'=>'item-'.$i,
            'class' => 'deletetrip'
    ))); ?>
</div>