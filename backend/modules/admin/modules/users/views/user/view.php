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
<?php
if (Yii::app()->getModule("users")->enableProfileImages)
{
    $this->widget("packages.users.widgets.AUserImageWidget",
        array(
            "user" => $model,
            "attribute" => 'thumbnail',
            "htmlOptions" => array(
                "class" => "left thumbnail"
            )
        ));
}
?>
<h2><?php echo CHtml::link(CHtml::encode($model->name), array('view', 'id' => $model->id)); ?></h2>
<b><?php echo CHtml::encode($model->getAttributeLabel('email')); ?>:</b>
<?php echo CHtml::encode($model->email); ?>
<br/>
<?php $this->widget('zii.widgets.CDetailView', array(
    'data' => $model,
    'htmlOptions' => array(
        "class" => "detail-view right",
    ),
    'attributes' => array(
        'id',
        'name',
        'email',
        'requireNewPassword:boolean'
    ),
)); ?>
<?php

$this->endWidget();