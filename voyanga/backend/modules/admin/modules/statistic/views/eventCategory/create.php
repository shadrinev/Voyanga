<?php
$this->breadcrumbs=array(
    'Категории событий'=>array('admin'),
    'Создание',
);

$this->beginWidget("AAdminPortlet",
    array(
        "title" => "Создание новой категории событий"
    ));
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>

<?php $this->endWidget(); ?>