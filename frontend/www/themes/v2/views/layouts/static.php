<?php
$cs = Yii::app()->getClientScript();
$images = Yii::app()->assetManager->getPublishedUrl(Yii::getPathOfAlias('frontend.www.themes.v2.assets'));
$theme = Yii::app()->theme->baseUrl;
if (YII_DEBUG || Yii::app()->clientScript->buildingMode)
{
    Yii::app()->clientScript->registerPackage('appCss');
    Yii::app()->clientScript->registerPackage('appJs');
}
else
{
    $path = Yii::getPathOfAlias('webroot');
    $suffix = require_once($path.'/suffix.php');
    Yii::app()->clientScript->registerCssFile('/themes/v2/css/all'.$suffix.'.min.css');
    Yii::app()->clientScript->registerScriptFile('/js/all'.$suffix.'.min.js');
}
Yii::app()->clientScript->registerPackage('landing');
Yii::app()->clientScript->registerScriptFile('/js/enterCredentials.js');
Yii::app()->clientScript->registerScriptFile('/js/completed.js');
Yii::app()->clientScript->registerScriptFile('/js/eventPage.js');
Yii::app()->clientScript->registerScriptFile('/js/tourPage.js');
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
<body>
<?php $this->renderPartial('//layouts/_counters'); ?>
<script type="text/javascript">
    window.currentCityCode = '<?php echo Geoip::getCurrentCity()->code;?>';
    window.currentCityCodeReadable = '<?php echo Geoip::getCurrentCity()->localRu;?>';
    $(function(){
        Raven.config('<?php echo Yii::app()->params['sentry.dsn']; ?>').install()
    })
</script>

<div class="wrapper">

    <?php echo $this->renderPartial('//layouts/_header'); ?>

    <?php echo $content; ?>

    <!-- FOOTER -->
    <?php echo $this->renderPartial('//layouts/_footer'); ?>
    <!-- END FOOTER-->
</div>
</div>
<!-- END WRAPPER -->
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
