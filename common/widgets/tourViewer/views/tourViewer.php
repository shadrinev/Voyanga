<span id='tour-output<?php echo $suffix ?>'></span>
<?php $templateVariable = 'handlebarTour'.$suffix; ?>
<?php $this->beginWidget('common.components.handlebars.HandlebarsWidget', array('id'=>'tour'.$suffix, 'compileVariable' => $templateVariable)) ?>
{{#each items}}
    {{#if isFlight}}
        <?php $this->render('_tour_flight', array('pathToAirlineImg'=>$pathToAirlineImg)); ?>
    {{/if}}
    {{#if isHotel}}
        <?php $this->render('_tour_hotel'); ?>
    {{/if}}
{{/each}}
<?php $this->endWidget(); ?>
<?php Yii::app()->clientScript->registerScript('tour-basket-'.$suffix, "
    Handlebars.registerHelper('humanTime', function(duration) {
        var hours = Math.floor(duration / 3600),
            min = Math.floor((duration - hours * 3600) / 60),
            sec = duration - 60 * min - 3600 * hours,
            result = (hours > 0 ) ? hours + ' ч. ' : '';
            result += (min > 0 ) ? min + ' мин. ' : '';
            result += (sec > 0) ? sec + ' сек.' : '';
            return result;
    });

    $.getJSON('".$urlToBasket."/orderId/".$orderId."')
        .done(function(data) {
            var html = {$templateVariable}(data);
            $('#tour-output".$suffix."').html(html);
        })
        .fail(function(data){
            $('#tour-output".$suffix."').html(data);
        });

    $('.detail-view').live('click', function() {
        var openElement = $('.detail-' + $(this).data('key'));
        openElement.removeClass('hide');
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
", CClientScript::POS_READY); ?>