<?php
/**
 * User: oleg
 * Date: 30.05.12
 * Time: 14:08
 * To change this template use File | Settings | File Templates.
 */
class LoggingAdminModule extends ABaseAdminModule
{
    /**
     * The menu items to show for this module.
     * These menu items will be shown in the sidebar in the admin interface
     * @see CMenu::$items
     * @var array
     */
    protected $_menuItems = array(
        array(
            "label" => "Логирование",
            "url" => "#",
            "linkOptions" => array(
                "icon" => "icon-music",
            ),
            "items" => array(
                array(
                    "label" => "GDS Запросы",
                    "url" => array("/admin/logging/gdsRequest"),
                ),
                array(
                    "label" => "Hotel Запросы",
                    "url" => array("/admin/logging/hotelRequest"),
                ),
                array(
                    "label" => "GeoNames",
                    "url" => array("/admin/logging/geoNames"),
                ),
            )
        )
    );
}
