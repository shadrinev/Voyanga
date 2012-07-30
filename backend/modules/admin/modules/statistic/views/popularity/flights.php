<?php
$this->breadcrumbs=array(
    'Статистика'=>array('admin'),
    'Рейсы'
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
    'ajaxUpdate'=>true,
	'columns'=>array(
        array(
            'header'=>'Откуда',
            'value'=>'CHtml::link($data->departureCity->localRu,array("/admin/statistic/popularity/directions", "cityId"=>$data->departureCity->id))',
            'type'=>'html'
        ),
        array(
            'header'=>'Куда',
            'value'=>'CHtml::link($data->arrivalCity->localRu,array("/admin/statistic/popularity/directionsTo", "cityId"=>$data->arrivalCity->id))',
            'type'=>'html'
        ),
        array(
            'name'=>'value',
            'header'=>'Число поисков',
            'value'=>'$data->value'
        ),
	),
)); ?>

<?php $this->endWidget(); ?>
