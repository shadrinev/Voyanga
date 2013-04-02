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
        app.runWithModule('hotels');
        app.activeModule('hotels');
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
        $(".mapsBigAll").show();
        $(".mapsBigAll").animate({opacity: 1}, 700);
        var value = {lat: 52, lng: 10};
        waitElement(".mapsBigAll",function (element){
                gMap = new google.maps.Map(element[0], {'mapTypeControl': false, 'panControl': false, 'zoomControlOptions':{position: google.maps.ControlPosition.LEFT_TOP, style: google.maps.ZoomControlStyle.SMALL}, 'streetViewControl': false, 'zoom': 3, 'mapTypeId': google.maps.MapTypeId.TERRAIN, 'center': new google.maps.LatLng(value.lat, value.lng)});
        });


    };

    $(document).ready(function () {
        initLandingPage();
        //eventPhotos = new EventPhotoBox(window.eventPhotos);
    })

</script>
<div class="wrapper" style="height: 700px">
<div class="maps">
    <div class="innerBlockMain" style="margin: 0px auto 0;">
        <div class="mapsBigAll" style="display: none"></div>
    </div>
</div>
<div class="panel-index" style="bottom: -30px;">

    <div class="board" data-bind="style: {height: fakoPanel().height}">
        <div class="newTitleHead">
            <div class="leftPoint" data-bind="swapPanel: {to: fakoPanel().prevPanel}"><i data-bind='text: fakoPanel().prevPanelLabel'>Только отели</i><span></span></div>
            <h1 class="title"><span data-bind="html: fakoPanel().mainLabel"></span></h1>
            <div class="rightPoint" data-bind="swapPanel: {to: fakoPanel().nextPanel}"><span></span><i data-bind='text: fakoPanel().nextPanelLabel'>Только авиабилеты</i></div>
        </div>

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
                                                   data-bind="autocomplete: {source:'city/airport_req/1', iata: $data.city, readable: cityReadable, readableAcc: cityReadableAcc, readableGen: cityReadableGen, readablePre: cityReadablePre}"
                                                   autocomplete="off">
                                            <input type="text" tabindex="-1" class="input-path">

                                            <div class="date noDate"
                                                 data-bind="click: showCalendar, html:checkInHtml(), css: {'noDate': !checkIn()}"></div>
                                            <div class="date noDate"
                                                 data-bind="click: showCalendar, html:checkOutHtml(), css: {'noDate': !checkOut()}"></div>
                                        </div>
                                    </div>
                                </td>

                                <td class="tdPeople final">
                                    <span data-bind="template: {name: peopleSelectorVM.template, data: peopleSelectorVM}">
                                    </span>
                                </td>
                                <td class="tdButton">

                                    <div class="btn-find inactive"
                                         data-bind="click: navigateToNewSearch, css: {inactive: formNotFilled}"></div>

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
        <div class="hotel-ico"></div>
        <div class="clear"></div>
        <!-- BTN MINIMIZE -->

    </div>
    <!-- END PANEL -->
    </div>
    <!-- CALENDAR -->
    <div class="calenderWindow z-indexTop"
         data-bind="template: {name: 'calendar-template-hotel', afterRender: reRenderCalendar}"
         style="top: -302px; overflow: hidden; height: 341px;">
    </div>
    <!-- END CALENDAR -->
    </div>
</div>
<?php foreach ($hotelsCaches as $cityId => $hotelsInfo): ?>
<?php echo $this->renderPartial('//landing/_hotelList', array('city' => City::getCityByPk($cityId), 'hotelsInfo' => $hotelsInfo)); ?>
<?php endforeach; ?>

<!--<div class="sub-head event" style="height: auto;width: auto;" data-bind="css: {calSelectedPanelActive: !fakoPanel().calendarHidden()}">

<div class="board"  style="position: static;">
<div class="constructor" style="position: static;">-->



<!-- END CENTER BLOCK -->

<?php echo $this->renderPartial('//landing/_bestFlights', array('currentCity' => $currentCity, 'flightCacheFromCurrent' => $flightCacheFromCurrent)); ?>
<div class="headBlockTwo" style="margin-bottom: 60px">
    <div class="center-block textSeo">
        <br>
        <h1>Бронирование отелей онлайн</h1>

        <p>Voyanga.com — система онлайн бронирования отелей. Мы предоставляем сервис по бронированию гостиниц по 
            всему миру в режиме реального времени. Вы всегда можете быть уверены в качестве предоставляемых нами услуг. 
            Мы работаем с десятком различных поставщиков данных, сравнивая и выбирая для вас самые лучшие цены. В нашей базе
            более 200 тысяч отелей и благодаря инновационной системе ранжирования мы всегда предоставим необходимую информацию в 
            самом удобном виде с подробным описанием номеров, фотографиями и описанием услуг.
            
        <p>Наша креглосуточная служба поддержки всегда поможет с выбором подходящей гостиницы, проконсультирует с вариантами
            оплаты и ответит на все интересующие вопросы. Бронируйте отели самостоятельно вместе с нами. Благодаря нашему 
            <a href = 'http://voyanga.com/land/'>каталогу</a> по всем странам, вы можете нагладно увидеть все отели мира на одном сайте.
        </p>    
        
        
        <h2>Почему у нас самые низкие цены?</h2>
            Мы работаем с множеством различных систем бронирования отелей, а наш интеллектуальный алгоритм подбора всегда 
            учитывает цены из всех источников и предлагает самый оптимальный вариант. Мы работаем напрямую с множеством
            отелей, которые дают нам самые лучшие условия на рынке. Если вы где-то найдете цену на номер в отеле лучше, то
            вы всегда можете позвонить в нашу службу поддержки пользователей и мы предложим вам цену лучше. 
            Мы не берем никаких комиссий, у нас не существует скрытых платежей, мы заботимся о своих пользователях, 
            стараясь предоставить лучший сервис.
            
        <h2>Как забронировать гостиницу на сайте</h2>
        <p>
            Для того чтобы забронировать отель, выберите город а также задайте даты размещения, нажмите кнопку поиска. 
            Через несколько секунд на экране появится перечень гостиниц и номеров, доступных для бронирования. 
            Выберите интересующий вас отель, опираясь на информацию и отзывы об отелях. Для оформления 
            бронирования гостиницы вам остается только внести данные о себе и перейти к оплате. Подтверждение бронирования 
            гостиницы автоматически посылается в отель и отправляется вам на почту сразу же послезавершения оплаты.
        </p>
        
        <h2>Почему бронировать отели нужно на voyanga.com?</h2>

        <p>Мы предоставляем самый лучший сервис по бронированию гостиниц, делая все, чтобы вы смогли выбрать 
           отель на необходимые даты легко и быстро. Наш отдел по работе с пользователями предоставляет всю 
           необходимую информацию, а также помогает в решении любых вопросов. 
           Во-вторых, у нас самый удобный интерфейс по бронированию, где в удобном виде предоставляется самая необходимая информация. 
           В-третьих, у нас самая большая база отелей по всему миру и самые низкие цены. Как бы далеко вы ни собирались, 
           на Voyanga.com можно с легкостью выбрать и забронировать отель всего в несколько кликов.
           Ну и наконец, наш сайт полностю отвечает всем мировым стандартам безопасности, мы гарантируем безопасность проведения платежей.
        </p>

        <p>Так же мы предоставляем дополнительные услуги по бронированию <a href='http://voyanga.com'>авиабилетов</a>.</p>

    </div>
</div>
<div class="clear"></div>
