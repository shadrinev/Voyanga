<?php
/**
 * The input form for the {@link AAuthRole} model
 * @var AAuthRole $model The AAuthRole model
 */
?>
<div class="form wide">

    <?php $form = $this->beginWidget('bootstrap.widgets.BootActiveForm', array(
    'id' => 'aauth-role-form',
    'enableAjaxValidation' => true,
    )); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->textFieldRow($model, 'name', array('size' => 60, 'maxlength' => 64)); ?>
    <p class='hint'>Please enter a unique name for this role.</p>

    <?php echo $form->textAreaRow($model, 'description', array('rows' => 6, 'cols' => 50)); ?>
    <p class='hint'>Please enter a short description for this role</p>

    <?php echo $form->textAreaRow($model, 'bizrule', array('rows' => 6, 'cols' => 50)); ?>
    <p class='hint'>Here you can enter a <b>valid</b> PHP expression that determines whether this role really
        applies.</p>

    <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.BootButton', array(
        'buttonType'=>'submit',
        'type'=>'primary',
        'label'=>$model->isNewRecord ? 'Создать' : 'Сохранить',
    )); ?>

<?php $this->endWidget(); ?>