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

<?php echo $form->textFieldRow($booking,'contactPhone');?>
<?php echo $form->textFieldRow($booking,'contactEmail');?>

<h2>Информация о паспортах взрослых</h2>
<?php foreach ($adultPassports as $i=>$passport): ?>
    <?php $this->renderPartial('flightBooker.views._adult_passport_form', array('i'=>$i, 'passport'=>$passport, 'form'=>$form)); ?>
<?php endforeach ?>

<?php if ($childrenPassports): ?><h2>Информация о паспортах детей</h2><?php endif; ?>
<?php foreach ($childrenPassports as $i=>$passport): ?>
    <?php $this->renderPartial('flightBooker.views._child_passport_form', array('i'=>$i, 'passport'=>$passport, 'form'=>$form)); ?>
<?php endforeach ?>

<?php if ($infantPassports): ?><h2>Информация о младенцах</h2><?php endif; ?>
<?php foreach ($infantPassports as $i=>$passport): ?>
    <?php $this->renderPartial('flightBooker.views._infant_passport_form', array('i'=>$i, 'passport'=>$passport, 'form'=>$form)); ?>
<?php endforeach ?>



<div class="form-actions">
    <?php $this->widget('bootstrap.widgets.BootButton', array(
    'buttonType'=>'submit',
    'type'=>'primary',
    'label'=>'Продолжить',
)); ?>
</div>

<?php $this->endWidget(); ?>

<?php $this->endWidget(); ?>

