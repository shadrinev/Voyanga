(function($){
    $(function(){
        $('html').on('click', '.addRoom', function (){
             var $this = $(this),
                counter = $this.data('counter'),
                template = $.trim($('.roomItemTemplate').html()),
                postion = $('.linksArea'),
                frag;

            frag = template.replace( /{{i}}/ig, counter );
            postion.append(frag);
            counter++;
            console.log(counter);
            $this.data('counter', counter);
            console.log($this.data('counter'));
        });
        $('html').on('click', '.deleteRoom', function (){
            var $this = $(this),
                $addLink = $this.parent().find('.addRoom'),
                counter = $addLink.data('counter'),
                $deleting = $('.'+$this.data('del'));
            console.log(counter);
            if (counter==1)
                return false;
            $deleting.remove();
            counter--;
            $addLink.data('counter', counter);
        });
    })
})(window.jQuery)