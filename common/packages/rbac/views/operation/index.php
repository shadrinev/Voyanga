<?php
/**
 * Shows a list of {@link AAuthRole} models
 * @var AAuthRole $model The AAuthRole model used for searching
 */
$this->breadcrumbs = array(
    'RBAC' => array('rbac/index'),
    'Операции'
);

$this->beginWidget("AAdminPortlet", array(
    "menuItems" => array(
        array(
            "label" => "Создать",
            "url" => array("/admin/rbac/operation/create"),
        ),
    ),
    "title" => "Операции авторизации"
));
?>
<p class='info box'>Operations are the lowest level in the authorisation hierarchy, they can be assigned to tasks or
    directly to roles</p>
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
