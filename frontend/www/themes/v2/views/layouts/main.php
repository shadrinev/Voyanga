<?php
$cs = Yii::app()->getClientScript();
$cs->reset();
$images = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('frontend.www.themes.v2.assets'));
$theme = Yii::app()->theme->baseUrl;
Yii::app()->clientScript->registerPackage('appCss');
Yii::app()->clientScript->registerPackage('appJs');
Yii::app()->clientScript->registerScriptFile('/js/runApp.js');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link rel="shortcut icon" href="<?= $theme ?>/images/favicon.png" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title>Voyanga v.0.1 - Trip Flight Rework</title>
    <script type="text/javascript"
            src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBdPg3WqRnITMLhY4OeXyk4bCa4qBEdF8U&sensor=false">
    </script>
    <script type="text/javascript" src="http://api.voyanga.com/API.js"></script>
</head>
<body data-bind="css: {fixed: in1}">
<?php echo $content; ?>
<div class="wrapper" data-bind="css: {'scroll-none': in1}">
    <div class="head" id="header">
        <!-- CENTER BLOCK -->
        <div class="center-block">
            <a href="/" class="logo">Voyanga</a>
            <a href="javascript:void(0)" onclick="openPopUpProj()" class="about">О проекте</a>

            <div class="telefon">
                <img src="<?= $theme ?>/images/tel.png">
            </div>
            <div class="slide-turn-mode">
                <div class="switch"><span class="l"></span><span class="c"></span><span class="r"></span></div>
                <div class="bg-mask"></div>

                <ul>
                    <li id="h-tours-slider" class="planner btn"  data-bind="click: slider.click"><a href="#tours">Планировщик</a></li>
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
    <!-- ko if:in1 -->
    <div class="panel-index">
    	<h1 class="title">Спланируй свое путешествие <img src="<?= $theme ?>/images/saleTitle.png"></h1>
        <div class="board" data-bind="style: {height: fakoPanel().height}">
            <!-- ko if:fakoPanel().template=='tour-panel-template' -->
                <div class="constructor">
                    <!-- BOARD CONTENT -->
                        <div class="board-content" data-bind="template: { name: fakoPanel().template, data: fakoPanel(), afterRender: fakoPanel().afterRender }"></div>
                    <!-- END BOARD CONTENT -->
                    <div data-bind="attr: {class: fakoPanel().icon}"></div>
                </div>
            <!-- /ko -->
            <!-- ko if:fakoPanel().template!='tour-panel-template' -->
                <div class="sub-head" data-bind="css: {calSelectedPanelActive: !fakoPanel().calendarHidden()}">
                    <!-- CENTER BLOCK -->
                    <div class="center-block">
                        <!-- PANEL -->
                        <div class="panel"
                             data-bind="template: { name: fakoPanel().template, data: fakoPanel, afterRender: fakoPanel().afterRender }">
                        </div>
                        <!-- END PANEL -->
                    </div>
                    <div data-bind="attr: {class: fakoPanel().icon}"></div>
                </div>
            <!-- /ko -->
            <!-- END CONSTRUCTOR -->
            <div class="leftPageBtn" data-bind="swapPanel: {to: fakoPanel().prevPanel}"></div>
            <div class="rightPageBtn" data-bind="swapPanel: {to: fakoPanel().nextPanel}"></div>
        </div>
        <!-- CALENDAR -->
        <div class="calenderWindow z-indexTop" data-bind="template: {name: 'calendar-template-hotel', afterRender: reRenderCalendar}" style="top: -302px; display: none;"></div>
        <!-- END CALENDAR -->
    </div>
    <!-- /ko -->

    <!-- SUB HEAD IF WE NOT ON THE MAIN -->
    <!-- ko ifnot: in1 -->
    <div class="sub-head" data-bind="css: {calSelectedPanelActive: !fakoPanel().calendarHidden()}">
        <!-- CENTER BLOCK -->
            <div class="center-block">
                <!-- PANEL -->
                <div class="panel"
                     data-bind="template: { name: fakoPanel().template, data: fakoPanel, afterRender: fakoPanel().afterRender }">
                </div>
                <!-- END PANEL -->
            </div>
            <!-- END CENTER BLOCK -->
	    <div class="calenderWindow z-indexTop" data-bind="template: {name: 'calendar-template', afterRender: reRenderCalendar}" 	
             style="top: 64px; display: none;"></div>
        <!--====**********===-->
    </div>
    <!-- /ko -->
    <!-- END SUB HEAD -->
    <!--====**********===-->
    <!-- ALL CONTENT -->
    <!-- ko ifnot: in1 -->
    <div class="center-block"
         data-bind="template: {name: activeView(), data: viewData(), afterRender: contentRendered}">
    </div>
    <!-- /ko -->
    <!-- SLIDE TOURS -->
    <!-- ko if:in1 -->
        <div class="slideTours" data-bind="template: {name: 'event-index', data: events, afterRender: mapRendered}"></div>
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
<span data-bind="template: {if: in1, name: 'event-map', data: events}"></span>
<!-- END MAPS -->
<div id="loadWrapBg" class="loadWrapBg" style='display: none;'>
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
<div id="loadWrapBgMin" class="loadWrapBg" style='display: none;'>
    <div id="loadContentWinMin"></div>
</div>
<div class="mainWrapBg" style='display: none;'>
	<div class="projectPopUp">
			
			<div class="centerImg">

			</div>
            <div class="itemsProj">

            </div>
            <!-- ДЛЯ ЖЕНИ -->
            <ul class="textSlideProj">
                <li class="items" rel="<?= $theme ?>/images/about01.jpg">
                    <h3>Анализ динамики за целый месяц</h3>
                    <p>Можно подобрать самый дешевый билет, если вам не столь важна дата поездки.
                        Аналогично  подобрать самый дешевый билет, если вам не столь важна дата поездки.
                        Третья строчка, если вам не столь важна дата поездки.</p>
                </li>
                <li class="items" rel="<?= $theme ?>/images/about02.jpg"><h3>Анализ динамики за целый месяц 2 </h3>
                    <p>Можно подобрать самый дешевый билет, если вам не столь важна дата поездки.
                        Аналогично  подобрать самый дешевый билет, если вам не столь важна дата поездки.
                        Третья строчка, если вам не столь важна дата поездки.</p>
                </li>
                <li class="items" rel="<?= $theme ?>/images/about03.jpg"><h3>Анализ динамики за целый месяц 3</h3>
                    <p>Можно подобрать самый дешевый билет, если вам не столь важна дата поездки.
                        Аналогично  подобрать самый дешевый билет, если вам не столь важна дата поездки.
                        Третья строчка, если вам не столь важна дата поездки.</p>
                </li>
                <li class="items" rel="<?= $theme ?>/images/about04.jpg"><h3>Анализ динамики за целый месяц 4</h3>
                    <p>Можно подобрать самый дешевый билет, если вам не столь важна дата поездки.
                        Аналогично  подобрать самый дешевый билет, если вам не столь важна дата поездки.
                        Третья строчка, если вам не столь важна дата поездки.</p>
                </li>
            </ul>

		<div class="counters">
			<div class="bgCount">

			</div>
		</div>
		<div id="boxClose" onclick="closePopUpProj()" ></div>
	</div>
	<div class="naviProj">
		<div class="left" onclick="ClikLeftProj()"></div>
		<div class="right" onclick="ClikRightProj()"></div>
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