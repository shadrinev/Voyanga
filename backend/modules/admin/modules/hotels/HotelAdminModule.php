<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 17.08.12
 * Time: 12:08
 * To change this template use File | Settings | File Templates.
 */
class HotelAdminModule extends ABaseAdminModule
{
    /**
     * The menu items to show for this module.
     * These menu items will be shown in the sidebar in the admin interface
     * @see CMenu::$items
     * @var array
     */
    protected $_menuItems = array(
        array(
            "label" => "Отели",
            "url" => "#",
            "linkOptions" => array(
                "icon" => "icon-music",
            ),
            "items" => array(
                array(
                    "label" => "Мэппинг названий номеров",
                    "url" => array("/admin/hotels/roomNames/manage"),
                ),
                array(
                    "label" => "Русские названия номеров",
                    "url" => array("/admin/hotels/roomNames/rusNamesManage"),
                ),
            )
        )
    );
}
