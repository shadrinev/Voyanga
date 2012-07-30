<?php
/**
 * Shows a list of {@link AAuthTask} models
 * @var AAuthTask $model The AAuthTask model used for searching
 */
$this->breadcrumbs = array(
    'RBAC' => array('rbac/index'),
    'Задачи'
);

$this->beginWidget("AAdminPortlet", array(
    "menuItems" => array(
        array(
            "label" => "Создать",
            "url" => array("/admin/rbac/task/create"),
        ),
    ),
    "title" => "Задачи авторизации"
));
?>
<p class='info box'>Tasks are groups of operations that can be assigned to one or more roles.</p>
<?php $this->widget('bootstrap.widgets.BootGridView', array(
    'id' => 'aauth-task-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
        array(
            "name" => "name",
            "value" => 'CHtml::link($data->name,array("view","slug" => $data->slug))',
            "type" => "raw",
        ),
        'description',

    ),
));
$this->endWidget();
?>