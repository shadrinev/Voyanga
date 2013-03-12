/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 25.07.12
 * Time: 13:40
 * To change this template use File | Settings | File Templates.
 */


Date.fromIso = function (dateIsoString) {
    if (typeof dateIsoString == 'string') {
        var initArray = dateIsoString.split('-');
        return new Date(initArray[0], (initArray[1] - 1), initArray[2]);
    }
    else {
        return dateIsoString;
    }
};

(function () {
    var D = new Date('2011-06-02T09:34:29+02:00');
    if (isNaN(D) || D.getUTCMonth() !== 5 || D.getUTCDate() !== 2 ||
        D.getUTCHours() !== 7 || D.getUTCMinutes() !== 34) {
        Date.fromISO = function (s) {
            var day, tz,
                rx = /^(\d{4}\-\d\d\-\d\d([tT][\d:\.]*)?)([zZ]|([+\-])(\d\d):(\d\d))?$/,
                p = rx.exec(s) || [];
            if (p[1]) {
                day = p[1].split(/\D/);
                for (var i = 0, L = day.length; i < L; i++) {
                    day[i] = parseInt(day[i], 10) || 0;
                }
                day[1] -= 1;
                day = new Date(Date.UTC.apply(Date, day));
                if (!day.getDate()) return NaN;
                if (p[5]) {
                    tz = (parseInt(p[5], 10) * 60);
                    if (p[6]) tz += parseInt(p[6], 10);
                    if (p[4] == '+') tz *= -1;
                    if (tz) day.setUTCMinutes(day.getUTCMinutes() + tz);
                }
                return day;
            }
            return NaN;
        }
    }
    else {
        Date.fromISO = function (s) {
            return new Date(s);
        }
    }
})();

