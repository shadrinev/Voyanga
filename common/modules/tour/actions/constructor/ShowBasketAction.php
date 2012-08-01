<?php
/**
 * User: Kuklin Mikhail (kuklin@voyanga.com)
 * Company: Easytrip LLC
 * Date: 01.08.12
 * Time: 11:00
 */
class ShowBasketAction extends CAction
{
    public function run($isTab=false)
    {
        if ($isTab)
            $this->controller->renderPartial('showBasket');
        else
            $this->controller->render('showBasket');
    }
}
