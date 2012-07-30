<?php
/**
 * The input form for the {@link AUser} model
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
    <?php echo $form->textFieldRow($model, 'email', array('size' => 60, 'maxlength' => 450)); ?>
    <?php if (Yii::app()->getModule("users")->enableProfileImages) : ?>
         <?php echo $form->fileFieldRow($model, 'thumbnail'); ?>
    <?php endif ?>

    <!--<div class="row">
        <?php echo $form->labelEx($model, 'requireNewPassword'); ?>
        <?php echo $form->checkbox($model, 'requireNewPassword'); ?>
        <p class='hint'>Check this box to require the user to change their password on next login.</p>
        <?php echo $form->error($model, 'requireNewPassword'); ?>
    </div>-->

    <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.BootButton', array('buttonType' => 'submit', 'icon' => 'ok', 'label' => Yii::t('admin', $model->isNewRecord ? 'Зарегистрировать' : 'Сохранить'))); ?>
    </div>
    <?php $this->endWidget(); ?>
</div><!-- form -->