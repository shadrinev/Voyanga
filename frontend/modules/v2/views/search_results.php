<?php
$images = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('frontend.www.themes.v2.assets'));
?>
<script type="text/html" id="avia-content">
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
                    <a data-bind="click: showDetails" href="#">Подробнее<br> о перелете</a>
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
<!-- ko if:rtStacked -->
<div class="other-time" >
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
    <a href="#" data-bind="click: showDetails" class="details">Подробнее <span>о перелете</span></a>
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
<div id="body-popup" style="display:none;">
    <div id="popup">
        <div>
            <div id="boxTopLeft"></div>
            <div id="boxTopCenter"></div>
            <div id="boxTopRight"></div>
            <div class="clear"></div>
        </div>
        <div>
            <div id="boxMiddleLeft"></div>
            <div id="boxContent">
                <div id="contentBox">
                    <div id="avia-ticket-info-popup">
                        <div class="tickets-details" style="margin-left: -21px; margin-top: -23px; margin-right: -19px; margin-bottom: -15px;">
                        <div class="top-head-tickets">
        <div class="date">
            19 мая, Пн
        </div>
        <h3>Туда</h3>

        <div class="other-time">
            <div class="variation">
                <ul class="minimize">
                    <li>
                        Варианты вылета:
                    </li>
                    <li>
                        <input type="radio" name="radio01" id="name01" checked="checked">
                        <label for="name01"><span>06:10</span></label>
                    </li>
                    <li>
                        <input type="radio" name="radio01" id="name02" checked="checked">
                        <label for="name02"><span>06:10</span></label>
                    </li>
                    <li class="active">
                        <input type="radio" name="radio01" id="name03" checked="checked">
                        <label for="name03"><span>06:10</span></label>
                    </li>
                    <li>
                        <input type="radio" name="radio01" id="name02" checked="checked">
                        <label for="name02"><span>06:10</span></label>
                    </li>
                    <li>
                        <input type="radio" name="radio01" id="name02" checked="checked">
                        <label for="name02"><span>06:10</span></label>
                    </li>
                    <li>
                        <input type="radio" name="radio01" id="name02" checked="checked">
                        <label for="name02"><span>06:10</span></label>
                    </li>
                    <li>
                        <input type="radio" name="radio01" id="name02" checked="checked">
                        <label for="name02"><span>06:10</span></label>
                    </li>
                    <li>
                        <input type="radio" name="radio01" id="name02" checked="checked">
                        <label for="name02"><span>06:10</span></label>
                    </li>
                    <li>
                        <input type="radio" name="radio01" id="name02" checked="checked">
                        <label for="name02"><span>06:10</span></label>
                    </li>
                    <li>
                        <input type="radio" name="radio01" id="name02" checked="checked">
                        <label for="name02"><span>06:10</span></label>
                    </li>
                </ul>
            </div>
        </div>

    </div>
    <div class="content">
        <div class="start-path">
            <div class="information">
                <div class="start-fly">
                    <div class="time">
                        9:40
                    </div>
                    <div class="icon jet"></div>
                    <div class="place">
                        <span class="city">Санкт-Петербург,</span> <span class="airport">Пулково-2</span>
                    </div>
                </div>
                <div class="time-fly">
                    <div class="icon wait"></div>
                    <div class="info">
                        Перелет продлится 1 ч. 50 м.
                    </div>
                </div>
                <div class="finish-fly no-way">
                    <div class="time">
                        9:40
                    </div>
                    <div class="icon jet"></div>
                    <div class="place">
                        <span class="city">Санкт-Петербург,</span> <span class="airport">Пулково-2</span>
                    </div>
                </div>
            </div>
            <div class="aviacompany">
                <img src="<?= $images ?>/images/FV_FNM_20120711.png"><br>
                AZW1545
            </div>
        </div>
        <div class="transitum">
            Пересадка: между рейсами 1 ч. 30 м.
        </div>
        <div class="end-path">
            <div class="information">
                <div class="start-fly">
                    <div class="time">
                        9:40
                    </div>
                    <div class="icon jet"></div>
                    <div class="place">
                        <span class="city">Санкт-Петербург,</span> <span class="airport">Пулково-2</span>
                    </div>
                </div>
                <div class="time-fly">
                    <div class="icon wait"></div>
                    <div class="info">
                        Перелет продлится 1 ч. 50 м.
                    </div>
                </div>
                <div class="finish-fly">
                    <div class="time">
                        9:40
                    </div>
                    <div class="icon jet"></div>
                    <div class="place">
                        <span class="city">Санкт-Петербург,</span> <span class="airport">Пулково-2</span>
                    </div>
                </div>
            </div>
            <div class="aviacompany">
                <img src="<?= $images ?>/images/FV_FNM_20120711.png"><br>
                AZW1545
            </div>
        </div>
    </div>
    <div class="middle-head-tickets">
        <div class="date">
            19 мая, Пн
        </div>
        <h3>Обратно</h3>

        <div class="other-time">
            <div class="variation">
                <ul class="minimize">
                    <li>
                        Варианты вылета:
                    </li>
                    <li>
                        <input type="radio" name="radio01" id="name01" checked="checked">
                        <label for="name01"><span>06:10</span></label>
                    </li>
                    <li>
                        <input type="radio" name="radio01" id="name02" checked="checked">
                        <label for="name02"><span>06:10</span></label>
                    </li>
                    <li class="active">
                        <input type="radio" name="radio01" id="name03" checked="checked">
                        <label for="name03"><span>06:10</span></label>
                    </li>
                    <li>
                        <input type="radio" name="radio01" id="name02" checked="checked">
                        <label for="name02"><span>06:10</span></label>
                    </li>
                    <li>
                        <input type="radio" name="radio01" id="name02" checked="checked">
                        <label for="name02"><span>06:10</span></label>
                    </li>
                    <li>
                        <input type="radio" name="radio01" id="name02" checked="checked">
                        <label for="name02"><span>06:10</span></label>
                    </li>
                    <li>
                        <input type="radio" name="radio01" id="name02" checked="checked">
                        <label for="name02"><span>06:10</span></label>
                    </li>
                    <li>
                        <input type="radio" name="radio01" id="name02" checked="checked">
                        <label for="name02"><span>06:10</span></label>
                    </li>
                    <li>
                        <input type="radio" name="radio01" id="name02" checked="checked">
                        <label for="name02"><span>06:10</span></label>
                    </li>
                    <li>
                        <input type="radio" name="radio01" id="name02" checked="checked">
                        <label for="name02"><span>06:10</span></label>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="start-path">
            <div class="information">
                <div class="start-fly">
                    <div class="time">
                        9:40
                    </div>
                    <div class="icon jet"></div>
                    <div class="place">
                        <span class="city">Санкт-Петербург,</span> <span class="airport">Пулково-2</span>
                    </div>
                </div>
                <div class="time-fly">
                    <div class="icon wait"></div>
                    <div class="info">
                        Перелет продлится 1 ч. 50 м.
                    </div>
                </div>
                <div class="finish-fly no-way">
                    <div class="time">
                        11:40
                    </div>
                    <div class="icon jet"></div>
                    <div class="place">
                        <span class="city">Санкт-Петербург,</span> <span class="airport">Пулково-2</span>
                    </div>
                </div>
            </div>
            <div class="aviacompany">
                <img src="<?= $images ?>/images/FV_FNM_20120711.png"><br>
                AZW1545
            </div>
        </div>
        <div class="transitum">
            Пересадка: между рейсами 1 ч. 30 м.
        </div>
        <div class="end-path">
            <div class="information">
                <div class="start-fly">
                    <div class="time">
                        12:40
                    </div>
                    <div class="icon jet"></div>
                    <div class="place">
                        <span class="city">Санкт-Петербург,</span> <span class="airport">Пулково-2</span>
                    </div>
                </div>
                <div class="time-fly">
                    <div class="icon wait"></div>
                    <div class="info">
                        Перелет продлится 1 ч. 50 м.
                    </div>
                </div>
                <div class="finish-fly">
                    <div class="time">
                        15:40
                    </div>
                    <div class="icon jet"></div>
                    <div class="place">
                        <span class="city">Санкт-Петербург,</span> <span class="airport">Пулково-2</span>
                    </div>
                </div>
            </div>
            <div class="aviacompany">
                <img src="<?= $images ?>/images/FV_FNM_20120711.png"><br>
                AZW1545
            </div>
        </div>
    </div>
    <hr class="lines">
    <div class="yes">
        <span style="color:#2e333b;" class="f14 bold">Оформить</span>
        <a class="btn-order" href="#">
            <span class="cost">63 502</span> <span class="rur f26">o</span>
        </a>
    </div></div></div>
