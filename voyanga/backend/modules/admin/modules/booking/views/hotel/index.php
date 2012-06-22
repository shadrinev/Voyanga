<?php
/**
 * A view used to create new {@link User} models
 * @var User $model The User model to be inserted
 */

$this->breadcrumbs = array(
    'Бронирование'=>array('/admin/booking/'),
    'Гостиница'=>array('/admin/booking/hotel'),
    'Поиск'
);

$this->beginWidget("AAdminPortlet",
    array(
        "sidebarMenuItems" => $items,
        "title" => "Поиск гостиницы"
    ));
?>

<?php echo $this->renderPartial('_form_hotel', array('model'=>$hotelForm, 'autosearch'=>$autosearch, 'cityName'=>$cityName, 'duration'=>$duration)); ?>


<?php
$this->endWidget(); ?>