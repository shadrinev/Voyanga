/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 02.08.12
 * Time: 13:58
 * To change this template use File | Settings | File Templates.
 */
constructorViewer = new Object();
constructorViewer.tabsJson = null;
constructorViewer.flightTypeConst = 1;
constructorViewer.hotelTypeConst = 2;
constructorViewer.init = function(){
    console.log(constructorViewer.tabsJson);
    for(var i in constructorViewer.tabsJson){
        var tabElem = constructorViewer.tabsJson[i];
        if(tabElem.info.type == 'flight'){
            if(tabElem.fill === false)
            {
                console.log('make flight request');

                var requestParams = {RouteForm: new Array(),
                    FlightForm: {flightClass: 'E',adultCount: 2,childCount: 0, infantCount:0}
                };
                var firstElem = true;
                console.log(tabElem);
                for(var j in tabElem.info.flights){
                    var flight = tabElem.info.flights[j];
                    var flightParams = new Object();
                    flightParams.departureDate = flight.departureDate;
                    flightParams.departureCityId = flight.cityFromId;
                    flightParams.arrivalCityId = flight.cityToId;
                    if(firstElem){
                        firstElem = false;
                        requestParams.adultCount = flight.adultCount;
                        requestParams.childCount = flight.childCount;
                        requestParams.infantCount = flight.infantCount;
                    }
                    console.log(flightParams);
                    requestParams.RouteForm.push(flightParams);
                }
                console.log(requestParams);
                //$.getJSON('/tour/constructor/flightSearch',requestParams,function(jData){console.log(jData);});

                $.ajax({
                url: '/tour/constructor/flightSearch',
                dataType: 'json',
                data: requestParams,
                context: $('#'+tabElem.id),
                timeout: 90000
                })
                .done(function(data) {
                    var html = flightSearchResult(data);
                    //console.log(data);
                    $(this).html(html);
                    cartElemId = $(this).attr('id');
                    cartElemId = cartElemId.slice(0,-4);

                    $(this).find('.buy').data('cartElemId',cartElemId);
                    $(this).find('.buy').live('click',function(){
                        var key1 = $(this).attr('href');
                        var pos = key1.indexOf('key/');
                        if(pos>0){
                            key1 = key1.slice(pos+4);
                        }
                        pos = key1.indexOf('_');
                        if(pos>0){
                            var searchId = key1.slice(0,pos);
                            var key2 = key1.slice(pos+1);
                        }
                        cartElemId = $(this).data('cartElemId');
                        $.getJSON('/tour/basket/fillCartElement/type/'+constructorViewer.flightTypeConst+'/cartElementId/'+cartElemId+'/key/'+key2+'/searchId/'+searchId)
                            .done(function(data){

                                console.log(data);
                            });

                        //console.log(key1);
                        //console.log(key2);
                        //console.log(searchId);
                        return false;
                    });
                    //$('#popupInfo').modal('hide');
                })
                .fail(function(data){
                    console.log(data);
                    if (data.statusText=='timeout')
                       data.responseText = 'Время ожидания запроса превышено.';
                        $(this).html('<div class=\"alert alert-error\">Произошла ошибка! Попробуйте <a id=\"repeatFlightSearch\" href=\"#\">повторить поиск</a>.<br>Текст ошибки:<br>'+data.responseText+'</div>');
                });
            }

        }
        if(tabElem.info.type == 'hotel'){
            console.log('hl');
        }
    }
}

$(window).load(function(){
    constructorViewer.init();

});
