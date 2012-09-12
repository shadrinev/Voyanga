<?php $this->beginWidget('common.components.handlebars.HandlebarsWidget', array('id'=>'tour', 'compileVariable' => $variable)) ?>
    <table class="table table-bordered" id='detail-{{flightKey}}' style='background-color: #f0f0f0'>
        <thead>
        <th>Вылет</th>
        <th>Прилёт</th>
        <th>Авиакомпания</th>
        <th>Продолжительность полёта</th>
        </thead>
        <tbody>
        {{#each flight.flightParts}}
        <tr>
            <td>{{datetimeBegin}}, {{departureCity}}, {{departureAirport}}</td>
            <td>{{datetimeEnd}}, {{arrivalCity}}, {{arrivalAirport}}</td>
            <td><img src='/img/airlines/{{transportAirline}}.png'></td>
            <td>{{humanTime duration}}</td>
        </tr>
        {{/each}}
        </tbody>
    </table>
<?php $this->endWidget(); ?>