<?php $this->beginWidget('common.components.handlebars.HandlebarsWidget', array('id'=>'hotels', 'compileVariable' => $variable)) ?>
<table class="table" width="100%">
    <thead>
    <tr>
        <th>Цена</th>
        <th>Номера</th>
        <th>Действие</th>
    </tr>
    </thead>
    <tbody>
    {{#each hotels}}
    <tr>
        <td>{{rubPrice}} руб.</td>
        <td>
            {{#each rooms}}
                {{size}}
            {{/each}}
        </td>
        <td>
            <a class="btn" href="/booking/hotel/buy/key/<?php echo $cacheId?>_{{searchId}}_{{resultId}}">выбрать</a>
            <a class='btn btn-info btn-mini chooseRoom' data-cacheid="<?php echo $cacheId?>" data-hotelid="{{hotelId}}" data-roomid="{{resultId}}">добавить в тур</a>
        </td>
    </tr>
    <tr>
        <td colspan="3" style="padding-left: 25px;">
            <table class="table" width="100%">
                <tbody>
                {{#each rooms}}
                <tr>
                    <td>{{size}}</td>
                    <td>{{type}}</td>
                    <td>{{view}}</td>
                    <td>{{meal}}</td>
                    <td>{{mealBreakfast}}</td>
                </tbody>
                {{/each}}
            </table>
        </td>
    </tr>
    {{/each}}
    </tbody>
</table>
<?php $this->endWidget(); ?>
<span id='hotel-results'></span>
<?php Yii::app()->clientScript->registerScript('hotel-result', "
    var data = ".$results.";
    html = ".$variable."(data);
    $('#hotel-results').html(html);
    $('.chooseRoom').on('click',function(){
        var key1 = $(this).data('cacheid'),
            key2 = $(this).data('hotelid'),
            key3 = $(this).data('roomid'),
            btn = $(this);
        $.getJSON('/tour/basket/add/type/".Hotel::TYPE."/key/'+key1+'/searchId/'+key2+'/searchId2/'+key3)
            .done(function(data) {
                $.getJSON('/tour/basket/show')
                    .done(function(data) {
                        var html = handlebarTour(data);
                        $('#tour-output').html(html);
                        console.log(data);
                        btn.removeClass('btn-info').removeClass('chooseRoom').addClass('btn-inverse').html('Добавлено');
                    })
                    .fail(function(data){
                        /*$('#tour-output').html(data);*/
                        btn.removeClass('btn-info').addClass('btn-danger').html('Ошибка!');
                    });
                $('#popupInfo').modal('hide');
            });
    });
", CClientScript::POS_READY); ?>