(function($){
    $(function(){
        $('html').on('click', '.addRoute', function (){
            var $this = $(this),
                counter = $this.data('counter'),
                template = $.trim($('#routeItemTemplate').html()),
                position = $('#routesArea'),
                frag;

            frag = template.replace( /{{i}}/ig, counter );
            var tmp = position.append(frag);
            $('.datepicker', tmp).datepicker({'weekStart':1,'format':'dd.mm.yyyy','language':'ru'});
            $('.fromField').typeahead({
                'items':10,
                'ajax':{
                    'url':'/ajax/cityForFlight',
                    'timeout':5,
                    'displayField':'label',
                    'triggerLength':2,
                    'method':'get',
                    'loadingClass':'loading-circle'
                },
                'onselect': function(res){
                    this.$element.siblings('input.departureCity').val(res.id)
                },
                'matcher': function(){return true}
            });
            $('.toField').typeahead({
                'items':10,
                'ajax':{
                    'url':'/ajax/cityForFlight',
                    'timeout':5,
                    'displayField':'label',
                    'triggerLength':2,
                    'method':'get',
                    'loadingClass':'loading-circle'
                },
                'onselect': function(res){
                    this.$element.siblings('input.arrivalCity').val(res.id)
                },
                'matcher': function(){return true}
            });
            counter++;
            if (counter>=1)
            {
                $('input.isRoundTrip').removeAttr('checked');
                $('input.isRoundTrip').attr('disabled', 'disabled');
                $('span.backdate').hide();
            }
            $this.data('counter', counter);
        });
        $('html').on('click', '.deleteRoute', function (){
            var $this = $(this),
                $addRoute = $('.addRoute'),
                counter = $addRoute.data('counter'),
                $deleting = $('#'+$this.data('del'));

            if (counter<=1)
                return;
            $deleting.remove();
            counter--;
            if (counter==1)
            {
                $('input.isRoundTrip').removeAttr('disabled');
            }
            $addRoute.data('counter', counter);
        });
        $('.datepicker').datepicker({'weekStart':1,'format':'dd.mm.yyyy','language':'ru'}).on('changeDate', function(ev){$(this).datepicker("hide")});
        $('.fromField').typeahead({
            'items':10,
            'ajax':{
                'url':'/ajax/cityForFlight',
                'timeout':5,
                'displayField':'label',
                'triggerLength':2,
                'method':'get',
                'loadingClass':'loading-circle'
            },
            'onselect': function(res){
                this.$element.siblings('input.departureCity').val(res.id)
            },
            'matcher': function(){return true}
        });
        $('.toField').typeahead({
            'items':10,
            'ajax':{
                'url':'/ajax/cityForFlight',
                'timeout':5,
                'displayField':'label',
                'triggerLength':2,
                'method':'get',
                'loadingClass':'loading-circle'
            },
            'onselect': function(res){
                this.$element.siblings('input.arrivalCity').val(res.id)
            },
            'matcher': function(){return true}
        });
        $('.isRoundTrip').each(function (i, el){
            var roundTrip = $(el).parent().siblings('span.backdate');
            $(el).attr('checked') !='checked' ? roundTrip.hide() : roundTrip.show();
        });
        $('input.isRoundTrip').change(function(){
            var roundTrip = $(this).parent().siblings('span.backdate');
            $(this).attr('checked') != 'checked' ? roundTrip.hide() : roundTrip.show();
        });
    });
})(window.jQuery)