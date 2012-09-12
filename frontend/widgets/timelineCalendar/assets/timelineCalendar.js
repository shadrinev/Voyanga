/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 25.07.12
 * Time: 13:40
 * To change this template use File | Settings | File Templates.
 */
timelineCalendarKnob = {
    options:{
        animate:false,
        slider:false
    },

    _create:function () {
        var self = this,
            o = this.options;
        //console.log(this);
        this._mouseInit();

    },
    //_init: function(){
    //	this._mouseInit(); // начинаем обработку поведения мыши
    //},
    destroy:function () {
        this._mouseDestroy();
    },
    _mouseStart:function (e) {
        this.options.startEvent(e, this.element);
    },
    _mouseDrag:function (e) {
        this.options.dragEvent(e, this.element);
    },
    _mouseStop:function (event) {
        this.options.endEvent(event, this.element);
        return false;

    }
};
$.widget("ui.timelineCalendarKnob", $.ui.mouse, timelineCalendarKnob);

TimelineCalendar = new Object();
TimelineCalendar.jObj = null;
TimelineCalendar.weekDays = new Array('Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс');
TimelineCalendar.monthNames = new Array('янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек');
TimelineCalendar.dayCellWidth = 110;
TimelineCalendar.slider = new Object();
TimelineCalendar.slider.monthArray = new Array();
TimelineCalendar.slider.totalLines = 1;
TimelineCalendar.slider.knobWidth = 1;
TimelineCalendar.slider.knobPos = 0;
TimelineCalendar.slider.width = 0;
TimelineCalendar.slider.knobSlideAction = false;
TimelineCalendar.slider.animateScrollAction = false;
TimelineCalendar.slider.startEvent = function (e, obj) {
    //console.log(obj);
    if (TimelineCalendar.slider.knobSlideAction) {
        obj.data('xStart', e.pageX);
        obj.data('posStart', TimelineCalendar.slider.knobPos);
    }
};
TimelineCalendar.slider.endEvent = function (e, obj) {
    if (TimelineCalendar.slider.knobSlideAction) {
        TimelineCalendar.slider.knobSlideAction = false;
    }
};
TimelineCalendar.slider.dragEvent = function (e, obj) {
    if (TimelineCalendar.slider.knobSlideAction) {
        var xDelta = e.pageX - obj.data('xStart');
        var posDelta = Math.round((xDelta / TimelineCalendar.slider.width) * 10000) / 100;
        TimelineCalendar.slider.knobPos = obj.data('posStart') + posDelta;
        if (TimelineCalendar.slider.knobPos < 0) TimelineCalendar.slider.knobPos = 0;
        if (TimelineCalendar.slider.knobPos > (100 - TimelineCalendar.slider.knobWidth)) TimelineCalendar.slider.knobPos = (100 - TimelineCalendar.slider.knobWidth);
        $('#timelineCalendarKnob').css('left', TimelineCalendar.slider.knobPos + '%');
        var scrollHeight = TimelineCalendar.jObj.find('.calendarGrid').prop('scrollHeight');
        var scrollTop = Math.round(scrollHeight * (TimelineCalendar.slider.knobPos / 100));
        TimelineCalendar.jObj.find('.calendarGrid').scrollTop(scrollTop);
    }
};
TimelineCalendar.slider.mouseDown = function (e) {
    var xLeft = Math.round($('#timelineCalendarKnob').offset().left);
    var xRight = xLeft + Math.round($('#timelineCalendarKnob').width());
    if ((e.pageX >= xLeft) && (e.pageX <= xRight)) {
        TimelineCalendar.slider.knobSlideAction = true;
    }
};
TimelineCalendar.slider.mouseUp = function (e) {
    TimelineCalendar.slider.knobSlideAction = false;
};
TimelineCalendar.slider.animateStep = function (now, fx) {
    var data = fx.elem.id + ' ' + fx.prop + ': ' + now;
    if (fx.unit == 'px') {
        var posLeft = Math.round((now / TimelineCalendar.slider.width) * 10000) / 100;
    } else {
        var posLeft = now;
    }
    TimelineCalendar.slider.knobPos = posLeft;
    var scrollHeight = TimelineCalendar.jObj.find('.calendarGrid').prop('scrollHeight');
    var scrollTop = Math.round(scrollHeight * (TimelineCalendar.slider.knobPos / 100));
    TimelineCalendar.jObj.find('.calendarGrid').scrollTop(scrollTop);
}
TimelineCalendar.slider.monthMouseUp = function (e) {
    if (!TimelineCalendar.slider.knobSlideAction) {
        TimelineCalendar.slider.animateScrollAction = true;
        var newPos = $(this).css('left');
        $('#timelineCalendarKnob').animate({
                left:[newPos, 'easeOutCubic']
            },
            {
                duration:1500,
                step:TimelineCalendar.slider.animateStep,
                easing:'easeOutCubic',
                complete:function () {
                    TimelineCalendar.slider.animateScrollAction = false;
                }
            });
    }
};