<div id="boxClose"></div></div></div><div id="boxMiddleRight"></div><div class="clear"></div></div><div><div id="boxBottomLeft"></div><div id="boxBottomCenter"></div><div id="boxBottomRight"></div></div></div></div>
</script>
<script type="text/html" id="avia-panel-template">
    <div class="path">
        <div class="data">
            <input class="input-path" type="text" placeholder="Куда">
                <div class="tumblr">
                    <label for="there-back">
                        <div class="one" data-bind="css: {active: !rt()}, click: selectOneWay"></div>
                        <div class="two" data-bind="css: {active: rt()}, click: selectRoundTrip"></div>
                        <div class="switch"></div>
                    </label>
                    <input id="there-back" type="checkbox" data-bind="checked: rt()">
                </div>
            <input class="input-path" type="text" placeholder="Откуда">
        </div>
        <div class="how-many-man">
            <div class="content">
                <!-- ko if: overall()>5 -->
                    <!-- ko if: adults()>0 -->
                    <div class="man"></div>
                    <!-- ko if: adults()>1 -->
                        <div class="count"><span>x</span><i data-bind="text: adults()"></i></div>
                    <!-- /ko -->
                    <!-- /ko -->
                    <!-- ko if: (sum_children())>0 -->
                    <div class="child"></div>
                    <!-- ko if: (sum_children())>1 -->
                        <div class="count"><span>x</span><i data-bind="text: sum_children()"></i></div>
                    <!-- /ko -->
                    <!-- /ko -->
                <!-- /ko -->
                <!-- ko if: overall()<=5 -->
                    <div class="man" data-bind="repeat: adults()"></div>
                    <div class="child" data-bind="repeat: sum_children()"></div>
                <!-- /ko -->
                </div>
                    <div class="btn"></div>
                    <div class="popup">
                        <div class="adults">
                            <div class="inputDIV">
                                <input type="text" name="adult" data-bind="css: {active: adults() > 0}, value: adults">
                                    <a href="#" class="plusOne">+</a>
                                    <a href="#" class="minusOne">-</a>
                            </div>
                            взрослых
                        </div>
                        <div class="childs">
                            <div class="inputDIV">
                                <input type="text" name="adult2" data-bind="css: {active: children() > 0}, value: children">
                                    <a href="#" class="plusOne">+</a>
                                    <a href="#" class="minusOne">-</a>
                            </div>
                            детей до 12 лет
                        </div>
                        <div class="small-childs">
                            <div class="inputDIV">
                                <input type="text" name="adult3" data-bind="css: {active: infants() > 0}, value: infants">
                                        <a href="#" class="plusOne">+</a>
                                        <a href="#" class="minusOne">-</a>
                                </div>
                                детей до 2 лет
                            </div>

                        </div>
                    </div>

                    <a class="btn-find">Найти</a>
                </div>

                <!-- BTN MINIMIZE -->
                <a href="#" class="btn-minimizePanel" data-bind="css: {active: minimized()}, click:minimize">
                    <!-- ko if: minimized() -->
                    <span></span> развернуть
                    <!-- /ko -->
                    <!-- ko if: !minimized() -->
                    <span></span> свернуть
                    <!-- /ko -->
                </a>
                <div class="minimize-rcomended">
                <a href="#" class="btn-minimizeRecomended"> вернуть рекомендации</a>
                </div>
