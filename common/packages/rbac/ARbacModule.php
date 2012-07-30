<?php

/**
 * Provides administration functionality for Role Based Access Control features.
 * @author Charles Pick
 * @package packages.rbac
 */
class ARbacModule extends ABaseAdminModule
{
    public function init()
    {
        Yii::import('packages.rbac.models.*');
        Yii::import('packages.linkable.*');
    }

    /**
     * The menu items to show for this module.
     * These menu items will be shown in the sidebar in the admin interface
     * @see CMenu::$items
     * @var array
     */
    protected $_menuItems = array(
        array(
            "label" => "RBAC",
            "linkOptions" => array(
                "class" => "group icon",
            ),
            "items" => array(
                array(
                    "label" => "Роли",
                    "url" => array("/admin/rbac/role/index"),
                ),
                array(
                    "label" => "Задачи",
                    "url" => array("/admin/rbac/task/index"),
                ),
                array(
                    "label" => "Операции",
                    "url" => array("/admin/rbac/operation/index"),
                )
            )
        )
    );

    public $defaultController = "rbac";

}
