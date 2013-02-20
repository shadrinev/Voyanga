<script>
    //window.flightBestPrice = <?php //echo json_encode($flightCache); ?>;
    window.defaultCity = '<?php echo  $currentCity->code; ?>';


    function setDepartureDate(strDate) {
        VoyangaCalendarStandart.values = new Array(moment(strDate)._d);
        window.app.fakoPanel().departureDate(moment(strDate)._d);
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
        var panelSet = new AviaPanel();
        panelSet.departureCity(window.defaultCity);
        //panelSet.arrivalCity(window.pointCity);
        panelSet.calendarActive(false);

        //panelSet.minimized(true);
        //panelSet.calendarHidden(true);
        window.setTimeout(function () {
            panelSet.calendarActive(true);

        }, 1000);


        panelSet.rt(false);
        //panelSet.sp.calendarActivated(false);
        app.fakoPanel(panelSet);


        ko.applyBindings(app);
        ko.processAllDeferredBindingUpdates();
    };

    $(document).ready(function () {
        initLandingPage();
        //eventPhotos = new EventPhotoBox(window.eventPhotos);
    })

</script>
<div class="headBlockOne">
    <div class="center-block">
        <h1>Авиабилеты в <?php echo $countryMorph['caseAcc'];?></h1>
        <?php if ($flightCache): ?>
        <h3>Стоимость из
            <?php
            foreach ($citiesFrom as $cityPoint):
                ?>
                <a href="#" class="cityChoise<?php echo $cityPoint['cityId'] == $currentCity->id ? ' active' : '';?>">
                    <span><?php echo $cityPoint['cityName'];?></span>
                </a>
                <?php

            endforeach;
            ?>
        </h3>
        <?php else: ?>
        <br/>
        <?php endif;?>
    </div>
    <?php if ($flightCache): ?>
    <table class="tableFlight first up">
        <thead>
        <tr>
            <td class="tdEmpty">

            </td>
            <td class="tdFlight">
                Рейс
            </td>
            <td class="tdTo">
                Туда
            </td>
            <td class="tdFrom">
                Обратно
            </td>
            <td class="tdPrice">
                Цена
            </td>
        </tr>
        </thead>
        <tbody>
            <?php
            $firstHalf = round(count($flightCache) / 2);
            $secondHalf = count($flightCache) - $firstHalf;
            $i = 0;
            foreach ($flightCache as $fc):
                $i++;
                if ($i <= $firstHalf):
                    $back = ($fc->dateBack == '0000-00-00' ? false : true);
                    ?>
                <tr<?php echo (($i + 1) % 2) == 0 ? ' class="select"' : '';?>>
                    <td class="tdEmpty">

                    </td>
                    <td class="tdFlight">
                        <div><?php echo City::getCityByPk($fc->from)->localRu;?> <span
                            class="<?php echo $back ? 'toFrom' : 'to';?>"></span> <?php echo City::getCityByPk($fc->to)->localRu;?>
                        </div>
                    </td>
                    <td class="tdTo">
                        <?php echo date('d.m', strtotime($fc->dateFrom));?>
                    </td>
                    <td class="tdFrom">
                        <?php echo ($fc->dateBack == '0000-00-00' ? '' : date('d.m', strtotime($fc->dateBack)));?>
                    </td>
                    <td class="tdPrice">
                        <a href="<?php echo '/land/' . City::getCityByPk($fc->to)->country->code . '/' . City::getCityByPk($fc->from)->code . '/' . City::getCityByPk($fc->to)->code . ($fc->dateBack == '0000-00-00' ? '/trip/OW' : '');?>"><span
                            class="price"><?php echo UtilsHelper::formatPrice($fc->priceBestPrice);?></span> <span
                            class="rur">o</span></a>
                    </td>
                </tr>
                    <?php
                endif;
            endforeach;?>
        </tbody>
    </table>
    <table class="tableFlight second up">
        <thead>
        <tr>

            <td class="tdFlight">
                Рейс
            </td>
            <td class="tdTo">
                Туда
            </td>
            <td class="tdFrom">
                Обратно
            </td>
            <td class="tdPrice">
                Цена
            </td>
            <td class="tdEmpty">

            </td>
        </tr>
        </thead>
        <tbody>
            <?php $i = 0;
            foreach ($flightCache as $fc):
                $i++;
                if ($i > $firstHalf):
                    $back = ($fc->dateBack == '0000-00-00' ? false : true);?>
                <tr<?php echo ($i % 2) == 0 ? ' class="select"' : '';?>>
                    <td class="tdFlight">
                        <div><?php echo City::getCityByPk($fc->from)->localRu;?> <span
                            class="<?php echo $back ? 'toFrom' : 'to';?>"></span> <?php echo City::getCityByPk($fc->to)->localRu;?>
                        </div>
                    </td>
                    <td class="tdTo">
                        <?php echo date('d.m', strtotime($fc->dateFrom));?>
                    </td>
                    <td class="tdFrom">
                        <?php echo ($fc->dateBack == '0000-00-00' ? '' : date('d.m', strtotime($fc->dateBack)));?>
                    </td>
                    <td class="tdPrice">
                        <a href="<?php echo '/land/' . City::getCityByPk($fc->to)->country->code . '/' . City::getCityByPk($fc->from)->code . '/' . City::getCityByPk($fc->to)->code . ($fc->dateBack == '0000-00-00' ? '/trip/OW' : '');?>"><span
                            class="price"><?php echo UtilsHelper::formatPrice($fc->priceBestPrice);?></span> <span
                            class="rur">o</span></a>
                    </td>
                </tr>
                    <?php
                endif;
            endforeach;?>

        </tbody>
    </table>
    <?php endif;?>
    <div class="clear"></div>

