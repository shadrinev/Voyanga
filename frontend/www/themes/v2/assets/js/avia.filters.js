/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 30.08.12
 * Time: 14:12
 * To change this template use File | Settings | File Templates.
 */
function AviaFilter (options){
    var defaults = {
        visible: true,
        jObj: null,
        objName: null,
        //onchange: function (){},
        //change: function(){},
        init: function(){},
        hide:function(){
            this.jObj.parent().hide();
        },
        show: function(){
            this.jObj.parent().show();
        }
    };
    options = $.extend({},defaults,options);
    for(key in options){
        this[key] = options[key];
    }
    //this.options = options;
}
AviaFilter.prototype.getValue = function(){
    return this.jObj.val();
}
AviaFilter.prototype.onchange = function(){
    AviaFilters.onchange();
}
AviaFilter.prototype.change = function(value){
    this.jObj.val(value);
}

function getAviaFilterTimeSlider(options){
    var defaults = {
        init: function(options){
            var self = this;
            this.jObj = $(this.objName);
            //console.log(this.jObj);
            var fromTime = '0:00';
            var toTime = '23:59';
            if( options != undefined && options.fromTime != undefined ){
                fromTime = options.fromTime;
            }
            if( options != undefined && options.toTime != undefined ){
                toTime = options.toTime;
            }

            if(typeof fromTime == 'string' && fromTime.indexOf(':') != -1){
                var time_arr = fromTime.split(':');
                var fromVal = time_arr[0]*60 + parseInt(time_arr[1]);
            }else{
                var fromVal = fromTime;
            }
            if(typeof toTime == 'string' && toTime.indexOf(':') != -1){
                time_arr = toTime.split(':');
                var toVal = time_arr[0]*60 + parseInt(time_arr[1]);
            }else{
                var toVal = toTime;
            }
            if((fromVal - 15) >= 0){fromVal = fromVal - 15;}
            if((toVal + 15) <= 1440){toVal = toVal + 15;}

            this.jObj.val(fromVal+';'+toVal);

            this.jObj.slider({
                from: fromVal,
                to: toVal,
                step: 15,
                dimension: '',
                skin: 'round_voyanga',
                scale: false,
                limits: false,
                minInterval: 60,
                calculate: self.calculate,
                onstatechange: function( value ){
                    //console.dir( this );
                    //console.log(value);
                    return false;
                },
                callback: function(){self.onchange();}
            });
            if( options != undefined && options.visible != undefined ){
                if(options.visible == false){
                    this.jObj.parent().hide();
                }
            }
        },
        calculate: function( value ){
            var hours = Math.floor( value / 60 );
            var mins = ( value - hours*60 );
            return (hours < 10 ? "0"+hours : hours) + ":" + ( mins == 0 ? "00" : mins );
        },
        onchange: function (){
            //console.log(this);
            //console.log(this.jObj.val());
            this.jObj.change();
        },
        getValue:function(){
            var val = this.jObj.val();
            var times_arr = val.split(';');
            return {fromTime: times_arr[0],toTime: times_arr[1]}
        },
        hide:function(){
            this.jObj.parent().hide();
        },
        show: function(){
            this.jObj.parent().show();
            //console.log(this.jObj.slider());
            this.jObj.slider().onresize();
        }
    };
    options = $.extend({},defaults,options);
    return new AviaFilter(options);
}

