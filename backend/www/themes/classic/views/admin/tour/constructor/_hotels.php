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
            <a class='btn btn-success choose' href="#{{hotelId}}" data-resultid="{{resultId}}">выбрать</a>
        </td>
    </tr>
    {{/each}}
    </tbody>
</table>
<?php $this->endWidget(); ?>