<?php
$this->breadcrumbs=array(
    'Категории событий'=>array('admin'),
    $model->title,
);

$this->beginWidget("AAdminPortlet",
    array(
        "title" => "Просмотр категории событий"
    ));
?>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
    'data'=>$model,
    'attributes'=>array(
        'id',
        'title',
        'level',
    ),
)); ?>

<?php $this->endWidget(); ?>