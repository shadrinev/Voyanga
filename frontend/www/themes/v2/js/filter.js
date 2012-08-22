filterSlideKnob = {
    options: {
        animate: false,
        slider: false
    },

    _create: function() {
        var self = this, o = this.options;
        this._mouseInit();
    },
    destroy: function(){
        this._mouseDestroy();
    },
    _mouseStart: function(e){
        this.options.startEvent(e,this.element);
    },
    _mouseDrag: function(e){
        this.options.dragEvent(e,this.element);
    },
    _mouseStop: function( event ) {
        this.options.endEvent(event,this.element);
        return false;
    }
};
$.widget("ui.filterSlideKnob",$.ui.mouse, filterSlideKnob);


function filterSlider() {
	$('.slide-filter').filterSlideKnob({
		startEvent: function (e,obj){obj.data('xStart', e.pageX); console.log(e.pageX);},	
		endEvent: function (e,obj){console.log('hi');},
		dragEvent: function (e,obj){
			var leftPos = obj.css('left');
			leftPos = leftPos.slice(0, -2);
			limitX = obj.parent().width() - obj.width();
			
			console.log(leftPos);
		}
	});
}
$(window).load(filterSlider);