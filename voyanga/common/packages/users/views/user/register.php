<?php
/**
 * The user registration form
 * @var AUser $model the user model
 */
$this->pageTitle = "Signup Now";
?>
<div class="well login">
    <h1><?php echo Yii::t('admin', 'Регистрация'); ?></h1>

    <?php $form = $this->beginWidget('bootstrap.widgets.BootActiveForm', array(
        'id' => 'user-form',
        'enableAjaxValidation' => true,
        'htmlOptions' => array('class' => 'well'),
    )); ?>

    <?php echo $form->textFieldRow($model, 'name', array('class' => 'span3', 'size' => 60, 'maxlength' => 450)); ?>
    <?php echo $form->textFieldRow($model, 'email', array('class' => 'span3', 'size' => 60, 'maxlength' => 450)); ?>
    <?php echo $form->passwordFieldRow($model, 'password', array('class' => 'span3', 'size' => 60, 'maxlength' => 450)); ?>
    <?php $this->widget('bootstrap.widgets.BootButton', array('buttonType' => 'submit', 'icon' => 'ok', 'label' => Yii::t('admin','Зарегистрироваться'))); ?>

    <?php
    $this->endWidget();
    ?>
</div>
