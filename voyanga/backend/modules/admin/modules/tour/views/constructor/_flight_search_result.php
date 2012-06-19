<?php $this->beginWidget('common.components.handlebars.HandlebarsWidget', array('id'=>'flight-search', 'compileVariable' => $variable)) ?>
<div class="entry">
    <h1>{{title}}</h1>

    <table class="table table-striped" width="100%">
        <thead>
            <tr>
                <th>Авиакомпания</th>
                <th>Цена</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        {{#each flightVoyages}}
        <tr>
            <td><img src='http://frontend.oleg.voyanga/themes/classic/images/airlines/{{valCompany}}.png'></td>
            <td>{{price}} руб.</td></li>
            <td><a class='btn btn-mini chooseFlight' data-searchkey='{{flightKey}}'>Выбрать</a></td>
        </tr>
        {{/each}}
        </tbody>
    </table>
    <span id='searchId' data-searchId='{{searchId}}'></span>
</div>
<?php $this->endWidget(); ?>