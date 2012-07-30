<?php
$this->breadcrumbs=array(
	'События'=>array('admin'),
	'Управление',
);

$this->beginWidget("AAdminPortlet", array(
    "menuItems" => array(
        array(
            "label" => "Обновить",
            "url" => array("flight"),
        ),
    ),
    "title" => "Статистика поисков перелётов"
));
?>

<?php $this->widget('bootstrap.widgets.BootGridView',array(
	'id'=>'event-grid',
	'dataProvider'=>$dataProvider,
	//'filter'=>$model,
	'columns'=>array(
        array(
            'header'=>'Дата',
            'value'=>'$data->primaryKey'
        ),
        array(
            'header'=>'Число поисков',
            'value'=>'$data->value["count"]'
        ),
	),
)); ?>

<?php $this->endWidget(); ?>
