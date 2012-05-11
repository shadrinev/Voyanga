<?php
/**
 * Provides user administration functions
 * @author Charles Pick
 * @package packages.users.admin
 */
class AUserAdminModule extends ABaseAdminModule
{
    /**
     * The menu items to show for this module.
     * These menu items will be shown in the sidebar in the admin interface
     * @see CMenu::$items
     * @var array
     */
    protected $_menuItems = array(
        array(
            "label" => "Работа с пользователями",
            "url" => "#",
/*            "linkOptions" => array(
                "class" => "user icon",
            ),*/
            "items" => array(
                array(
                    "label" => "Пользователи",
                    "url" => array("/admin/users/user/index"),
                ),
                array(
                    "label" => "Группы",
                    "url" => array("/admin/users/group/index"),
                ),
            )
        )
    );
}