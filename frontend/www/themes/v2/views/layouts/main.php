<?php
$cs = Yii::app()->getClientScript();
$cs->reset();
$images = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('frontend.www.themes.v2.assets'));
Yii::app()->clientScript->registerPackage('everything');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Voyanga v.0.1 - Trip Flight Rework</title>
</head>

<body>
<div class="wrapper">
    <div class="head" id="header">
        <!-- CENTER BLOCK -->
        <div class="center-block">
            <a href="/" class="logo">Voyanga</a>
            <a href="/" class="about">О проекте</a>

            <div class="telefon">
                <img src="<?= $images ?>/images/tel.png">
            </div>
            <div class="slide-turn-mode">
                <div class="switch"><span class="l"></span><span class="c"></span><span class="r"></span></div>
                <div class="bg-mask"></div>

                <ul>
                    <li class="planner btn"><a href="#">Планировщик</a></li>
                    <li class="aviatickets btn" data-bind="css: {active: activeTab == 'avia'}"><a href="#">Авиабилеты</a></li>
                    <li class="hotel btn" data-bind="css: {active: activeTab == 'hotel'}"><a href="#">Отели</a></li>
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
<?php echo $content; ?>

            </div>
            <!-- END MAIN BLOCK -->
            <!-- FILTER BLOCK -->
            <div class="filter-block">
                <div class="filter-content">

                    <div class="slide-filter">
                        <img src="<?= $images ?>/images/img-filter-slide01.png">
                    </div>

                    <div class="div-filter">

                        <div class="slider-filter">
                            <img src="<?= $images ?>/images/img-filter-slide02.png">
                        </div>

                        <input type="checkbox" name="ch00" id="ch00"> <label for="ch00">Только короткие пересадки</label>

                    </div>
                    <div class="div-filter">
                        <div class="slider-filter" style="text-align:center; margin-bottom:18px;">
                            <img src="<?= $images ?>/images/tuda.png">
                        </div>
                        <h4>Время вылета</h4>

                        <div class="slide-filter">
                            <img src="<?= $images ?>/images/img-slide-time01.png">
                            <br>
                            <br>
                            <br>
                            <div style="width: 200px; margin-left: 0px;">
                            <input id="departureTimeSlider" type="slider" name="departureTimeSlider" value="480;1020" />
                            </div>
                            <script type="text/javascript">
                                var sl = {
                                    from: 480,
                                    to: 1080,
                                    step: 15,
                                    dimension: '',
                                    skin: 'round_voyanga',
                                    scale: false,
                                    limits: false,
                                    minInterval: 60,
                                    value: "480;1020",
                                    calculate: function( value ){
                                        var hours = Math.floor( value / 60 );
                                        var mins = ( value - hours*60 );
                                        return (hours < 10 ? "0"+hours : hours) + ":" + ( mins == 0 ? "00" : mins );
                                    },
                                    onstatechange: function( value ){
                                        //console.dir( this );
                                        //console.log(value);
                                        return false;
                                    }
                                }
                                $("#departureTimeSlider").slider(sl);
                            </script>
                        </div>
                        <h4>Время прилета</h4>

                        <div class="slide">
                            <img src="<?= $images ?>/images/img-slide-time02.png">
                            <div></div>

                            <select id='coolSlider' class="selectSlider"><option value="12">opt1</option><option value="13" selected="selected">opt2</option><option value="17">opt20</option></select>
                            <script type="text/javascript">
                                $("#coolSlider").selectSlider({am:'kg',kg:'am2'});
                            </script>
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
                        <img src="<?= $images ?>/images/FV_FNM_20120711.png"><br>
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
