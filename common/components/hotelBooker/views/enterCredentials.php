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
<?php echo CHtml::errorSummary($model->bookingForm); ?>
<?php echo $form->textFieldRow($model->bookingForm,'contactEmail');?>
<?php echo $form->textFieldRow($model->bookingForm,'contactPhone');?>

<?php foreach ($model->roomsPassports as $i=> $roomPassport): ?>
    <?php foreach ($roomPassport->adultsPassports as $j=>$adultPassport): ?>
        <?php echo CHtml::errorSummary($adultPassport); ?>
        <?php echo $form->radioButtonListInlineRow($adultPassport, "[$i][$j]genderId", $adultPassport->getPossibleGenders()); ?>
        <?php echo $form->textFieldRow($adultPassport,"[$i][$j]firstName");?>
        <?php echo $form->textFieldRow($adultPassport,"[$i][$j]lastName");?>
    <?php endforeach; ?>
    <?php foreach ($roomPassport->childrenPassports as $j=>$childPassport): ?>
        <?php echo CHtml::errorSummary($adultPassport); ?>
        <?php echo $form->textFieldRow($childPassport,"[$i][$j]firstName");?>
        <?php echo $form->textFieldRow($childPassport,"[$i][$j]lastName");?>
    <?php endforeach; ?>
<?php endforeach; ?>


<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.BootButton', array(
    'buttonType'=>'submit',
    'type'=>'primary',
    'label'=>'Продолжить',
)); ?>
</div>

<?php $this->endWidget(); ?>

<?php $this->endWidget(); ?>

