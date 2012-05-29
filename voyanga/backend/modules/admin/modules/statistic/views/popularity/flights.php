<?php
$this->breadcrumbs=array(
	'События'=>array('admin'),
	'Управление',
);

$this->beginWidget("AAdminPortlet", array(
    "menuItems" => array(
        array(
            "label" => "Обновить",
            "url" => array("flights"),
        ),
    ),
    "title" => "Статистика поисков перелётов"
));
?>

<?php $this->widget('bootstrap.widgets.BootGridView',array(
	'id'=>'event-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
        array(
            'header'=>'Откуда',
            'value'=>'$data->departureCity->localRu'
        ),
        array(
            'header'=>'Куда',
            'value'=>'$data->arrivalCity->localRu'
        ),
        array(
            'name'=>'value["count"]',
            'header'=>'Число поисков',
            'value'=>'$data->value["count"]'
        ),
	),
)); ?>

<?php $this->endWidget(); ?>
