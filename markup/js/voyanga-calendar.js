/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 25.07.12
 * Time: 13:40
 * To change this template use File | Settings | File Templates.
 */
VoyangaCalendarKnob = {
    options: {
        animate: false,
        slider: false
    },

    _create: function() {
        var self = this,
            o = this.options;
        //console.log(this);
        this._mouseInit();

    },
    //_init: function(){
    //	this._mouseInit(); // начинаем обработку поведения мыши
    //},
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
$.widget("ui.VoyangaCalendarKnob",$.ui.mouse, VoyangaCalendarKnob);

VoyangaCalendar = new Object();
VoyangaCalendar.jObj = null;
VoyangaCalendar.weekDays = new Array('Пн','Вт','Ср','Чт','Пт','Сб','Вс');
VoyangaCalendar.monthNames = new Array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь');
VoyangaCalendar.dayCellWidth = 180;
VoyangaCalendar.slider = new Object();
VoyangaCalendar.slider.monthArray = new Array();
VoyangaCalendar.slider.totalLines = 1;
VoyangaCalendar.slider.linesWidth = 5;
VoyangaCalendar.slider.knobWidth = 1;
VoyangaCalendar.slider.knobPos = 0;
VoyangaCalendar.slider.width = 0;
VoyangaCalendar.slider.knobSlideAction = false;
VoyangaCalendar.slider.animateScrollAction = false;
VoyangaCalendar.slider.onresize = function(){
    VoyangaCalendar.slider.width = VoyangaCalendar.jObj.find('.monthLineVoyanga').width();
    //console.log('onresize');
};
VoyangaCalendar.slider.startEvent = function(e,obj){
    //console.log(obj);
    if(VoyangaCalendar.slider.knobSlideAction){
        obj.data('xStart', e.pageX);
        obj.data('posStart', VoyangaCalendar.slider.knobPos);
    }
};
VoyangaCalendar.slider.endEvent = function(e,obj){
    if(VoyangaCalendar.slider.knobSlideAction){
        VoyangaCalendar.slider.knobSlideAction = false;
    }
};
VoyangaCalendar.slider.dragEvent = function(e,obj){
    if(VoyangaCalendar.slider.knobSlideAction){
        var xDelta = e.pageX - obj.data('xStart');
        var posDelta = Math.round((xDelta / VoyangaCalendar.slider.width)*10000)/100;
        VoyangaCalendar.slider.knobPos = obj.data('posStart') + posDelta;
        if(VoyangaCalendar.slider.knobPos < 0) VoyangaCalendar.slider.knobPos = 0;
        if(VoyangaCalendar.slider.knobPos > (100 - VoyangaCalendar.slider.knobWidth)) VoyangaCalendar.slider.knobPos = (100 - VoyangaCalendar.slider.knobWidth);
        $('#voyangaCalendarKnob').css('left',VoyangaCalendar.slider.knobPos + '%');
        var scrollHeight = VoyangaCalendar.jObj.find('.calendarGridVoyanga').prop('scrollHeight');
        var scrollTop = Math.round(scrollHeight*(VoyangaCalendar.slider.knobPos / 100));
        VoyangaCalendar.jObj.find('.calendarGridVoyanga').scrollTop(scrollTop);
    }
};
VoyangaCalendar.slider.mouseDown = function(e){
    var xLeft = Math.round($('#voyangaCalendarKnob').offset().left);
    var xRight = xLeft + Math.round($('#voyangaCalendarKnob').width());
    if((e.pageX >= xLeft) && (e.pageX <= xRight)){
        VoyangaCalendar.slider.knobSlideAction = true;
        if(VoyangaCalendar.slider.animateScrollAction){
            $('#voyangaCalendarKnob').stop(true);
            VoyangaCalendar.slider.animateScrollAction = false;
        }
    }
};
VoyangaCalendar.slider.mouseUp = function(e){
    VoyangaCalendar.slider.knobSlideAction = false;
};
VoyangaCalendar.slider.animateStep = function(now, fx){
    var data = fx.elem.id + ' ' + fx.prop + ': ' + now;
    if(fx.unit == 'px'){
        var posLeft = Math.round((now / VoyangaCalendar.slider.width)*10000)/100;
    }else{
        var posLeft = now;
    }
    VoyangaCalendar.slider.knobPos = posLeft;
    var scrollHeight = VoyangaCalendar.jObj.find('.calendarGridVoyanga').prop('scrollHeight');
    var scrollTop = Math.round(scrollHeight*(VoyangaCalendar.slider.knobPos / 100));
    VoyangaCalendar.jObj.find('.calendarGridVoyanga').scrollTop(scrollTop);
}
VoyangaCalendar.slider.monthMouseUp = function(e){
    if(!VoyangaCalendar.slider.knobSlideAction)
    {
        VoyangaCalendar.slider.animateScrollAction = true;
        $('#voyangaCalendarKnob').stop(true);
        var newPos = $(this).parent().css('left');
        if(newPos.indexOf('px') != -1){
            newPos = newPos.substr(0, newPos.length -2);
            newPos = Math.round((newPos / VoyangaCalendar.slider.width)*10000)/100;
            newPos = newPos + '%';
        }
        //var newPos = $(this).css('left');
        $('#voyangaCalendarKnob').animate({
                left: [newPos, 'easeOutCubic']
            },
            {
                duration: 1500,
                step: VoyangaCalendar.slider.animateStep,
                easing: 'easeOutCubic',
                complete: function(){VoyangaCalendar.slider.animateScrollAction = false;}
        });
    }
};

VoyangaCalendar.slider.scrollEvent = function(e){
    if(!VoyangaCalendar.slider.animateScrollAction){
        var scrollHeight = VoyangaCalendar.jObj.find('.calendarGridVoyanga').prop('scrollHeight');
        VoyangaCalendar.slider.knobPos = Math.round((VoyangaCalendar.jObj.find('.calendarGridVoyanga').scrollTop() / scrollHeight)*1000)/10;
        $('#voyangaCalendarKnob').css('left',VoyangaCalendar.slider.knobPos + '%');
    }
};

/*VoyangaCalendar.slider.mousewheelEvent = function(e){
    console.log(e);
    var rolled = 0;
    var event = e.originalEvent;
    if ('wheelDelta' in event) {
        rolled = event.wheelDelta;
    }
    else {  // Firefox
        // The measurement units of the detail and wheelDelta properties are different.
        rolled = -40 * event.detail;
    }
    //console.log(rolled);

    //var scrollHeight = VoyangaCalendar.jObj.find('.calendarGrid').prop('scrollHeight');
    console.log(VoyangaCalendar.jObj.find('.calendarGrid').scrollTop());

    var scrollTop = VoyangaCalendar.jObj.find('.calendarGrid').scrollTop() - rolled;
    VoyangaCalendar.jObj.find('.calendarGrid').scrollTop(scrollTop);
    return false;
};*/
VoyangaCalendar.slider.init = function(){
    console.log(VoyangaCalendar.slider.monthArray);
    for(var i in VoyangaCalendar.slider.monthArray){
        var leftPercent = VoyangaCalendar.slider.monthArray[i].line / (VoyangaCalendar.slider.totalLines - VoyangaCalendar.slider.linesWidth);
        leftPercent =  Math.round((1 - (VoyangaCalendar.slider.linesWidth / VoyangaCalendar.slider.totalLines) )*leftPercent*1000 )/10;
        if(i < (VoyangaCalendar.slider.monthArray.length - 1) ){
            var k=parseInt(i)+1;

            var widthPercent = (VoyangaCalendar.slider.monthArray[k].line - VoyangaCalendar.slider.monthArray[i].line) / VoyangaCalendar.slider.totalLines;
            //var widthPercent = 4/(VoyangaCalendar.slider.totalLines);
        }else{
            var widthPercent = (VoyangaCalendar.slider.totalLines - VoyangaCalendar.slider.monthArray[i].line) / VoyangaCalendar.slider.totalLines;
        }
        widthPercent = Math.round(widthPercent*1000)/10;

        var newHtml = '<div class="monthNameVoyanga" style="left: '+leftPercent+'%; width: '+widthPercent+'%"><div class="monthWrapper">'+VoyangaCalendar.slider.monthArray[i].name+'</div></div>';
        VoyangaCalendar.jObj.find('.monthLineVoyanga').append(newHtml);
    }
    VoyangaCalendar.slider.knobWidth = Math.round((VoyangaCalendar.slider.linesWidth / VoyangaCalendar.slider.totalLines)*10000)/100;
    $('#voyangaCalendarKnob').css('width',VoyangaCalendar.slider.knobWidth + '%');
    //VoyangaCalendar.slider.width = VoyangaCalendar.jObj.find('.monthLineVoyanga').width();
    $(window).on('resize',VoyangaCalendar.slider.onresize);
    $(window).load(VoyangaCalendar.slider.onresize);

    VoyangaCalendar.jObj.find('.calendarGridVoyanga').on('scroll',VoyangaCalendar.slider.scrollEvent);
    //VoyangaCalendar.jObj.find('.calendarGrid').on('mousewheel',VoyangaCalendar.slider.mousewheelEvent);
    //VoyangaCalendar.jObj.find('.calendarGrid').on('DOMMouseScroll',VoyangaCalendar.slider.mousewheelEvent);
    VoyangaCalendar.jObj.find('.monthLineVoyanga').mousedown(VoyangaCalendar.slider.mouseDown);
    VoyangaCalendar.jObj.find('.monthLineVoyanga').mouseup(VoyangaCalendar.slider.mouseUp);
    //VoyangaCalendar.jObj.find('.monthLineVoyanga .monthNameVoyanga').mouseup(VoyangaCalendar.slider.monthMouseUp);
    VoyangaCalendar.jObj.find('.monthLineVoyanga .monthNameVoyanga .monthWrapper').mouseup(VoyangaCalendar.slider.monthMouseUp);
    VoyangaCalendar.jObj.find('.monthLineVoyanga').VoyangaCalendarKnob({
        startEvent: function (e,obj){VoyangaCalendar.slider.startEvent(e,obj);},
        endEvent: function (e,obj){VoyangaCalendar.slider.endEvent(e,obj);},
        dragEvent: function (e,obj){VoyangaCalendar.slider.dragEvent(e,obj);}
    });
};


VoyangaCalendar.getDay = function (dateObj){
    var dayNum = dateObj.getDay();
    if(dayNum == 0){
        dayNum = 6;
    }
    else{
        dayNum = dayNum - 1;
    }
    return dayNum;
}
VoyangaCalendar.generateGrid = function (){
    var firstDay = new Date();


    var startMonth = firstDay.getMonth();
    var tmpDate = new Date(firstDay.toDateString());
    tmpDate.setDate(1);
    var weekDay = VoyangaCalendar.getDay(tmpDate);
    //console.log(weekDay);
    var startDate = firstDay.getDate();
    var startYear = firstDay.getFullYear();
    //console.log(tmpDate);
    tmpDate.setDate(-VoyangaCalendar.getDay(tmpDate) + 1);
    //tmpDate.setDate(0);
    //console.log(tmpDate);
    var needStop = false;
    var lineNumber = 0;
    while(!needStop)
    {
        var newHtml = '<div class="calendarLineVoyanga" id="weekNum-'+lineNumber+'" data-weeknum="'+lineNumber+'">';
        for(var i=0;i<7;i++){
            var label = '<div class="dayLabel'+((i>=5 && i<7) ? ' weekEnd' : '')+'">'+tmpDate.getDate()+'</div>';

            
            if(tmpDate.getDate() == 1){
                label = label + ' <div class="monthLabel">' + VoyangaCalendar.monthNames[tmpDate.getMonth()] +'</div>';
                var monthObject = new Object();
                monthObject.line = lineNumber;
                monthObject.name = VoyangaCalendar.monthNames[tmpDate.getMonth()];
                VoyangaCalendar.slider.monthArray.push(monthObject);
            }
            var dateLabel = tmpDate.getFullYear()+'-'+(tmpDate.getMonth()+1)+'-'+tmpDate.getDate();
            newHtml = newHtml + '<div class="dayCellVoyanga" id="dayCell-'+dateLabel+'"><div class="innerDayCellVoyanga">'+label+'</div></div>';
            tmpDate.setDate(tmpDate.getDate()+1);
        }
        newHtml = newHtml + '</div>';
        VoyangaCalendar.jObj.find('.calendarDIVVoyanga').append(newHtml);
        if(tmpDate.getFullYear() > startYear){
            if(tmpDate.getMonth() >= startMonth ){
                needStop = true;
            }
        }
        //if(lineNumber > 4){
            //needStop = true;
        //}
        lineNumber++;
    }
    VoyangaCalendar.slider.monthArray.pop();
    VoyangaCalendar.slider.totalLines = lineNumber;
    //console.log(VoyangaCalendar.slider.totalLines);
}


VoyangaCalendar.init = function (){

    VoyangaCalendar.jObj = $('#voyanga-calendar');

    //console.log(VoyangaCalendar.calendarEvents);
    //return true;
    VoyangaCalendar.generateGrid();
    //return true;
    VoyangaCalendar.slider.init();

}
$(document).ready(function(){
    VoyangaCalendar.init();
});