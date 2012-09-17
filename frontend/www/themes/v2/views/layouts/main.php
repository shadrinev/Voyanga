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
    <script type="text/javascript" async
      src="http://maps.googleapis.com/maps/api/js?key=AIzaSyBdPg3WqRnITMLhY4OeXyk4bCa4qBEdF8U&sensor=false">
    </script>

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
            <div class="panel" data-bind="template: {name: activeModule() + '-panel-template', data: panel()}">
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
            <div id="content" data-bind="template: {name: activeView(), data: viewData(), afterRender: contentRendered}">
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
</div>
<?php
    $templates = Array('avia.index', 'avia.results', 'avia.popup',
                       'avia.panel', 'avia.filters', 'avia.cheapest',
                       'hotels.index', 'hotels.results', 'hotels.panel',
                       'hotels.popup', 'hotels.filters','hotels.info','hotels.timeline'
        );
    foreach($templates as $template)
    {
        $this->renderPartial('www.themes.v2.views.' . $template);
    }
?>
</body>
</html>
