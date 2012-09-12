(function($){
    $(function(){
        $('html').on('click', '.addItem', function (){
            var $this = $(this),
                counter = $this.data('counter'),
                template = $.trim($('#itemTemplate').html()),
                position = $('#itemsArea'),
                frag;

            frag = template.replace( /{{i}}/ig, counter );
            var tmp = position.append(frag);
            $('.cityId', tmp).typeahead({
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
                    this.$element.siblings('input.city').val(res.id)
                },
                'matcher': function(){return true}
            });
            $this.data('counter', ++counter);
        });

        $('html').on('click', '.deleteItem', function (){
            var $this = $(this),
                $additem = $('.addItem'),
                counter = $additem.data('counter'),
                $deleting = $('#'+$this.data('del'));

            if (counter<=1)
                return;
            $deleting.remove();
            $additem.data('counter', --counter);
        });
    });
})(window.jQuery)