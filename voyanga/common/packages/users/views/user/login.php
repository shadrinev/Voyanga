<div class="well login">
    <?php
    /**
     * Displays a form to allow the user to login
     * @var ALoginForm $model The login form model
     */
    ?>
    <h1><?php echo Yii::t('admin', 'Вход в панель администрирования'); ?></h1>

    <?php $form = $this->beginWidget('bootstrap.widgets.BootActiveForm', array(
    'id' => 'user-form',
    'enableAjaxValidation' => true,
    'htmlOptions' => array('class' => 'well'),
)); ?>

    <?php echo $form->textFieldRow($model, 'email', array('class' => 'span3', 'size' => 60, 'maxlength' => 450)); ?>
    <?php echo $form->passwordFieldRow($model, 'password', array('class' => 'span3', 'size' => 60, 'maxlength' => 450)); ?>
    <?php echo $form->checkboxRow($model, 'rememberMe'); ?>

    <?php $this->widget('bootstrap.widgets.BootButton', array('buttonType' => 'submit', 'icon' => 'ok', 'label' => 'Login')); ?><br>
    <?php echo CHtml::link(Yii::t('admin','Зарегистрироваться'), array('user/register')); ?>

    <?php
    $this->endWidget();
    ?>
</div>
