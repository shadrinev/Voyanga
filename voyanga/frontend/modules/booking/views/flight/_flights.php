<?php $this->beginWidget('common.components.handlebars.HandlebarsWidget', array('id'=>'flight', 'compileVariable' => $variable)) ?>
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
    {{#each flightVoyages}}
    <tr>
        <td>{{flights.0.flightParts.0.datetimeBegin}}</td>
        <td>{{flights.0.departureCity}}</td>
        <td>{{flights.0.arrivalCity}}</td>
        <td><img src='/img/airlines/{{valCompany}}.png'></td>
        <td>{{price}} руб.</td>
        <td><a class="btn btn-info detail-view" data-key='{{flightKey}}'>Подробнее</a>
            <a class="btn btn-mini btn-success buy" href='/admin/booking/flight/buy/key/{{../searchId}}_{{flightKey}}'>Купить</a></td>
    </tr>
    <td colspan="6">
        <table class="table table-bordered" id='detail-{{flightKey}}' style='display: none; background-color: #f0f0f0'>
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
                    <td><img src='/img/airlines/{{transportAirline}}.png'></td>
                    <td>{{humanTime duration}}</td>
                </tr>
                {{/each}}
            </tbody>
        </table>
    </td>
    {{/each}}
    </tbody>
</table>
<span id='searchKey' data-key='{{searchId}}'></span>
<?php $this->endWidget(); ?>
<?php Yii::app()->clientScript->registerScript('flight-tab', "
    Handlebars.registerHelper('humanTime', function(duration) {
        var hours = Math.floor(duration / 3600),
            min = Math.floor((duration - hours * 3600) / 60),
            sec = duration - 60 * min - 3600 * hours,
            result = (hours > 0 ) ? hours + ' ч. ' : '';
            result += (min > 0 ) ? min + ' мин. ' : '';
            result += (sec > 0) ? sec + ' сек.' : '';
            return result;
    });

    $('.detail-view').live('click', function() {
        var openElement = $('#detail-' + $(this).data('key'));
        console.log(openElement);
        openElement.toggle();
    });
", CClientScript::POS_READY); ?>