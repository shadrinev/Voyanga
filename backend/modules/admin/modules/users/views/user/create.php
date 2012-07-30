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
        "title" => "Создание нового пользователя"
    ));
?>
<p class='info box'>Чтобы создать нового пользователя заполните форму ниже.</p>
<?php echo $this->renderPartial('_form', array('model' => $model)); ?>
<?php
$this->endWidget();