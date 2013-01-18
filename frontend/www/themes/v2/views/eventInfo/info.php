<?php
$images = '/themes/v2';
?>
<script>
    window.toursArr = <?php echo json_encode($tours); ?>;
    window.defaultCity = <?php echo $defaultCity; ?>;
    window.tripRaw = window.toursArr[window.defaultCity];
    window.eventPhotos = <?php echo json_encode($pictures); ?>;
    window.eventId = <?php echo $event->id; ?>;
    $(document).ready(function(){
        initEventPage();
        //eventPhotos = new EventPhotoBox(window.eventPhotos);
    })
</script>
<!-- EVENTS -->
<div class="events">
    <div class="center-block">
        <div class="main-block">
            <!-- eventsContent -->
            <div id="eventsContent">
                <h1><?php echo $event->title;?></h1>
                <em class="f17"><?php echo $event->preview;?></em>
                <div class="clear" style="margin-top: 20px"></div>
                <!-- ko if: itemsToBuy.photoBox.boxHeight() > 0 -->
                <div class="photoGallery" data-bind="template: {name: 'event-photo-box', data: itemsToBuy.photoBox, afterRender: itemsToBuy.photoBox.afterRender},style:{height: itemsToBuy.photoBox.boxHeight() + 'px'}">
                </div>
                <div data-bind="style:{height: itemsToBuy.photoBox.boxHeight()+'px'}" style="width: 100%"></div>
                <!-- /ko -->
                <!-- ko if: itemsToBuy.photoBox.boxHeight() == 0 -->
                <div class="photoAlbum">
                    <img src="<?php echo (isset($event->pictureBig) ? $event->imgSrc.$event->pictureBig->url : $event->defaultBigImageUrl);?>">
                </div>
                <!-- /ko -->

                <div class="clear" style="margin-top: 20px"></div>
                <div class="rightBlock">
                    <h3>Вылет из:</h3>
                    <div>
                    <select data-bind="slider: true, value: itemsToBuy.selectedCity">
                        <?php foreach($twoCities as $cityId=>$city):?>
                            <?php echo '<option value="'.$cityId.'" '.($defaultCity == $cityId ? 'selected="selected"' : '').'>'.$city['localRu'].'</option>';?>
                        <?php endforeach;?>
                    </select>
                    </div>
                    <a href="#" data-bind="click: itemsToBuy.gotoAndShowPanel" class="otherCity">Другой город</a>
                    <img src="/themes/v2/images/hr-gradient-events.png" style="margin-top: 5px;">
                    <div class="divPrice"><span class="price" data-bind="text: itemsToBuy.fullPrice()">15 600 </span> <span class="rur">o</span></div>
                    <a href="#" style="margin-top:0px;" class="otherCity" data-bind="text: itemsToBuy.overviewPricePeople(),click: itemsToBuy.gotoAndShowPanel">Цена за 2 взрослых</a>
                    <span class="check">Последняя проверка цены<br>выполнена 29 сентября, 18:04</span>
                    <img src="/themes/v2/images/hr-gradient-events.png" style="margin-top: 5px;">
                    <div style="width: 144px; height: 40px;">
                        <div class="btn-check" data-bind="click: itemsToBuy.activePanel().navigateToNewSearchMainPage,visible: !itemsToBuy.visiblePanel(), css: {inactive: itemsToBuy.activePanel().formNotFilled}"></div>
                    </div>
                </div>
                <div class="textBlock"><?php echo $event->description;?></div>
            </div>
            <!-- end eventsContent -->
        </div>
    </div>
