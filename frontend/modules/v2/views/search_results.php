<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Voyanga v.0.1 - Trip Flight Rework</title>
</head>

<body>
<div class="wrapper">
<div class="head">
    <!-- CENTER BLOCK -->
    <div class="center-block">
        <a href="/" class="logo">Voyanga</a>
        <a href="/" class="about">О проекте</a>

        <div class="telefon">
            <img src="/images/tel.png">
        </div>
        <div class="slide-turn-mode">
            <div class="switch"><span class="l"></span><span class="c"></span><span class="r"></span></div>
            <div class="bg-mask"></div>

            <ul>
                <li class="planner active btn"><a href="#">Планировщик</a></li>
                <li class="aviatickets btn"><a href="#">Авиабилеты</a></li>
                <li class="hotel btn"><a href="#">Отели</a></li>
                <li class="finish-stages btn"><a href="#">Готовые туры</a></li>
            </ul>
        </div>

        <div class="login-window full">
            <a href="#">
                <span class="text">Регистрация и вход</span>
                <span class="point"></span>
            </a>
        </div>
    </div>
    <!-- END CENTER BLOCK -->
</div>
<!-- END HEAD -->
<!--====**********===-->
<!-- SUB HEAD -->
<div class="sub-head">
    <!-- CENTER BLOCK -->
    <div class="center-block">
        <!-- PANEL -->
        <div class="panel">
            <div class="btn-timeline-and-condition">
                <a href="#" class="btn-timeline active">Таймлайн</a>
                <a href="#" class="btn-condition">Условия</a>
            </div>

            <div class="slide-tmblr">

                <div class="condition">
                    <div class="whence date">
                        <input name="name01" type="text" class="input">
                        <a href="#" class="day">
                            <span class="f17">12</span>
                            <br>мая
                        </a>
                    </div>
                    <div class="tumblr">
                        <div class="one active"></div>
                        <div class="two"></div>

                        <div class="switch"></div>
                    </div>
                    <div class="where">
                        <input name="name01" type="text" class="input">
                        <a href="#" class="day">
                            <span class="f17">12</span>
                            <br>мая
                        </a>
                    </div>

                    <div class="how-many-man">
                        <div class="content">
                            <div class="man"></div>
                            <div class="child"></div>
                        </div>
                        <div class="btn"></div>

                        <div class="popup">
                            <div class="adults">
                                <input name="adult" type="text" value="1">
                                взрослых
                            </div>
                            <div class="childs">
                                <input name="adult" type="text" value="1">
                                детей до 12 лет
                            </div>
                            <div class="small-childs">
                                <input name="adult" type="text" value="0">
                                детей до 2 лет
                            </div>

                        </div>
                    </div>

                    <a class="btn-find">Найти</a>

                    <div class="clear"></div>
                </div>

            </div>

            <div class="clear"></div>
            <!-- BTN MINIMIZE -->
            <a href="#" class="btn-minimizePanel"><span></span> свернуть</a>

            <div class="minimize-rcomended">
                <a href="#" class="btn-minimizeRecomended"> вернуть рекомендации</a>
            </div>
        </div>
        <!-- END PANEL -->
    </div>
    <!-- END CENTER BLOCK -->
</div>
<!-- END SUB HEAD -->
<!--====**********===-->
<!-- ALL CONTENT -->
<div class="center-block">

<!-- MAIN BLOCK -->
<div class="main-block">
<div id="content">
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

</div>
<!-- END MAIN BLOCK -->
<!-- FILTER BLOCK -->
<div class="filter-block">
    <div class="filter-content">

        <div class="slide-filter">
            <img src="/images/img-filter-slide01.png">
        </div>

        <div class="div-filter">

            <div class="slider-filter">
                <img src="/images/img-filter-slide02.png">
            </div>

            <input type="checkbox" name="ch00" id="ch00"> <label for="ch00">Только короткие пересадки</label>

        </div>
        <div class="div-filter">
            <div class="slider-filter" style="text-align:center; margin-bottom:18px;">
                <img src="/images/tuda.png">
            </div>
            <h4>Время вылета</h4>

            <div class="slide-filter">
                <img src="/images/img-slide-time01.png">
            </div>
            <h4>Время прилета</h4>

            <div class="slide">
                <img src="/images/img-slide-time02.png">
            </div>
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
                <li><input type="checkbox" data-bind="checked: active"> <label for="ch01" data-bind="text: name">Аэрофлот</label></li>
            </ul>
            <div class="all-list">
                <a href="#">Все авиакомпании</a>
            </div>
        </div>
    </div>
</div>
<!-- END FILTER BLOCK -->
<div class="clear"></div>
</div>
<!-- END ALL CONTENT -->
</div>


<!-- ==== POPUP === -->
<div id="tuda-obratno" style="display: none;">
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
            <img src="/images/FV_FNM_20120711.png"><br>
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
            <img src="/images/FV_FNM_20120711.png"><br>
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
            <img src="/images/FV_FNM_20120711.png"><br>
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
            <img src="/images/FV_FNM_20120711.png"><br>
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
</div>
</div>
</div>
<div id="tuda" style="display: none;">
    <div class="tickets-details"
         style="margin-left: -21px; margin-top: -23px; margin-right: -19px; margin-bottom: -15px;">

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
                    <div class="finish-fly">
                        <div class="time">
                            11:50
                        </div>
                        <div class="icon jet"></div>
                        <div class="place">
                            <span class="city">Москва,</span> <span class="airport">Домодедово</span>
                        </div>
                    </div>
                </div>
                <div class="aviacompany">
                    <img src="/images/FV_FNM_20120711.png"><br>
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
        </div>
    </div>
</div>
<div id="tuda-wait" style="display: none;">
    <div class="tickets-details"
         style="margin-left: -21px; margin-top: -23px; margin-right: -19px; margin-bottom: -15px;">

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
                    <img src="/images/FV_FNM_20120711.png"><br>
                    AZW1545
                </div>
            </div>
            <div class="transitum">
                Пересадка: между рейсами 1 ч. 30 м.
            </div>
            <div class="mid-path">
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
                    <img src="/images/FV_FNM_20120711.png"><br>
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
                    <img src="/images/FV_FNM_20120711.png"><br>
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
        </div>
    </div>
</div>
</body>
</html>
