<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
    //'type' =>'search',
    'id'=>'tour-builder-form',
    'enableAjaxValidation'=>true,
    'htmlOptions'=>array(
        'enctype' => 'multipart/form-data'
    )
)); ?>

    <?php $this->widget('frontend.widgets.tourBuilder.TourBuilderWidget', array('model' => $model, 'attribute'=>'trips')); ?>

<?php $this->endWidget(); ?>