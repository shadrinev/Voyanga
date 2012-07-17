(function($){
    $(function(){
        $('html').on('click', '.addRoute', function (){
            var $this = $(this),
                counter = $this.data('counter'),
                template = $.trim($('#routeItemTemplate').html()),
                position = $('#routesArea'),
                frag;

            frag = template.replace( /{{i}}/ig, counter );
            position.append(frag);
            $('.datepicker').datepicker({'weekStart':1,'format':'dd.mm.yyyy','language':'ru'});
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
    });
})(window.jQuery)