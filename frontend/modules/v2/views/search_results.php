
<!-- DIV RECOMENDED AND GRAFIK -->
<h1><span>Выберите авиабилет</span> Санкт-Петербург → Амстердам, 19 мая</h1>

<div class="recomended-content" data-bind="with: results.cheapest">
<div class="recommended-ticket">
    <div class="ticket-items">
        <div class="ribbon-cheapest"></div>
        <div class="content">
            <div class="airlines-line">
                <img src="/images/ico-airlline-russia.png"> <span data-bind="text:airline">Россия</span>
            </div>
            <div class="date-time-city">
                <div class="start">
                    <div class="date" data-bind="text: departureDayMo()">
                        28 мая
                    </div>
                    <div class="time" data-bind="text: departureTime()">
                        21:20
                    </div>
                    <div class="city" data-bind="text: departureCity(), attr:{rel:departureCity()}">Москва</div>
                    <div class="airport" data-bind="text: departureAirport()">
                        Домодедово
                    </div>
                </div>
                <div class="how-long">
                    <div class="path">
                        В пути
                    </div>
                    <div class="ico-path"></div>
                    <div class="time" data-bind="text: fullDuration()">
                        3 ч. 30 м.
                    </div>
                </div>
                <div class="finish">
                    <div class="date" data-bind="text: arrivalDayMo()">
                        29 мая
                    </div>
                    <div class="time" data-bind="text: arrivalTime()">
                        00:50
                    </div>
                    <div class="city" data-bind="text:arrivalCity(), attr:{rel:arrivalCity()}">Санкт-Петербург</div>
                    <div class="airport" data-bind="text: arrivalAirport()">
                        Пулково
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <!-- END DATE -->
            <!-- ko if: roundTrip -->
            <div class="line-two-ticket">
                <span class="end"></span>
            </div>
            <div class="airlines-line">
                <img src="/images/ico-airlline-russia.png"/> <span data-bind="text:airline">Россия</span>
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
                <div class="how-long">
                    <div class="path">
                        В пути
                    </div>
                    <div class="ico-path"></div>
                    <div class="time" data-bind="text: rtFullDuration()">
                        3 ч. 30 м.
                    </div>
                </div>
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
                <div class="clear"></div>
            </div>
            <!-- /ko -->
            <!-- END DATE -->

            <div class="line-dashed-ticket">
                <span class="end"></span>
            </div>
            <div class="details-selecte">
                <div class="details">
                    <a class="tuda-obratno" href="#">Подробнее<br> о перелете</a>
                </div>
                <a href="#" class="btn-cost">
                    <span class="l"></span>
                    <span class="text">Выбрать</span>
                    <span class="price" data-bind="text: price">300 250</span>
                    <span class="rur">o</span>
                </a>
            </div>
        </div>

        <span class="lt"></span>
        <span class="rt"></span>
        <span class="lv"></span>
        <span class="rv"></span>
        <span class="bh"></span>
    </div>
</div>
<!-- END TICKET -->