</div>
<!-- END EVENTS -->
<!--====**********===-->
<!-- SUB HEAD -->

        <!-- ko if: itemsToBuy.correctTour() -->
        <!-- PANEL -->
        <div class="sub-head event" style="height: auto;width: auto;" data-bind="css: {calSelectedPanelActive: !itemsToBuy.activePanel().calendarHidden()}">

            <div class="board"  style="position: static;">
                <div class="constructor" style="position: static;">
                    <!-- BOARD CONTENT -->
                    <div class="board-content" data-bind="with: itemsToBuy.activePanel()" style="position: static;height: auto;">
                        <!-- ko template: {foreach: $data.panels, afterRender: $data.afterRender, beforeRemove: $data.beforeRemove} -->
                            <!-- ko if: $index()!=0 -->
                            <div class="deleteTab" data-bind="click: $parent.deletePanel"></div>
                            <!-- /ko -->
                        <div class="panel">
                            <table class="panelTable constructorTable">
                                <tr>
                                    <td class="tdCityStart">
                                        <div class="cityStart">
                                            <!-- ko if: $index()==0 || ($parent.isFirst()) -->
                                            <div class="to">
                                                Старт из:
                                                <a href="#"><span data-bind="click: showFromCityInput, text: $parent.startCityReadableGen">Санкт-Петербурга</span></a>
                                            </div>
                                            <div class="startInputTo">
                                                <div class="bgInput">
                                                    <div class="left"></div>
                                                    <div class="center"></div>
                                                    <div class="right"></div>
                                                </div>
                                                <input type="text" tabindex="-1" class="input-path" data-bind="blur: hideFromCityInput">
                                                <input type="text" placeholder="Санкт-Петербург" class="second-path" data-bind="blur: hideFromCityInput, autocomplete: {source:'city/airport_req/1', iata: $parent.startCity, readable: $parent.startCityReadable, readableAcc: $parent.startCityReadableAcc, readableGen: $parent.startCityReadableGen}" autocomplete="off" style="">
                                            </div>
                                            <!-- /ko -->
                                        </div>
                                    </td>
                                    <td class="tdCity">
                                        <div class="data">
                                            <div class="from" data-bind="css: {active: checkIn()}">
                                                <div class="bgInput">
                                                    <div class="left"></div>
                                                    <div class="center"></div>
                                                    <div class="right"></div>
                                                </div>
                                                <input type="text" placeholder="Куда едем?" class="second-path" data-bind="hasfocus: hasfocus, click: hideFromCityInput, autocomplete: {source:'city/airport_req/1', iata: $data.city, readable: cityReadable, readableAcc: cityReadableAcc, readableGen: cityReadableGen}, css: {isFirst: $parent.isFirst()}" autocomplete="off">
                                                <input type="text" tabindex="-1" class="input-path">

                                                <div class="date noDate" data-bind="click: showCalendar, html:checkInHtml(), css: {'noDate': !checkIn()}"></div>
                                                <div class="date noDate" data-bind="click: showCalendar, html:checkOutHtml(), css: {'noDate': !checkOut()}"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="tdAddTour">
                                        <!-- ko if: ($index()+1) == $length() -->
                                        <a href="#" class="add-tour" data-bind="click: $parent.addPanel, visible: !$parent.isMaxReached()"></a>
                                        <!-- /ko -->
                                    </td>

                                    <td class="tdPeople" data-bind="css: {final: ($index()+1) == $length(), notFinal: ($index()+1) != $length()}">
                                        <!-- ko if: ($index()+1) == $length() -->
                                        <span data-bind="template: { data: $data.peopleSelectorVM}">
                                          <div class="how-many-man hotel">
                                              <!-- ko foreach: rawRooms -->
                                              <div class="content" data-bind="click: $parent.showPeoplePopup">
                                                  <span class="num" data-bind="text: $index() + 1">1</span>
                                                  <div class="man" data-bind="repeat: adults"></div>
                                                  <div class="child" data-bind="repeat: children"></div>
                                              </div>
                                              <!-- /ko -->
                                              <div class="btn" data-bind="click: showPeoplePopup"></div>

                                           </div>
                                        </span>
                                        <!-- /ko -->
                                    </td>
                                    <td class="tdButton">
                                        <!-- ko if: ($index()+1) == $length() -->
                                        <div class="btn-find inactive" data-bind="click: $parent.navigateToNewSearchMainPage, css: {inactive: $parent.formNotFilled}"></div>
                                        <!-- /ko -->
                                    </td>

                                </tr>
                            </table>
                        </div>


                        <!-- /ko -->
                    </div>
                    <!-- END BOARD CONTENT -->
                    <!-- ko with: itemsToBuy.activePanel() -->
                        <!-- ko template: {foreach: $data.panels} -->
                            <!-- ko if: ($index()+1) == $length() -->
                                <!-- ko template: { data: $data.peopleSelectorVM }-->
                                <div class="popupPeople">
                                    <!-- ko foreach: {data: roomsView, afterRender: afterRenderPeoplePopup } -->
                                    <div class="float">
                                        <!-- ko foreach: $data -->
                                        <div class="number-hotel">
                                            <a href="#" class="del-hotel" data-bind="click:removeRoom">удалить</a>
                                            <h5>Номер <span data-bind="text: index + 1">1</span></h5>
                                            <div class="one-str">
                                                <div class="adults">
                                                    <div class="inputDIV">
                                                        <input type="text" data-bind="value: adults, css:{active: adults}" class="active">
                                                        <a href="#" class="plusOne" data-bind="click:plusOne" rel="adults">+</a>
                                                        <a href="#" class="minusOne" data-bind="click:minusOne" rel="adults">-</a>
                                                    </div>
                                                    взрослых
                                                </div>
                                                <div class="childs">
                                                    <div class="inputDIV">
                                                        <input type="text" data-bind="value: children, css:{active: children}" name="adult2" class="">
                                                        <a href="#" class="plusOne" data-bind="click:plusOne" rel="children">+</a>
                                                        <a href="#" class="minusOne" data-bind="click:minusOne" rel="children">-</a>
                                                    </div>
                                                    детей от 12 до 18 лет
                                                </div>
                                            </div>
                                            <div class="one-str" data-bind="foreach: ages, visible: ages().length" style="display: none;"></div>
                                            <a href="#" data-bind="click: addRoom, visible: last() &amp;&amp; index&lt;3" class="addOtherRoom"><span class="ico-plus"></span>Добавить еще один номер.</a>
                                        </div>
                                        <!-- /ko -->
                                    </div>
                                    <!-- /ko -->
                                </div>
                                <!-- /ko -->
                            <!-- /ko -->
                        <!-- /ko -->
                    <!-- /ko -->

                </div>



                <!-- END CONSTRUCTOR -->

            </div>
            <div class="clear"></div>
            <!-- BTN MINIMIZE -->
            <a href="#" class="btn-minimizePanel" data-bind="click: itemsToBuy.togglePanel,html: '<span></span>'+itemsToBuy.showPanelText()"><span></span></a>
            <div class="minimize-rcomended">
                <a href="#" class="btn-minimizeRecomended"> вернуть рекомендации</a>
            </div>
        </div>
        <!-- END PANEL -->
        <!-- CALENDAR -->
        <div class="calenderWindow z-indexTop" data-bind="template: {name: 'calendar-template-hotel', afterRender: reRenderCalendarEvent}" style="top: -302px; overflow: hidden; height: 341px;display:none;">
        </div>
        <!-- END CALENDAR -->
        <!-- /ko -->

    <!-- END CENTER BLOCK -->