</div>

<div class="sub-head event" style="height: auto;width: auto;"
     data-bind="css: {calSelectedPanelActive: !fakoPanel().calendarHidden()}">
    <div class="board" style="position: static;">
        <div class="constructor" style="position: static;">
            <!-- BOARD CONTENT -->
            <div class="panel" data-bind="template: {data: fakoPanel(), afterRender: fakoPanel().afterRender }">
                <table class="panelTable avia">
                    <tbody>
                    <tr>
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
                                    <input class="input-path departureCity" type="text" tabindex="-1"
                                           style="width: 227.5px;">
                                    <input class="second-path departureCity" type="text" placeholder="Откуда"
                                           data-bind="autocomplete: {source:'city/airport_req/1', iata: departureCity, readable: departureCityReadable, readableAcc: departureCityReadableAcc, readableGen: departureCityReadableGen}"
                                           style="width: 227.5px;" autocomplete="off">

                                    <div class="date" data-bind="click: showCalendar">
                                        <span class="f17" data-bind="text: departureDateDay()"></span>
                                        <br>
                                        <span class="month" data-bind="text: departureDateMonth()"></span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="tdTumblr">
                            <div class="tumblr">
                                <label for="there-back">
                                    <div class="one" data-bind="css: {active: !rt()}, click: selectOneWay"></div>
                                    <div class="two active"
                                         data-bind="css: {active: rt()}, click: selectRoundTrip"></div>
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
                                    <input class="input-path arrivalCity" type="text" tabindex="-1"
                                           style="width: 227.5px;">
                                    <input class="second-path arrivalCity" placeholder="Куда"
                                           data-bind="autocomplete: {source:'city/airport_req/1', iata: arrivalCity, readable: arrivalCityReadable, readableAcc: arrivalCityReadableAcc, readableGen: arrivalCityReadableGen}"
                                           style="width: 227.5px;" autocomplete="off">

                                    <div class="date" data-bind="click: showCalendar">
                                        <span class="f17" data-bind="text: rtDateDay()"></span>
                                        <br>
                                        <span class="month" data-bind="text: rtDateMonth()"></span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="tdPeople">
                        <span data-bind="template: {name: passengers.template, data: passengers}">
                        </span>
                        </td>
                        <td class="tdButton">
                            <a class="btn-find inactive"
                               data-bind="click: navigateToNewSearch, css: {inactive: formNotFilled}">Найти</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>


            <!-- END BOARD CONTENT -->
            <!-- ko with: fakoPanel() -->
            <!-- ko with: passengers -->
            <div class="popupPeople active avia" style="display: none;">
                <div class="adults">
                    <div class="inputDIV">
                        <input type="text" name="adult" data-bind="css: {active: adults() > 0}, value: adults"
                               class="active">
                        <a href="#" class="plusOne" data-bind="click: plusOne" rel="adults" style="display: none;">+</a>
                        <a href="#" class="minusOne" data-bind="click: minusOne" rel="adults"
                           style="display: none;">-</a>
                    </div>
                    взрослых
                </div>
                <div class="childs">
                    <div class="inputDIV">
                        <input type="text" name="adult2" data-bind="css: {active: children() > 0}, value: children"
                               class="">
                        <a href="#" class="plusOne" data-bind="click: plusOne" rel="children"
                           style="display: none;">+</a>
                        <a href="#" class="minusOne" data-bind="click: minusOne" rel="children"
                           style="display: none;">-</a>
                    </div>
                    детей до 12 лет
                </div>
                <div class="small-childs">
                    <div class="inputDIV">
                        <input type="text" name="adult3" data-bind="css: {active: infants() > 0}, value: infants"
                               class="">
                        <a href="#" class="plusOne" data-bind="click: plusOne" rel="infants"
                           style="display: none;">+</a>
                        <a href="#" class="minusOne" data-bind="click: minusOne" rel="infants"
                           style="display: none;">-</a>
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
<div class="calenderWindow z-indexTop"
     data-bind="template: {name: 'calendar-template-hotel', afterRender: reRenderCalendarStatic}"
     style="top: -302px; overflow: hidden; height: 341px; position: static;">
</div>
<!-- END CALENDAR -->


<?php echo $this->renderPartial('//landing/_hotelList', array('city' => (object)$countryMorph, 'hotelsInfo' => $hotelsInfo)); ?>
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