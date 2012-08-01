<?php
/** @var $this Controller */
/** @var breadcrumbs  */
$this->breadcrumbs=array(
    'Туры'=>array('admin'),
    $order->name,
);

$this->beginWidget("AAdminPortlet", array(
    "menuItems" => array(
        array(
            "label" => "Создать",
            "url" => array("constructor/new/clear/1"),
        ),
    ),
    "sidebarMenuItems" => array(
        array(
            "label" => "Редактировать",
            "url" => array("constructor/new"),
        ),
    ),
    "title" => 'Тур " '.$order->name.'"'
));
?>

<?php $this->widget('site.common.widgets.tourViewer.TourViewerWidget',array('urlToBasket'=>$this->createUrl('/admin/tour/basket/show'), 'pathToAirlineImg'=>'http://frontend.voyanga/img/airlines/')); ?>

<?php $this->endWidget(); ?>