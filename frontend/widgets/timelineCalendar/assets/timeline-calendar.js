/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 14.09.12
 * Time: 14:40
 * To change this template use File | Settings | File Templates.
 */


VoyangaCalendarTimeline = new VoyangaCalendarClass({jObj:'#voyanga-calendar',values:new Array(),twoSelect: true});
VoyangaCalendarTimeline.slider = new VoyangaCalendarSlider({
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
        //VoyangaCalendar.jObj.find('.calendarGrid').on('mousewheel',VoyangaCalendar.slider.mousewheelEvent);
        //VoyangaCalendar.jObj.find('.calendarGrid').on('DOMMouseScroll',VoyangaCalendar.slider.mousewheelEvent);
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
console.log(this.jObj);
VoyangaCalendarTimeline.onCellOver = function(obj,e){
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
VoyangaCalendarTimeline.onCellOut = function(obj,e){
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
VoyangaCalendarTimeline.getCellByDate = function(oDate){
    var dateLabel = oDate.getFullYear()+'-'+(oDate.getMonth()+1)+'-'+oDate.getDate();
    return $('#dayCell-'+dateLabel);
}
VoyangaCalendarTimeline.onCellClick = function(obj,e){
    var jCell = $(obj);
    if(!jCell.hasClass('inactive')){
        var cellDate = Date.fromIso(jCell.data('cell-date'));
        if(this.twoSelect){
            if(this.values.length == 2){

                this.getCellByDate(this.values[0]).removeClass('selectData from startMonth');
                var tmpDate = new Date(this.values[0].toDateString());
                tmpDate.setDate(tmpDate.getDate()+1);
                while(tmpDate < this.values[1]){
                    this.getCellByDate(tmpDate).removeClass('selectDay');
                    tmpDate.setDate(tmpDate.getDate()+1);
                }

                this.getCellByDate(this.values[1]).removeClass('selectData to startMonth');
                this.values = new Array();
            }else if(this.values.length == 1){

                if(cellDate < this.values[0]){
                    this.getCellByDate(this.values[0]).removeClass('selectData from startMonth');
                    this.values = new Array();
                }else{
                    this.values.push(cellDate);
                    jCell.addClass('selectData to');
                    if(cellDate.getDate() == 1){
                        jCell.addClass('startMonth');
                    }
                    var tmpDate = new Date(this.values[0].toDateString());
                    tmpDate.setDate(tmpDate.getDate()+1);
                    while(tmpDate < this.values[1]){
                        this.getCellByDate(tmpDate).addClass('selectDay');
                        tmpDate.setDate(tmpDate.getDate()+1);
                    }
                }

            }
        }else{
            if(this.values.length == 1){
                this.getCellByDate(this.values[0]).removeClass('selectData from startMonth');
                this.values = new Array();
            }
        }
        if(this.values.length == 0){
            this.values.push(cellDate);
            jCell.addClass('selectData from');
            if(cellDate.getDate() == 1){
                jCell.addClass('startMonth');
            }
        }
    }
}
VoyangaCalendarTimeline.generateGrid = function(){
    var firstDay = new Date();
    //var firstDay = new Date('2012-04-10');
    var dayToday = new Date();
    dayToday.setMinutes(0,0,0);
    dayToday.setHours(0);
    //dayToday.setSeconds(0);

    var self = this;


    var startMonth = firstDay.getMonth();
    var tmpDate = new Date(firstDay.toDateString());
    tmpDate.setDate(1);
    var weekDay = this.getDay(tmpDate);
    //console.log(weekDay);
    var startDate = firstDay.getDate();
    var startYear = firstDay.getFullYear();
    //console.log(tmpDate);
    tmpDate.setDate(-this.getDay(tmpDate) + 1);
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
                label = label + ' <div class="monthLabel">' + this.monthNames[tmpDate.getMonth()] +'</div>';
                var monthObject = new Object();
                monthObject.line = lineNumber;
                monthObject.name = this.monthNames[tmpDate.getMonth()];
                this.slider.monthArray.push(monthObject);
            }
            var dateLabel = tmpDate.getFullYear()+'-'+(tmpDate.getMonth()+1)+'-'+tmpDate.getDate();
            newHtml = newHtml + '<div class="dayCellVoyanga'+((tmpDate < dayToday) ? ' inactive' : '')+'" id="dayCell-'+dateLabel+'" data-cell-date="'+dateLabel+'"><div class="innerDayCellVoyanga">'+label+'</div></div>';
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
    /*this.jObj.find('.dayCellVoyanga').on('mouseover',function (e) {var obj = this; self.onCellOver(obj,e);});
     this.jObj.find('.dayCellVoyanga').on('mouseout',function (e) {var obj = this; self.onCellOut(obj,e);});*/
    this.jObj.find('.dayCellVoyanga').hover(function (e) {var obj = this; self.onCellOver(obj,e);},function (e) {var obj = this; self.onCellOut(obj,e);});
    this.jObj.find('.dayCellVoyanga').on('click',function (e) {var obj = this; self.onCellClick(obj,e);});

    this.slider.totalLines = lineNumber;
    console.log(this.slider.totalLines);
}


VoyangaCalendarTimeline.init = function (){
    this.slider.jObj = this.jObj;
    if(typeof this.jObj == 'string'){
        this.jObj = $(this.jObj);
    }

    this.generateGrid();
    //return true;
    this.slider.init();

}