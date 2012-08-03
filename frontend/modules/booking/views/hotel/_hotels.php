<?php $this->beginWidget('common.components.handlebars.HandlebarsWidget', array('id'=>'hotels', 'compileVariable' => $variable)) ?>
<table class="table" width="100%">
    <thead>
    <tr>
        <th>Название отеля</th>
        <th>Категория отеля</th>
        <th>Рейтинг</th>
        <th>Цена</th>
        <th>Действие</th>
    </tr>
    </thead>
    <tbody>
    {{#each hotels}}
    <tr>
        <td>{{hotelName}}</td>
        <td>{{category}}</td>
        <td>{{rating}}</td>
        <td>{{rubPrice}} руб.</td>
        <td>
            <a class='btn btn-success' href="/booking/hotel/info/cacheId/<?php echo $cacheId?>/hotelId/{{hotelId}}">выбрать</a>
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
", CClientScript::POS_READY); ?>