<?php
$this->breadcrumbs=array(
    'Категории событий'=>array('admin'),
    'Управление',
);

$this->beginWidget("AAdminPortlet", array(
    "menuItems" => array(
        array(
            "label" => "Создать",
            "url" => array("create"),
        ),
    ),
    "title" => "Категории событий"
));
?>

<?php $this->widget('ext.QTreeGridView.CQTreeGridView',array(
    'id'=>'event-category-grid',
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'ajaxUpdate'=>false,
    'columns'=>array(
        'title',
        array(
            'class'=>'bootstrap.widgets.BootButtonColumn',
        ),
    ),
)); ?>

<?php $this->endWidget(); ?>