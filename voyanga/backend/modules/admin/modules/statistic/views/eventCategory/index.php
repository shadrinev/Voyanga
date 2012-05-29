<?php
$this->breadcrumbs=array(
    'Категории событий',
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

<?php $this->widget('bootstrap.widgets.BootListView',array(
    'dataProvider'=>$dataProvider,
    'itemView'=>'_view',
)); ?>

<?php $this->endWidget(); ?>