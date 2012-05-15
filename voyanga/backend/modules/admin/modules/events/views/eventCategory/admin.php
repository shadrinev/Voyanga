<?php
$this->breadcrumbs=array(
    'Категории событий'=>array('admin'),
    'Управление',
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){ 
    $('.search-form').toggle(); 
    return false; 
}); 
$('.search-form form').submit(function(){ 
    $.fn.yiiGridView.update('event-category-grid', { 
        data: $(this).serialize() 
    }); 
    return false; 
}); 
");

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