<?php
/**
 * A view used to create new {@link AAuthRole} models
 * @var AAuthRole $model The AAuthRole model to be inserted
 */

$this->breadcrumbs = array(
    'RBAC' => array('rbac/index'),
    'Роли' => array('index'),
    'Создать',
);

$this->beginWidget("AAdminPortlet", array(
    "title" => "Новая роль авторизации"
));
?>
<p class='info box'>Roles are groups of tasks and operations that can be assigned to one or more users. Users can have
    many roles.</p>
<?php echo $this->renderPartial('_form', array('model' => $model)); ?>
<?php $this->endWidget(); ?>