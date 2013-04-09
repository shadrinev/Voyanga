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
    <?php if (!isset($this->description)) $metadesc = Yii::app()->params['description.default']; else $metadesc = $this->description ?>
    <meta name="description" content="<?php echo $metadesc ?>"/>
    <?php if (!YII_DEBUG): ?>
        <script>
            var _rollbarParams = {"server.environment": "<?php echo Yii::app()->params['env.code'] ?>"};
            _rollbarParams["notifier.snippet_version"] = "2"; var _rollbar=["07c62e6cb5334856804ec3a260644fda", _rollbarParams]; var _ratchet=_rollbar;
            (function(w,d){w.onerror=function(e,u,l){_rollbar.push({_t:'uncaught',e:e,u:u,l:l});};var i=function(){var s=d.createElement("script");var
                f=d.getElementsByTagName("script")[0];s.src="//d37gvrvc0wt4s1.cloudfront.net/js/1/rollbar.min.js";s.async=!0;
                f.parentNode.insertBefore(s,f);};if(w.addEventListener){w.addEventListener("load",i,!1);}else{w.attachEvent("onload",i);}})(window,document);
        </script>
    <?php endif ?>
    <script type="text/javascript"
            src="//maps.googleapis.com/maps/api/js?key=AIzaSyBdPg3WqRnITMLhY4OeXyk4bCa4qBEdF8U&sensor=false">
    </script>
</head>
<body>
<?php $this->renderPartial('//layouts/_counters'); ?>
<script type="text/javascript">
    window.currentCityCode = '<?php echo Geoip::getCurrentCity()->code;?>';
    window.currentCityCodeReadable = '<?php echo Geoip::getCurrentCity()->localRu;?>';
    window.fromPartner = '<?php echo Yii::app()->user->getState('fromPartner', 0) ?>';
    window.pid = '<?php echo Partner::getCurrentPartnerKey() ?>';
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
<?php echo $this->renderPartial('//layouts/_popup', array('theme'=>$theme,'page'=>'static')); ?>
<div id="trackCode">11-11</div>
</body>
</html>
