<?php
$am = Yii::app()->getAssetManager();
$assets = realpath(dirname(__FILE__).'/../../');
$path = $am->publish($assets, false, -1, YII_DEBUG);

$cs = Yii::app()->getClientScript();
$cs->reset();
$js = Array("jquery-1.7.2.min.js", "jquery.dotdotdot-1.5.1.js", "knockout-2.1.0.js" , "resize-new.js",
            'slide-mode.js', 'popup.js', 'tickets.js', 'panel.js', 'script.js',
            'data.js', "flightSearchResults.js");
foreach($js as $file)
{
    $cs->registerScriptFile($path.'/js/'.$file);
}
$cs->registerCssFile($path.'/css/reset.style.css');
$cs->registerCssFile($path.'/css/style.css');
$cs->registerCssFile($path.'/css/popup.css');
?>
<?php echo $content; ?>