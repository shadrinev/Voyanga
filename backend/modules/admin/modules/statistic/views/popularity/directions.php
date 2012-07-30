<?php
$this->breadcrumbs=array(
	'Статистика'=>array('admin'),
    'Рейсы' => array('flights'),
	'Из города '.$report->fromCity->localRu,
);

$this->beginWidget("AAdminPortlet", array(
    "menuItems" => array(
        array(
            "label" => "Обновить",
            "url" => array("directions","cityId"=>$report->fromCity->id),
        ),
    ),
    "title" => "Статистика поисков перелётов из города ".$report->fromCity->localRu
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
            'value'=>'$data->departureCity->localRu'
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
