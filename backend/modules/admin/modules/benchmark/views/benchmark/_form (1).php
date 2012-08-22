<?php
/**
 * The input form for the {@link ABenchmark} model
 * @var ABenchmark $model The ABenchmark model
 */
?>
<div class="row">

    <?php $form = $this->beginWidget('bootstrap.widgets.BootActiveForm', array(
    'id' => 'abenchmark-form',
    'enableAjaxValidation' => false,
    'htmlOptions' => array('class' => 'span5 well'),
    'focus' => array($model, 'url'),
)); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->textFieldRow($model, 'url', array('size' => 60, 'maxlength' => 1024)); ?>

    <?php echo $form->textFieldRow($model, 'route', array('size' => 60, 'maxlength' => 255)); ?>

    <?php echo $form->labelEx($model, 'params'); ?>
    <?php
    $this->widget("packages.arrayInput.AArrayInputWidget",
        array(
            "model" => $model,
            "attribute" => "params"
        ));
    ?>
    <p class='hint'>Here you can add parameters to pass to the route.</p>
    <?php echo $form->error($model, 'params'); ?>

    <?php $this->widget('bootstrap.widgets.BootButton', array('buttonType' => 'submit', 'icon' => 'ok', 'label' => 'Создать')); ?>
    <?php $this->endWidget(); ?>

</div><!-- form -->