<!--====**********===-->
<div class="center-block" data-bind="if: itemsToBuy.correctTour()">
    <div class="allTripEvent">
        <h2>Ваша поездка во всех подробностях</h2>
        <table class="allTripTable">
            <tr>
                <td class="firstTd">
                    <div data-bind="template: {name: 'print-event-trip', data: itemsToBuy}"></div>
                    <div class="hr-bg big">
                        <img width="100%" height="31" src="/themes/v2/images/shadow-hotel.png">
                    </div>
                    <div class="btn-check" data-bind="click: itemsToBuy.activePanel().navigateToNewSearchMainPage, css: {inactive: itemsToBuy.activePanel().formNotFilled}"></div>
                </td>
                <td class="secondTd">

                </td>
            </tr>
        </table>
    </div>
</div>
<script type="text/html" id="print-event-trip">
    <div class="allTrip" data-bind="foreach: items">
        <div class="block" data-bind="if: $index()==0">
            <div class="when">
                <div class="date" data-bind="html: $parent.dateHtml()">
                </div>
            </div>
            <div class="info">
                <div class="text">
                    <table class="headTitle">
                        <tr>
                            <td class="icoTD">
                                <div class="ico-hotel"></div>      </td>
                            <td class="nameTD">
                                <div class="title"><span  data-bind="text:$parent.startCity"> Санкт-Петербург</span><span class="f13"> &mdash; начало путешествия</span></div>
                            </td>
                            <td class="costTD"></td>
                            <td class="removeTD"></td>
                        </tr>
                    </table>
                </div>
                <div class="hr-bg">
                    <img src="<?php echo $images.'/images/shadow-hotel.png' ?>" width="100%" height="31">
                </div>
            </div>
        </div>
        <div class="block" data-bind=" css:{end: $index()==$length()-1}">
            <div class="when">
                <div class="date" data-bind="html:dateHtml(),attr: {'class': 'date '+ dateClass()} ">
                </div>
            </div>
            <div class="info">
                <div class="text">
                    <table class="headTitle">
                        <tr>
                            <td class="icoTD">
                                <div data-bind="css: {'ico-jet': isAvia(), 'ico-hotel': isHotel()}"></div>
                            </td>
                            <td class="nameTD">
                                <div class="title" data-bind="html: overviewText()"></div>
                            </td>
                            <td class="costTD">
                                <span data-bind="text: $parent.overviewPeople()"></span>
                                <span class="costs" data-bind="html: priceHtml()"></span>
                            </td>
                        </tr>
                    </table>
                    <div data-bind="template: {name: overviewTemplate, data: $data}"></div>
                    <!-- ЗДЕСЬ БИЛЕТ -->

                </div>
                <div class="hr-bg">
                    <img src="<?php echo $images.'/images/shadow-hotel.png' ?>" width="100%" height="31">
                </div>
            </div>
        </div>
    </div>
