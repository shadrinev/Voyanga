<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 15.05.12
 * Time: 12:10
 */
class StatisticAdminModule extends ABaseAdminModule
{
    public function init()
    {
        Yii::import('site.common.components.statistic.reports.*');
    }

    /**
     * The menu items to show for this module.
     * These menu items will be shown in the sidebar in the admin interface
     * @see CMenu::$items
     * @var array
     */
    protected $_menuItems = array(
        array(
            "label" => "Статистика",
            "url" => "#",
            "linkOptions" => array(
                "icon" => "icon-music",
            ),
            "items" => array(
                array(
                    "label" => "Поиски перелётов",
                    "url" => array("/admin/statistic/search/flight"),
                ),
            )
        )
    );
}
