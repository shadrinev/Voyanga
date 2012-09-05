<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 03.09.12
 * Time: 11:57
 * To change this template use File | Settings | File Templates.
 */
class PartnersAdminModule extends ABaseAdminModule
{
    /**
     * The menu items to show for this module.
     * These menu items will be shown in the sidebar in the admin interface
     * @see CMenu::$items
     * @var array
     */
    protected $_menuItems = array(
        array(
            "label" => "Партнерка",
            "url" => "#",
            "linkOptions" => array(
                "icon" => "icon-music",
            ),
            "items" => array(
                array(
                    "label" => "Партнеры",
                    "url" => array("/admin/partners/partnerManage"),
                ),
            )
        )
    );
}