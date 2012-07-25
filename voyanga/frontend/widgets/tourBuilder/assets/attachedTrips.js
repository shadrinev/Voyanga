(function($){
    $(function(){
        $('html').on('click', '.addTrip', function (){
            var $this = $(this),
                counter = $this.data('counter'),
                template = $.trim($('#tripItemTemplate').html()),
                position = $('#tripsArea'),
                frag;

            frag = template.replace( /{{i}}/ig, counter );
            var tmp = position.append(frag);
            $('.tripFromField', tmp).typeahead({
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
                    this.$element.siblings('input.tripCity').val(res.id)
                },
                'matcher': function(){return true}
            });
            //!!changing counter here
            var $prev = $('#trip'+(counter-1)),
                $next = $('#trip'+(counter));
            console.log($prev, $next);
            counter++
            if (counter>=1)
            {
                $('.startDate', $next).val($('.endDate', $prev).val());
            }
            $('.datepicker', tmp).datepicker({'weekStart':1,'format':'dd.mm.yyyy','language':'ru'});
            $this.data('counter', counter);
        });

        $('html').on('click', '.deletetrip', function (){
            var $this = $(this),
                $addtrip = $('.addTrip'),
                counter = $addtrip.data('counter'),
                $deleting = $('#'+$this.data('del'));

            if (counter<=1)
                return;
            $deleting.remove();
            counter--;
            if (counter==1)
            {
                $('input.isRoundTrip').removeAttr('disabled');
            }
            $addtrip.data('counter', counter);
        });
        $('.datepicker', $('.tourBuilder')).datepicker({'weekStart':1,'format':'dd.mm.yyyy','language':'ru'}).on('changeDate', function(ev){$(this).datepicker("hide")});
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
    });
})(window.jQuery)