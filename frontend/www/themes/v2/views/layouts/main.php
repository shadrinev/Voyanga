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
                    <li id="h-avia-slider" class="aviatickets btn" data-bind="click: slider.click"><a href="#">Авиабилеты</a></li>
                    <li id="h-hotels-slider" class="hotel btn" data-bind="click: slider.click"><a href="#hotels">Отели</a></li>
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
            <div class="panel" data-bind="template: {name: activeModule() + '-panel-template', data: panel(), afterRender: contentRendered}">
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
            <div id="content" data-bind="template: {name: activeView(), data: viewData()}">
            </div>
            <!-- END MAIN BLOCK -->
            <!-- FILTER BLOCK -->
            <!-- ko if: _sidebar() != 'dummy' -->
            <div class="filter-block" data-bind="template: {name: activeSidebar(), data: sidebarData()}">
            </div>
            <!-- /ko -->
            <!-- END FILTER BLOCK -->
            <div class="clear"></div>
        </div>
        <!-- END ALL CONTENT -->
    </div>


    <!-- ==== POPUP === -->
    </div>
<script type="text/html" id="avia-index">
<h1> Hello, INDEX PAGE </h1>
</script>
<!-- FIXME include it -->
<script type="text/html" id="avia-panel-template">
<table class="panelTable AVIA">
	<tr>
		<td class="contTD">
				<div class="data">
					<div class="from">
						<input class="input-path" type="text" placeholder="Куда" data-bind="value: departureCity()">
						<div class="date">
							<span class="f17">12</span>
							<br>
							<span class="month">мая</span>
						</div>
					</div>
					<div class="tumblr">
	                    <label for="there-back">
	                        <div class="one" data-bind="css: {active: !rt()}, click: selectOneWay"></div>
	                        <div class="two" data-bind="css: {active: rt()}, click: selectRoundTrip"></div>
	                        <div class="switch"></div>
	                    </label>
	                    <input id="there-back" type="checkbox" data-bind="checked: rt()">
	                </div>
					<div class="to">
						<input class="input-path" type="text" placeholder="Откуда" data-bind="value: arrivalCity()">
						<div class="date">
							<span class="f17">12</span>
							<br>
							<span class="month">мая</span>
						</div>
					</div>
				</div>
			<div class="how-many-man">
            <div class="content">
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
                    <div class="man" data-bind="repeat: adults()"></div>
                    <div class="child" data-bind="repeat: sum_children()"></div>
                <!-- /ko -->
                </div>
                    <div class="btn"></div>
                    <div class="popup">
                        <div class="adults">
                            <div class="inputDIV">
                                <input type="text" name="adult" data-bind="css: {active: adults() > 0}, value: adults">
                                    <a href="#" class="plusOne" data-bind="click: plusOne" rel="adults">+</a>
                                    <a href="#" class="minusOne" data-bind="click: minusOne" rel="adults">-</a>
                            </div>
                            взрослых
                        </div>
                        <div class="childs">
                            <div class="inputDIV">
                                <input type="text" name="adult2" data-bind="css: {active: children() > 0}, value: children">
                                    <a href="#" class="plusOne" data-bind="click: plusOne" rel="children">+</a>
                                    <a href="#" class="minusOne" data-bind="click: minusOne" rel="children">-</a>
                            </div>
                            детей до 12 лет
                        </div>
                        <div class="small-childs">
                            <div class="inputDIV">
                                <input type="text" name="adult3" data-bind="css: {active: infants() > 0}, value: infants">
                                        <a href="#" class="plusOne" data-bind="click: plusOne" rel="infants">+</a>
                                        <a href="#" class="minusOne" data-bind="click: minusOne" rel="infants">-</a>
                                </div>
                                детей до 2 лет
                            </div>

                        </div>
                    </div>
		</td>
		<td class="btnTD">
			<a class="btn-find" data-bind="click: navigateToNewSearch">Найти</a>
		</td>
	</tr>
</table>

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
<?php
 $this->renderPartial('v2.views.hotels_results');
?>
</body>
</html>
