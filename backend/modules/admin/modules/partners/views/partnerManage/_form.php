<?php
/**
 * The input form for the {@link Partner} model
 * @var AUser $model The User model
 */
?>
<div class="row">
    <?php $form = $this->beginWidget('bootstrap.widgets.BootActiveForm', array(
    'id' => 'user-form',
    'enableAjaxValidation' => true,
    'htmlOptions' => array('class' => 'span4 well', 'enctype' => 'multipart/form-data'),
    'focus' => array($model, 'name'),
    )); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->textFieldRow($model, 'name', array('size' => 50, 'maxlength' => 50)); ?>
    <?php echo $form->passwordFieldRow($model, 'password', array('size' => 45, 'maxlength' => 45)); ?>
    <?php echo CHtml::label('Сгенерировать пароль','genPass'); ?>
    <?php echo CHtml::checkBox('genPass',false,array()); ?>
    <?php echo $form->textFieldRow($model, 'cookieTime', array('size' => 50, 'maxlength' => 4)); ?>


    <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.BootButton', array('buttonType' => 'submit', 'icon' => 'ok', 'label' => Yii::t('admin', $model->isNewRecord ? 'Зарегистрировать' : 'Сохранить'))); ?>
    </div>
    <?php $this->endWidget(); ?>
</div><!-- form -->