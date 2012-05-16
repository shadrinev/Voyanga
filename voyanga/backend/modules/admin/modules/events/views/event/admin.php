<?php
$this->breadcrumbs=array(
	'События'=>array('admin'),
	'Управление',
);

$this->beginWidget("AAdminPortlet", array(
    "menuItems" => array(
        array(
            "label" => "Создать",
            "url" => array("create"),
        ),
    ),
    "title" => "События"
));
?>

<?php $this->widget('bootstrap.widgets.BootGridView',array(
	'id'=>'event-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'startDate',
		'endDate',
		'cityId',
		'address',
		'contact',
		/*
		'status',
		'preview',
		'description',
		*/
		array(
			'class'=>'bootstrap.widgets.BootButtonColumn',
		),
	),
)); ?>

<?php $this->endWidget(); ?>
