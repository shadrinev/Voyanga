<?php
/**
 * User: Kuklin Mikhail (mikhail@clevertech.biz)
 * Company: Clevertech LLC.
 * Date: 03.08.12 9:21
 */
class NavigationController extends FrontendController
{
    public $defaultAction = 'showMenu';

    public function actionShowMenu()
    {
        $this->render('showMenu');
    }
}
