<?php
/**
 * A view used to update {@link AAuthTask} models
 * @var AAuthTask $model The AAuthTask model to be updated
 */

$this->breadcrumbs = array(
    'RBAC' => array('rbac/index'),
    'Задачи' => array('index'),
    $model->name => array('view', 'slug' => $model->slug),
    'Редактировать',
);

$this->beginWidget("AAdminPortlet",
    array(

        "title" => "Edit Authorisation Task: " . $model->name,
        "menuItems" => array(
            array(
                "label" => "View",
                "url" => array("/admin/rbac/task/view", "slug" => $model->slug),
            ),
            array(
                "label" => "Delete",
                "url" => "#",
                'linkOptions' => array(
                    'class' => 'delete',
                    'submit' => array('delete', 'slug' => $model->slug),
                    'confirm' => 'Are you sure you want to delete this item?'
                ),
            )
        )
    ));
?>
<?php echo $this->renderPartial('_form', array('model' => $model)); ?>
<?php
$this->endWidget();
?>