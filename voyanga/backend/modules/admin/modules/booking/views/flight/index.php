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
        "title" => "Поиск перелёта"
    ));
?>

<?php echo $this->renderPartial('_form_flight', array('model'=>$flightForm)); ?>

<div id='results'>Результат:</div>

<?php
$this->endWidget(); ?>