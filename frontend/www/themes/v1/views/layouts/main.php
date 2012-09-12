<?php if (!isset($active)) $active = 'other'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- styles -->
    <link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl; ?>/css/application.css">

    <!-- for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le fav and touch icons -->
    <link rel="shortcut icon" href="<?php echo Yii::app()->theme->baseUrl; ?>/images/favicon.ico">
    <!--<link rel="apple-touch-icon" href="<?php echo Yii::app()->theme->baseUrl; ?>/images/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="72x72"
          href="<?php echo Yii::app()->theme->baseUrl; ?>/images/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114"
          href="<?php echo Yii::app()->theme->baseUrl; ?>/images/apple-touch-icon-114x114.png">-->
    <?php
    Yii::app()->getClientScript()->registerScriptFile('/js/utils.js');
    ?>
</head>
<body>
<div id="loadingDiv" class="hide">
    <img src='/img/spinner_big.gif'>
</div>
<div id="errorAjax" class="hide alert alert-error">Server exception</div>
<div class="wrapper">
    <div class="container">
        <?php $this->widget('bootstrap.widgets.BootTabbable', array(
        'type'=>'tabs', // 'tabs' or 'pills'
        'tabs'=>array(
            array('label'=>'Конструктор', 'content'=>$content['tour'], 'active'=>($active=='tour')),
            array('label'=>'Авиа', 'content'=>$content['avia'], 'active'=>($active=='avia')),
            array('label'=>'Отели', 'content'=>$content['hotel'], 'active'=>($active=='hotel')),
            array('label'=>'Остальное', 'content'=>$content['other'], 'active'=>($active=='other')),
            ),
        )); ?>
    </div>
    <div class="push"><!--//--></div>
</div>

<footer class="container-fluid footer">
    <p>Copyright &copy; <?php echo date('Y'); ?> by Voyanga.com<br/>
      All Rights Reserved.<br/>
</footer>
<script type="text/javascript">
$(function(){
    $('#loadingDiv')
        .ajaxStart(function() {
            $(this).show();
        })
        .ajaxStop(function() {
            $(this).hide();
        });

    $('#ajaxError')
        .ajaxError(function() {
            var that = $(this);
            that.show();
            console.log('Error happens');
            setTimeout(function(){
                that.hide('fast');
            }, 1000);
        });

});
</script>
</body>
</html>