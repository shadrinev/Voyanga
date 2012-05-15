<?php
$this->breadcrumbs=array(
	'События'=>array('admin'),
	$model->title=>array('view','id'=>$model->id),
	'Редактирование',
);

$this->menu=array(
	array('label'=>'List Event','url'=>array('index')),
	array('label'=>'Create Event','url'=>array('create')),
	array('label'=>'View Event','url'=>array('view','id'=>$model->id)),
	array('label'=>'Manage Event','url'=>array('admin')),
);
?>

<h1>Update Event <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>