<div class="prices-of-3days">
    <div class="ticket">
        <div class="one-way">
            <ul class="schedule-of-prices">
                <li>
                    <div class="price" style="bottom: 80px">-100</div>
                    <div class="chart" style="background-position: center 55px;"></div>
                    <div class="week">пн</div>
                    <div class="date">16</div>
                </li>
                <li>
                    <div class="price" style="bottom: 55px">-100</div>
                    <div class="chart" style="background-position: center 80px;"></div>
                    <div class="week">вт</div>
                    <div class="date">17</div>
                </li>
                <li>
                    <div class="price" style="bottom: 75px">-100</div>
                    <div class="chart" style="background-position: center 60px;"></div>
                    <div class="week">ср</div>
                    <div class="date">18</div>
                </li>
                <li class="active">
                    <div class="price" style="bottom: 85px">3 250</div>
                    <div class="chart" style="background-position: center 50px;"></div>
                    <div class="week">чт</div>
                    <div class="date">19</div>
                </li>
                <li>
                    <div class="price" style="bottom: 75px">-100</div>
                    <div class="chart" style="background-position: center 60px;"></div>
                    <div class="week">пт</div>
                    <div class="date">20</div>
                </li>
                <li>
                    <div class="price" style="bottom: 110px">-100</div>
                    <div class="chart" style="background-position: center 25px;"></div>
                    <div class="week">сб</div>
                    <div class="date">21</div>
                </li>
                <li>
                    <div class="price" style="top: 45px">-100</div>
                    <div class="chart" style="background-position: center 60px;"></div>
                    <div class="week">вс</div>
                    <div class="date">22</div>
                </li>
            </ul>
            <div class="month">
                Май
            </div>
        </div>
        <div class="two-way" data-bind="visible: roundTrip">
            <ul class="schedule-of-prices">
                <li>
                    <div class="price" style="bottom: 80px">-100</div>
                    <div class="chart" style="background-position: center 55px;"></div>
                    <div class="week">пн</div>
                    <div class="date">16</div>
                </li>
                <li>
                    <div class="price" style="bottom: 55px">-100</div>
                    <div class="chart" style="background-position: center 80px;"></div>
                    <div class="week">вт</div>
                    <div class="date">17</div>
                </li>
                <li>
                    <div class="price" style="bottom: 75px">-100</div>
                    <div class="chart" style="background-position: center 60px;"></div>
                    <div class="week">ср</div>
                    <div class="date">18</div>
                </li>
                <li class="active">
                    <div class="price" style="bottom: 85px">3 250</div>
                    <div class="chart" style="background-position: center 50px;"></div>
                    <div class="week">чт</div>
                    <div class="date">19</div>
                </li>
                <li>
                    <div class="price" style="bottom: 75px">-100</div>
                    <div class="chart" style="background-position: center 60px;"></div>
                    <div class="week">пт</div>
                    <div class="date">20</div>
                </li>
                <li>
                    <div class="price" style="bottom: 110px">-100</div>
                    <div class="chart" style="background-position: center 25px;"></div>
                    <div class="week">сб</div>
                    <div class="date">21</div>
                </li>
                <li>
                    <div class="price" style="top: 45px">-100</div>
                    <div class="chart" style="background-position: center 60px;"></div>
                    <div class="week">вс</div>
                    <div class="date">22</div>
                </li>
            </ul>
            <div class="month">
                Май
            </div>
        </div>

        <div class="cena">
            <div class="total-td">
                <span class="text">Итого</span>

                <div class="all-price">
                    80 250 <span class="rur">o</span>
                </div>
            </div>
            <div class="look-td">
                <a href="#" class="btn-look">
                    Посмотреть
                </a>
            </div>
            <div class="clear"></div>
        </div>
        <span class="lt"></span>
        <span class="rt"></span>
        <span class="lv"></span>
        <span class="rv"></span>
        <span class="th"></span>
        <span class="bh"></span>
    </div>

    <div class="clear"></div>
</div>
<!-- END RECOMENDED AND GRAFIK -->
<div class="clear"></div>
</div>
<div class="ticket-content">
<h2>Все результаты</h2>

<div class="order-div">
    Сортировать по: <a href="#" class="order-show">цене</a> <a href="#start" class="order-hide">времени вылета</a> <a
        href="#start" class="order-hide">времени прилета</a>
</div>
<div class="clear"></div>

