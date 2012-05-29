<?php
$this->breadcrumbs=array(
    'Категории событий'=>array('admin'),
    $model->title=>array('view','id'=>$model->id),
    'Редактирование',
);

$this->beginWidget("AAdminPortlet",
    array(
        "title" => "Редактирование категории событий"
    ));
?>

<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>

<?php $this->endWidget(); ?>