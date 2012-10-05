/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 25.07.12
 * Time: 13:40
 * To change this template use File | Settings | File Templates.
 */
/*VoyangaCalendarKnob = {
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
$.widget("ui.VoyangaCalendarKnob",$.ui.mouse, VoyangaCalendarKnob);/**/


Date.fromIso = function (dateIsoString){
    if(typeof dateIsoString == 'string'){
        var initArray = dateIsoString.split('-');
        return new Date(initArray[0],(initArray[1]-1),initArray[2]);
    }
    else{
        return dateIsoString;
    }
}
MouseDraggable = {
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
$.widget("ui.MouseDraggable",$.ui.mouse, MouseDraggable);

VoyangaCalendarSlider = function(options){
    var defaults = {
        monthArray: new Array(),
        jObj: null,
        totalLines: 1,
        knobWidth: 1,
        knobPos: 0,
        linesWidth: 5,
        width: 0,//recalc on window resize
        knobSlideAction: false,
        animateScrollAction: false,
        onresize: function(){
            this.width = this.jObj.find('.monthLineVoyanga').width();
        },
        startEvent: function(e,obj){
            if(this.knobSlideAction){
                obj.data('xStart', e.pageX);
                obj.data('posStart', this.knobPos);
            }
        },
        endEvent: function(e,obj){
            if(this.knobSlideAction){
                this.knobSlideAction = false;
            }
        },
        dragEvent: function(e,obj){
            if(this.knobSlideAction){
                var xDelta = e.pageX - obj.data('xStart');
                var posDelta = Math.round((xDelta / this.width)*10000)/100;
                this.knobPos = obj.data('posStart') + posDelta;
                if(this.knobPos < 0) this.knobPos = 0;
                if(this.knobPos > (100 - this.knobWidth)) this.knobPos = (100 - this.knobWidth);
                this.jObj.find('.knobVoyanga').css('left',this.knobPos + '%');
                this.jObj.find('.knobUpAllMonth').css('left',this.knobPos + '%');
                var scrollHeight = this.jObj.find('.calendarGridVoyanga').prop('scrollHeight');
                var scrollTop = Math.round(scrollHeight*(this.knobPos / 100));
                this.jObj.find('.calendarGridVoyanga').scrollTop(scrollTop);
                this.knobMove();
            }
        },
        mouseDown: function(e){
            var xLeft = Math.round(this.jObj.find('.knobVoyanga').offset().left);
            var xRight = xLeft + Math.round(this.jObj.find('.knobVoyanga').width());
            if((e.pageX >= xLeft) && (e.pageX <= xRight)){
                this.knobSlideAction = true;
                if(this.animateScrollAction){
                    this.jObj.find('.knobVoyanga').stop(true);
                    this.animateScrollAction = false;
                }
            }
        },
        mouseUp: function(e){
            this.knobSlideAction = false;
        },
        mousewheelEvent: function(e){
            //console.log(e);
            var rolled = 0;
            var event = e.originalEvent;
            if ('wheelDelta' in event) {
                rolled = event.wheelDelta;
            }
            else {  // Firefox
                // The measurement units of the detail and wheelDelta properties are different.
                rolled = -40 * event.detail;
            }
            var direction = (rolled > 0)? 1 : -1;
            if(Math.abs(rolled) > 60){
                rolled = 60* direction;
            }

            //var scrollHeight = TimelineCalendar.jObj.find('.calendarGrid').prop('scrollHeight');
            //console.log(this.jObj.find('.calendarGridVoyanga').scrollTop());

            var scrollTop = this.jObj.find('.calendarGridVoyanga').scrollTop() - rolled;
            this.jObj.find('.calendarGridVoyanga').scrollTop(scrollTop);
            return false;
        },
        animateStep: function(now, fx){
            var data = fx.elem.id + ' ' + fx.prop + ': ' + now;
            if(fx.unit == 'px'){
                var posLeft = Math.round((now / this.width)*10000)/100;
            }else{
                var posLeft = now;
            }
            this.knobPos = posLeft;
            this.jObj.find('.knobUpAllMonth').css('left',this.knobPos + '%');
            var scrollHeight = this.jObj.find('.calendarGridVoyanga').prop('scrollHeight');
            var scrollTop = Math.round(scrollHeight*(this.knobPos / 100));
            this.jObj.find('.calendarGridVoyanga').scrollTop(scrollTop);
            this.knobMove();
        },
        getPercent: function (pos){
            if(pos.indexOf('px') != -1){
                if(this.width < 100){
                    this.onresize();
                }
                pos = pos.substr(0, pos.length -2);
                pos = Math.round((pos / this.width)*10000)/100;
                pos = pos;
            }else if(pos.indexOf('%') != -1){
                pos = pos.substr(0, pos.length -1);
                pos = Math.round((pos)*100)/100;
                pos = pos;
            }
            return pos;
        },
        knobMove: function(){
            //var xLeft = Math.round(this.jObj.find('.knobVoyanga').offset().left);
            //var xRight = xLeft + Math.round(this.jObj.find('.knobVoyanga').width());
            var pWidth = this.knobWidth;
            //console.log(pWidth);
            var pLeft = this.knobPos;
            //console.log(pLeft);
            var pRight = pLeft + pWidth;
            var self = this;
            this.jObj.find('.monthNameVoyanga').each(function(){
                var pMonthLeft = self.getPercent($(this).css('left'));
                var pMonthWidth = self.getPercent($(this).css('width'));
                if( ( (pRight - pMonthLeft) > (pMonthWidth * 0.6) ) && ( (pMonthLeft + pMonthWidth - pLeft) > (pMonthWidth * 0.6) )){
                    $(this).addClass('highlited');
                    //console.log((pRight - pMonthLeft));

                }else{
                    $(this).removeClass('highlited');
                }
            });
        },
        monthMouseUp: function(obj,e){
            if(!this.knobSlideAction)
            {
                this.animateScrollAction = true;
                this.jObj.find('.knobVoyanga').stop(true);
                var newPos = $(obj).parent().css('left');
                newPos = this.getPercent(newPos)+'%';
                //var newPos = $(this).css('left');
                var self = this;
                this.jObj.find('.knobVoyanga').animate({
                        left: [newPos, 'easeOutCubic']
                    },
                    {
                        duration: 800,
                        step: function(now,fx){self.animateStep(now,fx);},
                        easing: 'easeOutCubic',
                        complete: function(){self.animateScrollAction = false;}
                    });
            }
        },
        scrollEvent: function(e){
            if(!this.animateScrollAction){
                var scrollHeight = this.jObj.find('.calendarGridVoyanga').prop('scrollHeight');
                this.knobPos = Math.round((this.jObj.find('.calendarGridVoyanga').scrollTop() / scrollHeight)*1000)/10;
                this.jObj.find('.knobVoyanga').css('left',this.knobPos + '%');
                this.jObj.find('.knobUpAllMonth').css('left',this.knobPos + '%');
                this.knobMove();
            }
        },

        init: function(){}
    };
    options = $.extend({},defaults,options);
    for(key in options){
        this[key] = options[key];
    }
}
VoyangaCalendarClass = function(options){
    var defaults = {
        jObj: null,
        weekDays: new Array('Пн','Вт','Ср','Чт','Пт','Сб','Вс'),
        monthNames: new Array('Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'),
        dayCellWidth: 180,
        getDay: function (dateObj){
            var dayNum = dateObj.getDay();
            if(dayNum == 0){
                dayNum = 6;
            }
            else{
                dayNum = dayNum - 1;
            }
            return dayNum;
        }
    };
    options = $.extend({},defaults,options);
    for(key in options){
        this[key] = options[key];
    }
}
/**/
VoyangaCalendarStandart = new VoyangaCalendarClass({jObj:'#voyanga-calendar',values:new Array()});
VoyangaCalendarStandart.initialized = false;

VoyangaCalendarStandart.slider = new VoyangaCalendarSlider({
    init: function(){
        //console.log(this.monthArray);
        console.log(this.jObj);
        if(typeof this.jObj == 'string'){
            this.jObj = $(this.jObj);
        }
        var self = this;
        for(var i in this.monthArray){
            var leftPercent = this.monthArray[i].line / (this.totalLines - this.linesWidth);
            leftPercent =  Math.round((1 - (this.linesWidth / this.totalLines) )*leftPercent*1000 )/10;
            if(i < (this.monthArray.length - 1) ){
                var k=parseInt(i)+1;

                var widthPercent = (this.monthArray[k].line - this.monthArray[i].line) / this.totalLines;
                //var widthPercent = 4/(VoyangaCalendar.slider.totalLines);
            }else{
                var widthPercent = (this.totalLines - this.monthArray[i].line) / this.totalLines;
            }
            widthPercent = Math.round(widthPercent*1000)/10;

            var newHtml = '<div class="monthNameVoyanga" style="left: '+leftPercent+'%; width: '+widthPercent+'%"><div class="monthWrapper">'+this.monthArray[i].name+'</div></div>';
            this.jObj.find('.monthLineVoyanga').append(newHtml);
        }
        this.knobWidth = Math.round((this.linesWidth / this.totalLines)*10000)/100;
        this.jObj.find('.knobVoyanga').css('width',this.knobWidth + '%');
        this.jObj.find('.knobUpAllMonth').css('width',this.knobWidth + '%');
        //VoyangaCalendar.slider.width = VoyangaCalendar.jObj.find('.monthLineVoyanga').width();
        $(window).on('resize',function(){self.onresize();});
        $(window).load(function(){self.onresize();self.knobMove();});

        this.jObj.find('.calendarGridVoyanga').on('scroll',function(e){self.scrollEvent(e);});
        //console.log('set wheel actions');
        this.jObj.find('.calendarGridVoyanga').on('mousewheel',function (e){self.mousewheelEvent(e);});
        this.jObj.find('.calendarGridVoyanga').on('DOMMouseScroll',function (e){self.mousewheelEvent(e);});
        //console.log(this);
        this.jObj.find('.monthLineVoyanga').mousedown(function(e){self.mouseDown(e);});
        this.jObj.find('.monthLineVoyanga').mouseup(function(e){self.mouseUp(e);});
        //VoyangaCalendar.jObj.find('.monthLineVoyanga .monthNameVoyanga').mouseup(VoyangaCalendar.slider.monthMouseUp);
        this.jObj.find('.monthLineVoyanga .monthNameVoyanga .monthWrapper').mouseup(function(e){var obj = this;self.monthMouseUp(obj,e);});
        this.jObj.find('.monthLineVoyanga').MouseDraggable({
            startEvent: function (e,obj){self.startEvent(e,obj);},
            endEvent: function (e,obj){self.endEvent(e,obj);},
            dragEvent: function (e,obj){self.dragEvent(e,obj);}
        });
    }
});

VoyangaCalendarStandart.onCellOver = function(obj,e){
    var jCell = $(obj);
    if(!jCell.hasClass('inactive')){
        var cellDate = Date.fromIso(jCell.data('cell-date'));
        if(this.values.length == 1){
            if(cellDate < this.values[0]){
                jCell.addClass('from');
            }else{
                if(this.twoSelect){
                    jCell.addClass('to');
                }else{
                    jCell.addClass('from');
                }
            }

        }else{
            jCell.addClass('from');
        }
        if(cellDate.getDate() == 1){
            jCell.addClass('startMonth');
        }
    }
}
VoyangaCalendarStandart.onCellOut = function(obj,e){
    var jCell = $(obj);
    if(!jCell.hasClass('inactive')){
        var cellDate = Date.fromIso(jCell.data('cell-date'));
        if(this.values.length == 1){
            if(cellDate < this.values[0]){
                jCell.removeClass('from');
            }else{
                if(this.twoSelect){
                    jCell.removeClass('to');
                }else{
                    jCell.removeClass('from');
                }
            }

        }else{
            jCell.removeClass('from');
        }
        if(cellDate.getDate() == 1){
            jCell.removeClass('startMonth');
        }
        if(this.values.length > 0){
            if(this.values[0].valueOf() == cellDate.valueOf()){
                jCell.addClass('selectData from');
                if(cellDate.getDate() == 1){
                    jCell.addClass('startMonth');
                }
            }
        }
        if(this.values.length > 1){
            if(this.values[1].valueOf() == cellDate.valueOf()){
                jCell.addClass('selectData to');
                if(cellDate.getDate() == 1){
                    jCell.addClass('startMonth');
                }
            }
        }
    }
}
VoyangaCalendarStandart.getCellByDate = function(oDate){
    var dateLabel = oDate.getFullYear()+'-'+(oDate.getMonth()+1)+'-'+oDate.getDate();
    return $('#dayCell-'+dateLabel);
}

VoyangaCalendarStandart.update = function(dontset){
    // FIXME SUPER SLOW
    $('.dayCellVoyanga').removeClass('selectData from to selectDay');

/*
                if(cellDate.getDate() == 1){
                    jCell.addClass('startMonth');
                }
        if(cellDate.getDate() == 1){
            jCell.addClass('startMonth');
        }
*/

    if(this.values.length) {
	var jCell = this.getCellByDate(this.values[0]);
	jCell.addClass('selectData from');

	if(this.values.length > 1) {
	    jCell = this.getCellByDate(this.values[1]);
	    jCell.addClass('selectData to');
	    var tmpDate = new Date(this.values[0].toDateString());
            tmpDate.setDate(tmpDate.getDate()+1);
            while(tmpDate < this.values[1]){
                this.getCellByDate(tmpDate).addClass('selectDay');
                tmpDate.setDate(tmpDate.getDate()+1);
            }
	}
	if(!dontset)
	    this.panel().setDate(this.values);
    }
}

VoyangaCalendarStandart.onCellClick = function(obj){
    var jCell = $(obj);
    if(jCell.hasClass('inactive'))
	return;
    var cellDate = Date.fromIso(jCell.data('cell-date'));
    if(this.twoSelect){
        if(this.values.length == 2){
            this.values = new Array();
        }else if(this.values.length == 1){
            if(cellDate < this.values[0]){
                this.values = new Array();
            }else{
                this.values.push(cellDate);
            }
        }
    }else{
        if(this.values.length != 0){
            this.values = new Array();
        }
    }
    if(this.values.length == 0){
        this.values.push(cellDate);
    } 
    VoyangaCalendarStandart.update();
}
function getMonday(d) {
    d = new Date(d);
    var day = d.getDay(),
        diff = d.getDate() - day + (day == 0 ? -6:1); // adjust when day is sunday
    return new Date(d.setDate(diff));
}

VoyangaCalendarStandart.generateGrid = function(){
    var firstDay = new Date();
    var dayToday = new Date();
    dayToday.setMinutes(0,0,0);
    dayToday.setHours(0);
    var self = this;
    var startMonth = firstDay.getMonth();
    var tmpDate = getMonday(firstDay);
    var weekDay = this.getDay(tmpDate);
    var startDate = firstDay.getDate();
    var startYear = firstDay.getFullYear();
    var needStop = false;
    var lineNumber = 0;
    while(!needStop)
    {
        var newHtml = '<div class="calendarLineVoyanga" id="weekNum-'+lineNumber+'" data-weeknum="'+lineNumber+'">';
        for(var i=0;i<7;i++){
            var label = '<div class="dayLabel'+((i>=5 && i<7) ? ' weekEnd' : '')+'">'+tmpDate.getDate()+'</div>';
	    
            if(tmpDate.getDate() == 1){
                label = label + ' <div class="monthLabel">' + this.monthNames[tmpDate.getMonth()] +'</div>';
                var monthObject = new Object();
                monthObject.line = lineNumber;
                monthObject.name = this.monthNames[tmpDate.getMonth()];
                this.slider.monthArray.push(monthObject);
            }
            var dateLabel = tmpDate.getFullYear()+'-'+(tmpDate.getMonth()+1)+'-'+tmpDate.getDate();
            var dateLabelApi = tmpDate.getDate()+'.'+(tmpDate.getMonth()+1)+'.'+tmpDate.getFullYear();
            newHtml = newHtml + '<div class="dayCellVoyanga' + ((tmpDate < dayToday) ? ' inactive' : '')+'" id="dayCell-'+dateLabel+'" data-cell-date="'+dateLabel+'" data-cell-date-api="'+dateLabelApi+'"><div class="innerDayCellVoyanga">'+label+'</div></div>';
            tmpDate.setDate(tmpDate.getDate()+1);
        }
        newHtml = newHtml + '</div>';
        this.jObj.find('.calendarDIVVoyanga').append(newHtml);
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
    var lastLineMonth = this.slider.monthArray[this.slider.monthArray.length - 1].line;
    if((lineNumber -lastLineMonth) < 2){
        this.slider.monthArray.pop();
    }
    this.jObj.find('.dayCellVoyanga').hover(function (e) {var obj = this; self.onCellOver(obj,e);},function (e) {var obj = this; self.onCellOut(obj,e);});
    this.jObj.find('.dayCellVoyanga').click(function (e) {var obj = this; self.onCellClick(obj,e);});

    this.slider.totalLines = lineNumber;
}

VoyangaCalendarStandart.newValueHandler = function(newCalendarValue) {
    console.log("new calendar value INC", newCalendarValue);
    VoyangaCalendarStandart.twoSelect = newCalendarValue.twoSelect;
    if(!newCalendarValue.twoSelect && VoyangaCalendarStandart.values.length > 1)
	VoyangaCalendarStandart.values = VoyangaCalendarStandart.values.slice(0,1);
//    VoyangaCalendarStandart.values = new Array();
//    if (!newCalendarValue.from)
//	return;
//    VoyangaCalendarStandart.values.push(newCalendarValue.from);
//    if(newCalendarValue.twoSelect)
//	VoyangaCalendarStandart.values.push(newCalendarValue.to);
      VoyangaCalendarStandart.update(true);
}


VoyangaCalendarStandart.newValueHandler2 = function(newCalendarValue) {
    VoyangaCalendarStandart.values = new Array();
    if (!newCalendarValue.from)
	return;
    VoyangaCalendarStandart.values.push(newCalendarValue.from);
    if(newCalendarValue.twoSelect)
	VoyangaCalendarStandart.values.push(newCalendarValue.to);
      VoyangaCalendarStandart.update(true);
}


VoyangaCalendarStandart.init = function (panel){

    VoyangaCalendarStandart.slider.jObj = this.jObj;
    this.panel = panel;
    panel.subscribe(function(newPanel) {
	// recet calendar on panel change
	if(newPanel.template) {
	    if(VoyangaCalendarStandart.subscription)
		VoyangaCalendarStandart.subscription.dispose();
	    VoyangaCalendarStandart.subscription = newPanel.calendarValue.subscribe(VoyangaCalendarStandart.newValueHandler);
	    VoyangaCalendarStandart.newValueHandler(newPanel.calendarValue());
	    VoyangaCalendarStandart.newValueHandler2(newPanel.calendarValue());
	}
    });
    if(typeof this.jObj == 'string'){
        this.jObj = $(this.jObj); 
    }
    VoyangaCalendarStandart.generateGrid();
    VoyangaCalendarStandart.slider.init();
}.bind(VoyangaCalendarStandart);
