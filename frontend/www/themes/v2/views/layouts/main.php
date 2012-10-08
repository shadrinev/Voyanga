<?php
$cs = Yii::app()->getClientScript();
$cs->reset();
$images = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('frontend.www.themes.v2.assets'));
$theme = Yii::app()->theme->baseUrl;
Yii::app()->clientScript->registerPackage('appCss');
Yii::app()->clientScript->registerPackage('appJs');
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Voyanga v.0.1 - Trip Flight Rework</title>
    <!--<script type="text/javascript"
            src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBdPg3WqRnITMLhY4OeXyk4bCa4qBEdF8U&sensor=false">
    </script>-->
</head>

<body data-bind="css: {fixed: isEvent()}">
<div class="wrapper" data-bind="css: {'scroll-none': isEvent()}">
    <div class="head" id="header">
        <!-- CENTER BLOCK -->
        <div class="center-block">
            <a href="/" class="logo">Voyanga</a>
            <a href="/" class="about">О проекте</a>

            <div class="telefon">
                <img src="<?= $theme ?>/images/tel.png">
            </div>
            <div class="slide-turn-mode">
                <div class="switch"><span class="l"></span><span class="c"></span><span class="r"></span></div>
                <div class="bg-mask"></div>

                <ul>
                    <li id="h-tours-slider" class="planner btn"><a href="#tours">Планировщик</a></li>
                    <li id="h-avia-slider" class="aviatickets btn" data-bind="click: slider.click"><a href="#avia">Авиабилеты</a>
                    </li>
                    <li id="h-hotels-slider" class="hotel btn" data-bind="click: slider.click"><a href="#hotels">Отели</a></li>
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

    <!-- BOARD IF WE ARE AT THE MAIN -->
    <!-- ko if: isEvent() -->
    <div class="panel-index">
        <div class="board" style="height: 0px;">
            <div class="constructor">
                <!-- BOARD CONTENT -->
                <!-- ko foreach: panels -->
                    <div class="board-content" data-bind="template: { name: $data.template, data: $data, afterRender: $data.afterRender }"></div>
                <!-- /ko -->
                <!-- END BOARD CONTENT -->

                <div class="constructor-ico"></div>

            </div>

            <!-- END CONSTRUCTOR -->
            <div class="leftPageBtn"></div>
            <div class="rightPageBtn"></div>
        </div>
    </div>
    <!-- /ko -->

    <!-- SUB HEAD IF WE NOT ON THE MAIN -->
    <!-- ko if: !isEvent()-->
    <div class="sub-head" data-bind="css: {calSelectedPanelActive: !fakoPanel().calendarHidden(), zIndexTopUp: fakoPanel().calendarShadow()}">
        <!-- CENTER BLOCK -->
            <div class="center-block">
                <!-- PANEL -->
                <div class="panel"
                     data-bind="template: { name: fakoPanel().template, data: fakoPanel, afterRender: fakoPanel().afterRender }">
                </div>
                <!-- END PANEL -->
            </div>
            <!-- END CENTER BLOCK -->
        <!-- CALENDAR -->
        <div class="calenderWindow z-indexTop" data-bind="template: {name: 'calendar-template'}"
             style="top: 70px; display: none;">
        </div>
        <!-- END CALENDAR -->
        <!--====**********===-->
    </div>
    <!-- /ko -->
    <!-- END SUB HEAD -->
    <!--====**********===-->
    <!-- ALL CONTENT -->
    <div class="center-block"
         data-bind="template: {if: isNotEvent(), name: activeView(), data: viewData(), afterRender: contentRendered}">
    </div>
    <!-- SLIDE TOURS -->
    <!-- ko if: isEvent() -->
    <div class="slideTours"
         data-bind="template: {name: activeView(), data: viewData(), afterRender: mapRendered}">
    </div>
    <!-- /ko -->
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
<!-- FIXME -->
<!-- ko if: isEvent() -->
<div class="maps" data-bind="template: {name: 'event-map', data: viewData()}">
</div>
<!-- /ko -->
<!-- END MAPS -->
<div id="loadWrapBg" style='display: none;'>
    <div id="loadContentWin">
        <div id="loadGIF"><img src="/themes/v2/images/loading-5frame.gif"></div>
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
$templates = Yii::app()->params['frontend.app.templates'];
foreach ($templates as $template)
{
    echo "<!-- START OF TEMPLATE $template -->\n";
    $this->renderPartial('www.themes.v2.views.' . $template);
    echo "<!-- END OF TEMPLATE $template -->\n";
}
?>
</body>
</html>
