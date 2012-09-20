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
constructorViewer.init = function () {
    console.log(constructorViewer.tabsJson);
    for (var i in constructorViewer.tabsJson) {
        var tabElem = constructorViewer.tabsJson[i];
        if (tabElem.info.type == 'flight') {
            if (tabElem.fill === false) {
                console.log('make flight request');
                var requestParams = {
                    RouteForm:new Array(),
                    FlightForm:{
                        flightClass:'E',
                        adultCount:2,
                        childCount:0,
                        infantCount:0
                    }
                };
                var firstElem = true;
                console.log(tabElem);
                for (var j in tabElem.info.flights) {
                    var flight = tabElem.info.flights[j],
                        flightParams = new Object();
                    flightParams.departureDate = flight.departureDate;
                    flightParams.departureCityId = flight.cityFromId;
                    flightParams.arrivalCityId = flight.cityToId;
                    if (firstElem) {
                        firstElem = false;
                        requestParams.FlightForm.adultCount = flight.adultCount;
                        requestParams.FlightForm.childCount = flight.childCount;
                        requestParams.FlightForm.infantCount = flight.infantCount;
                    }
                    requestParams.RouteForm.push(flightParams);
                }
                console.log(requestParams);

                $.ajax({
                    url:'/admin/tour/constructor/flightSearch',
                    dataType:'json',
                    data:requestParams,
                    context:$('#' + tabElem.id),
                    timeout:90000
                })
                    .done(function (data) {
                        var html = flightSearchResult(data);
                        //console.log(data);
                        $(this).html(html);
                        cartElemId = $(this).attr('id');
                        cartElemId = cartElemId.slice(0, -4);

                        $(this).find('.buy').data('cartElemId', cartElemId);
                        $(this).find('.buy').live('click', function () {
                            var key1 = $(this).attr('href'),
                                pos = key1.indexOf('key/'),
                                $this = $(this);
                            if (pos > 0) {
                                key1 = key1.slice(pos + 4);
                            }
                            pos = key1.indexOf('_');
                            if (pos > 0) {
                                var searchId = key1.slice(0, pos);
                                var key2 = key1.slice(pos + 1);
                            }
                            cartElemId = $(this).data('cartElemId');
                            $.getJSON('/admin/tour/basket/fillCartElement/type/' + constructorViewer.flightTypeConst + '/cartElementId/' + cartElemId + '/key/' + key2 + '/searchId/' + searchId)
                                .done(function (data) {
                                    $this.text('Выбрано').removeClass('btn-success').addClass('btn-danger');
                                    console.log(data);
                                })
                                .fail(function (data){
                                    alert('Произошла ошибка: ' + data);
                                });
                            return false;
                        });
                    })
                    .fail(function (data) {
                        console.log(data);
                        if (data.statusText == 'timeout')
                            data.responseText = 'Время ожидания запроса превышено.';
                        $(this).html('<div class=\"alert alert-error\">Произошла ошибка! Попробуйте <a id=\"repeatFlightSearch\" href=\"#\">повторить поиск</a>.<br>Текст ошибки:<br>' + data.responseText + '</div>');
                    });
            }

        }
        if (tabElem.info.type == 'hotel') {
            console.log('Searching hotel');
            if (tabElem.fill === false) {
                var requestParams = {
                    HotelRoomForm:new Array(),
                    HotelForm:{
                        cityId:tabElem.info.cityId,
                        fromDate:tabElem.info.checkIn,
                        duration:tabElem.info.duration
                    }
                };
                var rooms = tabElem.info.room;
                $.each(rooms, function (i, room) {
                    var roomParams = new Object();
                    roomParams.adultCount = room.adultCount;
                    roomParams.childCount = room.childCount;
                    roomParams.childAge = room.childAge;
                    roomParams.cots = room.cots;
                    requestParams.HotelRoomForm.push(roomParams);
                });

                $.ajax({
                    url:'/admin/tour/constructor/hotelSearch',
                    dataType:'json',
                    data:requestParams,
                    context:$('#' + tabElem.id),
                    timeout:90000
                })
                    .done(function (data) {
                        console.log(data);
                        var html = hotelSearchResult(data.hotels);
                        $(this).html(html);

                        cartElemId = $(this).attr('id');
                        cartElemId = cartElemId.slice(0, -4);

                        $(this).find('.choose').data('cartElemId', cartElemId);
                        $(this).find('.choose').data('cacheId', data.cacheId);
                        $(this).find('.choose').live('click', function () {
                            var cacheId = $(this).data('cacheId'),
                                resultId = $(this).data('resultid'),
                                $this = $(this);
                            btn = $(this);

                            cartElemId = $(this).data('cartElemId');
                            $.getJSON('/admin/tour/basket/fillCartElement/type/' + constructorViewer.hotelTypeConst + '/cartElementId/' + cartElemId + '/key/' + resultId + '/searchId/' + cacheId)
                                .done(function (data) {
                                    $this.text('Выбрано').removeClass('btn-success').addClass('btn-danger');
                                    console.log(data);
                                })
                                .fail(function (data){
                                    alert('Произошла ошибка: ' + data);
                                });
                            return false;
                        });


                    })
                    .fail(function (data) {
                        console.log(data);
                        if (data.statusText == 'timeout')
                            data.responseText = 'Время ожидания запроса превышено.';
                        $(this).html('<div class=\"alert alert-error\">Произошла ошибка! Попробуйте <a id=\"repeatFlightSearch\" href=\"#\">повторить поиск</a>.<br>Текст ошибки:<br>' + data.responseText + '</div>');
                    });
            }
            console.log(tabElem);
        }
    }
}

/*
 adultCount: 2
 checkIn: "13.09.2012"
 checkOut: "01.10.2012"
 childCount: 0
 cityId: "4787"
 infantCount: 0
 type: "hotel"
 HotelForm[cityId] =
 HotelForm[fromDate] =
 HotelForm[duration] =
 HotelRoomForm[0][adultCount]
 HotelRoomForm[0][childCount]
 HotelRoomForm[0][infantCount]
 HotelRoomForm[0][childAge]
 HotelRoomForm[0][cots]

 */
$(window).load(function () {
    constructorViewer.init();

});
