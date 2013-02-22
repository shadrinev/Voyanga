<?php
$cs = Yii::app()->getClientScript();
$cs->reset();
$images = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('frontend.www.themes.v2.assets'));
$theme = Yii::app()->theme->baseUrl;
if (YII_DEBUG || Yii::app()->clientScript->buildingMode)
{
    Yii::app()->clientScript->registerPackage('appCss');
    Yii::app()->clientScript->registerPackage('appJs');
}
else
{
    Yii::app()->clientScript->registerCssFile('/themes/v2/css/all.min.css');
    Yii::app()->clientScript->registerScriptFile('/js/all.min.js');
}
Yii::app()->clientScript->registerScriptFile('/js/runApp.js');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<!--[if IE 8 ]>    <html xmlns="http://www.w3.org/1999/xhtml" class="ie ie8"> <![endif]-->
<!--[if IE 9 ]>    <html xmlns="http://www.w3.org/1999/xhtml" class="ie ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html xmlns="http://www.w3.org/1999/xhtml" class=""> <!--<![endif]-->
<head>
    <link rel="shortcut icon" href="<?= $theme ?>/images/favicon.png" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <?php if (!isset($this->title)) $title = Yii::app()->params['title.default']; else $title = $this->title ?>
    <title><?php echo $title ?></title>
    <script type="text/javascript"
            src="//maps.googleapis.com/maps/api/js?key=AIzaSyBdPg3WqRnITMLhY4OeXyk4bCa4qBEdF8U&sensor=false">
    </script>
</head>
<body data-bind="css: {fixed: in1}">
<?php $this->renderPartial('//layouts/_counters'); ?>
<script type="text/javascript">
    window.currentCityCode = '<?php echo Geoip::getCurrentCity()->code;?>';
    $(function(){
        Raven.config('<?php echo Yii::app()->params['sentry.dsn']; ?>').install()
    })
</script>

<?php echo $content; ?>
<div class="wrapper" data-bind="css: {'scroll-none': in1}">

    <?php echo $this->renderPartial('//layouts/_header'); ?>

    <!-- BOARD IF WE ARE AT THE MAIN -->
    <!-- ko if:in1 -->
    <div class="panel-index">

        <div class="board" data-bind="style: {height: fakoPanel().height}">
            <div class="newTitleHead">
                <div class="leftPoint" data-bind="swapPanel: {to: fakoPanel().prevPanel}">Только отели<span></span></div>
                    <h1 class="title"><span data-bind="html: fakoPanel().mainLabel"></span></h1>
                <div class="rightPoint" data-bind="swapPanel: {to: fakoPanel().nextPanel}"><span></span>Только авиабилеты</div>
            </div>
            <!-- ko if:fakoPanel().template=='tour-panel-template' -->
                <div class="constructor">
                    <!-- BOARD CONTENT -->
                        <div class="board-content" data-bind="template: { name: fakoPanel().template, data: fakoPanel(), afterRender: fakoPanel().afterRender }"></div>
                    <!-- END BOARD CONTENT -->
                    <div data-bind="attr: {'class': fakoPanel().icon}"></div>
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
                    <div data-bind="attr: {'class': fakoPanel().icon}"></div>
                </div>
            <!-- /ko -->
            <!-- END CONSTRUCTOR -->
            <div class="leftPageBtn" data-bind="swapPanel: {to: fakoPanel().prevPanel}"></div>
            <div class="rightPageBtn" data-bind="swapPanel: {to: fakoPanel().nextPanel}"></div>
        </div>
        <!-- CALENDAR -->
        <div class="calenderWindow z-indexTop" data-bind="template: {name: 'calendar-template-hotel', afterRender: reRenderCalendar}" style="top: -302px; height: 0;"></div>
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
    <?php echo $this->renderPartial('//layouts/_footer'); ?>
    <!-- END FOOTER-->

</div>
<div class="gShL"></div>
<div class="gShR"></div>

<!-- END WRAPPER -->
<!-- MAPS -->
<!-- FIXME -->
<span data-bind="template: {'if': in1, 'name': 'event-map', 'data': events, 'afterRender': events.afterRender}"></span>
<!-- END MAPS -->

<?php
$templates = Yii::app()->params['frontend.app.templates'];
foreach ($templates as $template)
{
    echo "<!-- START OF TEMPLATE $template -->\n";
    $this->renderPartial('www.themes.v2.views.' . $template);
    echo "<!-- END OF TEMPLATE $template -->\n";
}
?>
<?php echo $this->renderPartial('//layouts/_popup', array('theme'=>$theme)); ?>
</body>
</html>
