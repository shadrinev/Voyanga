<?php //print_r($flightCache);?>
<script>
    //window.flightBestPrice = <?php //echo json_encode($flightCache); ?>;
    window.defaultCity = '<?php echo $currentCity->code; ?>';
    window.pointCity = '<?php echo $city->code; ?>';

    window.flightCache = <?php echo json_encode($flightCache);?>;
    window.bestDateData = <?php echo json_encode($flightCacheBestPrice);?>;

    function setDepartureDate(strDate){
        window.app.fakoPanel().departureDate(moment(strDate)._d);
    }
    function setBackDate(strDate){
        window.app.fakoPanel().rtDate(moment(strDate)._d);
    }
    initLandingPage = function() {
        var app, avia, hotels, tour;
        window.voyanga_debug = function() {
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
        var panelSet = new AviaPanel();
        panelSet.departureCity(window.defaultCity);
        panelSet.arrivalCity(window.pointCity);
        var landBP = new landBestPriceSet(window.flightCache);
        landBP.setDirectBestPrice(window.bestDateData);

        app.landBP = landBP;

        panelSet.rt(true);
        //panelSet.sp.calendarActivated(false);
        app.fakoPanel(panelSet);
        //setDepartureDate('<?php //echo $flightCache[$activeMin]['date'];?>');


        ko.applyBindings(app);
        ko.processAllDeferredBindingUpdates();
    };

    $(document).ready(function(){
        initLandingPage();
        //eventPhotos = new EventPhotoBox(window.eventPhotos);
    })

</script>
<div class="headBlockOne">
    <div class="center-block">
        <h1>Авиабилеты в <?php echo $city->caseAcc;?></h1>
        <h3>Стоимость на месяц вперед из
            <?php
            foreach($citiesFrom as $cityPoint):
                ?>
                <a href="#" class="cityChoise<?php echo $cityPoint['cityId']==$currentCity->id ? ' active':'';?>">
                    <span><?php echo $cityPoint['cityName'];?></span>
                </a>
                <?php

            endforeach;
            ?>
        </h3>
    </div>
    <div class="center-block">
        <div class="floatLeft">
            <ul class="grafik first-child" data-bind="foreach: landBP.datesArr">
                <!-- ko if: landBP -->
                    <li class="grafikMean" data-bind="css: 'grafikMean' + (landBP.selected() ? ' active' : ''), click: landBP.selectThis"">
                        <div class="price" style="bottom: 30px" data-bind="style: { bottom: landBP.showWidth() + 'px'}, text: landBP.showPriceText()"></div>
                        <div class="statusBar" style="height: 30px" data-bind="style: { height: landBP.showWidth() + 'px'}"></div>
                    </li>
                <!-- /ko -->
                <!-- ko ifnot: landBP -->
                <li class="grafikMean inactive">
                    <div class="price question" style="bottom: 0px; left: 0px;">?</div>
                </li>
                <!-- /ko -->
            </ul>
        </div>
        <div class="floatRight textBlockPrice">
            <div class="cena"> от <span class="price" data-bind="text: landBP.selectedPrice()">3 250</span> <span class="rur">o</span></div>
            <div>
                Самая низкая цена<br>
                по этому направлению:<br>
                <a href="#" data-bind="text: landBP.bestDate(),click: landBP.bestDateClick">12 июня 2012</a>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <hr>
    <div class="center-block">
        <div class="floatLeft">
            <ul class="grafik second-child" data-bind="foreach: landBP.active().results()">
                <!-- ko if: landBP -->
                <li class="grafikMean" data-bind="css: 'grafikMean' + (landBP.selected() ? ' active' : ''), click: landBP.selectThis">
                    <div class="price" style="bottom: 30px" data-bind="style: { bottom: landBP.showWidth() + 'px'}, text: landBP.showPriceText()"></div>
                    <div class="statusBar" style="height: 30px" data-bind="style: { height: landBP.showWidth() + 'px'}"></div>
                </li>
                <!-- /ko -->
                <!-- ko ifnot: landBP -->
                <li class="grafikMean inactive">
                    <div class="price question" style="bottom: 0px; left: 0px;">?</div>
                </li>
                <!-- /ko -->
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div class="bgDate">
    <div class="center-block">
    <div class="floatLeft">
        <ul class="date" data-bind="foreach: landBP.datesArr">
            <li data-bind="css: monthChanged ? 'newMonth' : ''">
                <div class="month" data-bind="text: monthName,visible: monthName">Май</div>
                <div data-bind="html: dateText,css: 'day'+ (today ? ' today' : '')">
                    пн <br>
                    <span>16</span>
                </div>
            </li>
        </ul>
    </div>
    </div>
    </div>

</div>

<div class="sub-head event" style="height: auto;width: auto;" data-bind="css: {calSelectedPanelActive: !fakoPanel().calendarHidden()}">

    <div class="board"  style="position: static;">
        <div class="constructor" style="position: static;">
            <!-- BOARD CONTENT -->
            <div class="panel" data-bind="template: {data: fakoPanel(), afterRender: fakoPanel().afterRender }">
                <table class="panelTable avia">
                    <tbody><tr>
                        <td class="tdCityStart">
                            Все направления<br>
                            500+ авиакомпаний
                        </td>
                        <td class="tdCity">

                            <div class="data" style="width: 247.5px;">
                                <div class="from" data-bind="css: {active: fromChosen}">
                                    <div class="bgInput">
                                        <div class="left"></div>
                                        <div class="center"></div>
                                        <div class="right"></div>
                                    </div>
                                    <input class="input-path departureCity" type="text" tabindex="-1" style="width: 227.5px;">
                                    <input class="second-path departureCity" type="text" placeholder="Откуда" data-bind="autocomplete: {source:'city/airport_req/1', iata: departureCity, readable: departureCityReadable, readableAcc: departureCityReadableAcc, readableGen: departureCityReadableGen}" style="width: 227.5px;" autocomplete="off">
                                    <div class="date" data-bind="click: showCalendar">
                                        <span class="f17" data-bind="text: departureDateDay()"></span>
                                        <br>
                                        <span class="month" data-bind="text: departureDateMonth()"></span>
                                    </div>
                                </div>
                            </div></td>
                        <td class="tdTumblr">
                            <div class="tumblr">
                                <label for="there-back">
                                    <div class="one" data-bind="css: {active: !rt()}, click: selectOneWay"></div>
                                    <div class="two active" data-bind="css: {active: rt()}, click: selectRoundTrip"></div>
                                    <div class="switch" style="left: 35px;"></div>
                                </label>
                                <input id="there-back" type="checkbox" data-bind="checked: rt()">
                            </div>
                        </td>
                        <td class="tdCity">
                            <div class="data" style="width: 247.5px;">
                                <div class="to" data-bind="css: {active: rtFromChosen}">
                                    <div class="bgInput">
                                        <div class="left"></div>
                                        <div class="center"></div>
                                        <div class="right"></div>
                                    </div>
                                    <input class="input-path arrivalCity" type="text" tabindex="-1" style="width: 227.5px;">
                                    <input class="second-path arrivalCity" placeholder="Куда" data-bind="autocomplete: {source:'city/airport_req/1', iata: arrivalCity, readable: arrivalCityReadable, readableAcc: arrivalCityReadableAcc, readableGen: arrivalCityReadableGen}" style="width: 227.5px;" autocomplete="off">
                                    <div class="date" data-bind="click: showCalendar">
                                        <span class="f17" data-bind="text: rtDateDay()"></span>
                                        <br>
                                        <span class="month" data-bind="text: rtDateMonth()"></span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="tdPeople">
                        <span data-bind="template: { data: passengers,afterRender: passengers.afterRenderPeoplePopup}">
                            <div class="how-many-man">
                                <div class="content active" data-bind="click: showPeoplePopup">
                                    <!-- ko if: overall()>5 -->
                                    <!-- ko if: adults()>0 -->
                                    <div class="man"></div>
                                    <div class="count"><span>x</span><i data-bind="text: adults()"></i></div>
                                    <!-- /ko -->
                                    <!-- ko if: (sum_children())>0 -->
                                    <div class="child"></div>
                                    <div class="count"><span>x</span><i data-bind="text: sum_children()"></i></div>
                                    <!-- /ko -->
                                    <!-- /ko -->
                                    <!-- ko if: overall()<=5 -->
                                    <div class="man" data-bind="repeat: adults"></div>
                                    <div class="child" data-bind="repeat: sum_children"></div>
                                    <!-- /ko -->
                                </div>
                                <div class="btn active" data-bind="click: showPeoplePopup"></div>

                            </div>
                        </span>
                        </td>
                        <td class="tdButton">
                            <a class="btn-find inactive" data-bind="click: navigateToNewSearch, css: {inactive: formNotFilled}">Найти</a>
                        </td>
                    </tr>
                    </tbody></table>
            </div>


            <!-- END BOARD CONTENT -->
            <!-- ko with: fakoPanel() -->
            <!-- ko with: passengers -->
            <div class="popupPeople active avia" style="display: none;">
                <div class="adults">
                    <div class="inputDIV">
                        <input type="text" name="adult" data-bind="css: {active: adults() > 0}, value: adults" class="active">
                        <a href="#" class="plusOne" data-bind="click: plusOne" rel="adults" style="display: none;">+</a>
                        <a href="#" class="minusOne" data-bind="click: minusOne" rel="adults" style="display: none;">-</a>
                    </div>
                    взрослых
                </div>
                <div class="childs">
                    <div class="inputDIV">
                        <input type="text" name="adult2" data-bind="css: {active: children() > 0}, value: children" class="">
                        <a href="#" class="plusOne" data-bind="click: plusOne" rel="children" style="display: none;">+</a>
                        <a href="#" class="minusOne" data-bind="click: minusOne" rel="children" style="display: none;">-</a>
                    </div>
                    детей до 12 лет
                </div>
                <div class="small-childs">
                    <div class="inputDIV">
                        <input type="text" name="adult3" data-bind="css: {active: infants() > 0}, value: infants" class="">
                        <a href="#" class="plusOne" data-bind="click: plusOne" rel="infants" style="display: none;">+</a>
                        <a href="#" class="minusOne" data-bind="click: minusOne" rel="infants" style="display: none;">-</a>
                    </div>
                    детей до 2 лет
                </div>

            </div>
            <!-- /ko -->
            <!-- /ko -->


        </div>

        <!-- END CONSTRUCTOR -->

    </div>
    <div class="clear"></div>
</div>
<!-- END PANEL -->

<!-- CALENDAR -->
<div class="calenderWindow z-indexTop" data-bind="template: {name: 'calendar-template-hotel', afterRender: reRenderCalendarStatic}" style="top: -302px; overflow: hidden; height: 341px; position: static;">
</div>
<!-- END CALENDAR -->
<?php echo $this->renderPartial('//landing/_hotelList',array('city'=>$city,'hotelsInfo'=>$hotelsInfo)); ?>
<?php echo $this->renderPartial('//landing/_bestFlights',array('currentCity'=>$currentCity,'flightCacheFromCurrent'=>$flightCacheFromCurrent)); ?>
<div class="headBlockTwo" style="margin-bottom: 60px">
    <div class="center-block textSeo">
        <h2>Что такое Voyanga</h2>
        <p>Voyanga.com — это самый простой, удобный и современный способ поиска и покупки авиабилетов. Мы постоянно работаем над развитием и улучшением сервиса. Наш сайт подключен сразу к нескольким системам бронирования, что позволяет сравнивать тарифы и подбирать наиболее выгодные и удобные тарифы и рейсы.</p>
        <p>Наша компания официально аккредитована в Международной ассоциации авиаперевозчиков (IATA) и в российской транспортной клиринговой палате (ТКП). Мы прошли все необходимые процедуры для оформления электронных билетов на рейсы российских и зарубежных авиакомпаний.</p>
        <p>Помимо сайта у нас есть собственная служба бронирования, которая находится в нашем офисе. Всегда можно позвонить и вам помогут и ответят на все вопросы. Офис компании находится в Санкт-Петербурге.</p>
        <h2>Как посетить 10 стран по цене Айфона</h2>
        <p>Voyanga.com — это самый простой, удобный и современный способ поиска и покупки авиабилетов. Мы постоянно работаем над развитием и улучшением сервиса. Наш сайт подключен сразу к нескольким системам бронирования, что позволяет сравнивать тарифы и подбирать наиболее выгодные и удобные тарифы и рейсы.</p>
        <p>Наша компания официально аккредитована в Международной ассоциации авиаперевозчиков (IATA) и в российской транспортной клиринговой палате (ТКП). Мы прошли все необходимые процедуры для оформления электронных билетов на рейсы российских и зарубежных авиакомпаний.</p>
        <p>Помимо сайта у нас есть собственная служба бронирования, которая находится в нашем офисе. Всегда можно позвонить и вам помогут и ответят на все вопросы. Офис компании находится в Санкт-Петербурге.</p>

    </div>
</div>
<div class="clear"></div>