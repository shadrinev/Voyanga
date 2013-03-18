<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 18.03.13
 * Time: 18:21
 * To change this template use File | Settings | File Templates.
 */

class DictionariesAdminModule extends ABaseAdminModule
{
    /**
     * The menu items to show for this module.
     * These menu items will be shown in the sidebar in the admin interface
     * @see CMenu::$items
     * @var array
     */
    protected $_menuItems = array(
        array(
            "label" => "Словари",
            "url" => "#",
            "linkOptions" => array(
                "icon" => "icon-music",
            ),
            "items" => array(
                array(
                    "label" => "Добавление городов и аэропортов",
                    "url" => array("/admin/dictionaries/cities/admin"),
                ),
            )
        )
    );
}
