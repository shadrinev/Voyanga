<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oleg
 * Date: 19.07.12
 * Time: 12:04
 * To change this template use File | Settings | File Templates.
 */
class OrdersAdminModule extends ABaseAdminModule
{
    public function init()
    {
        Yii::import('site.backend.modules.admin.modules.booking.models.*');
    }

    /**
     * The menu items to show for this module.
     * These menu items will be shown in the sidebar in the admin interface
     * @see CMenu::$items
     * @var array
     */
    protected $_menuItems = array(
        array(
            "label" => "Бронирование",
            "url" => "#",
            "linkOptions" => array(
                "icon" => "icon-user",
            ),
            "items" => array(
                array(
                    "label" => "Перелёт",
                    "url" => array("/admin/orders/"),
                ),
                array(
                    "label" => "Гостиница",
                    "url" => array("/admin/booking/hotel/"),
                ),
                array(
                    "label" => "Тур",
                    "url" => array("/admin/booking/tour/"),
                ),
            )
        )
    );
}