<?php
/**
 * A view used to create new {@link User} models
 * @var User $model The User model to be inserted
 */

$this->breadcrumbs = array(
    'Бронирование'=>array('/admin/booking/'),
    'Перелёт',
);

$this->beginWidget("AAdminPortlet",
    array(
        "sidebarMenuItems" => $items,
        "title" => "Поиск перелёта"
    ));
?>

<?php echo $this->renderPartial('_form_flight', array('model'=>$flightForm, 'autosearch'=>$autosearch, 'fromCityName'=>$fromCityName, 'toCityName'=>$toCityName)); ?>

<span id='flight-search-result'></span>

<?php
$this->endWidget(); ?>