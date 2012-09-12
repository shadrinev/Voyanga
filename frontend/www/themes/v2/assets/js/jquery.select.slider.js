/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 29.08.12
 * Time: 16:33
 * To change this template use File | Settings | File Templates.
 */
(function ($) {
    $.selectSlider = {
        init: function (obj,options) {
            var _this = $(obj);
            var mainClass = _this.attr('class');
            _this.data('options',options);
            _this.hide();
            var aOptions = Array();
            var selectDiv = $(
                '<div class="' + mainClass + ' jsslidecheckbox"><div class="slidecheckbox">' +
                    '<ul class="selectList" />' +
                    '<div class="switch"><i class="left"></i><i class="right"></i></div>'+
                    '<i class="left"></i><i class="right"></i>'+
                '</div></div>'
            );
            selectDiv.data('select',_this);
            _this.data('selectDiv',selectDiv);
            var elementWidth = 100/_this.find('option').length;
            selectDiv.data('elementWidth',elementWidth);
            selectDiv.find('.switch').css('width',elementWidth+'%');
            //console.log(elementWidth);
            _this.find('option').each(function (index) {
                var listElement = $('<li><a href="#" onclick="return false">' + $(this).text() + '</a></li>');
                listElement.data('original_option',$(this));
                listElement.data('ind',index);
                listElement.data('option-value', $(this).val());
                listElement.css('width',selectDiv.data('elementWidth')+'%');
                listElement.click(function () {
                    //listElement.css('width',selectDiv.data('elementWidth')+'%');
                    var oldVal = _this.val();
                    if(oldVal != listElement.data('option-value')){
                        _this.val(listElement.data('option-value'));
                        selectDiv.find('.switch').animate({'left':selectDiv.data('elementWidth')*listElement.data('ind') + '%'});
                        selectDiv.data('active').find('a').css('text-shadow','none');
                        selectDiv.data('active').find('a').animate({
                                'color': '#2e333b'//,
                                //'text-shadow': '0px 1px 0px #FFF'
                            },
                            function() {
                                //console.log('old');
                                //console.log(this);
                                $(this).css('text-shadow', '0px 1px 0px #FFF');
                            }
                        );
                        selectDiv.data('active').removeClass('active');
                        listElement.find('a').css('text-shadow','none');
                        listElement.find('a').animate({
                                'color': 'white'//,
                                //'text-shadow': '0px 1px 0px #0b5b88'
                            },
                            function() {
                                //console.log('new');
                                //console.log(this);
                                $(this).css('text-shadow', '0px 1px 0px #0b5b88');
                                $(this).parent().addClass('active');
                                _this.change();
                            }
                        );
                        //console.log(listElement);
                        selectDiv.data('active',listElement);

                    }
                });
                /*listElement.bind('mouseenter',function () {
                    $(this).addClass('hover');
                });
                listElement.bind('mouseleave',function () {
                    $(this).removeClass('hover');
                });*/
                selectDiv.find('.selectList').append(listElement);

                if(listElement.data('original_option').filter(':selected').length > 0) {
                    //$('.'+ mainClass + '.currentItem', replacement).text($(this).text());
                    listElement.addClass('active');
                    selectDiv.data('active',listElement);
                    selectDiv.find('.switch').css('left',selectDiv.data('elementWidth')*listElement.data('ind') + '%');
                }
            });
            _this.after(selectDiv);
            //console.log(options);

        },
        reSize: function (obj) {
            $(this).show();
            var thisWidth = $(this)[0].clientWidth;
            var thisHeight = $(this)[0].clientHeight;
        }
    }
    $.fn.selectSlider = function (action,options) {
        if(typeof action == 'string'){

        }else{
            options = action;
            var defaultOptions = {kg:'am'};
            options = $.extend({},defaultOptions,options);
            $(this).each(function () {
                $.selectSlider.init(this,options);
            });
        }

    }
})(jQuery);