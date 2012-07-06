<?php
$this->breadcrumbs=array(
    'Туры'=>array('index'),
    'Просмотр',
);

$this->beginWidget("AAdminPortlet", array(
    "menuItems" => array(
        array(
            "label" => "Создать",
            "url" => array("/admin/tour/constructor/new"),
        ),
    ),
    "title" => "Туры"
));
?>

<?php $this->widget('bootstrap.widgets.BootGridView',array(
    'id'=>'event-grid',
    'dataProvider'=>$dataProvider,
    //'filter'=>$model,
    'columns'=>array(
        'name',
        'createdAt',
        'userId',
        array(
            'class'=>'bootstrap.widgets.BootButtonColumn',
        ),
    ),
)); ?>

<?php $this->endWidget(); ?>