MouseDraggable = {
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
$.widget("ui.MouseDraggable", $.ui.mouse, MouseDraggable);

VoyangaCalendarSlider = function (options) {
    var defaults = {
        monthArray:new Array(),
        jObj:null,
        totalLines:1,
        knobWidth:1,
        knobPos:0,
        linesWidth:5,
        width:0, //recalc on window resize
        knobSlideAction:false,
        animateScrollAction:false,
        onresize:function () {
            this.width = this.jObj.find('.monthLineVoyanga').width();
        },
        startEvent:function (e, obj) {
            if (this.knobSlideAction) {
                obj.data('xStart', e.pageX);
                obj.data('posStart', this.knobPos);
                if (this.width < 100) {
                    this.onresize();
                }
            }
        },
        endEvent:function (e, obj) {
            if (this.knobSlideAction) {
                this.knobSlideAction = false;
            }
        },
        dragEvent:function (e, obj) {
            if (this.knobSlideAction) {
                var xDelta = e.pageX - obj.data('xStart');
                var posDelta = Math.round((xDelta / this.width) * 10000) / 100;
                this.knobPos = obj.data('posStart') + posDelta;
                if (this.knobPos < 0) this.knobPos = 0;
                if (this.knobPos > (100 - this.knobWidth)) this.knobPos = (100 - this.knobWidth);
                this.jObj.find('.knobVoyanga').css('left', this.knobPos + '%');
                this.jObj.find('.knobUpAllMonth').css('left', this.getKnobUpLeft());
                var scrollHeight = this.jObj.find('.calendarGridVoyanga').prop('scrollHeight');
                var scrollTop = Math.round(scrollHeight * (this.knobPos / 100));
                this.jObj.find('.calendarGridVoyanga').scrollTop(scrollTop);
                this.knobMove();
            }
        },
        mouseDown:function (e) {
            var xLeft = Math.round(this.jObj.find('.knobVoyanga').offset().left);
            var xRight = xLeft + Math.round(this.jObj.find('.knobVoyanga').width());
            if ((e.pageX >= xLeft) && (e.pageX <= xRight)) {
                this.knobSlideAction = true;
                if (this.animateScrollAction) {
                    this.jObj.find('.knobVoyanga').stop(true);
                    this.animateScrollAction = false;
                }
            }
        },
        mouseUp:function (e) {
            this.knobSlideAction = false;
        },
        mousewheelEvent:function (e) {
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
            var direction = (rolled > 0) ? 1 : -1;
            if (Math.abs(rolled) > 60) {
                rolled = 60 * direction;
            }

            //var scrollHeight = TimelineCalendar.jObj.find('.calendarGrid').prop('scrollHeight');
            //console.log(this.jObj.find('.calendarGridVoyanga').scrollTop());

            var scrollTop = this.jObj.find('.calendarGridVoyanga').scrollTop() - rolled;
            this.jObj.find('.calendarGridVoyanga').scrollTop(scrollTop);
            return false;
        },
        animateStep:function (now, fx) {
            var data = fx.elem.id + ' ' + fx.prop + ': ' + now;
            if (fx.unit == 'px') {
                var posLeft = Math.round((now / this.width) * 10000) / 100;
            } else {
                var posLeft = now;
            }
            this.knobPos = posLeft;
            this.jObj.find('.knobUpAllMonth').css('left', this.getKnobUpLeft());
            var scrollHeight = this.jObj.find('.calendarGridVoyanga').prop('scrollHeight');
            var scrollTop = Math.round(scrollHeight * (this.knobPos / 100));
            this.jObj.find('.calendarGridVoyanga').scrollTop(scrollTop);
            this.knobMove();
        },
        getKnobUpLeft:function () {
            return this.knobPos + '%'
        },
        getPercent:function (pos) {
            if (pos.indexOf('px') != -1) {
                if (this.width < 100) {
                    this.onresize();
                }
                pos = pos.substr(0, pos.length - 2);
                pos = Math.round((pos / this.width) * 10000) / 100;
            } else if (pos.indexOf('%') != -1) {
                pos = pos.substr(0, pos.length - 1);
                pos = Math.round((pos) * 100) / 100;
            }
            return pos;
        },
        knobMove:function () {
            //var xLeft = Math.round(this.jObj.find('.knobVoyanga').offset().left);
            //var xRight = xLeft + Math.round(this.jObj.find('.knobVoyanga').width());
            var pWidth = this.knobWidth;
            //console.log(pWidth);
            var pLeft = this.knobPos;
            //console.log(pLeft);
            var pRight = pLeft + pWidth;
            var self = this;
            this.jObj.find('.monthNameVoyanga').each(function () {
                var pMonthLeft = self.getPercent($(this).css('left'));
                var pMonthWidth = self.getPercent($(this).css('width'));
                if (( (pRight - pMonthLeft) > (pMonthWidth * 0.6) ) && ( (pMonthLeft + pMonthWidth - pLeft) > (pMonthWidth * 0.6) )) {
                    $(this).addClass('highlited');
                    //console.log((pRight - pMonthLeft));

                } else {
                    $(this).removeClass('highlited');
                }
            });
        },
        monthMouseUp:function (obj, e) {
            if (!this.knobSlideAction) {
                this.animateScrollAction = true;
                this.jObj.find('.knobVoyanga').stop(true);
                var newPos = $(obj).parent().css('left');
                newPos = this.getPercent(newPos) + '%';
                //var newPos = $(this).css('left');
                var self = this;
                if (this.width < 100) {
                    this.onresize();
                }
                this.jObj.find('.knobVoyanga').animate({
                        left:[newPos, 'easeOutCubic']
                    },
                    {
                        duration:800,
                        step:function (now, fx) {
                            self.animateStep(now, fx);
                        },
                        easing:'easeOutCubic',
                        complete:function () {
                            self.animateScrollAction = false;
                        }
                    });
            }
        },
        scrollEvent:function (e) {
            if (!this.animateScrollAction) {
                var scrollHeight = this.jObj.find('.calendarGridVoyanga').prop('scrollHeight');
                this.knobPos = Math.round((this.jObj.find('.calendarGridVoyanga').scrollTop() / scrollHeight) * 1000) / 10;
                this.jObj.find('.knobVoyanga').css('left', this.knobPos + '%');
                this.jObj.find('.knobUpAllMonth').css('left', this.getKnobUpLeft());
                this.knobMove();
            }
        },

        init:function () {
        }
    };
    options = $.extend({}, defaults, options);
    for (key in options) {
        this[key] = options[key];
    }
}
VoyangaCalendarClass = function (options) {
    var defaults = {
        jObj:null,
        weekDays:new Array('Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'),
        monthNames:new Array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'),
        dayCellWidth:180,
        getDay:function (dateObj) {
            var dayNum = dateObj.getDay();
            if (dayNum == 0) {
                dayNum = 6;
            }
            else {
                dayNum = dayNum - 1;
            }
            return dayNum;
        }
    };
    options = $.extend({}, defaults, options);
    for (key in options) {
        this[key] = options[key];
    }
}
/**/
VoyangaCalendarStandart = new VoyangaCalendarClass({values:new Array()});
VoyangaCalendarStandart.initialized = false;

VoyangaCalendarStandart.slider = new VoyangaCalendarSlider({
    init:function () {
        //console.log(this.monthArray);
        var self = this;
        for (var i in this.monthArray) {
            var leftPercent = this.monthArray[i].line / (this.totalLines - this.linesWidth);
            leftPercent = Math.round((1 - (this.linesWidth / this.totalLines) ) * leftPercent * 1000) / 10;
            if (i < (this.monthArray.length - 1)) {
                var k = parseInt(i) + 1;

                var widthPercent = (this.monthArray[k].line - this.monthArray[i].line) / this.totalLines;
                //var widthPercent = 4/(VoyangaCalendar.slider.totalLines);
            } else {
                var widthPercent = (this.totalLines - this.monthArray[i].line) / this.totalLines;
            }
            widthPercent = Math.round(widthPercent * 1000) / 10;

            var newHtml = '<div class="monthNameVoyanga" style="left: ' + leftPercent + '%; width: ' + widthPercent + '%"><div class="monthWrapper">' + this.monthArray[i].name + '</div></div>';
            this.jObj.find('.monthLineVoyanga').append(newHtml);
        }
        this.knobWidth = Math.round((this.linesWidth / this.totalLines) * 10000) / 100;
        this.jObj.find('.knobVoyanga').css('width', this.knobWidth + '%');
        this.jObj.find('.knobUpAllMonth').css('width', this.knobWidth + '%');
        //VoyangaCalendar.slider.width = VoyangaCalendar.jObj.find('.monthLineVoyanga').width();
        $(window).on('resize', function () {
            self.onresize();
        });
        $(window).load(function () {
            self.onresize();
            self.knobMove();
        });

        this.jObj.find('.calendarGridVoyanga').on('scroll', function (e) {
            self.scrollEvent(e);
            return false
        });
        //console.log('set wheel actions');
        this.jObj.find('.calendarGridVoyanga').on('mousewheel', function (e) {
            self.mousewheelEvent(e);
            if (e.preventDefault)
                e.preventDefault();
            e.returnValue = false;
        });
        this.jObj.find('.calendarGridVoyanga').on('DOMMouseScroll', function (e) {
            self.mousewheelEvent(e);
            if (e.preventDefault)
                e.preventDefault();
            e.returnValue = false;
        });
        //console.log(this);
        this.jObj.find('.monthLineVoyanga').mousedown(function (e) {
            self.mouseDown(e);
        });
        this.jObj.find('.monthLineVoyanga').mouseup(function (e) {
            self.mouseUp(e);
        });
        //VoyangaCalendar.jObj.find('.monthLineVoyanga .monthNameVoyanga').mouseup(VoyangaCalendar.slider.monthMouseUp);
        this.jObj.find('.monthLineVoyanga .monthNameVoyanga .monthWrapper').mouseup(function (e) {
            var obj = this;
            self.monthMouseUp(obj, e);
        });
        this.jObj.find('.monthLineVoyanga').MouseDraggable({
            startEvent:function (e, obj) {
                self.startEvent(e, obj);
            },
            endEvent:function (e, obj) {
                self.endEvent(e, obj);
            },
            dragEvent:function (e, obj) {
                self.dragEvent(e, obj);
            }
        });
        if (this.minimalLine) {
            var scrollTop = (this.minimalLine / this.totalLines) * this.jObj.find('.calendarGridVoyanga').prop('scrollHeight');
            this.jObj.find('.calendarGridVoyanga').scrollTop(scrollTop);
        }
    }
});

VoyangaCalendarStandart.setCellFrom = function (jCell) {
    if (!jCell.hasClass('from')) {
        jCell.addClass('from');
        if (VoyangaCalendarStandart.valuesDescriptions[0]) {
            if (jCell.find('.fromDesc').length == 0) {
                //jCell.append('<div class="fromDesc"><table><tr><td>' + VoyangaCalendarStandart.valuesDescriptions[0] + '</td></tr></table></div>');
                jCell.append('<div class="fromDesc"><div class="relate"><div class="absUp">' + VoyangaCalendarStandart.valuesDescriptions[0] + '</div><div class="visHid">' + VoyangaCalendarStandart.valuesDescriptions[0] + '</div></div>');
            }
        }
        //var cellDate = Date.fromIso(jCell.data('cell-date'));
        //if (cellDate.getDate() == 1) {
        //    jCell.addClass('startMonth');
        //}
    }
    VoyangaCalendarStandart.lastFromOverCell = jCell;
}

VoyangaCalendarStandart.unsetCellFrom = function (jCell) {
    if (jCell.hasClass('from')) {
        jCell.removeClass('from');
    }
    if (jCell.find('.fromDesc').length) {
        jCell.find('.fromDesc').remove();
    }
}

VoyangaCalendarStandart.setCellTo = function (jCell) {
    if (!jCell.hasClass('to')) {
        jCell.addClass('to');
        if (VoyangaCalendarStandart.valuesDescriptions.length > 1 && VoyangaCalendarStandart.valuesDescriptions[1] && (!jCell.hasClass('from'))) {
            if (jCell.find('.toDesc').length == 0) {
                jCell.append('<div class="toDesc"><div class="relate"><div class="absUp">' + VoyangaCalendarStandart.valuesDescriptions[1] + '</div><div class="visHid">' + VoyangaCalendarStandart.valuesDescriptions[1] + '</div></div>');
            }
        }
        if (VoyangaCalendarStandart.valuesDescriptions.length > 2 && VoyangaCalendarStandart.valuesDescriptions[2] && (jCell.hasClass('from'))) {
            jCell.addClass('fromTo');
            if (jCell.find('.fromToDesc').length == 0) {
                jCell.append('<div class="fromToDesc"><div class="relate"><div class="absUp">' + VoyangaCalendarStandart.valuesDescriptions[2] + '</div><div class="visHid">' + VoyangaCalendarStandart.valuesDescriptions[2] + '</div></div>');
            }
        }
    }
    VoyangaCalendarStandart.lastToOverCell = jCell;
}

VoyangaCalendarStandart.unsetCellTo = function (jCell) {
    if (jCell.hasClass('to')) {
        jCell.removeClass('to');
    }
    if (jCell.hasClass('fromTo')) {
        jCell.removeClass('fromTo');
    }
    if (jCell.find('.toDesc').length) {
        jCell.find('.toDesc').remove();
    }
    if (jCell.find('.fromToDesc').length) {
        jCell.find('.fromToDesc').remove();
    }
}

VoyangaCalendarStandart.setCellInterval = function (jCell, diff) {
    if (VoyangaCalendarStandart.intervalLastCell) {
        VoyangaCalendarStandart.unsetCellInterval();
    }
    VoyangaCalendarStandart.intervalLastCell = jCell;
    if (!jCell.hasClass('intervalDescription')) {
        jCell.addClass('intervalDescription');
        var cnt = diff - parseInt(VoyangaCalendarStandart.intervalDescription);
        var strCnt = 'всего ' + Utils.wordAfterNum(cnt, VoyangaCalendarStandart.intervalWords[0], VoyangaCalendarStandart.intervalWords[1], VoyangaCalendarStandart.intervalWords[2]);
        jCell.append('<div class="intDesc">' + strCnt + '</div>');
    }
}

VoyangaCalendarStandart.unsetCellInterval = function () {
    //VoyangaCalendarStandart.intervalLastCell
    if (VoyangaCalendarStandart.intervalLastCell) {
        if (VoyangaCalendarStandart.intervalLastCell.hasClass('intervalDescription')) {
            VoyangaCalendarStandart.intervalLastCell.removeClass('intervalDescription');
            if (VoyangaCalendarStandart.intervalLastCell.find('.intDesc').length) {
                VoyangaCalendarStandart.intervalLastCell.find('.intDesc').remove();
            }
        }
    }
    VoyangaCalendarStandart.intervalLastCell = false;
}

VoyangaCalendarStandart.onCellOver = function (obj, e) {
    if (!VoyangaCalendarStandart.isMobileDevice) {
        var jCell = $(obj);
        if (!jCell.hasClass('inactive')) {
            var cellDate = Date.fromIso(jCell.data('cell-date'));
            if (this.values.length == 1) {
                if (cellDate < this.values[0]) {
                    VoyangaCalendarStandart.setCellFrom(jCell);
                } else {
                    if (this.twoSelect) {
                        VoyangaCalendarStandart.setCellTo(jCell);
                    } else {
                        VoyangaCalendarStandart.setCellFrom(jCell);
                    }
                }

            } else {
                VoyangaCalendarStandart.setCellFrom(jCell);
            }
            //if (cellDate.getDate() == 1) {
            //    jCell.addClass('startMonth');
            //}
        }
    }
}
VoyangaCalendarStandart.onCellOut = function (obj, e) {
    if (!VoyangaCalendarStandart.isMobileDevice) {
        var jCell = $(obj);
        if (!jCell.hasClass('inactive')) {
            var cellDate = Date.fromIso(jCell.data('cell-date'));
            if (this.values.length == 1) {
                if (cellDate < this.values[0]) {
                    //jCell.removeClass('from');
                    VoyangaCalendarStandart.unsetCellFrom(jCell);
                } else {
                    if (this.twoSelect) {
                        VoyangaCalendarStandart.unsetCellTo(jCell);
                    } else {
                        //jCell.removeClass('from');
                        VoyangaCalendarStandart.unsetCellFrom(jCell);
                    }
                }

            } else {
                //jCell.removeClass('from');
                VoyangaCalendarStandart.unsetCellFrom(jCell);
            }
            //if (cellDate.getDate() == 1) {
            //    jCell.removeClass('startMonth');
            //}
            if (this.values.length > 0) {
                if (this.values[0].valueOf() == cellDate.valueOf()) {
                    VoyangaCalendarStandart.setCellFrom(jCell);
                    jCell.addClass('selectData');
                    //if (cellDate.getDate() == 1) {
                    //    jCell.addClass('startMonth');
                    //}
                }
            }
            if (this.values.length > 1) {
                if (this.values[1].valueOf() == cellDate.valueOf()) {
                    VoyangaCalendarStandart.setCellTo(jCell);
                    jCell.addClass('selectData');
                    //if (cellDate.getDate() == 1) {
                    //    jCell.addClass('startMonth');
                    //}
                }
            }
        }
    }
}
VoyangaCalendarStandart.getCellByDate = function (oDate) {
    if (oDate) {
        var dateLabel = oDate.getFullYear() + '-' + (oDate.getMonth() + 1) + '-' + oDate.getDate();
        return $('#dayCell-' + dateLabel);
    } else {
//        console.log('bad date value:', oDate);
        return false;
    }

}

VoyangaCalendarStandart.update = function (dontset) {
    VoyangaCalendarStandart.unsetCellInterval();
    var selIndex = 0;
    if (VoyangaCalendarStandart.lastFromCell) {
     //   console.log('try unset from', VoyangaCalendarStandart.lastFromCell);
        VoyangaCalendarStandart.unsetCellFrom(VoyangaCalendarStandart.lastFromCell);
    }
    if (VoyangaCalendarStandart.lastToCell) {
     //   console.log('try unset to', VoyangaCalendarStandart.lastToCell);
        VoyangaCalendarStandart.unsetCellTo(VoyangaCalendarStandart.lastToCell);
    }
    // FIXME SUPER SLOW
    $('.dayCellVoyanga').removeClass('selectData from to selectDay');

//    console.log('dontset is ' + (dontset ? 'true' : 'false') + ' values:', this.values);

    if (this.values.length) {
        var jCell = this.getCellByDate(this.values[0]);
        VoyangaCalendarStandart.setCellFrom(jCell);
        VoyangaCalendarStandart.lastFromCell = jCell;
        jCell.addClass('selectData');
        selIndex = 1;

        if (this.values.length > 1) {
            selIndex = 2;
            jCell = this.getCellByDate(this.values[1]);
            VoyangaCalendarStandart.setCellTo(jCell);
            VoyangaCalendarStandart.lastToCell = jCell;
            jCell.addClass('selectData');
            //clone date object
            var tmpDate = moment(moment(this.values[0]))._d;
            tmpDate.setDate(tmpDate.getDate() + 1);
            while (tmpDate < this.values[1]) {
                this.getCellByDate(tmpDate).addClass('selectDay');
                tmpDate.setDate(tmpDate.getDate() + 1);
            }
            if (VoyangaCalendarStandart.intervalDescription) {
                var diff = moment(this.values[1]).diff(moment(this.values[0]), 'days');
                if (diff >= 2) {
                    tmpDate.setDate(tmpDate.getDate() - 1);
                    jCell = this.getCellByDate(tmpDate);
                    VoyangaCalendarStandart.setCellInterval(jCell, diff);
                }
            }
        }
        if (!dontset) {
//            console.log('sat date and value will be updated', this.panel());
            VoyangaCalendarStandart.scrollDate = this.values[0];
            if (this.panel().setDate) {
                this.panel().setDate(this.values);
            }
        }
    }
    if (VoyangaCalendarStandart.selectionIndex) {
        VoyangaCalendarStandart.selectionIndex(selIndex);
    }
}

VoyangaCalendarStandart.onCellClick = function (obj) {
    var jCell = $(obj);
    if (jCell.hasClass('inactive'))
        return;
    var cellDate = Date.fromIso(jCell.data('cell-date'));
    var dontset = true;

    if (this.twoSelect) {
        if (this.values.length == 2) {
            this.values = new Array();
        } else if (this.values.length == 1) {
            if (cellDate < this.values[0]) {
                this.values = new Array();
            } else {
                if (this.hotels) {
                    if (moment(this.values[0]).diff(moment(cellDate), 'days') == 0) {
                        return;
                    }
                }
                dontset = false;
                this.values.push(cellDate);
            }
        }
    } else {
        if (this.values.length != 0) {
            this.values = new Array();
        }
        dontset = false;

    }
    VoyangaCalendarStandart.unsetCellFrom(jCell);
    VoyangaCalendarStandart.unsetCellTo(jCell);


    if (this.values.length == 0) {
        this.values.push(cellDate);
    }
    VoyangaCalendarStandart.update(dontset);
}

VoyangaCalendarStandart.generateGrid = function () {
    var firstDay = new Date();
    var dayToday = new Date();
    dayToday.setMinutes(0, 0, 0);
    dayToday.setHours(0);
    var self = this;
    var startMonth = firstDay.getMonth();
    var tmpDate = moment(moment(firstDay))._d;//clone Date object
    tmpDate.setDate(1);
    tmpDate.setDate(tmpDate.getDate() - this.getDay(tmpDate));//set Monday
    var weekDay = this.getDay(tmpDate);
    var startDate = firstDay.getDate();
    var startYear = firstDay.getFullYear();
    var needStop = false;
    var lineNumber = 0;
    this.slider.monthArray = new Array();
    while (!needStop) {
        var newHtml = '<div class="calendarLineVoyanga" id="weekNum-' + lineNumber + '" data-weeknum="' + lineNumber + '">';
        for (var i = 0; i < 7; i++) {
            var label = '<div class="dayLabel' + ((i >= 5 && i < 7) ? ' weekEnd' : '') + '">' + tmpDate.getDate() + '</div>';

            if (tmpDate.getDate() == 1) {
                label = label + ' <div class="monthLabel">' + this.monthNames[tmpDate.getMonth()] + '</div>';
                var monthObject = new Object();
                monthObject.line = lineNumber;
                monthObject.name = this.monthNames[tmpDate.getMonth()];
                this.slider.monthArray.push(monthObject);
            }
            var dateLabel = tmpDate.getFullYear() + '-' + (tmpDate.getMonth() + 1) + '-' + tmpDate.getDate();
            var dateLabelApi = tmpDate.getDate() + '.' + (tmpDate.getMonth() + 1) + '.' + tmpDate.getFullYear();
            newHtml = newHtml + '<div class="dayCellVoyanga' + ((tmpDate < dayToday) ? ' inactive' : '') + ((tmpDate.getDate() == 1) ? ' startMonth' : '') + '" id="dayCell-' + dateLabel + '" data-cell-date="' + dateLabel + '" data-cell-date-api="' + dateLabelApi + '"><div class="innerDayCellVoyanga">' + label + '</div></div>';
            tmpDate.setDate(tmpDate.getDate() + 1);
        }
        newHtml = newHtml + '</div>';
        this.jObj.find('.calendarDIVVoyanga').append(newHtml);
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
    var lastLineMonth = this.slider.monthArray[this.slider.monthArray.length - 1].line;
    if ((lineNumber - lastLineMonth) < 2) {
        this.slider.monthArray.pop();
    }
    this.jObj.find('.dayCellVoyanga').hover(function (e) {
        var obj = this;
        self.onCellOver(obj, e);
    }, function (e) {
        var obj = this;
        self.onCellOut(obj, e);
    });
    this.jObj.find('.dayCellVoyanga').click(function (e) {
        var obj = this;
        self.onCellClick(obj, e);
    });


    this.slider.totalLines = lineNumber;
}

VoyangaCalendarStandart.clear = function () {
//    console.log('clear all values');
    VoyangaCalendarStandart.values = [];
    VoyangaCalendarStandart.update(true);
}

VoyangaCalendarStandart.minimalDateUpdated = function () {
    var dateLabel = this.minimalDate.getFullYear() + '-' + (this.minimalDate.getMonth() + 1) + '-' + this.minimalDate.getDate();
    this.slider.minimalLine = $('#dayCell-' + dateLabel).parent().data('weeknum');

    dayCell = this.jObj.find('#weekNum-0 .dayCellVoyanga:eq(0)');
    if (dayCell.length) {
        var dd = Date.fromIso(dayCell.data('cell-date'));
        var stop = false;
        var lineNumber = 0;
        while (!stop) {
            for (var i = 0; i < 7; i++) {
                dateLabel = dd.getFullYear() + '-' + (dd.getMonth() + 1) + '-' + dd.getDate();
                if (dd < this.minimalDate) {
                    $('#dayCell-' + dateLabel).addClass('inactive');
                } else {
                    if ($('#dayCell-' + dateLabel).hasClass('inactive')) {
                        $('#dayCell-' + dateLabel).removeClass('inactive');
                    } else {
                        stop = true;
                        break;
                    }
                }
                dd.setDate(dd.getDate() + 1);
            }
        }
    }
}

VoyangaCalendarStandart.scrollToDate = function (dateVar, forceScroll) {
    if(VoyangaCalendarStandart.checkCalendarValue() && dateVar){
        if (!forceScroll) {
            forceScroll = false;
        }
        var dateLabel = dateVar.getFullYear() + '-' + (dateVar.getMonth() + 1) + '-' + dateVar.getDate();
        var scrollLine = $('#dayCell-' + dateLabel).parent().data('weeknum');
        var scrollTop = (scrollLine / this.slider.totalLines) * this.jObj.find('.calendarGridVoyanga').prop('scrollHeight');
        var deltaNeedScroll = scrollTop - this.jObj.find('.calendarGridVoyanga').scrollTop();
        var lineHeight = Math.round(this.jObj.find('.calendarGridVoyanga').prop('scrollHeight') / VoyangaCalendarStandart.slider.totalLines);
       // console.log('delta need scroll', deltaNeedScroll, lineHeight);
        if ((deltaNeedScroll < 0) || (deltaNeedScroll > VoyangaCalendarStandart.slider.linesWidth * lineHeight) || forceScroll) {
            var scrollVal = scrollTop - lineHeight;
            if(scrollVal < 0){
                scrollVal = 0;
            }
            this.jObj.find('.calendarGridVoyanga').scrollTop(scrollVal);
        }
    }
}

VoyangaCalendarStandart.newValueHandler = function (newCalendarValue) {
    //console.log('send new value handler, now values:', VoyangaCalendarStandart.values, ' calVal:', newCalendarValue);
    if (newCalendarValue.hotels) {
        VoyangaCalendarStandart.hotels = true;
        $('#voyanga-calendar').addClass('hotel');
    } else {
        VoyangaCalendarStandart.hotels = false;
        $('#voyanga-calendar').removeClass('hotel');
    }
    if (VoyangaCalendarStandart.lastFromOverCell) {
        VoyangaCalendarStandart.unsetCellFrom(VoyangaCalendarStandart.lastFromOverCell);
    }
    if (VoyangaCalendarStandart.lastToOverCell) {
        VoyangaCalendarStandart.unsetCellTo(VoyangaCalendarStandart.lastToOverCell);
    }
    if (newCalendarValue.values !== undefined && newCalendarValue.values.length == 0) {
        //console.log('values defined', newCalendarValue.values);
        newCalendarValue.values.push(new Date());
        VoyangaCalendarStandart.values = new Array();
        VoyangaCalendarStandart.values.push(new Date());
        newCalendarValue.from = new Date();
    }
    if (newCalendarValue.selectionIndex !== undefined) {
        VoyangaCalendarStandart.selectionIndex = newCalendarValue.selectionIndex;
    } else {
        VoyangaCalendarStandart.selectionIndex = false;
    }
    if (newCalendarValue.valuesDescriptions !== undefined) {
        VoyangaCalendarStandart.valuesDescriptions = newCalendarValue.valuesDescriptions;
    } else {
        VoyangaCalendarStandart.valuesDescriptions = ['', ''];
    }
    if (newCalendarValue.intervalDescription !== undefined && newCalendarValue.intervalDescription) {
        VoyangaCalendarStandart.intervalDescription = newCalendarValue.intervalDescription;
    } else {
        VoyangaCalendarStandart.intervalDescription = false;
    }
    //if((newCalendarValue.twoSelect == true) && (VoyangaCalendarStandart.twoSelect == false)){

    //}
    var dontset = true;
    VoyangaCalendarStandart.twoSelect = newCalendarValue.twoSelect;
    if (!newCalendarValue.twoSelect && VoyangaCalendarStandart.values.length > 0) {
        //VoyangaCalendarStandart.values = VoyangaCalendarStandart.values.slice(0, 1);
        VoyangaCalendarStandart.values = new Array(VoyangaCalendarStandart.values[0]);
        dontset = false;
    }
    //console.log('values(1):', VoyangaCalendarStandart.values);
    VoyangaCalendarStandart.minimalDateChanged = false;
    var needScroll = false;
    if (newCalendarValue.activeSearchPanel) {
        if (newCalendarValue.activeSearchPanel.prevSearchPanel()) {
            minDt = newCalendarValue.activeSearchPanel.prevSearchPanel().checkOut();
            if ((!VoyangaCalendarStandart.minimalDate && minDt) || (minDt && VoyangaCalendarStandart.minimalDate.toString() != minDt.toString())) {
                VoyangaCalendarStandart.minimalDate = minDt;
                needScroll = true;
                VoyangaCalendarStandart.scrollDate = VoyangaCalendarStandart.minimalDate;
                VoyangaCalendarStandart.minimalDateChanged = true;
            }
        } else {
            minDt = new Date();
            minDt.setHours(0);
            minDt.setMinutes(0);
            minDt.setSeconds(0);
            minDt.setMilliseconds(0);
            if ((!VoyangaCalendarStandart.minimalDate) || (VoyangaCalendarStandart.minimalDate && VoyangaCalendarStandart.minimalDate.toString() != minDt.toString())) {
                VoyangaCalendarStandart.minimalDate = minDt;
                needScroll = true;
                VoyangaCalendarStandart.scrollDate = VoyangaCalendarStandart.minimalDate;
                VoyangaCalendarStandart.minimalDateChanged = true;
            }
        }
        if (VoyangaCalendarStandart.alreadyInited && VoyangaCalendarStandart.minimalDateChanged) {
            VoyangaCalendarStandart.minimalDateUpdated();
        } else if (VoyangaCalendarStandart.alreadyInited) {
            //console.log('not inited', VoyangaCalendarStandart.minimalDateChanged);
        }
    } else {
        //console.log('else????')
    }
    //console.log('values(2):', VoyangaCalendarStandart.values);
    if ((newCalendarValue.from && !needScroll) || (newCalendarValue.from && newCalendarValue.from.toString() != VoyangaCalendarStandart.scrollDate.toString())) {
        needScroll = true;
        VoyangaCalendarStandart.scrollDate = newCalendarValue.from;
    }

    //console.log('values(3):', VoyangaCalendarStandart.values);
    //console.log('if conductions. newCalendarValue.twoSelect == false:', (newCalendarValue.twoSelect == false), 'VoyangaCalendarStandart.values.length,', VoyangaCalendarStandart.values.length);
    if ((newCalendarValue.twoSelect == false) && VoyangaCalendarStandart && VoyangaCalendarStandart.values && (VoyangaCalendarStandart.values.length == 1)) {
        //may be need compare with old from value
        dontset = false;
    } else {
        if ((newCalendarValue.twoSelect == true && (!newCalendarValue.from || !newCalendarValue.to) || (newCalendarValue.twoSelect == false && (!newCalendarValue.from) ) )) {
            if (VoyangaCalendarStandart != undefined)
                VoyangaCalendarStandart.values = new Array();
        }
        else {
            VoyangaCalendarStandart.values = new Array();
            VoyangaCalendarStandart.values.push(newCalendarValue.from);
            if (newCalendarValue.twoSelect)
                VoyangaCalendarStandart.values.push(newCalendarValue.to);
            dontset = false;
        }
    }
    //console.log('values(4):', VoyangaCalendarStandart.values);

    if (needScroll) {
  //      console.log('scrollDate', VoyangaCalendarStandart.scrollDate);
        VoyangaCalendarStandart.scrollToDate(VoyangaCalendarStandart.scrollDate);
    }
    VoyangaCalendarStandart.update(dontset);
}

VoyangaCalendarStandart.compareArrays = function (arr1, arr2) {
    if ((!arr1 && arr2) || (!arr2 && arr1)) {
        return false;
    }
    if ((typeof arr1 == 'object') && (typeof arr1 == typeof arr2) && (arr1.length == arr2.length)) {
        for (var propName in arr1) {
            if (arr1[propName] != arr2[propName]) {
                return false;
            }
        }
        return true;
    } else {
        return false;
    }
}
VoyangaCalendarStandart.compareCalendarValue = function (oldValue, newValue) {
    if ((!oldValue && newValue) || (!newValue && oldValue)) {
        return false;
    }
    for (var propName in newValue) {
        if (propName == 'from') {
            if ((oldValue[propName] != newValue[propName]) && (newValue[propName] != VoyangaCalendarStandart.values[0])) {
                return false;
            }
        } else if (propName == 'to') {
            if ((oldValue[propName] != newValue[propName]) && (newValue[propName] != VoyangaCalendarStandart.values[1])) {
                return false;
            }
        } else if (($.isArray(oldValue[propName]))) {
            if (!VoyangaCalendarStandart.compareArrays(oldValue[propName], newValue[propName])) {
                return false;
            }
        } else if (oldValue[propName] != newValue[propName]) {

            return false;
        }
    }
    return true;
}

VoyangaCalendarStandart.init = function (panel, element) {
    this.jObj = $(element);
    VoyangaCalendarStandart.slider.jObj = this.jObj;
    this.alreadyInited = false;
    this.minimalDateChanged = false;
    this.valuesDescriptions = ['', ''];
    this.intervalDescription = false;
    this.intervalWords = ['ночь', 'ночи', 'ночей'];
    this.intervalLastCell = false;
    this.selectionIndex = false;
    this.lastFromCell = false;
    this.lastToCell = false;
    this.lastFromOverCell = false;
    this.lastToOverCell = false;
    this.checkCalendarValue = ko.observable(true);
    this.isMobileDevice = DetectMobileQuick() || DetectTierTablet();
    var self = this;
   // console.log('ReINIT calendar');
    this.oldCalendarValue = null;
    if (!this.panel || (this.panel && panel() != this.panel() )) {
        this.panel = panel;
        panel.subscribe(function (newPanel) {
            if (newPanel.template) {
                if (VoyangaCalendarStandart.subscription)
                    VoyangaCalendarStandart.subscription.dispose();
                //console.log('iil be st new subscr');
                VoyangaCalendarStandart.subscription = newPanel.calendarValue.subscribe(
                    function (calendarValue) {
                        if (VoyangaCalendarStandart.checkCalendarValue() && !VoyangaCalendarStandart.compareCalendarValue(self.oldCalendarValue, calendarValue)) {
                  //          console.log('values not same, new value detected');
                            //VoyangaCalendarStandart.clear();
                            self.oldCalendarValue = calendarValue;
                            VoyangaCalendarStandart.newValueHandler(calendarValue);
                        }
                    }
                );
                self.oldCalendarValue = newPanel.calendarValue();
                VoyangaCalendarStandart.values = new Array();
                VoyangaCalendarStandart.newValueHandler(newPanel.calendarValue());
            }

        });
        newPanel = panel();
        panel.notifySubscribers(panel());
        /*if (newPanel.template) {
         if (VoyangaCalendarStandart.subscription)
         VoyangaCalendarStandart.subscription.dispose();
         VoyangaCalendarStandart.subscription = newPanel.calendarValue.subscribe(VoyangaCalendarStandart.newValueHandler);
         self.oldCalendarValue = newPanel.calendarValue();
         VoyangaCalendarStandart.newValueHandler(newPanel.calendarValue());
         }*/
    } else {
//        console.log('Else', panel, this.panel, this.panel(), panel());
    }

    VoyangaCalendarStandart.generateGrid();
    VoyangaCalendarStandart.slider.init();
    if (this.minimalDateChanged) {
        this.minimalDateUpdated();
    }
    this.alreadyInited = true;
}.bind(VoyangaCalendarStandart);
