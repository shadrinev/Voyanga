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

<?php $this->renderPartial('/constructor/_tour', array('showDelete'=>false, 'showSaveTour'=>false)); ?>

<?php $this->endWidget(); ?>