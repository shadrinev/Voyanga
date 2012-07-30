<?php
/**
 * Displays a password reset form
 * @var AUser $model the model to reset the password for
 */
$this->pageTitle = "Reset Your Password";
?>
<div class="well login">
    <h1><?php echo Yii::t('admin', 'Сброс пароля'); ?></h1>

    <?php $form = $this->beginWidget('bootstrap.widgets.BootActiveForm', array(
        'id' => 'user-form',
        'enableAjaxValidation' => true,
        'htmlOptions' => array('class' => 'well'),
    )); ?>

    <?php echo $form->textFieldRow($model, 'email', array('size' => 60, 'maxlength' => 450)); ?>

    <?php $this->widget('bootstrap.widgets.BootButton', array('buttonType' => 'submit', 'icon' => 'ok', 'label' => Yii::t('admin','Сбросить пароль'))); ?>

    <?php
        $this->endWidget();
    ?>
</div>