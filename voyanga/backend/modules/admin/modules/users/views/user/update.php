<?php
/**
 * A view used to update {@link User} models
 * @var User $model The User model to be updated
 */
$this->breadcrumbs = array(
    'Пользователи' => array('index'),
    $model->name => array('view', 'id' => $model->id),
    'Редактирование',
);

$this->beginWidget("AAdminPortlet",
    array(
        "title" => $model->name,
        "menuItems" => array(
            array(
                "label" => "Просмотреть",
                "url" => array("/admin/users/user/view", "id" => $model->id),
            ),
            array(
                "label" => "Удалить",
                "url" => "#",
                'linkOptions' => array(
                    'class' => 'delete',
                    'submit' => array('delete', 'id' => $model->id),
                    'confirm' => 'Are you sure you want to delete this item?'
                ),
            )
        )
    ));
?>


<?php echo $this->renderPartial('_form', array('model' => $model)); ?>
<?php
$this->endWidget();