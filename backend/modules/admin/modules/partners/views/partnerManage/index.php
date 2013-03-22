<?php
$this->breadcrumbs=array(
	'Partners',
);

$this->menu=array(
	array('label'=>'Create Event','url'=>array('create')),
	array('label'=>'Manage Event','url'=>array('admin')),
);
?>

<h1>Управление аккаунтами партнеров</h1>

<form method="post">
<?php $this->widget('bootstrap.widgets.BootGridView',array(
    'id'=>'event-grid',
    'dataProvider'=>$dataProvider,
    //'filter'=>$model,
    'columns'=>array(
        array(
            'header'=>'id',
            'value'=>'$data->id'
        ),
        array(
            'header'=>'имя',
            'value'=>'$data->name'
        ),
        array(
            'header'=>'время привязки партнера',
            'value'=>'$data->cookieTime'
        ),
        array(
            'header'=>'ключ',
            'value'=>'$data->partnerKey'
        ),
        array(
            'header'=>'API CliendId',
            'value'=>'$data->clientId'
        ),
        array(
            'header'=>'API Key',
            'value'=>'$data->apiKey'
        ),
        array(
            'class'=>'bootstrap.widgets.BootButtonColumn',
            'template'=>'{update} {delete}',
            'updateButtonUrl'=>'"/admin/partners/partnerManage/edit/id/".$data->primaryKey',
            'deleteConfirmation'=>'Вы действительно хотите удалить партнера?',
            'buttons' => array('update' => array(
                'click'=>'js: function () {document.modifyName($(this).attr("href"));}',     // a JS function to be invoked when the button is clicked
            ),
            ),
            'updateButtonOptions'=>array('class'=>'update','data-object-id'=>'$data->primaryKey'),
            'deleteButtonOptions'=>array('class'=>'delete','data-object-id'=>'$data->primaryKey')
        ),
    ),
)); ?>
    <a href="/admin/partners/partnerManage/edit/" class="btn">Добавить нового партнера</a>
</form>