<!-- ko foreach: results -->
<div class="ticket-items" data-bind="visible: visible">
<div class="content">
<div class="airlines">
    <div>
        <img src="/images/ico-airlline-russia.png"><br>
        <span data-bind="text:airline">Россия</span>
    </div>
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
            В пути <span data-bind="text: fullDuration()">8 ч. 30 м.</span>
        </div>
        <div class="ico-path">
            <!-- ko foreach: stopsRatio() -->
            <span class="cup" style="left: 40%;" data-bind="style: {left: $data+'%'}"></span>
            <!-- /ko -->
            <span class="down"></span>
        </div>
        <!-- ko if: !direct() -->
        <div class="path">
            Пересадка в <span data-bind="text: stopoverText()">Москве</span>
        </div>
        <!-- /ko -->
        <!-- ko if: direct() -->
        <div class="path no-wait">
               Без пересадок
        </div>
        <!-- /ko -->
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
</div>
<!-- END DATE TIME CITY -->
<!-- ko if: stacked() -->
<div class="other-time">
    <div class="title">Также вы можете вылететь в</div>
    <div class="btn-minimize"><a href="#">Списком</a></div>
    <div class="clear"></div>
    <ul class="minimize" data-bind="foreach: voyages">
        <li>
            <a href="#" class="ico-path-time" data-bind="css: {hover: departureTime() == $parent.departureTime() }, click: $parent.chooseStacked">
                <input type="radio" data-bind="value: hash(), checked: $parent.hash()">

                <div class="path">
                    <div class="in-path"><span>В пути </span><span data-bind="text: fullDuration()">9 ч. 20 м.</span></div>
                    <div class="start" data-bind="text:departureTime()">06:10</div>
                    <div class="finish" data-bind="text:arrivalTime()">08:10</div>
                </div>
            </a>
        </li>
    </ul>
</div>
<!-- /ko -->
<div class="line-two-ticket" data-bind="if: roundTrip">
    <span class="l"></span>
    <span class="end"></span>
    <span class="r"></span>
</div>
<div class="date-time-city" data-bind="if: roundTrip">
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
            В пути <span data-bind="text: rtFullDuration()">8 ч. 30 м.</span>
        </div>
        <div class="ico-path">
            <div class="ico-path">
                <!-- ko foreach: rtStopsRatio() -->
                <span class="cup" style="left: 40%;" data-bind="style: {left: $data+'%'}"></span>
                <!-- /ko -->
                <span class="down"></span>
            </div>
            <span class="down"></span>
        </div>
        <!-- ko if: !rtDirect() -->
        <div class="path">
            Пересадка в <span data-bind="text: rtStopoverText()">Москве</span>
        </div>
        <!-- /ko -->
        <!-- ko if: rtDirect() -->
        <div class="path no-wait">
            Без пересадок
        </div>
        <!-- /ko -->
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
</div>
<!-- END DATE TIME CITY -->
<!-- ko if:rtStacked() -->
<div class="other-time" data-bind="if: roundTrip">
    <div class="title">Также вы можете вылететь в</div>
    <div class="btn-minimize"><a href="#">Списком</a></div>
    <div class="clear"></div>
    <ul class="minimize" data-bind="foreach: rtVoyages()">
            <li>
                <a href="#" class="ico-path-time" data-bind="css: {hover: departureTime() == $parent.rtDepartureTime() }, click: $parent.chooseRtStacked">
                    <input type="radio" data-bind="value: hash(), checked: $parent.rtHash()">

                    <div class="path">
                        <div class="in-path"><span>В пути </span><span data-bind="text: fullDuration()">9 ч. 20 м.</span></div>
                        <div class="start" data-bind="text:departureTime()">06:10</div>
                        <div class="finish" data-bind="text:arrivalTime()">08:10</div>
                    </div>
                </a>
            </li>
        </ul>
</div>
<!-- /ko -->
</div>
<!-- END CENTER BLOCK -->
<div class="buy-ticket">
    <div class="text">
        <span>Купить</span>
        <a href="#" class="btn-cost">
            <span class="l"></span>
            <span class="price" data-bind="text: price">3 250</span>
            <span class="rur">o</span>
        </a>
    </div>
    <a href="#" class="details">Подробнее <span>о перелете</span></a>
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
<!-- END TICKET -->
<!-- /ko -->
</div>
<!-- END TICKET CONTENT -->