TimelineCalendar.slider.scrollEvent = function (e) {
    if (!TimelineCalendar.slider.animateScrollAction) {
        var scrollHeight = TimelineCalendar.jObj.find('.calendarGrid').prop('scrollHeight');
        TimelineCalendar.slider.knobPos = Math.round((TimelineCalendar.jObj.find('.calendarGrid').scrollTop() / scrollHeight) * 1000) / 10;
        $('#timelineCalendarKnob').css('left', TimelineCalendar.slider.knobPos + '%');
    }
};

/*TimelineCalendar.slider.mousewheelEvent = function(e){
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

 //var scrollHeight = TimelineCalendar.jObj.find('.calendarGrid').prop('scrollHeight');
 console.log(TimelineCalendar.jObj.find('.calendarGrid').scrollTop());

 var scrollTop = TimelineCalendar.jObj.find('.calendarGrid').scrollTop() - rolled;
 TimelineCalendar.jObj.find('.calendarGrid').scrollTop(scrollTop);
 return false;
 };*/
TimelineCalendar.slider.init = function () {
    for (var i in TimelineCalendar.slider.monthArray) {
        var leftPercent = TimelineCalendar.slider.monthArray[i].line / (TimelineCalendar.slider.totalLines - 3);
        leftPercent = Math.round((1 - (3 / TimelineCalendar.slider.totalLines) ) * leftPercent * 1000) / 10;
        var newHtml = '<div class="monthName" style="left: ' + leftPercent + '%">' + TimelineCalendar.slider.monthArray[i].name + '</div>';
        TimelineCalendar.jObj.find('.monthLine').append(newHtml);
    }
    TimelineCalendar.slider.knobWidth = Math.round((3 / TimelineCalendar.slider.totalLines) * 10000) / 100;
    $('#timelineCalendarKnob').css('width', TimelineCalendar.slider.knobWidth + '%');
    TimelineCalendar.slider.width = TimelineCalendar.jObj.find('.monthLine').width();

    TimelineCalendar.jObj.find('.calendarGrid').on('scroll', TimelineCalendar.slider.scrollEvent);
    //TimelineCalendar.jObj.find('.calendarGrid').on('mousewheel',TimelineCalendar.slider.mousewheelEvent);
    //TimelineCalendar.jObj.find('.calendarGrid').on('DOMMouseScroll',TimelineCalendar.slider.mousewheelEvent);
    TimelineCalendar.jObj.find('.monthLine').mousedown(TimelineCalendar.slider.mouseDown);
    TimelineCalendar.jObj.find('.monthLine').mouseup(TimelineCalendar.slider.mouseUp);
    TimelineCalendar.jObj.find('.monthLine .monthName').mouseup(TimelineCalendar.slider.monthMouseUp);
    TimelineCalendar.jObj.find('.monthLine').timelineCalendarKnob({
        startEvent:function (e, obj) {
            TimelineCalendar.slider.startEvent(e, obj);
        },
        endEvent:function (e, obj) {
            TimelineCalendar.slider.endEvent(e, obj);
        },
        dragEvent:function (e, obj) {
            TimelineCalendar.slider.dragEvent(e, obj);
        }
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
TimelineCalendar.getDay = function (dateObj) {
    var dayNum = dateObj.getDay();
    if (dayNum == 0) {
        dayNum = 6;
    }
    else {
        dayNum = dayNum - 1;
    }
    return dayNum;
}
TimelineCalendar.generateGrid = function () {
    var firstDay = new Date();


    var startMonth = firstDay.getMonth();
    var tmpDate = new Date(firstDay.toDateString());
    tmpDate.setDate(1);
    var weekDay = TimelineCalendar.getDay(tmpDate);
    //console.log(weekDay);
    var startDate = firstDay.getDate();
    var startYear = firstDay.getFullYear();
    //console.log(tmpDate);
    tmpDate.setDate(-TimelineCalendar.getDay(tmpDate) + 1);
    //tmpDate.setDate(0);
    //console.log(tmpDate);
    var needStop = false;
    var lineNumber = 0;
    while (!needStop) {
        var newHtml = '<div class="calendarLine" id="weekNum-' + lineNumber + '" data-weeknum="' + lineNumber + '">';
        for (var i = 0; i < 7; i++) {
            var label = tmpDate.getDate();
            if (label == 1) {
                label = label + ' ' + TimelineCalendar.monthNames[tmpDate.getMonth()];
                var monthObject = new Object();
                monthObject.line = lineNumber;
                monthObject.name = TimelineCalendar.monthNames[tmpDate.getMonth()];
                TimelineCalendar.slider.monthArray.push(monthObject);
            }
            var dateLabel = tmpDate.getFullYear() + '-' + (tmpDate.getMonth() + 1) + '-' + tmpDate.getDate();
            newHtml = newHtml + '<div class="dayCell" id="dayCell-' + dateLabel + '">' + label + '</div>';
            tmpDate.setDate(tmpDate.getDate() + 1);
        }
        newHtml = newHtml + '</div>';
        TimelineCalendar.jObj.find('.calendarGrid').append(newHtml);
        if (tmpDate.getFullYear() > startYear) {
            if (tmpDate.getMonth() >= startMonth) {
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
    //console.log(TimelineCalendar.slider.totalLines);
}
TimelineCalendar.eventsCompareFunction = function (a, b) {
    if (a.dayStart < b.dayStart) {
        return -1;
    } else if (a.dayStart > b.dayStart) {
        return 1;
    } else {
        if ((a.type == 'flight') && (b.type == 'hotel')) {
            return -1;
        } else if ((a.type == 'hotel') && (b.type == 'flight')) {
            return 1;
        } else {
            return 0;
        }
    }
}
TimelineCalendar.generateHotelDiv = function (HotelEvent) {
    var totalDays = HotelEvent.dayEnd.valueOf() - HotelEvent.dayStart.valueOf();
    totalDays = Math.round(totalDays / (3600 * 24 * 1000));
    //console.log('generate hotel div');

    if (totalDays == 0) {
        totalDays = 1;
    }
    //console.log(totalDays);
    var dayWidth = TimelineCalendar.dayCellWidth;
    //console.log(dayWidth);
    var outHtml = '<div class="calendarHotel ' + HotelEvent.color + '" style="width: ' + (dayWidth * totalDays) + 'px"><div class="relHotel">';
    outHtml = outHtml + '<div class="leftPartHotel"></div>';
    outHtml = outHtml + '<div class="rightPartHotel"></div>';
    outHtml = outHtml + '<div class="hotelDescription">' + HotelEvent.description + '</div>';
    outHtml = outHtml + '';
    outHtml = outHtml + '</div></div>';
    return outHtml;
}

TimelineCalendar.generateFlightDiv = function (FlightEvent) {
    var totalDays = FlightEvent.dayEnd.valueOf() - FlightEvent.dayStart.valueOf();
    totalDays = Math.floor(totalDays / (3600 * 24 * 1000));
    //console.log('generate flight div');

    //console.log(FlightEvent);
    totalDays = totalDays + 1;
    /*if(totalDays == 0){
     totalDays = 1;
     }*/
    //console.log(totalDays);
    //console.log(totalDays);
    var dayWidth = TimelineCalendar.dayCellWidth;
    //console.log(dayWidth);
    var names = FlightEvent.description.split('||');

    var outHtml = '<div class="calendarFlight ' + FlightEvent.color + '" style="width: ' + (dayWidth * totalDays) + 'px"><div class="relFlight">';
    outHtml = outHtml + '<div class="fromCity">' + names[0] + '</div>';
    outHtml = outHtml + '<div class="toCity">' + names[1] + '</div>';
    outHtml = outHtml + '';
    outHtml = outHtml + '';
    outHtml = outHtml + '</div></div>';
    return outHtml;
}

TimelineCalendar.generateEvents = function () {
    TimelineCalendar.dayCellWidth = TimelineCalendar.jObj.find('.dayCell:first').width() + 2;
    for (var i in TimelineCalendar.calendarEvents) {
        if (TimelineCalendar.calendarEvents[i].type == 'hotel') {

            //console.log(TimelineCalendar.calendarEvents[i]);
            /** @var dt Date */
            var dt = TimelineCalendar.calendarEvents[i].dayStart;
            var dateLabel = dt.getFullYear() + '-' + (dt.getMonth() + 1) + '-' + dt.getDate();
            //console.log(dateLabel);

            var weekObj = $('#dayCell-' + dateLabel).parent();
            var weekNum = weekObj.data('weeknum');
            //console.log(weekNum);
            var tmpDate = new Date(dt.toString());
            //console.log(tmpDate);
            //return;
            var eventLength = TimelineCalendar.calendarEvents[i].dayEnd.valueOf() - TimelineCalendar.calendarEvents[i].dayStart.valueOf();
            var hotelDiv = TimelineCalendar.generateHotelDiv(TimelineCalendar.calendarEvents[i]);
            var dayWidth = TimelineCalendar.dayCellWidth;
            //alert(TimelineCalendar.calendarEvents[i].dayEnd+'???');

            eventLength = Math.round(eventLength / (3600 * 24 * 1000));
            var renderedLength = 0;
            var endDraw = false;
            var firstTime = true;
            //alert('numRender:'+numRender+' renderedLength:'+renderedLength+' eventLength:'+eventLength);
            //continue;
            while (!endDraw) {
                var newEventElement = $(hotelDiv);
                if (firstTime) {
                    var numRender = 7 - TimelineCalendar.getDay(tmpDate) - 0.5;
                    //console.log('day:'+TimelineCalendar.getDay(tmpDate));
                    //console.log(numRender);
                    var leftPos = (7 - numRender) * dayWidth;
                    //console.log(leftPos);
                    firstTime = false;
                } else {
                    var numRender = 7;
                    var leftPos = -renderedLength * dayWidth;
                }
                newEventElement.css('left', leftPos + 'px');

                //console.log(newEventElement);
                //alert('numRender:'+numRender+' renderedLength:'+renderedLength+' eventLength:'+eventLength);
                weekObj.append(newEventElement);
                renderedLength = renderedLength + numRender;
                if (renderedLength >= eventLength) {
                    endDraw = true;
                }
                weekNum++;
                weekObj = $('#weekNum-' + weekNum);
            }
        } else if (TimelineCalendar.calendarEvents[i].type == 'flight') {
            //console.log(TimelineCalendar.calendarEvents[i]);
            /** @var dt Date */
            var dt = TimelineCalendar.calendarEvents[i].dayStart;
            var dateLabel = dt.getFullYear() + '-' + (dt.getMonth() + 1) + '-' + dt.getDate();
            //console.log(dateLabel);

            var weekObj = $('#dayCell-' + dateLabel).parent();
            var weekNum = weekObj.data('weeknum');
            //console.log(weekNum);
            var tmpDate = new Date(dt.toString());
            //console.log(tmpDate);
            //return;
            var eventLength = TimelineCalendar.calendarEvents[i].dayEnd.valueOf() - TimelineCalendar.calendarEvents[i].dayStart.valueOf();
            var flightDiv = TimelineCalendar.generateFlightDiv(TimelineCalendar.calendarEvents[i]);


            //continue;
            var dayWidth = TimelineCalendar.dayCellWidth;

            eventLength = Math.round(eventLength / (3600 * 24 * 1000));
            var renderedLength = 0;
            var endDraw = false;
            var firstTime = true;
            //continue;
            while (!endDraw) {
                var newEventElement = $(flightDiv);
                if (firstTime) {
                    var numRender = 7 - TimelineCalendar.getDay(tmpDate);
                    //console.log('day:'+TimelineCalendar.getDay(tmpDate));
                    //console.log(numRender);
                    var leftPos = (7 - numRender) * dayWidth;
                    //console.log(leftPos);
                    firstTime = false;
                } else {
                    var numRender = 7;
                    var leftPos = -renderedLength * dayWidth;
                }
                newEventElement.css('left', leftPos + 'px');

                //console.log(newEventElement);
                weekObj.append(newEventElement);
                renderedLength = renderedLength + numRender;
                if (renderedLength >= eventLength) {
                    endDraw = true;
                }
                weekNum++;
                weekObj = $('#weekNum-' + weekNum);
            }
        }
    }
}

TimelineCalendar.prepareEvents = function () {
    console.log(TimelineCalendar.calendarEvents);
    $.each(TimelineCalendar.calendarEvents, function (ind, el) {
        el.dayStart = Date.fromIso(el.dayStart);
        el.dayEnd = Date.fromIso(el.dayEnd);
    });
}

TimelineCalendar.init = function () {
    TimelineCalendar.prepareEvents();
    TimelineCalendar.jObj = $('#timelineCalendar');
    TimelineCalendar.calendarEvents.sort(TimelineCalendar.eventsCompareFunction);

    //console.log(TimelineCalendar.calendarEvents);
    //return true;
    TimelineCalendar.generateGrid();
    //return true;
    TimelineCalendar.generateEvents();
    TimelineCalendar.slider.init();
}
