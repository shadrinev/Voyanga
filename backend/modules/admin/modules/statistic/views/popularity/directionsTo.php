<?php
$this->breadcrumbs=array(
	'Статистика'=>array('admin'),
    'Рейсы' => array('flights'),
	'В город '.$report->fromCity->localRu,
);

$this->beginWidget("AAdminPortlet", array(
    "menuItems" => array(
        array(
            "label" => "Обновить",
            "url" => array("directionsTo","cityId"=>$report->fromCity->id),
        ),
    ),
    "title" => "Статистика поисков перелётов в город ".$report->fromCity->localRu
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
            'value'=>'$data->arrivalCity->localRu'
        ),
        array(
            'name'=>'value',
            'header'=>'Число поисков',
            'value'=>'$data->value'
        ),
	),
)); ?>

<?php $this->endWidget(); ?>
