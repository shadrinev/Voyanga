<?php
/**
 * A view used to create new {@link AAuthTask} models
 * @var AAuthTask $model The AAuthTask model to be inserted
 */

$this->breadcrumbs = array(
    'RBAC' => array('rbac/index'),
    'Задачи' => array('index'),
    'Создать',
);

$this->beginWidget("AAdminPortlet", array(
    "title" => "Новая задача авторизации"
));
?>
<p class='info box'>Tasks are groups of operations that can be assigned to one or more roles.</p>
<?php echo $this->renderPartial('_form', array('model' => $model)); ?>
<?php $this->endWidget(); ?>