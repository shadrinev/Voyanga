<?php
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

<?php $this->widget('site.common.widgets.tourViewer.TourViewerWidget',array('urlToBasket'=>$this->createUrl('/tour/basket/show'))); ?>

<?php $this->endWidget(); ?>