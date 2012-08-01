<span id='tour-output'></span>
<?php $templateVariable = 'handlebarTour'; ?>
<?php $this->beginWidget('common.components.handlebars.HandlebarsWidget', array('id'=>'tour', 'compileVariable' => $templateVariable)) ?>
{{#each items}}
    {{#if isFlight}}
        <?php $this->renderPartial('_tour_flight'); ?>
    {{/if}}
    {{#if isHotel}}
        <?php $this->renderPartial('_tour_hotel'); ?>
    {{/if}}
{{/each}}
<div class="actions">
    <a href="#" class="deleteTourButton btn btn-danger">Очистить тур</a>
    <a href="/tour/constructor/create" class="btn btn-success">Конструктор</a>
</div>
<div class="modal hide" id="tourSaveModal">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h3>Введите название тура</h3>
    </div>
    <div class="modal-body">
        <form class="well form-search" id='saveTourForm'>
            <input type="text" class="input-xlarge" id='tourName'>
        </form>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn" data-dismiss="modal">Отменить</a>
        <a href="#" class="btn btn-primary" id='saveTourButton'>Сохранить тур</a>
    </div>
</div>
<?php $this->endWidget(); ?>
<?php Yii::app()->clientScript->registerScript('tour-basket', "
    Handlebars.registerHelper('humanTime', function(duration) {
        var hours = Math.floor(duration / 3600),
            min = Math.floor((duration - hours * 3600) / 60),
            sec = duration - 60 * min - 3600 * hours,
            result = (hours > 0 ) ? hours + ' ч. ' : '';
            result += (min > 0 ) ? min + ' мин. ' : '';
            result += (sec > 0) ? sec + ' сек.' : '';
            return result;
    });

    $.getJSON('/tour/basket/show')
        .done(function(data) {
            var html = {$templateVariable}(data);
            $('#tour-output').html(html);
        })
        .fail(function(data){
            $('#tour-output').html(data);
        });

    $('.detail-view').live('click', function() {
        var openElement = $('.detail-' + $(this).data('key'));
        console.log(openElement);
        openElement.toggle();
    });

    $('.delete').live('click', function() {
         $.getJSON('/tour/basket/delete/key/'+$(this).data('key'))
            .done(function(data){
                var html = {$templateVariable}(data);
                $('#tour-output').html(html);
            })
            .fail(function(data){
                $('#tour-output').html(data);
            });
    });

    $('#saveTour').live('click', function() {
        $('#tourSaveModal').modal('show');
        $('#tourName').focus();
    });

    $('#saveTourButton').live('click',function() {
        var tourName = $('#tourName').val();
        $.getJSON('/tour/basket/save/name/'+tourName, function(data){
            var outputElement = $('#tourSaveModal .modal-body');
            $('#tourSaveModal .modal-footer').hide();
            outputElement.hide();
            if (data.result)
                outputElement.html('<div class=\"alert alert-success\">Тур сохранён</div>');
            else
                outputElement.html('<div class=\"alert alert-error\">Произошла ошибка!</div>');
            outputElement.show();
        });
        return false;
    });

    $('.deleteTourButton').live('click', function(){
        $.getJSON('/tour/basket/clear', function(data){
            var html = {$templateVariable}(data);
            $('#tour-output').html(html);
        });
        return false;
    });
", CClientScript::POS_READY); ?>