<script>
    //window.flightBestPrice = <?php //echo json_encode($flightCache); ?>;
    window.defaultCity = '<?php echo $currentCity->code; ?>';
    //window.apiEndPoint = 'http://api.oleg.voyanga';

    function setDepartureDate(strDate) {
        var checkIn = moment(strDate);
        window.app.fakoPanel().checkIn(checkIn._d);
        var checkOut = moment(checkIn);
        checkOut.add('days', 2);
        window.app.fakoPanel().checkOut(checkOut._d);

    }
    initLandingPage = function () {
        var app, avia, hotels, tour;
        window.voyanga_debug = function () {
            var args;
            args = 1 <= arguments.length ? __slice.call(arguments, 0) : [];
            return console.log.apply(console, args);
        };
        app = new Application();
        avia = new AviaModule();
        hotels = new HotelsModule();
        tour = new ToursModule();
        window.app = app;
        app.register('tours', tour, true);
        app.register('hotels', hotels);
        app.register('avia', avia);
        app.runWithModule('tours');
        app.activeModule('tours');
        var panelSet = new HotelsPanel();
        //panelSet.departureCity(window.defaultCity);
        panelSet.calendarActive(false);

        //panelSet.rt(false);
        //panelSet.sp.calendarActivated(false);
        app.fakoPanel(panelSet);

        setDepartureDate(moment(new Date()).add('days', 1).format('YYYY-MM-DD'));
        window.setTimeout(function () {
            panelSet.calendarActive(true);

        }, 1000);


        ko.applyBindings(app);
        ko.processAllDeferredBindingUpdates();
    };

    $(document).ready(function () {
        initLandingPage();
        //eventPhotos = new EventPhotoBox(window.eventPhotos);
    })

</script>
<?php foreach ($hotelsCaches as $cityId => $hotelsInfo): ?>
<?php echo $this->renderPartial('//landing/_hotelList', array('city' => City::getCityByPk($cityId), 'hotelsInfo' => $hotelsInfo)); ?>
<?php endforeach; ?>

<!--<div class="sub-head event" style="height: auto;width: auto;" data-bind="css: {calSelectedPanelActive: !fakoPanel().calendarHidden()}">

<div class="board"  style="position: static;">
<div class="constructor" style="position: static;">-->

<div class="sub-head event" style="height: auto;width: auto;"
     data-bind="css: {calSelectedPanelActive: !fakoPanel().calendarHidden()}">

    <div class="board" style="position: static;">
        <div class="constructor" style="position: static;">
            <!-- BOARD CONTENT -->
            <div class="board-content" data-bind="with: fakoPanel()" style="position: static;height: auto;">

                <div class="panel">
                    <table class="panelTable hotel">
                        <tr>
                            <td class="tdCityStart">
                                <span>Выберите город<br>200 000+ отелей</span>
                            </td>
                            <td class="tdCity">
                                <div class="data">
                                    <div class="from" data-bind="css: {active: checkIn()}">
                                        <div class="bgInput">
                                            <div class="left"></div>
                                            <div class="center"></div>
                                            <div class="right"></div>
                                        </div>
                                        <input type="text" placeholder="Куда едем?" class="second-path"
                                               data-bind="autocomplete: {source:'city/airport_req/1', iata: $data.city, readable: cityReadable, readableAcc: cityReadableAcc, readableGen: cityReadableGen}"
                                               autocomplete="off">
                                        <input type="text" tabindex="-1" class="input-path">

                                        <div class="date noDate"
                                             data-bind="click: showCalendar, html:checkInHtml(), css: {'noDate': !checkIn()}"></div>
                                        <div class="date noDate"
                                             data-bind="click: showCalendar, html:checkOutHtml(), css: {'noDate': !checkOut()}"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="tdAddTour">

                            </td>

                            <td class="tdPeople final">

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

                            </td>
                            <td class="tdButton">

                                <div class="btn-find inactive"
                                     data-bind="click: $parent.navigateToNewSearchMainPage, css: {inactive: $parent.formNotFilled}"></div>

                            </td>

                        </tr>
                    </table>
                </div>

            </div>
            <!-- END BOARD CONTENT -->
            <!-- ko with: fakoPanel() -->
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
                                    <input type="text" data-bind="value: children, css:{active: children}" name="adult2"
                                           class="">
                                    <a href="#" class="plusOne" data-bind="click:plusOne" rel="children">+</a>
                                    <a href="#" class="minusOne" data-bind="click:minusOne" rel="children">-</a>
                                </div>
                                детей от 12 до 18 лет
                            </div>
                        </div>
                        <div class="one-str" data-bind="foreach: ages, visible: ages().length"
                             style="display: none;"></div>
                        <a href="#" data-bind="click: addRoom, visible: last() &amp;&amp; index&lt;3"
                           class="addOtherRoom"><span class="ico-plus"></span>Добавить еще один номер.</a>
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

</div>
<!-- END PANEL -->
<!-- CALENDAR -->
<div class="calenderWindow z-indexTop"
     data-bind="template: {name: 'calendar-template-hotel', afterRender: reRenderCalendarStatic}"
     style="top: -302px; overflow: hidden; height: 341px; position: static;">
</div>
<!-- END CALENDAR -->


<!-- END CENTER BLOCK -->

<?php echo $this->renderPartial('//landing/_bestFlights', array('currentCity' => $currentCity, 'flightCacheFromCurrent' => $flightCacheFromCurrent)); ?>
<div class="headBlockTwo" style="margin-bottom: 60px">
    <div class="center-block textSeo">
        <h2>Что такое Voyanga</h2>

        <p>Voyanga.com — это самый простой, удобный и современный способ поиска и покупки авиабилетов. Мы постоянно
            работаем над развитием и улучшением сервиса. Наш сайт подключен сразу к нескольким системам бронирования,
            что позволяет сравнивать тарифы и подбирать наиболее выгодные и удобные тарифы и рейсы.</p>

        <p>Наша компания официально аккредитована в Международной ассоциации авиаперевозчиков (IATA) и в российской
            транспортной клиринговой палате (ТКП). Мы прошли все необходимые процедуры для оформления электронных
            билетов на рейсы российских и зарубежных авиакомпаний.</p>

        <p>Помимо сайта у нас есть собственная служба бронирования, которая находится в нашем офисе. Всегда можно
            позвонить и вам помогут и ответят на все вопросы. Офис компании находится в Санкт-Петербурге.</p>

        <h2>Как посетить 10 стран по цене Айфона</h2>

        <p>Voyanga.com — это самый простой, удобный и современный способ поиска и покупки авиабилетов. Мы постоянно
            работаем над развитием и улучшением сервиса. Наш сайт подключен сразу к нескольким системам бронирования,
            что позволяет сравнивать тарифы и подбирать наиболее выгодные и удобные тарифы и рейсы.</p>

        <p>Наша компания официально аккредитована в Международной ассоциации авиаперевозчиков (IATA) и в российской
            транспортной клиринговой палате (ТКП). Мы прошли все необходимые процедуры для оформления электронных
            билетов на рейсы российских и зарубежных авиакомпаний.</p>

        <p>Помимо сайта у нас есть собственная служба бронирования, которая находится в нашем офисе. Всегда можно
            позвонить и вам помогут и ответят на все вопросы. Офис компании находится в Санкт-Петербурге.</p>

    </div>
</div>
<div class="clear"></div>