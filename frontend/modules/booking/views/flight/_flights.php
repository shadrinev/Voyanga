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
        <td colspan="4"></td>
        <td><b>{{price}} руб.</b></td>
        <td><a class="btn btn-warning detail-view" data-key='{{flightKey}}'>Подробнее</a>
            <a class="btn btn-mini btn-success buy" href='/booking/flight/buy/key/{{../searchId}}_{{flightKey}}'>Выбрать</a>
            <a class='btn btn-mini btn-info chooseFlight' data-searchkey='{{flightKey}}'>добавить в конструктор</a>
        </td>
    </tr>
    {{#each flights}}
    <tr>
        <td>{{flightParts.0.datetimeBegin}}</td>
        <td>{{departureCity}}</td>
        <td>{{arrivalCity}}</td>
        <td><img src='http://test.voyanga.com/img/airlines/{{../valCompany}}.png'></td>
        <td></td>
        <td>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <table class="table table-bordered flight-detail-{{../flightKey}}" style='display: none; background-color: #f0f0f0'>
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
                    <td><img src='http://test.voyanga.com/img/airlines/{{transportAirline}}.png'></td>
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
    {{/each}}
    </tbody>
</table>
<span id='searchId' data-searchid='{{searchId}}'></span>
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
        var openElement = $('.flight-detail-' + $(this).data('key'));
        openElement.toggle();
    });
    $('.chooseFlight').live('click',function(){
        var key1 = $('#searchId').data('searchid'),
            key2 = $(this).data('searchkey'),
            btn  = $(this);

        $.getJSON('/tour/basket/add/type/".FlightVoyage::TYPE."/key/'+key1+'/searchId/'+key2)
            .done(function(data){
                $.getJSON('/tour/basket/show')
                    .done(function(data) {
                        var tourOutput = $('#tour-output');
                        if (handlebarTour != undefined)
                            var html = handlebarTour(data);
                        if (tourOutput)
                            tourOutput.html(html);

                        console.log(data);
                        btn.removeClass('btn-info').removeClass('chooseFlight').addClass('btn-inverse').html('Добавлено');
                    })
                    .fail(function(data){
                        /*$('#tour-output').html(data);*/
                        btn.removeClass('btn-info').addClass('btn-danger').html('Ошибка!');
                    });
                $('#popupInfo').modal('hide');
            });
    });
", CClientScript::POS_READY); ?>