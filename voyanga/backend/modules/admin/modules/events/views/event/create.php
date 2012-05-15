<?php
$this->breadcrumbs=array(
	'События'=>array('admin'),
	'Добавление',
);

$this->beginWidget("AAdminPortlet",
    array(
        "title" => "Добавить событие"
    ));
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>

<?php $this->endWidget(); ?>