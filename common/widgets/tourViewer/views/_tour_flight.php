<h4>Перелёт</h4>
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
    <tr>
        <td colspan="4"></td>
        <td><b>{{price}} руб.</b></td>
        <td><a class="btn btn-warning detail-view" data-key='{{flightKey}}'>Подробнее</a>
        </td>
    </tr>
    {{#each flights}}
    <tr>
        <td>{{flightParts.0.datetimeBegin}}</td>
        <td>{{departureCity}}</td>
        <td>{{arrivalCity}}</td>
        <td><img src='<?php echo $pathToAirlineImg ?>{{../valCompany}}.png'></td>
        <td></td>
        <td>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <table class="table table-bordered detail-{{../flightKey}} hide" style='background-color: #f0f0f0'>
                <thead>
                <th>Вылет</th>
                <th>Прилёт</th>
                <th>Авиакомпания</th>
                <th>Продолжительность полёта</th>
                </thead>
                <tbody>
                {{#each flightParts}}
                <tr>
                    <td>{{datetimeBegin}}, {{departureCity}}, {{departureAirport}}</td>
                    <td>{{datetimeEnd}}, {{arrivalCity}}, {{arrivalAirport}}</td>
                    <td><img src='<?php echo $pathToAirlineImg ?>{{transportAirline}}.png'></td>
                    <td>{{humanTime duration}}</td>
                </tr>
                {{/each}}
                </tbody>
            </table>
        </td>
    </tr>
    {{/each}}
    <tr>
        <td colspan="6" style="background-color: #b2e5ff; height: 5px;">&nbsp;</td>
    </tr>
    </tbody>
</table>