AviaFilters = new Object();
AviaFilters.rt = false;//Round trip
AviaFilters.onchange = function (){
    console.log('filters changed');
}
AviaFilters.getValues = function(){
    var ret = new Object();
    ret.flightClassFilter = AviaFilters.flightClassFilter.getValue();
    ret.onlyDirectFlightsFilter = AviaFilters.onlyDirectFlightsFilter.getValue();
    ret.departureTimeSliderDirect = AviaFilters.departureTimeSliderDirect.getValue();
    ret.arrivalTimeSliderDirect = AviaFilters.arrivalTimeSliderDirect.getValue();
    if(AviaFilters.rt){
        ret.departureTimeSliderReturn = AviaFilters.departureTimeSliderReturn.getValue();
        ret.arrivalTimeSliderReturn = AviaFilters.arrivalTimeSliderReturn.getValue();
    }

    return ret;
}
AviaFilters.init = function(options){
    if(options == undefined) options = {};
    console.log('start render filters');
    console.log(options);
    AviaFilters.flightClassFilter.init(options.flightClassFilter);
    AviaFilters.onlyDirectFlightsFilter.init(options.onlyDirectFlightsFilter);
    AviaFilters.showReturnFilters.init();
    AviaFilters.departureTimeSliderDirect.init(options.departureTimeSliderDirect);
    AviaFilters.arrivalTimeSliderDirect.init(options.arrivalTimeSliderDirect);
    AviaFilters.departureTimeSliderReturn.init(options.departureTimeSliderReturn);
    AviaFilters.arrivalTimeSliderReturn.init(options.arrivalTimeSliderReturn);
    if(options.rt == true){
        AviaFilters.rt = true;
        AviaFilters.departureTimeSliderDirect.show();
        AviaFilters.arrivalTimeSliderDirect.show();
        AviaFilters.departureTimeSliderReturn.hide();
        AviaFilters.arrivalTimeSliderReturn.hide();
    }else{
        AviaFilters.rt = false;
        //AviaFilters.showReturnFilters.hide();
        AviaFilters.departureTimeSliderReturn.hide();
        AviaFilters.arrivalTimeSliderReturn.hide();
        AviaFilters.showReturnFilters.hide();
    }
}
AviaFilters.show = function(){

}
AviaFilters.hide = function(){

}
AviaFilters.flightClassFilter = new AviaFilter({
    objName: '#aviaFlightClass',
    init: function(options){
        this.jObj = $(this.objName);
        if( options != undefined && options.value != undefined ){
            this.jObj.val(options.value);
        }
        this.jObj.selectSlider({});
        self = this;
        this.jObj.on('change',self.onchange);
    }
});
AviaFilters.onlyDirectFlightsFilter = new AviaFilter({
    objName: '#aviaOnlyDirectFlights',
    init: function(options){
        this.jObj = $(this.objName);
        if( options != undefined && options.value != undefined ){
            this.jObj.val(options.value);
        }
        this.jObj.selectSlider({});
        self = this;
        this.jObj.on('change',self.onchange);
    }
});

AviaFilters.showReturnFilters = new AviaFilter({
    objName: '#aviaShowReturnFilters',
    init: function(options){
        this.jObj = $(this.objName);
        if( options != undefined && options.value != undefined ){
            this.jObj.val(options.value);
        }
        this.jObj.selectSlider({});
        self = this;
        this.jObj.on('change',self.onchange);
    },
    onchange: function (){
        //console.log($(this).val());
        if($(this).val() == 1){
            AviaFilters.arrivalTimeSliderDirect.hide();
            AviaFilters.departureTimeSliderDirect.hide();
            AviaFilters.departureTimeSliderReturn.show();
            AviaFilters.arrivalTimeSliderReturn.show();
        }else{
            AviaFilters.departureTimeSliderReturn.hide();
            AviaFilters.arrivalTimeSliderReturn.hide();
            AviaFilters.arrivalTimeSliderDirect.show();
            AviaFilters.departureTimeSliderDirect.show();
        }
        //console.log(this.jObj.val());
    }
});

AviaFilters.departureTimeSliderDirect = getAviaFilterTimeSlider({objName: '#departureTimeSliderDirect'});

AviaFilters.arrivalTimeSliderDirect = getAviaFilterTimeSlider({objName: '#arrivalTimeSliderDirect'});

AviaFilters.departureTimeSliderReturn = getAviaFilterTimeSlider({objName: '#departureTimeSliderReturn'});

AviaFilters.arrivalTimeSliderReturn = getAviaFilterTimeSlider({objName: '#arrivalTimeSliderReturn'});



$(document).ready(function (){
    /*AviaFilters.init({
        flightClassFilter:{
            value: 'E'
        },
        departureTimeSliderDirect:{
            fromTime: 8*60,
            toTime: 21*60
        }
    });*/
});
