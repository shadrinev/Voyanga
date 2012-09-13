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
		'address',
		'contact',
		'statusName',
		'preview',
        'tagsString',
        array(
            'label' =>'Цена',
            'value'=>'Из Москвы: '.$model->priceMoscow.", из Питера: ".$model->pricePiter
        ),
		'description:raw',
        array(
            'label' => 'Ссылки',
            'value' => implode(', ', $model->links),
            'type'  => 'raw'
        ),
        array(
            'name'=>'pictureSmall',
            'value'=>isset($model->pictureSmall) ? CHtml::image($model->pictureSmall->url, $model->title).'<br><br>'.
                    $this->widget('BootButton', array(
                        'url'=>array('deleteResource','id'=>$model->id,'attribute'=>'pictureSmall'),
                        'label'=>'Удалить',
                        'type'=>'danger'
                    ), true)
                    : '',
            'type'=>'raw'
        ),
        array(
            'name'=>'pictureBig',
            'value'=>isset($model->pictureBig) ? CHtml::image($model->pictureBig->url, $model->title).'<br><br>'.
                $this->widget('BootButton', array(
                    'url'=>array('deleteResource','id'=>$model->id,'attribute'=>'pictureBig'),
                    'label'=>'Удалить',
                    'type'=>'danger'
                ), true)
                : '',
            'type'=>'raw'
        ),
        array(
            'name'=>'pictures',
            'value'=>!empty($model->pictures)  ? $this->widget('common.widgets.Gallery',array('model'=>$model, 'attribute'=>'pictures', 'id'=>'gallery'), true).
                $this->widget('BootButton', array(
                    'url'=>array('deleteResource','id'=>$model->id,'attribute'=>'pictures11'),
                    'label'=>'Удалить это изображение',
                    'type'=>'danger',
                    'htmlOptions' => array('id' => 'deleteImage'),
                ), true)."&nbsp;".
                $this->widget('BootButton', array(
                    'url'=>array('deleteResource','id'=>$model->id,'attribute'=>'pictures'),
                    'label'=>'Удалить всю галерею',
                    'type'=>'danger',
                    'size'=>'small'
                ), true) : '',
            'type'=>'raw'
        ),
	),
)); ?>

<?php foreach ($model->tours as $tour): ?>
    <h3><?php echo $tour->order->name; ?></h3>
    <?php $this->widget('site.common.widgets.tourViewer.TourViewerWidget',array(
        'orderId' => $tour->order->id,
        'urlToBasket' => $this->createUrl('/admin/tour/basket/show'),
        'urlToConstructor' => $this->createUrl('/admin/tour/constructor/new'),
        'pathToAirlineImg' => 'http://frontend.voyanga/img/airlines/'));
    ?>
<?php endforeach ?>

<?php $this->widget('bootstrap.widgets.BootButton', array(
    'url'=>Yii::app()->createUrl('/admin/tour/constructor/create', array('eventId'=>$model->id)),
    'label'=>'Составить тур',
)); ?>
<?php $this->endWidget(); ?>

<?php Yii::app()->clientScript->registerScript('deleteImageGallery', "
(function($){
        var urlPrefix = '".$this->createUrl("deleteResource",array("id"=>$model->id,"attribute"=>"pictures","name"=>''))."';
        $('#deleteImage').on('click', function(e){
                var imageName = $('#gallery').find('.active img').attr('alt'),
                    fullUrl = urlPrefix + '/' + imageName;
                    $(this).attr('href', fullUrl);
                    //console.log(fullUrl);
                }
        );
})(window.jQuery);
", CClientScript::POS_READY); ?>
