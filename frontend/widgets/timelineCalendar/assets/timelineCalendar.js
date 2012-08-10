/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 25.07.12
 * Time: 13:40
 * To change this template use File | Settings | File Templates.
 */
timelineCalendarKnob = {
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
$.widget("ui.timelineCalendarKnob",$.ui.mouse, timelineCalendarKnob);

TimelineCalendar = new Object();
TimelineCalendar.jObj = null;
TimelineCalendar.weekDays = new Array('Пн','Вт','Ср','Чт','Пт','Сб','Вс');
TimelineCalendar.monthNames = new Array('янв','фев','мар','апр','май','июн','июл','авг','сен','окт','ноя','дек');
TimelineCalendar.dayCellWidth = 110;
TimelineCalendar.slider = new Object();
TimelineCalendar.slider.monthArray = new Array();
TimelineCalendar.slider.totalLines = 1;
TimelineCalendar.slider.knobWidth = 1;
TimelineCalendar.slider.knobPos = 0;
TimelineCalendar.slider.width = 0;
TimelineCalendar.slider.knobSlideAction = false;
TimelineCalendar.slider.animateScrollAction = false;
TimelineCalendar.slider.startEvent = function(e,obj){
    console.log(obj);
    if(TimelineCalendar.slider.knobSlideAction){
        obj.data('xStart', e.pageX);
        obj.data('posStart', TimelineCalendar.slider.knobPos);
    }
};
TimelineCalendar.slider.endEvent = function(e,obj){
    if(TimelineCalendar.slider.knobSlideAction){
        TimelineCalendar.slider.knobSlideAction = false;
    }
};
TimelineCalendar.slider.dragEvent = function(e,obj){
    if(TimelineCalendar.slider.knobSlideAction){
        var xDelta = e.pageX - obj.data('xStart');
        var posDelta = Math.round((xDelta / TimelineCalendar.slider.width)*10000)/100;
        TimelineCalendar.slider.knobPos = obj.data('posStart') + posDelta;
        if(TimelineCalendar.slider.knobPos < 0) TimelineCalendar.slider.knobPos = 0;
        if(TimelineCalendar.slider.knobPos > (100 - TimelineCalendar.slider.knobWidth)) TimelineCalendar.slider.knobPos = (100 - TimelineCalendar.slider.knobWidth);
        $('#timelineCalendarKnob').css('left',TimelineCalendar.slider.knobPos + '%');
        var scrollHeight = TimelineCalendar.jObj.find('.calendarGrid').prop('scrollHeight');
        var scrollTop = Math.round(scrollHeight*(TimelineCalendar.slider.knobPos / 100));
        TimelineCalendar.jObj.find('.calendarGrid').scrollTop(scrollTop);
    }
};
TimelineCalendar.slider.mouseDown = function(e){
    var xLeft = Math.round($('#timelineCalendarKnob').offset().left);
    var xRight = xLeft + Math.round($('#timelineCalendarKnob').width());
    if((e.pageX >= xLeft) && (e.pageX <= xRight)){
        TimelineCalendar.slider.knobSlideAction = true;
    }
};
TimelineCalendar.slider.mouseUp = function(e){
    TimelineCalendar.slider.knobSlideAction = false;
};
TimelineCalendar.slider.animateStep = function(now, fx){
    var data = fx.elem.id + ' ' + fx.prop + ': ' + now;
    if(fx.unit == 'px'){
        var posLeft = Math.round((now / TimelineCalendar.slider.width)*10000)/100;
    }else{
        var posLeft = now;
    }
    TimelineCalendar.slider.knobPos = posLeft;
    var scrollHeight = TimelineCalendar.jObj.find('.calendarGrid').prop('scrollHeight');
    var scrollTop = Math.round(scrollHeight*(TimelineCalendar.slider.knobPos / 100));
    TimelineCalendar.jObj.find('.calendarGrid').scrollTop(scrollTop);
}
TimelineCalendar.slider.monthMouseUp = function(e){
    if(!TimelineCalendar.slider.knobSlideAction)
    {
        TimelineCalendar.slider.animateScrollAction = true;
        var newPos = $(this).css('left');
        $('#timelineCalendarKnob').animate({
                left: [newPos, 'easeOutCubic']
            },
            {
                duration: 1500,
                step: TimelineCalendar.slider.animateStep,
                easing: 'easeOutCubic',
                complete: function(){TimelineCalendar.slider.animateScrollAction = false;}
        });
    }
};

TimelineCalendar.slider.scrollEvent = function(e){
    if(!TimelineCalendar.slider.animateScrollAction){
        var scrollHeight = TimelineCalendar.jObj.find('.calendarGrid').prop('scrollHeight');
        TimelineCalendar.slider.knobPos = Math.round((TimelineCalendar.jObj.find('.calendarGrid').scrollTop() / scrollHeight)*1000)/10;
        $('#timelineCalendarKnob').css('left',TimelineCalendar.slider.knobPos + '%');
    }
};
TimelineCalendar.slider.init = function(){
    for(var i in TimelineCalendar.slider.monthArray){
        var leftPercent = TimelineCalendar.slider.monthArray[i].line / (TimelineCalendar.slider.totalLines - 3);
        leftPercent =  Math.round((1 - (3 / TimelineCalendar.slider.totalLines) )*leftPercent*1000 )/10;
        var newHtml = '<div class="monthName" style="left: '+leftPercent+'%">'+TimelineCalendar.slider.monthArray[i].name+'</div>';
        TimelineCalendar.jObj.find('.monthLine').append(newHtml);
    }
    TimelineCalendar.slider.knobWidth = Math.round((3 / TimelineCalendar.slider.totalLines)*10000)/100;
    $('#timelineCalendarKnob').css('width',TimelineCalendar.slider.knobWidth + '%');
    TimelineCalendar.slider.width = TimelineCalendar.jObj.find('.monthLine').width();

    TimelineCalendar.jObj.find('.calendarGrid').on('scroll',TimelineCalendar.slider.scrollEvent);
    TimelineCalendar.jObj.find('.monthLine').mousedown(TimelineCalendar.slider.mouseDown);
    TimelineCalendar.jObj.find('.monthLine').mouseup(TimelineCalendar.slider.mouseUp);
    TimelineCalendar.jObj.find('.monthLine .monthName').mouseup(TimelineCalendar.slider.monthMouseUp);
    TimelineCalendar.jObj.find('.monthLine').timelineCalendarKnob({
        startEvent: function (e,obj){TimelineCalendar.slider.startEvent(e,obj);},
        endEvent: function (e,obj){TimelineCalendar.slider.endEvent(e,obj);},
        dragEvent: function (e,obj){TimelineCalendar.slider.dragEvent(e,obj);}
    });
};


/**
 * Event have
 * Event.dayStart
 * Event.dayEnd
 * Event.type (flight/hotel)
 * Event.color
 * Event.description
 *
 * @type {Array}
 */
TimelineCalendar.calendarEvents = new Array();

TimelineCalendar.generateGrid = function (){
    var firstDay = new Date();


    var startMonth = firstDay.getMonth();
    var tmpDate = new Date(firstDay.toDateString());
    tmpDate.setDate(1);
    var weekDay = tmpDate.getDay();
    var startDate = firstDay.getDate();
    var startYear = firstDay.getFullYear();
    tmpDate.setDate(-tmpDate.getDay());
    var needStop = false;
    var lineNumber = 0;
    while(!needStop)
    {
        var newHtml = '<div class="calendarLine">';
        for(var i=0;i<7;i++){
            var label = tmpDate.getDate();
            if(label == 1){
                label = label + ' ' + TimelineCalendar.monthNames[tmpDate.getMonth()];
                var monthObject = new Object();
                monthObject.line = lineNumber;
                monthObject.name = TimelineCalendar.monthNames[tmpDate.getMonth()];
                TimelineCalendar.slider.monthArray.push(monthObject);
            }
            newHtml = newHtml + '<div class="dayCell">'+label+'</div>';
            tmpDate.setDate(tmpDate.getDate()+1);
        }
        newHtml = newHtml + '</div>';
        TimelineCalendar.jObj.find('.calendarGrid').append(newHtml);
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
    TimelineCalendar.slider.monthArray.pop();
    TimelineCalendar.slider.totalLines = lineNumber;
    console.log(TimelineCalendar.slider.totalLines);
}
TimelineCalendar.eventsCompareFunction = function(a, b)
{
    if(a.dayStart < b.dayStart){
        return -1;
    }else if(a.dayStart > b.dayStart){
        return 1;
    }else {
        if((a.type == 'flight') && (b.type == 'hotel')){
            return -1;
        }else if((a.type == 'hotel') && (b.type == 'flight')){
            return 1;
        }else{
            return 0;
        }
    }
}
TimelineCalendar.generateHotelDiv = function(HotelEvent)
{
    var totalDays = HotelEvent.dayEnd.valueOf() - HotelEvent.dayStart.valueOf();
    totalDays = Math.round(totalDays/(3600*24*1000));

    if(totalDays == 0){
        totalDays = 1;
    }
    //console.log(totalDays);
    var dayWidth = TimelineCalendar.dayCellWidth;
    //console.log(dayWidth);
    var outHtml = '<div class="calendarHotel '+HotelEvent.color+'" style="width: '+(dayWidth*totalDays)+'px">';
    outHtml = outHtml + '<div class="leftPartHotel"></div>';
    outHtml = outHtml + '<div class="rightPartHotel"></div>';
    outHtml = outHtml + '<div calss="hotelDescription">'+HotelEvent.description+'</div>';
    outHtml = outHtml + '';
    outHtml = outHtml + '</div>';
    return outHtml;
}

TimelineCalendar.generateEvents = function()
{
    TimelineCalendar.dayCellWidth = TimelineCalendar.jObj.find('.dayCell:first').width();
    for(var i in TimelineCalendar.calendarEvents)
    {
        if(TimelineCalendar.calendarEvents[i].tepe == 'hotel')
        {
            TimelineCalendar.generateHotelDiv(TimelineCalendar.calendarEvents[i]);
        }
    }

}
TimelineCalendar.init = function (){
    TimelineCalendar.jObj = $('#timelineCalendar');
    TimelineCalendar.calendarEvents.sort(TimelineCalendar.eventsCompareFunction);
    TimelineCalendar.generateGrid();
    TimelineCalendar.generateEvents();
    TimelineCalendar.slider.init();

}

$(document).ready(function(){
    /**
     * Event have
     * Event.dayStart
     * Event.dayEnd
     * Event.type (flight/hotel)
     * Event.color
     * Event.description
     *
     * @type {Array}
     */
    TimelineCalendar.calendarEvents = [{dayStart: new Date('2012-09-21'),dayEnd: new Date('2012-09-21'),type:'flight',color:'red',description:'Led - Mow'},{dayStart: new Date('2012-09-21'),dayEnd: new Date('2012-09-23'),type:'hotel',color:'red',description:'Californication Hotel'},{dayStart: new Date('2012-09-23'),dayEnd: new Date('2012-09-23'),type:'flight',color:'red',description:'Mow - Led'}];
    TimelineCalendar.init();
});