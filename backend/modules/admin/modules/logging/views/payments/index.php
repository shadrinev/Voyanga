<?php
$this->breadcrumbs=array(
    'Payments',
);

$this->menu=array();
?>

<h1>Payments</h1>

<?php $this->widget('bootstrap.widgets.BootGridView',array(
    'id'=>'event-grid',
    'dataProvider'=>$dataProvider,
    'template' =>"{pager}\n{summary}\n{items}\n{pager}",
    'columns'=>array(
        array(
            'header'=>'OrderId',
            'value'=>'$data->orderId'
        ),
        array(
            'header'=>'Метод',
            'value'=>'$data->methodName'
        ),
        array(
            'header'=>'TransactionId',
            'value'=>'$data->transactionId'
        ),

        array(
            'header'=>'Время запроса',
            'value'=>'date("Y-m-d H:i:s",$data->timestamp)'
        ),
        array(
            'header'=>'Время обработки',
            'value'=>'Yii::app()->format->formatNumber($data->executionTime)'
        ),
        array(
            'class'=>'bootstrap.widgets.BootButtonColumn',
            'updateButtonIcon'=>false,
            'template'=>'{view}',
            'viewButtonUrl'=>'"#".$data->primaryKey.""',
            'buttons' => array('view' => array(
                             'click'=>'js: function () {document.showRequestInfo($(this).attr("href"));}',     // a JS function to be invoked when the button is clicked
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
Yii::app()->clientScript->registerScript('loadRequestInfo','
    document.showRequestInfo = function (id){
    id = id.substr(1);
    $(\'#popupInfo .modal-body\').html("<p>Идет запрос данных...</p>");
    $(\'#popupInfo\').modal(\'show\');
        $.getJSON("/admin/logging/payments/getInfo/id/"+id)
        .done(function(data){
            console.log(data);
            textHtml = "";
            textHtml = textHtml + "<p>Метод:"+data.methodName+"</p>";
            textHtml = textHtml + "<p>Время отправки запроса:"+data.timestamp+"</p>";
            textHtml = textHtml + "<p>Запрос:</p>";
            textHtml = textHtml + data.request;
            textHtml = textHtml + "<p>Время ожидания ответа:"+data.executionTime+" сек</p>";
            if(!jQuery.isEmptyObject(data.errorDescription)){
                textHtml = textHtml + "<p>Описание ошибки:"+data.errorDescription+"</p>";
            }
            if(!jQuery.isEmptyObject(data.response)){
                textHtml = textHtml + "<p>Ответ:</p>";
                textHtml = textHtml + data.response;
            }
            $(\'#popupInfo .modal-body\').html(textHtml);
        })
        .fail(function(data){
            $(\'#popupInfo .modal-body\').html("Ошибка сервера");
            //btn.button("reset");
            //btn.html("Произошёл сбой");
            //e.preventDefault();
        });
    };
    if(window.location.hash){
        var id = window.location.hash;
        document.showRequestInfo(id);
    }
    ', CClientScript::POS_READY);
CTextHighlighter::registerCssFile();
?>
