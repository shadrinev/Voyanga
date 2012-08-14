<?php $this->beginWidget('common.components.handlebars.HandlebarsWidget', array('id'=>'hotels-all', 'compileVariable' => $variable)) ?>
<table class="table" width="100%">
    <thead>
    <tr>
        <th>Цена</th>
        <th>Размер номера</th>
        <th>Тип номера</th>
        <th>Количество в наличии</th>
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
            {{#each rooms}}
            {{type}}
            {{/each}}
        </td>
        <td>{{countNumbers}}</td>
    </tr>
    {{/each}}
    </tbody>
</table>
<?php $this->endWidget(); ?>
<span id='hotel-results-all'></span>
<?php Yii::app()->clientScript->registerScript('hotel-result-all', "
    var data = ".$results.";
    html = ".$variable."(data);
    $('#hotel-results-all').html(html);
", CClientScript::POS_READY); ?>