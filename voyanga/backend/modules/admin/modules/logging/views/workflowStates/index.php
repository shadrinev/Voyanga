<?php
$this->breadcrumbs=array(
    'WorkflowStates',
);

$this->menu=array();
?>

<h1>Workflow States</h1>

<?php $this->widget('bootstrap.widgets.BootGridView',array(
    'id'=>'event-grid',
    'dataProvider'=>$dataProvider,
    //'filter'=>$model,
    'columns'=>array(
        array(
            'header'=>'Объект',
            'value'=>'$data->className'
        ),
        array(
            'header'=>'Id',
            'value'=>'$data->objectId'
        ),
        array(
            'header'=>'Состояние',
            'value'=>'$data->lastState'
        ),
        array(
            'header'=>'Время обновления',
            'value'=>'date("Y-m-d H:i:s",$data->updated)'
        ),
        array(
            'header'=>'Параметры',
            'value'=>'$data->description'
        ),
        array(
            'class'=>'bootstrap.widgets.BootButtonColumn',
            'updateButtonIcon'=>false,
            'template'=>'{view}',
            'viewButtonUrl'=>'"#".$data->primaryKey.""',
            'buttons' => array('view' => array(
                             'click'=>'js: function () {document.showWfInfo($(this).attr("href"));}',     // a JS function to be invoked when the button is clicked
                        ),
                ),
            'viewButtonOptions'=>array('class'=>'view','data-object-id'=>'$data->primaryKey')
        ),
    ),
)); ?>

<?php $this->beginWidget('bootstrap.widgets.BootModal', array('id'=>'popupInfo')); ?>

<div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>
    <h3>Параметры запроса</h3>
</div>

<div class="modal-body">
    <p>Идет запрос данных...</p>
</div>

<div class="modal-footer">
    <?php $this->widget('bootstrap.widgets.BootButton', array(
    'label'=>'Close',
    'url'=>'#',
    'htmlOptions'=>array('data-dismiss'=>'modal'),
)); ?>
</div>

<?php $this->endWidget(); ?>

<?php
Yii::app()->clientScript->registerScriptFile('/js/workflowStates.js');
CTextHighlighter::registerCssFile();
?>
