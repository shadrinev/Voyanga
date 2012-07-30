<?php
$this->breadcrumbs=array(
	'События'=>array('admin'),
	$model->title=>array('view','id'=>$model->id),
	'Редактирование',
);

$this->beginWidget("AAdminPortlet",
    array(
        "title" => "Редактировать событие"
    ));
?>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>

<?php $this->endWidget(); ?>