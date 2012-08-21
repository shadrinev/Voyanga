<?php
$this->breadcrumbs=array(
	'Hotels',
);

$this->menu=array(
	array('label'=>'Create Event','url'=>array('create')),
	array('label'=>'Manage Event','url'=>array('admin')),
);
?>

<h1>Events</h1>

<?php $this->widget('bootstrap.widgets.BootListView',array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