</script>
<script type="text/html" id="avia-filters">
    <div class="filter-content">

        <div class="slide-filter">
            <img src="<?= $images ?>/images/img-filter-slide01.png">
            <select id='aviaFlightClass' class="selectSlider"><option value="B">Бизнес</option><option value="E" selected="selected">Эконом</option></select>
        </div>

        <div class="div-filter">

            <div class="slider-filter">
                <img src="<?= $images ?>/images/img-filter-slide02.png">
                <select id='aviaOnlyDirectFlights' class="selectSlider"><option value="0" selected="selected">Все рейсы</option><option value="1">Прямые</option></select>
            </div>

            <input type="checkbox" name="aviaShortTransits" id="aviaShortTransits"> <label for="aviaShortTransits">Только короткие пересадки</label>

        </div>
        <div class="div-filter">
            <div class="slider-filter" style="text-align:center; margin-bottom:18px;">
                <img src="<?= $images ?>/images/tuda.png">
                <br>
                <div style="width: 200px; margin-left: 0px;">
                    <select id='aviaShowReturnFilters' class="selectSlider"><option value="0" selected="selected">Туда</option><option value="1">Обратно</option></select>
                </div>
                <br>
                <br>
            </div>
            <h4>Время вылета</h4>

            <div class="slide-filter">
                <br>
                <br>
                <div style="width: 200px; margin-left: 0px;">
                    <input id="departureTimeSliderDirect" type="slider" name="departureTimeSlider" value="480;1020" />
                </div>
                <div style="width: 200px; margin-left: 0px;">
                    <input id="departureTimeSliderReturn" type="slider" name="departureTimeSlider" value="480;1020" />
                </div>

            </div>
            <h4>Время прилета</h4>

            <div class="slide-filter">
                <br />
                <div style="width: 200px; margin-left: 0px;">
                    <input id="arrivalTimeSliderDirect" type="slider" name="departureTimeSlider" value="480;1020" />
                </div>
                <div style="width: 200px; margin-left: 0px;">
                    <input id="arrivalTimeSliderReturn" type="slider" name="departureTimeSlider" value="480;1020" />
                </div>
            </div>
            <p>First name: <input data-bind="value: firstNameN" /></p>
            <p>Last name: <input data-bind="value: lastNameN" /></p>
            <h2>Hello, <span data-bind="text: fullNameN"> </span>!</h2>
            <script type="text/javascript">
                // Here's my data model
                var ViewModelN = function(first, last) {
                this.firstNameN = ko.observable(first);
                this.lastNameN = ko.observable(last);

                this.fullNameN = ko.computed(function() {
                // Knockout tracks dependencies automatically. It knows that fullName depends on firstName and lastName, because these get called when evaluating fullName.
                return this.firstNameN() + " " + this.lastNameN();
                }, this);
                };
                //var NVM = new ViewModel('mmm','pie');

                ko.applyBindings(new ViewModelN('mmm','pie')); // This makes Knockout
            </script>
            </div>

            <div class="div-filter">
                <h4>Москва</h4>
                <ul data-bind="foreach: results.airports">
                <li><input type="checkbox" data-bind="checked: active"> <label for="ch01" data-bind="text: name">Шереметьево</label></li>
                </ul>
                </div>
            <div class="div-filter">
                <h4>Москва <a href="#" class="clean">Очистить</a></h4>
            <ul data-bind="foreach: results.airlines">
                <li><input type="checkbox" data-bind="checked: active"> <label data-bind="text: name">Аэрофлот</label></li>
                </ul>
            <div class="all-list">
                <a href="#">Все авиакомпании</a>
                </div>
                </div>
            </div>
</script>
