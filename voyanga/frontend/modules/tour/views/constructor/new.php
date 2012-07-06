<?php
/**
 * A view used to create new {@link User} models
 * @var User $model The User model to be inserted
 */

$this->breadcrumbs = array(
    'Туры' => array('viewer/index'),
    'Создание',
);

?>
<h3>Добавьте перелёт:</h3>
<?php echo $this->renderPartial('_form_flight', array('model'=>$flightForm)); ?>

<h3>Добавьте отель:</h3>
<?php echo $this->renderPartial('_form_hotel', array('model'=>$hotelForm, 'autosearch'=>$autosearch, 'cityName'=>$cityName, 'duration'=>$duration)); ?>

<hr>
<h3>Результат:</h3>
<?php echo $this->renderPartial('_tour'); ?>
