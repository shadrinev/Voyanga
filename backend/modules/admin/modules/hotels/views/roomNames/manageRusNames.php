<?php
$this->breadcrumbs=array(
	'Hotels',
);

$this->menu=array(
	array('label'=>'Names Mapping','url'=>array('manage')),
	array('label'=>'Rus Names Manage','url'=>array('rusNamesManage')),
);
?>

<h1>Русские названия отелей</h1>

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
            'header'=>'roomName',
            'value'=>'$data->roomNameRus'
        ),
        array(
            'class'=>'bootstrap.widgets.BootButtonColumn',
            'updateButtonIcon'=>false,
            'template'=>'{update}',
            'updateButtonUrl'=>'"#".$data->primaryKey."||".$data->roomNameRus',
            'buttons' => array('update' => array(
                'click'=>'js: function () {document.modifyName($(this).attr("href"));}',     // a JS function to be invoked when the button is clicked
                ),
            ),
            'updateButtonOptions'=>array('class'=>'update','data-object-id'=>'$data->primaryKey')
        ),
    ),
)); ?>
    <input type="text" name="rusNameId" id="rusNameId" value="" placeholder="id">
    <?php $this->widget('bootstrap.widgets.BootTypeahead', array(
    'options'=>array(
        'items'=>10,
        'ajax' => array(
            'url' => "/admin/hotels/roomNames/rusRoomNames",
            'timeout' => 5,
            'displayField' => "value",
            'triggerLength' => 2,
            'method' => "get",
            'loadingClass' => "loading-circle",
        ),
        'onselect'=>'js:function(res){console.log(res);$("#rusNameId").val(res.id);document.idChange();}',
        'matcher'=>'js:function(){return true}',
    ),
    'htmlOptions'=>array(
        'value'=>'',
        'id'=>'roomNameRusField',
        'name'=>'roomNameRusField'
    )
)); ?>
    <input type="submit" name="smbset" id="smbset" value="Ok">
</form>
<?php

Yii::app()->clientScript->registerScript('modifyName','
    document.modifyName = function (info){
        info = info.slice(1);
        var arrParams = info.split("||");
        var id = arrParams[0];
        var name = arrParams[1];
        $("#rusNameId").val(id);
        $("#roomNameRusField").val(name);
        document.idChange();
    };

    document.idChange = function (){
        var idVal = $("#rusNameId").val();
        if(idVal.length>0){
            $("#smbset").val("Изменить");
        }else{
            $("#smbset").val("Добавить");
        }
    };
    $("#rusNameId").on("change",document.idChange);


    ', CClientScript::POS_READY);
?>