<span id='tour-output'></span>
<?php $templateVariable = 'handlebarTour'; ?>
<?php $this->beginWidget('common.extensions.handlebars.HandlebarsWidget', array('id'=>'tour', 'compileVariable' => $templateVariable)) ?>
<table class="table" width="100%">
    <thead>
    <tr>
        <th>Дата</th>
        <th>Откуда</th>
        <th>Куда</th>
        <th>Авиакомпания</th>
        <th>Цена</th>
        <th width="18%"></th>
    </tr>
    </thead>
    <tbody>
    {{#each items}}
    <tr>
        <td>{{flights.0.flightParts.0.datetimeBegin}}</td>
        <td>{{flights.0.departureCity}}</td>
        <td>{{flights.0.arrivalCity}}</td>
        <td><img src='http://frontend.oleg.voyanga/themes/classic/images/airlines/{{valCompany}}.png'></td>
        <td>{{price}} руб.</td>
        <td><a class="btn btn-info detail-view" data-key='{{key}}'>Подробнее</a>
            <a class="btn btn-mini btn-danger delete" data-key='{{key}}'>Удалить</a></td>
    </tr>
    <td colspan="6">
        <table class="table table-bordered" id='detail-{{key}}' style='display: none'>
            <thead>
                <th>Вылет</th>
                <th>Прилёт</th>
                <th>Авиакомпания</th>
                <th>Продолжительность полёта</th>
            </thead>
            <tbody>
                {{#each flights.0.flightParts}}
                <tr>
                    <td>{{datetimeBegin}}, {{departureCity}}, {{departureAirport}}</td>
                    <td>{{datetimeEnd}}, {{arrivalCity}}, {{arrivalAirport}}</td>
                    <td><img src='http://frontend.oleg.voyanga/themes/classic/images/airlines/{{transportAirline}}.png'></td>
                    <td>{{humanTime duration}}</td>
                </tr>
                {{/each}}
            </tbody>
        </table>
    </td>
    {{/each}}
    </tbody>
</table>
<?php $this->endWidget(); ?>
<?php Yii::app()->clientScript->registerScript('tour-basket', "
    Handlebars.registerHelper('humanTime', function(duration) {
        var hours = Math.floor(duration / 3600),
            min = Math.floor((duration - hours * 3600) / 60),
            sec = duration - 60 * min - 3600 * hours,
            result = (hours > 0 ) ? hours + ' ч. ' : '';
            result += (min > 0 ) ? min + ' мин. ' : '';
            result += (sec > 0) ? sec + ' сек.' : '';
            return result;
    });

    $.getJSON('/admin/tour/basket/show')
        .done(function(data) {
            var html = {$templateVariable}(data);
            $('#tour-output').html(html);
        })
        .fail(function(data){
            $('#tour-output').html(data);
        });

    $('.detail-view').live('click', function() {
        var openElement = $('#detail-' + $(this).data('key'));
        console.log(openElement);
        openElement.toggle();
    });

    $('.delete').live('click', function() {
         $.getJSON('/admin/tour/basket/delete/key/'+$(this).data('key'))
            .done(function(data){
                var html = {$templateVariable}(data);
                $('#tour-output').html(html);
            })
            .fail(function(data){
                $('#tour-output').html(data);
            });
    });
", CClientScript::POS_READY); ?>