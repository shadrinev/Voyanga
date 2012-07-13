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
//echo Yii::getPathOfAlias("site.backend.modules.admin.components.AAdminPortlet");die();
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

<?php echo $form->textFieldRow($passport,'firstName');?>
<?php echo $form->textFieldRow($passport,'lastName');?>

<?php echo $form->textFieldRow($booking,'contactPhone');?>
<?php echo $form->textFieldRow($booking,'contactEmail');?>

<?php echo $form->textFieldRow($passport,'birthday');?>
<?php echo $form->dropDownListRow($passport,'genderId', Passport::getPossibleGenders());?>
<?php echo $form->dropDownListRow($passport,'countryId', Country::getPossibleCountries());?>
<?php echo $form->dropDownListRow($passport,'documentTypeId', Passport::getPossibleTypes());?>
<?php echo $form->textFieldRow($passport,'series');?>
<?php echo $form->textFieldRow($passport,'number');?>


<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.BootButton', array(
    'buttonType'=>'submit',
    'type'=>'primary',
    'label'=>'Продолжить',
)); ?>
</div>

<?php $this->endWidget(); ?>

<?php $this->endWidget(); ?>

