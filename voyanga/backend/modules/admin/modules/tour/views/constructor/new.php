<?php
/**
 * A view used to create new {@link User} models
 * @var User $model The User model to be inserted
 */

$this->breadcrumbs = array(
    'Тур' => array('index'),
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

<h3>Добавьте отель:</h3>
<?php echo $this->renderPartial('_form_hotel'); ?>

<hr>
<h3>Результат:</h3>
<?php echo $this->renderPartial('_tour'); ?>
<?php
$this->endWidget(); ?>