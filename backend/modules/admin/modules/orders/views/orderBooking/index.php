<?php
$this->breadcrumbs=array(
    'Orders',
);

$this->menu=array(
    array('label'=>'Create Event','url'=>array('create')),
    array('label'=>'Manage Event','url'=>array('admin')),
);
?>

<h1>Заказы</h1>
<a href="<?= $navLink?>"><?= $navText?></a>
<form method="get">
    Ищет по NemoBookId, pnr, email
    <br />
    <input type="text" name="search" value="">
    <input type="submit" value="Искать">
</form>

<?php $this->widget('bootstrap.widgets.BootGridView',array(
    'id'=>'event-grid',
    'dataProvider'=>$dataProvider,
    //'filter'=>$model,
    'columns'=>array(
        array(
            'header'=>'Номер',
            'labelExpression'=> '$data->id',
            'urlExpression'=> '"/admin/orders/orderBooking/view/id/" . $data->id . "/"',
            'class'=>'CLinkColumn',
        ),
        array(
            'header'=>'email',
            'value'=>'$data->email'
        ),
        array(
            'header'=>'Пользователь',
            'value'=>'$data->userDescription'
        ),
        array(
            'header'=>'Товаров',
            'value'=>'$data->countBookings'
        ),
        array(
            'header'=>'Общий Статус',
            'value'=>'$data->orderStatus'
        ),
        array(
            'header'=>'Сумма заказа',
            'value'=>'$data->fullPrice'
        ),
        array(
            'header'=>'ID партнера',
            'value'=>'$data->partnerId'
        ),
        array(
            'class'=>'bootstrap.widgets.BootButtonColumn',
            'updateButtonIcon'=>false,
            'template'=>'{view}',
            'viewButtonUrl'=>'"#".$data->primaryKey.""',
            'buttons' => array('view' => array(
                             'click'=>'js: function () {document.showOrderInfo($(this).attr("href"));}',     // a JS function to be invoked when the button is clicked
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

<?php $this->beginWidget('common.components.handlebars.HandlebarsWidget', array('id'=>'orderBookingTmpl', 'compileVariable' => 'orderBookingTemplate')) ?>
<h1>Просмотр заказа</h1>

    <ul>
        <li>Номер заказа: {{id}}</li>
        <li>Email: {{email}}</li>
        <li>Phone: {{phone}}</li>
        <li>Пользователь: {{userDescription}}</li>
        <li>Дата создания: {{timestamp}}</li>
    </ul>
        Бронирования:
    <table class="table table-bordered grid-view" width="100%">
        <thead>
        <tr>
            <th>Бронирование</th>
            <th>Описание</th>
            <th>Состояние</th>
            <th>Цена</th>
            <th>WF/Логи</th>
        </tr>
        </thead>
        <tbody>
        {{#each bookings}}
        <tr>
            <td>{{type}}</td>
            <td>{{#each description}} {{this}}<br /> {{/each}}</td>
            <td>{{status}}</td>
            <td>{{price}}</td>
            <td class="button-column"><a href="{{wfUrl}}" rel="tooltip" title="Просмотреть"><i class="icon-eye-open"></i></a></td>
        </tr>
        {{/each}}

        </tbody>
    <table>

<?php $this->endWidget(); ?>
<?php


Yii::app()->clientScript->registerScriptFile('/js/orderBooking.js');
CTextHighlighter::registerCssFile();
