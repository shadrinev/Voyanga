<?php
/**
 * Shows a list of {@link AAuthRole} models
 * @var AAuthRole $model The AAuthRole model used for searching
 */
$this->breadcrumbs = array(
    'RBAC' => array('rbac/index'),
    'Роли'
);

$this->beginWidget("AAdminPortlet", array(
    "menuItems" => array(
        array(
            "label" => "Создать",
            "url" => array("/admin/rbac/role/create"),
        ),
    ),
    "title" => "Роли авторизации"
));
?>
<p class='info box'>Roles are groups of tasks and operations that can be assigned to one or more users. Users can have
    many roles.</p>
<?php $this->widget('bootstrap.widgets.BootGridView', array(
    'id' => 'aauth-role-grid',
    'dataProvider' => $model->search(),
    'filter' => $model,
    'columns' => array(
        array(
            "name" => "name",
            "value" => '$data->createLink()',
            "type" => "raw",
        ),
        'description',

    ),
));

$this->endWidget();
?>
