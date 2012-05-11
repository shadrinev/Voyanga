<?php
/**
 * Displays information for a particular {@link User} model
 * @var User $model The User model to show
 */
$this->breadcrumbs = array(
    'Пользователи' => array('index'),
    $model->name,
);
$this->beginWidget("AAdminPortlet",
    array(

        "title" => $model->name,
        "sidebarMenuItems" => array(
            array(
                "label" => "Информация",
                "url" => array("/admin/users/user/view", "id" => $model->id),
            ),
            array(
                "label" => "Группы",
                "url" => array("/admin/users/user/groups", "id" => $model->id),
            ),
        ),
        "menuItems" => array(
            array(
                "label" => "Редактировать",
                "url" => array("/admin/users/user/update", "id" => $model->id),
            ),
            /*array(
                "label" => "Impersonate",
                "url" => "#",
                'linkOptions' => array(
                    'submit' => array('impersonate', 'id' => $model->id),
                    'confirm' => 'Are you sure you want to impersonate this user? You will be logged out of your account and will have to log back in to access the admin section.'
                ),
            ),*/
            array(
                "label" => "Удалить",
                "url" => "#",
                'linkOptions' => array(
                    'class' => 'delete',
                    'submit' => array('delete', 'id' => $model->id),
                    'confirm' => 'Вы действительно хотите удалить пользователя '.$model->name.'?',
                ),
            )
        )
    ));
?>
<?php echo CHtml::encode($model->name) . " принадлежит к " . $model->totalGroups . " группе(-ам)"; ?>
<?php

$this->endWidget();