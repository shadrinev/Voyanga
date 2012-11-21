<?php
$cs = Yii::app()->getClientScript();
$cs->reset();
$images = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('frontend.www.themes.v2.assets'));
$theme = Yii::app()->theme->baseUrl;
Yii::app()->clientScript->registerPackage('appCss');
Yii::app()->clientScript->registerPackage('appJs');
Yii::app()->clientScript->registerScriptFile('/js/enterCredentials.js');
Yii::app()->clientScript->registerScriptFile('/js/eventPage.js');
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
<body>
<div class="wrapper">
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
                    <?php $currentModule = trim(strtolower(Yii::app()->user->getState('currentModule'))) ?>
                    <?php echo '!'.$currentModule.'!' ?>
                    <li id="h-tours-slider" class="planner btn<?php if ($currentModule=='tours') echo ' active'?>"><a href="/#tours">Планировщик</a></li>
                    <li id="h-avia-slider" class="aviatickets btn<?php if ($currentModule=='avia') echo ' active'?>"><a href="/#avia">Авиабилеты</a>
                    </li>
                    <li id="h-hotels-slider" class="hotel btn<?php if ($currentModule=='hotels') echo ' active'?>"><a href="/#hotels">Отели</a></li>
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

    <?php echo $content; ?>

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
