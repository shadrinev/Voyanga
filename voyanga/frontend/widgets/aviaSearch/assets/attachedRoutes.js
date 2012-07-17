(function($){
    $(function(){
        $('html').on('click', '.addRoute', function (){
            var $this = $(this),
                counter = $this.data('counter'),
                template = $.trim($('#routeItemTemplate').html()),
                postion = $('#routesArea'),
                frag;

            frag = template.replace( /{{i}}/ig, counter );
            postion.append(frag);
            counter++;
            $this.data('counter', counter);
        });
        $('html').on('click', '.deleteLink', function (){
            var $this = $(this),
                $addRoute = $('.addRoute'),
                counter = $addRoute.data('counter'),
                $deleting = $('#'+$this.data('del'));

            $deleting.remove();
            counter--;
            $addRoute.data('counter', counter);
        });
    })
})(window.jQuery)