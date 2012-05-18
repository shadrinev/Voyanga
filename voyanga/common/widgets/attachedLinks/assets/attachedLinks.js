(function($){
    $(function(){
        $('html').on('click', '.addLink', function (){
            var $this = $(this),
                counter = $this.data('counter'),
                template = $.trim($('#linkItemTemplate').html()),
                postion = $('#linksArea'),
                frag;

            frag = template.replace( /{{i}}/ig, counter );
            postion.append(frag);
            counter++;
            $this.data('counter', counter);
        });
        $('html').on('click', '.deleteLink', function (){
            var $this = $(this),
                $addLink = $('.addLink'),
                counter = $addLink.data('counter'),
                $deleting = $('#'+$this.data('del'));

            $deleting.remove();
            counter--;
            $addLink.data('counter', counter);
        });
    })
})(window.jQuery)