</script>
<script id="tours-event-avia-ticket" type="text/html">
    <div class="ticket-items">
        <div class="content">
            <div class="airlines">

            </div>
            <!-- END AIRLINES -->
            <div class="center-ticket">
                <div class="date-time-city">
                    <div class="start">
                        <div class="date" data-bind="text: departureDayMo()">
                            28 мая
                        </div>
                        <div class="time" data-bind="text: departureTime()">
                            21:20
                        </div>
                        <div class="city" data-bind="text: departureCity()">
                            Москва
                        </div>
                        <div class="airport" data-bind="text: departureAirport()">
                            Домодедово
                        </div>
                    </div>
                    <!-- END START -->
                    <div class="how-long">
                        <div class="time">
                            В пути <span data-bind="text: duration()">8 ч. 30 м.</span>
                        </div>
                        <div class="ico-path" data-bind="html: stopsRatio()">
                        </div>
                        <div class="path" data-bind="text:stopoverText(), css: {'no-wait': direct()}">
                        </div>
                    </div>
                    <!-- END HOW LONG -->
                    <div class="finish">
                        <div class="date" data-bind="text: arrivalDayMo()">
                            29 мая
                        </div>
                        <div class="time" data-bind="text: arrivalTime()">
                            00:50
                        </div>
                        <div class="city" data-bind="text: arrivalCity()">
                            Санкт-Петербург
                        </div>
                        <div class="airport" data-bind="text: arrivalAirport()">
                            Пулково
                        </div>
                    </div>
                    <!-- END FINISH -->
                    <div class="clear"></div>
                    <div class="airlinesLogo">
                        <img data-bind="attr: {'src': '/img/airline_logos/' + airline +'.png'}" >
                        <br>
                        <span data-bind="text:airlineName">Россия</span>
                    </div>
                </div>
                <!-- END DATE TIME CITY -->
                <!-- ko if: roundTrip -->
                <div class="line-two-ticket">
                    <span class="l"></span>
                    <span class="end"></span>
                    <span class="r"></span>
                </div>
                <div class="date-time-city">
                    <div class="start">
                        <div class="date" data-bind="text: rtDepartureDayMo()">
                            28 мая
                        </div>
                        <div class="time" data-bind="text: rtDepartureTime()">
                            21:20
                        </div>
                        <div class="city" data-bind="text: rtDepartureCity()">
                            Москва
                        </div>
                        <div class="airport" data-bind="text: rtDepartureAirport()">
                            Домодедово
                        </div>
                    </div>
                    <!-- END START -->
                    <div class="how-long">
                        <div class="time">
                            В пути <span data-bind="text: rtDuration()">8 ч. 30 м.</span>
                        </div>
                        <div class="ico-path" data-bind="html: rtStopsRatio()">
                        </div>
                        <div class="path" data-bind="text:rtStopoverText(), css: {'no-wait': rtDirect()}">
                        </div>
                    </div>
                    <!-- END HOW LONG -->
                    <div class="finish">
                        <div class="date" data-bind="text: rtArrivalDayMo()">
                            29 мая
                        </div>
                        <div class="time" data-bind="text: rtArrivalTime()">
                            00:50
                        </div>
                        <div class="city" data-bind="text: rtArrivalCity()">
                            Санкт-Петербург
                        </div>
                        <div class="airport" data-bind="text: rtArrivalAirport()">
                            Пулково
                        </div>
                    </div>
                    <!-- END FINISH -->
                    <div class="clear"></div>
                    <div class="airlinesLogo">
                        <img data-bind="attr: {'src': '/img/airline_logos/' + airline +'.png'}" >
                        <br>
                        <span data-bind="text:airlineName">Россия</span>
                    </div>
                </div>
                <!-- END DATE TIME CITY -->
                <!-- /ko -->

            </div>
            <div class="buy-ticket">
                <div class="text">
                    <!-- FIXME -->
                    <span class="txtBuy" data-bind="text: price"></span> <span class="rur">o</span>
                </div>
            </div>
            <!-- END BUY TICKET -->
            <div class="clear"></div>
        </div>

        <span class="lt"></span>
        <span class="rt"></span>
        <span class="lv"></span>
        <span class="rv"></span>
        <span class="bh"></span>
    </div>
