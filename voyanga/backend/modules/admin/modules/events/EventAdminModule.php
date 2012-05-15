<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 15.05.12
 * Time: 12:10
 */
class EventAdminModule extends ABaseAdminModule
{
    /**
     * The menu items to show for this module.
     * These menu items will be shown in the sidebar in the admin interface
     * @see CMenu::$items
     * @var array
     */
    protected $_menuItems = array(
        array(
            "label" => "События",
            "url" => "#",
            "linkOptions" => array(
                "icon" => "icon-music",
            ),
            "items" => array(
                array(
                    "label" => "События",
                    "url" => array("/admin/events/event/index"),
                ),
                array(
                    "label" => "Категориии",
                    "url" => array("/admin/events/eventCategory/"),
                ),
            )
        )
    );
}
