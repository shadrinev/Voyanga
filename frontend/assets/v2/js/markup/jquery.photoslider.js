/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 09.10.12
 * Time: 11:33
 * To change this template use File | Settings | File Templates.
 */
(function ($) {
    $.photoSlider = {
        init: function(obj,options) {
            var _this = $(obj);
            var mainClass = _this.attr('class');
            _this.data('options',options);
            var self = this;
            self.obj = _this;
            self.unloadedImages = 0;
            self.totalImages = 0;
            self.leftNavi = $('<div class="left-navi"></div>');
            self.leftNavi.hide();

            self.rightNavi = $('<div class="right-navi"></div>');
            self.rightNavi.hide();
            self.obj.after(self.rightNavi);
            self.obj.after(self.leftNavi);
            self.leftNavi.click(function(){self.leftClick();});
            self.rightNavi.click(function(){self.rightClick();});
            self.needSetOnresize = true;

            console.log('initing photoslider!!!!');
            //console.log(_this);
            //console.log(self);
            _this.find('img').each(function(ind,el){
                //console.log(el,ind);
                self.unloadedImages++;
                self.totalImages++;
                var img  = new Image;
                var src = $(el).attr('src');
                $(img).bind('load error',function(){
                    console.log('image is loaded',this);
                    self.unloadedImages--;
                    if(self.unloadedImages <= 0 ){
                        self.allImagesLoaded();
                    }
                });
                img.src = src;
            });
        },
        testLimits: function(){
            var self = this;
            var rightVisible = self.rightNavi.css('display') != 'none';
            if( ((self.fullWidth - self.leftPosition) > self.visibleWidth) && !rightVisible){
                self.rightNavi.fadeIn();
            }else if( ((self.fullWidth - self.leftPosition) <= self.visibleWidth) && rightVisible ){
                self.rightNavi.fadeOut();
            }
            var leftVisible = self.leftNavi.css('display') != 'none';
            if( (self.indexPosition > 0) && !leftVisible){
                self.leftNavi.fadeIn();
            }else if( (self.indexPosition <= 0) && leftVisible ){
                self.leftNavi.fadeOut();
            }
        },
        allImagesLoaded: function(){
            var self = this;
            console.log('allImages loaded');

            self.fullWidth = 0;
            var pos = self.obj.find('li:last').position();
            var lastWidth = self.obj.find('li:last').width();
            self.fullWidth = pos.left + lastWidth;
            self.indexPosition = 0;
            self.leftPosition = 0;
            self.transitionProcess = false;
            self.visibleWidth = self.obj.parent().width();
            self.testLimits();
            if(self.needSetOnresize){
                $(window).resize(function(){self.onresize()});
                self.needSetOnresize = false;
            }

            console.log(self);
        },
        transit: function(){
            var self = this;
            var pos = self.obj.find('li:eq('+self.indexPosition+')').position();
            //console.log(pos);
            if( self.indexPosition > 0 && ((self.fullWidth - pos.left) < self.visibleWidth) ){
                pos.left = self.fullWidth - self.visibleWidth;
            }
            self.leftPosition = pos.left;
            self.obj.animate({'left' : (-pos.left) +'px'}, 200, function() {
                self.transitionProcess = false;
                self.testLimits();
            });
        },
        rightClick: function(){
            var self = this;
            if(!self.fullWidth){
                self.allImagesLoaded();
            }

            if( !self.transitionProcess && ((self.fullWidth - self.leftPosition) > self.visibleWidth) && (self.indexPosition < self.totalImages) ){
                self.transitionProcess = true;
                self.indexPosition++;
                self.transit();
            }
        },
        leftClick: function(){
            var self = this;

            if( !self.transitionProcess && (self.indexPosition > 0) ){
                self.transitionProcess = true;
                self.indexPosition--;
                self.transit();
            }
        },
        onresize: function(){
            var self = this;
            var alignRight = (self.fullWidth - self.leftPosition) == self.visibleWidth;

            self.visibleWidth = self.obj.parent().width();

            if(alignRight){
                self.leftPosition = self.fullWidth - self.visibleWidth;
                self.obj.css({'left': (-self.leftPosition) + 'px'});
            }
        }
    }
    $.fn.photoSlider = function (action,options) {
        if(typeof action == 'string'){
            if(action == 'reinit'){
                $.photoSlider.allImagesLoaded();
            }
        }else{
            options = action;
            var defaultOptions = {kg:'am'};
            options = $.extend({},defaultOptions,options);

            $(this).each(function () {
                var self = this;
                window.setTimeout(function(){
                    $.photoSlider.init(self,options);
                }, 100);
            });
        }
    }
})(jQuery);