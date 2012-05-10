<?php
/**
 * Allows the user to enter a new password
 * @var AUser $model the model to change the password for
 */
$this->pageTitle = "Enter A New Password";
?>
<div class="well login">
    <h1><?php echo Yii::t('admin','Введите новый пароль') ?></h1>

    <?php $form = $this->beginWidget('bootstrap.widgets.BootActiveForm', array(
    'id' => 'user-form',
    'enableAjaxValidation' => true,
    'htmlOptions' => array('class' => 'well'),
)); ?>

    <?php echo $form->passwordFieldRow($model, 'password', array('size' => 60, 'maxlength' => 450)); ?>

    <?php $this->widget('bootstrap.widgets.BootButton', array('buttonType' => 'submit', 'icon' => 'ok', 'label' => Yii::t('admin','Сменить пароль'))); ?>

    <?php
    $this->endWidget();
    ?>
</div>
