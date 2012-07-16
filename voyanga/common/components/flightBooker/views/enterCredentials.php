<?php
/**
 * A view used to create new {@link User} models
 * @var User $model The User model to be inserted
 */

$this->breadcrumbs = array(
    'Бронирование'=>array('/admin/booking/'),
    'Перелёт'=>array('/admin/booking/flight'),
    'Поиск'=>array(),
    'Ввод данных'
);

$this->beginWidget("site.backend.modules.admin.components.AAdminPortlet",
    array(
        "title" => "Ввод данных"
    ));
?>

<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
    //'type' =>'search',
    'id'=>'flight-form',
    'enableAjaxValidation'=>false,
    'htmlOptions'=>array(
        'enctype' => 'multipart/form-data',
    )
)); ?>

<?php echo $form->textFieldRow($passport,'[1]firstName');?>
<?php echo $form->textFieldRow($passport,'[1]lastName');?>

<?php echo $form->textFieldRow($booking,'contactPhone');?>
<?php echo $form->textFieldRow($booking,'contactEmail');?>

<?php echo $form->textFieldRow($passport,'[1]birthday');?>
<?php echo $form->dropDownListRow($passport,'[1]genderId', Passport::getPossibleGenders());?>
<?php echo $form->dropDownListRow($passport,'[1]countryId', Country::getPossibleCountries());?>
<?php echo $form->dropDownListRow($passport,'[1]documentTypeId', Passport::getPossibleTypes());?>
<?php echo $form->textFieldRow($passport,'[1]series');?>
<?php echo $form->textFieldRow($passport,'[1]number');?>


<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.BootButton', array(
    'buttonType'=>'submit',
    'type'=>'primary',
    'label'=>'Продолжить',
)); ?>
</div>

<?php $this->endWidget(); ?>

<?php $this->endWidget(); ?>

