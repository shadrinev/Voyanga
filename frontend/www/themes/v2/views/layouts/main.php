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
    <script type="text/javascript"
            src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBdPg3WqRnITMLhY4OeXyk4bCa4qBEdF8U&sensor=false">
    </script>
    <script type="text/javascript" src="/js/iedebug.js"></script>
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
                    <li id="h-tours-slider" class="planner btn"><a href="#tours">Планировщик</a></li>
                    <li id="h-avia-slider" class="aviatickets btn" data-bind="click: slider.click"><a href="#">Авиабилеты</a>
                    </li>
                    <li id="h-hotels-slider" class="hotel btn" data-bind="click: slider.click"><a
                            href="#hotels">Отели</a></li>
                    <li id="h-event-slider" class="finish-stages btn" data-bind="click: slider.click"><a href="#events">Готовые туры</a></li>
                </ul>
            </div>

            <div class="login-window full" style="display:none;">
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
    <div class="sub-head"
         data-bind="css: {calSelectedPanelActive: !fakoPanel().calendarHidden(), zIndexTopUp: fakoPanel().calendarShadow()}">
        <!-- CENTER BLOCK -->
        <div class="center-block">
            <!-- PANEL -->
            <div class="panel"
                 data-bind="template: { name: fakoPanel().template, data: fakoPanel, afterRender: fakoPanel().afterRender }">
            </div>
            <!-- END PANEL -->
        </div>
        <!-- END CENTER BLOCK -->
    </div>
    <!-- END SUB HEAD -->
    <!--====**********===-->
    <!-- CALENDAR -->
    <div class="calenderWindow z-indexTop" data-bind="template: {name: 'calendar-template'}"
         style="margin-top: 36px; top: -341px;">
    </div>
    <!-- END CALENDAR -->
    <!--====**********===-->
    <!-- ALL CONTENT -->
    <div class="center-block"
         data-bind="visible: isNotEvent(), template: {name: activeView(), data: viewData(), afterRender: contentRendered}">
    </div>
    <!-- SLIDE TOURS -->
    <div class="slideTours"
         data-bind="visible: isEvent(), template: {name: activeView()}">
    </div>
    <!-- END SLIDE TOURS -->
    <!-- FOOTER -->
    <div class="footer">
        <div class="center-block">
            <ul class="foot-menu">
                <li><a href="#">О проекте</a></li>
                <li><a href="#">Вопросы и ответы</a></li>
                <li><a href="#">Контакты</a></li>
            </ul>
        </div>
    </div>
    <!-- END FOOTER-->
</div>
</div>
<!-- END WRAPPER -->
<!-- MAPS -->
<div class="maps"
     data-bind="visible: isEvent(), template: {name: 'event-map'}">
</div>
<!-- END MAPS -->
<div id="loadWrapBg" style='display: none;'>
    <div id="loadContentWin">
        <div id="loadGIF"><img src="/themes/v2/assets/images/loading-5frame.gif"></div>
        <div id="loadTXT">
            Voyanga ищет <br> лучшие предложения...<br>
            <ul id="loadLight">
                <li class=""></li>
                <li class=""></li>
                <li class=""></li>
                <li class=""></li>
                <li class="active"></li>
            </ul>
            <div id="changeText"></div>
        </div>
    </div>
</div>
<?php
$templates = Array('avia.index', 'avia.results', 'avia.popup',
    'avia.panel', 'avia.filters', 'avia.cheapest',
    'hotels.index', 'hotels.results', 'hotels.panel',
    'hotels.popup', 'hotels.filters', 'hotels.info', 'hotels.timeline',
    'tours.results', 'tours.index',
    'common.calendar',
    'event.index', 'event.map'
);
foreach ($templates as $template)
{
    $this->renderPartial('www.themes.v2.views.' . $template);
}
?>
</body>
</html>
