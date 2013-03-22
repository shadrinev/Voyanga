<?php
//print_r($citiesFrom);die();
if(isset($citiesFrom[($fromCity ? $fromCity->id : $currentCity->id)])){
    $bestPrice = $citiesFrom[($fromCity ? $fromCity->id : $currentCity->id)]['flightCacheBestPriceArr'];
    //print_r($bestPrice);die();
    if(!$bestPrice){
        $bestPrice = array('price'=> '?');
    }
}else{
    $bestPrice = array('price'=> '?');
}
?><script>
    //window.flightBestPrice = <?php //echo json_encode($flightCache); ?>;
    window.defaultCity = '<?php echo ($fromCity ? $fromCity->code : $currentCity->code); ?>';
    window.pointCity = '<?php echo $city->code; ?>';

    window.allCaches = <?php echo json_encode($citiesFrom);?>;

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
        app.runWithModule('avia');
        app.activeModule('avia');
        var panelSet = new AviaPanel();
        panelSet.departureCity(window.defaultCity);
        panelSet.arrivalCity(window.pointCity);
        panelSet.minimizedCalendar(false);
        //panelSet.calendarActive(false);

        //panelSet.minimized(true);
        //panelSet.calendarHidden(true);
        var landBP = new landingCitySelector(window.allCaches);
        landBP.rt(false);

        window.setTimeout(function () {
            //landBP.setDirectBestPrice(window.bestDateData);

        }, 200);
        window.setTimeout(function () {
            //panelSet.calendarActive(true);

        }, 1000);
        panelSet.rt(false);
        app.fakoPanel(panelSet);
        app.landBP = landBP;
        landBP.selectCity(window.defaultCity);
        ko.applyBindings(app);
        ko.processAllDeferredBindingUpdates();
    };

    $(document).ready(function () {
        initLandingPage();
        //eventPhotos = new EventPhotoBox(window.eventPhotos);
    })

</script>
<div class="headBlockOne">
    <?php
    $this->widget('common.components.BreadcrumbsVoyanga', array(
        'links'=>$this->breadLinks,
        'separator'=>' &rarr; ',
        'homeLink'=>CHtml::link('Voyanga','/'),
        'htmlOptions' => array(
            'class' => 'breadcrumbs'
        )
    ));
    ?>
    <?php if (!$fromCity): ?>
    <div class="center-block" data-bind="style: {overflow: landBP.showGrafik() ? '' : 'hidden'}">
        <h1>Авиабилеты в <?php echo $city->caseAcc;?></h1>

        <h3>Стоимость на месяц вперед из
            <!-- ko foreach: landBP.citiesInfoArr -->
            <a href="#" class="cityChoise" data-bind="click: $parent.landBP.selectCity,css: 'cityChoise' + ($parent.landBP.currentCityCode() === code ? ' active' : '')">
                <span data-bind="text: name"></span>
            </a>
            <!-- /ko -->

        </h3>
    </div>
    <?php else: ?>
    <div class="center-block" data-bind="style: {overflow: landBP.showGrafik() ? '' : 'hidden'}">
        <h1>Авиабилеты из <?php echo $fromCity->caseGen;?> в <?php echo $city->caseAcc;?></h1>
    </div>
    <?php endif;?>
    <div class="center-block" data-bind="visible: landBP.showGrafik">
        <h3 class="label">График цен</h3>
        <div class="floatLeft">
            <ul class="grafik first-child" data-bind="foreach: landBP.datesArr">
                <!-- ko ifnot: landBP.empty -->
                <li class="grafikMean" data-bind="css: 'grafikMean' + (landBP.selected() ? ' active' : ''), click: landBP.selectThis">
                <div class="price" style="bottom: 30px" data-bind="style: { bottom: landBP.showWidth() + 'px'}, text: landBP.showPriceText()"></div>
                <div class="statusBar" style="height: 30px" data-bind="style: { height: landBP.showWidth() + 'px'}"></div>
                </li>
                <!-- /ko -->
                <!-- ko if: landBP.empty -->
                <li class="grafikMean inactive" data-bind="css: 'grafikMean' + (landBP.selected() ? ' active' : ' inactive'), click: landBP.selectThis">
                    <div class="price question" style="bottom: 0px; left: 0px;">?</div>
                </li>
                <!-- /ko -->
            </ul>
        </div>
        <div class="floatRight textBlockPrice">
            <div class="cena" data-bind="if: landBP.selectedPrice() != '???'"> от <span class="price" data-bind="text: landBP.selectedPrice()">3 250</span> <span
                class="rur">o</span></div>
            <div>
                <span data-bind="text: landBP.selectedDates()"></span>
            </div>
            <div data-bind="visible: landBP.bestDate()" class="landBestPrice">
                Самая низкая цена:<br>
                от <span data-bind="text: Utils.formatPrice(landBP.bestPrice())"></span> р
                <a href="#" data-bind="text: landBP.bestDate(),click: landBP.bestDateClick">12 июня 2012</a>
            </div>
        </div>
        <div class="clear"></div>
    </div>
    <div class="bgDate" data-bind="visible: landBP.showGrafik">
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
                                           data-bind="autocomplete: {source:'city/airport_req/1', iata: departureCity, readable: departureCityReadable, readableAcc: departureCityReadableAcc, readableGen: departureCityReadableGen, readablePre: departureCityReadablePre}"
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
                                    <input class="input-path arrivalCity" type="text" tabindex="-1" style="width: 227.5px;">
                                    <input class="second-path arrivalCity" placeholder="Куда" data-bind="autocomplete: {source:'city/airport_req/1', iata: arrivalCity, readable: arrivalCityReadable, readableAcc: arrivalCityReadableAcc, readableGen: arrivalCityReadableGen, readablePre: arrivalCityReadablePre}" style="width: 227.5px;" autocomplete="off">

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
    <div class="fly-ico"></div>
    <div class="clear"></div>
</div>
<!-- END PANEL -->

<!-- CALENDAR -->
<div class="calenderWindow z-indexTop"
     data-bind="template: {name: 'calendar-template-hotel', afterRender: reRenderCalendarStatic}"
     style="top: -302px; overflow: hidden; height: 341px; position: static;">
</div>
<!-- END CALENDAR -->

<?php echo $this->renderPartial('//landing/_bestFlights', array('currentCity' => $currentCity, 'flightCacheFromCurrent' => $flightCacheFromCurrent)); ?>
<?php echo $this->renderPartial('//landing/_hotelList', array('city' => $city, 'hotelsInfo' => $hotelsInfo)); ?>
<div class="headBlockTwo" style="margin-bottom: 60px">
    <div class="center-block textSeo">
        <h2>Что такое Voyanga</h2>
        <p>Это инфотрмация о том что из <?php echo ($fromCity ? $fromCity->caseGen : $currentCity->caseGen); ?> в <?php echo $city->caseAcc; ?> можно добраться за <?php echo $bestPrice['price'];?> <span class="rur">o</span></p>
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