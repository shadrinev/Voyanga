<?php $this->beginWidget('common.components.handlebars.HandlebarsWidget', array('id'=>'hotels', 'compileVariable' => $variable)) ?>
<table class="table" width="100%">
    <thead>
    <tr>
        <th>Название отеля</th>
        <th>Категория отеля</th>
        <th>Цена</th>
    </tr>
    </thead>
    <tbody>
    {{#each hotels}}
    <tr>
        <td>{{hotelName}}</td>
        <td>{{category}}</td>
        <td>{{rubPrice}} руб.</td>
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