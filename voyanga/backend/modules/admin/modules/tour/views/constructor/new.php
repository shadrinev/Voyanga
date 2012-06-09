<?php
/**
 * A view used to create new {@link User} models
 * @var User $model The User model to be inserted
 */

$this->breadcrumbs = array(
    'Пользователь' => array('index'),
    'Создание',
);

$this->beginWidget("AAdminPortlet",
    array(
        "menuItems" => array(
            array(
                "label" => "Очистить",
                "url" => array("/admin/tour/constructor/drop"),
            ),
        ),
        "title" => "Создание нового тура"
    ));
?>
<h3>Добавьте перелёт:</h3>
<?php echo $this->renderPartial('_form_flight', array('model'=>$flightForm)); ?>

<p class='info box'>Добавьте отель:</p>
<?php echo $this->renderPartial('_form_hotel'); ?>

<hr>
<p class='info box'>Результат:</p>
<?php echo $this->renderPartial('_tour'); ?>
<?php
$this->endWidget(); ?>
<?php $this->renderPartial('_flight_search_result'); ?>