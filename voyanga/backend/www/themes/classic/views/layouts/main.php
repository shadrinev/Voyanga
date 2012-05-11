<?php $additionalItems = isset(Yii::app()->getModule("admin")->mainMenu) ? Yii::app()->getModule("admin")->mainMenu : array();
?>
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
</head>
<body>
<?php $this->widget('bootstrap.widgets.BootNavbar', array(
    'fixed' => BootNavbar::FIXED_TOP,
    'brand' => 'Voyanga',
    'brandUrl' => $this->createAbsoluteUrl('//'),
    'collapse' => true,
    'items' => array(
        array(
            'class' => 'bootstrap.widgets.BootMenu',
            'items' => CMap::mergeArray(array(
                array('label' => 'Home', 'url' => array('/site/index')),
                array('label' => 'About', 'url' => array('/site/page', 'view' => 'about')),
                array('label' => 'Contact', 'url' => array('/site/contact')),
                ),
                $additionalItems
            ),
        ),
        array(
            'class'=>'bootstrap.widgets.BootButtonGroup',
            'htmlOptions'=>array('class'=>'pull-right'),
            'type'=>'primary', // '', 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
            'buttons'=>array(
                array('label'=>Yii::t('admin','Войти'), 'url'=>'/users/user/login'),
                array('items'=>array(
                    array('label'=>Yii::t('admin','Зарегистрироваться'), 'url'=>'/users/user/register'),
                )),
            ),
            'visible' => !Yii::app()->user->isGuest
        ),
        array(
            'class'=>'bootstrap.widgets.BootButton',
            'htmlOptions'=>array('class'=>'pull-right'),
            'url' => array('/users/user/logout'),
            'label'=>Yii::t('admin','Выйти'),
            'type'=>'primary', // '', 'primary', 'info', 'success', 'warning', 'danger' or 'inverse'
            'size'=>'large', // '', 'large', 'small' or 'mini',
            'visible' => !Yii::app()->user->isGuest
        ),
        array(
            'class'=>'bootstrap.widgets.BootMenu',
            'htmlOptions'=>array('class'=>'pull-right'),
            'items'=>array(
                array('label' => isset(Yii::app()->user->model->name) ? Yii::app()->user->model->name : '', 'url' => array('/users/user/account'), 'visible' => !Yii::app()->user->isGuest),
            ),
        ),
    ),
)); ?>

<div class="wrapper">
    <div class="container">
        <?php if (isset($this->breadcrumbs)): ?>
        <?php $this->widget('bootstrap.widgets.BootBreadcrumbs', array(
            'links' => $this->breadcrumbs,
        )); ?>
        <?php endif?>
        <?php $this->widget('bootstrap.widgets.BootAlert'); ?>
        <?php echo $content; ?>
        <div class="push"><!--//--></div>
    </div>
</div>

<footer class="container-fluid footer">
        <p>Copyright &copy; <?php echo date('Y'); ?> by Voyanga.com<br/>
            All Rights Reserved.<br/>
        <?php echo Yii::powered(); ?></p>
</footer>
</body>
</html>