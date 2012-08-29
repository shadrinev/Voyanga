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
            var aOptions = Array();
            /*var replacement = $(
                '<div class="' + mainClass + '">' +
                    '<div class="' + mainClass + ' selectList" />' +
                    '<span class="' + mainClass + ' currentItem" />' +
                    '<span class="' + mainClass + ' moreButton" />' +
                '</div>'
            );
            replacement.data('select',_this);*/
            //_this.data('selectDiv',replacement);
            console.log(options);

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