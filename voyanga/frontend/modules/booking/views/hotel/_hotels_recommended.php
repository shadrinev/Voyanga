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
        <td><a class="btn" href="/booking/hotel/info/cacheId/<?php echo $cacheId?>/hotelId/{{hotelId}}">выбрать</a></td>
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
    var data = $.parseJSON('".$results."'),
        html = ".$variable."(data);
    $('#hotel-results').html(html);
", CClientScript::POS_READY); ?>