</script>
<script id="tours-event-hotels-ticket" type="text/html">
    <div class="hotels-tickets">
        <div class="content">
            <div class="full-info">
                <div class="preview-photo">
                    <ul>
                        <li><a href="#" data-bind="click: showPhoto,attr: {'href': frontPhoto.largeUrl}" class="photo"><img data-bind="attr:{src: frontPhoto.largeUrl}"></a></li>
                    </ul>
                    <div class="how-much" data-bind="visible: numPhotos">
                        <a href="#">Фотографий (<span data-bind="text: numPhotos">11</span>)</a>
                    </div>
                </div>
                <div class="description">
                    <div class="title">
                        <h2><span data-bind="text:hotelName">Рэдиссон Соня Отель</span> <span class="gradient"></span></h2>
                        <div data-bind="attr: {'class': 'stars ' + stars}"></div>
                    </div>
                    <div class="place">
                        <div class="street">
                            <span data-bind="text:address">Санкт-Петребург. ул. Морская Набережная, 31/2</span>
                            <span class="gradient"></span>
                        </div>
                        <a href="#"  data-bind="click: showMapDetails" class="in-the-map"><span class="ico-see-map"></span> <span class="link">На карте</span></a>
                    </div>
                    <div class="text">
                        <span data-bind="html: limitDesc.startText">Этот 4-звездочный отель расположен рядом с площадью Победы и парком Городов-Героев. К услугам гостей большой крытый бассейн и номера с телевизорами с плоским экраном...</span><span data-bind="visible: limitDesc.isBigText">...</span>
                    </div>
                </div>
                <div class="choose-a-hotel">
                    <div class="rating"  data-bind="visible: rating">
                        <div class="textRating" onmouseover="ratingHoverActive(this)" onmouseout="ratingHoverNoActive(this)">
                            <span class="value" data-bind="text: rating"></span>
                            <span class="text" data-bind="html: ratingName">рейтинг<br>отеля</span>
                        </div>
                        <div class="descrRating">
                            <strong><span data-bind="text: rating"></span> из 5 баллов</strong>
                            Рейтинг построен на основе анализа данных о качестве отеля и отзывах его посетителей.
                        </div>
                    </div>
                    <a class="details" data-bind="click: showDetails" href="#">Подробнее об отеле</a>
                </div>
                <div class="clear"></div>
            </div>
            <div class="details">
                <ul>
                    <li  class="not-show" data-bind="template: {name: 'hotel-roomSet-template', data: roomSets()[0]}" />
                </ul>
                <!-- div class="tab-ul" data-bind="visible: visibleRoomSets().length > 2">
                   <a href="#" data-bind="click: showAllResults,text: showAllText(),attr: {'class': isShowAll() ? 'active' : ''}">Посмотреть все результаты</a>
               </div -->
                <span class="lv"></span>
                <span class="rv"></span>
            </div>
        </div>
        <span class="lt"></span>
        <span class="rt"></span>
        <span class="lv"></span>
        <span class="rv"></span>
        <span class="bh"></span>
    </div>

</script>
<script id="event-photo-box" type="text/html">

            <div class="leftButton" data-bind="click: prev"></div>
            <div class="centerPosition"></div>
            <div class="rightButton" data-bind="click: next"></div>

</script>