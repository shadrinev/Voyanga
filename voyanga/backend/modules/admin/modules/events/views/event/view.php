<?php
$this->breadcrumbs=array(
	'События'=>array('admin'),
	$model->title,
);

$this->beginWidget("AAdminPortlet", array(
    "menuItems" => array(
        array(
            "label" => "Создать",
            "url" => array("create"),
        ),
    ),
    "sidebarMenuItems" => array(
        array(
            "label" => "Редактировать",
            "url" => array("update", 'id'=>$model->id),
        ),
    ),
    "title" => "Просмотр события \"".$model->title."\""
));
?>

<?php $this->widget('bootstrap.widgets.BootDetailView',array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'startDate',
		'endDate',
        'title',
		array(
            'label' =>'Город',
            'value'=>$model->city->localRu
        ),
		'address',
		'contact',
		'statusName',
		'preview',
        'tagsString',
		'description:raw',
        array(
            'name'=>'pictureSmall',
            'value'=>isset($model->pictureSmall) ? CHtml::image($model->pictureSmall->url, $model->title) : '',
            'type'=>'raw'
        ),
        array(
            'name'=>'pictureBig',
            'value'=>isset($model->pictureBig) ? CHtml::image($model->pictureBig->url, $model->title) : '',
            'type'=>'raw'
        ),
        array(
            'name'=>'pictures',
            'value'=>isset($model->pictures) ? $this->widget('common.widgets.Gallery',array('model'=>$model, 'attribute'=>'pictures'), true) : '',
            'type'=>'raw'
        ),
	),
)); ?>

<?php $this->endWidget(); ?>
